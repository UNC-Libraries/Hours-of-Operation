<?php

namespace Hoo\Admin;

use \Hoo\Model\Location;
use \Hoo\Model\Address;
use \Hoo\View;

defined( 'ABSPATH' ) or die();

class LocationController {
  protected $screen_hook_suffix = null;

  private $actions = array( 'add', 'create', 'edit', 'update', 'delete' );

  private $sub_pages = array(
    'add' => array(
      'parent' => 'hoo-location',
      'permissions' => 'manage_options',
      'menu_title' => 'Add New'
    ),
    'edit' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Edit Location'
    )
  );

  const SLUG = 'hoo-location';

  public function __construct($entity_manager) {
    $this->entity_manager = $entity_manager;

    wp_register_style( 'location-admin', HOO__PLUGIN_URL . 'assets/css/admin.css', array(), HOO_VERSION );

    wp_register_script( 'init-postbox', HOO__PLUGIN_URL . 'assets/js/init_postbox.js', array( 'postbox' ) );

    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    $this->init_hooks();

  }

  public function add_menu_pages() {

    $this->screen_hook_suffix = add_menu_page(
      __( 'Hours of Operation', 'hoo-location' ),
      __( 'Hours of Operation', 'hoo-location' ),
      'manage_options',
      'hoo-location',
      array( $this, 'index' ),
      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );

    foreach ( $this->sub_pages as $sub_page => $options ) {
      add_submenu_page(
        $options['parent'],
        __( $options['menu_title'], 'hoo-location' ),
        __( $options['menu_title'], 'hoo-location' ),
        $options['permissions'],
        "hoo-location-$sub_page",
        array( $this, $sub_page ) );


    }
  }


  public function enqueue_scripts() {

    wp_localize_script( 'init-postbox', 'HOO', array( 'page' => $_REQUEST['page'] ) );

    wp_enqueue_style( 'location-admin' );
    wp_enqueue_script( 'init-postbox' );

  }

  public function init_hooks() {

  }

  public function index() {

    $locations_table = new LocationList( $this->entity_manager );

    $locations_table->prepare_items();

    $view = new View( 'admin/location/index' );
    $view->render(
      array(
        'title' => 'Locations',
        'locations-table' => $locations_table
      ) );

  }

  private function add_meta_boxes( $location ) {

    $location_info_fields = new View( 'admin/partials/location_form_location_info_fields' );
    $location_publish_fields = new View( 'admin/partials/location_form_publish_fields' );
    $location_address_fields = new View( 'admin/partials/location_form_address_fields' );

    add_meta_box(
      'location-publish',
      'Publish',
      array( $location_publish_fields, 'render_metabox' ),
      'hoo-location-edit',
      'side',
      'high',
      array( 'location' => $location ) );

    add_meta_box(
      'location-info',
      'Location Info',
      array( $location_info_fields, 'render_metabox' ),
      'hoo-location-edit',
      'normal',
      'high',array( 'location' => $location ) );

    add_meta_box(
      'location-address',
      'Location Address',
      array( $location_address_fields, 'render_metabox' ),
      'hoo-location-edit',
      'normal',
      'high',array( 'location' => $location ) );

  }

  public function edit() {


    $view = new View( 'admin/location/location' );
    $view_options = array(
      'title' => 'Edit a Location',
      'page' => 'hoo-location-edit',
      'columns' => 2 );

    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_REQUEST['location_id'] );

    switch( $_REQUEST['action'] ) {
      case 'update':
        $location = $location->fromArray( $_REQUEST['location'] );
        $view_options['location'] = $location;
        $this->entity_manager->flush();
        break;

      default:
        $view_options['action'] = 'update';
        $view_options['location'] = $location;

    }

    $this->add_meta_boxes( $location );

    $view->render( $view_options );
  }

  public function create() {

    $location = new Location();
    $location = $location->fromArray( $_REQUEST['location'] );

    $this->entity_manager->persist( $location );
    $this->entity_manager->flush();

  }

  public function add() {
    $location = new Location();


    if ( $_REQUEST['action'] == 'create' ) {
      $location = $location->fromArray( $_REQUEST['location'] );
      $this->entity_manager->persist( $location );
      $this->entity_manager->flush();
      $this->index();
    } else {

      $view = new View( 'admin/location/location' );

      $this->add_meta_boxes( $location );

      $view->render(
        array(
          'title' => 'Add a Location',
          'columns' => 2,
          'location' => $location,
          'page' => 'hoo-location-add',
          'action' => 'create',
          'action-display' => 'Add' )
      );
    }
  }

  public function add_action_links( $links ) {

    return array_merge(
      array(
        'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ),
      $links );

  }

  public static function get_page_url() {

  }
}

?>

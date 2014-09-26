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
      'menu_title' => 'Add New Location'
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
    wp_register_script( 'location-order', HOO__PLUGIN_URL . 'assets/js/location-order.js', array( 'jquery-ui-sortable' ) );

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

    wp_enqueue_script( 'location-order' );
    wp_enqueue_script( 'init-postbox' );

  }

  public function init_hooks() {

    add_action( 'wp_ajax_location_order', array( $this, 'ajax_location_order' ) );

  }

  public function index() {
    $view_options = array( 'title' => 'Locations' );

    $locations_table = new LocationList( $this->entity_manager );

    $locations_table->prepare_items();
    $view_options['locations-table'] = $locations_table;

    $view = new View( 'admin/location/index' );
    $view->render( $view_options );

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
      'action' => 'update',
      'page' => 'hoo-location-edit',
      'columns' => 2 );

    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_REQUEST['location_id'] );
    $this->entity_manager->persist( $location );

    if ( $_REQUEST['action'] == 'update' ) {
      $location = $location->fromArray( $_REQUEST['location'] );
      $view_options['location'] = $location;
      $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Location updated' );
      $this->entity_manager->flush();
    } else {
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

      $locations_table = new LocationList( $this->entity_manager );
      $locations_table->prepare_items();

      $view_options = array(
        'locations-table' => $locations_table,
        'notification' => array( 'type' => 'updated', 'message' => 'Location Added' )
      );

      $view = new View( 'admin/location/index' );

    } else {

      $view = new View( 'admin/location/location' );

      $this->add_meta_boxes( $location );
      $view_options = array(
        'title' => 'Add a Location',
        'columns' => 2,
        'location' => $location,
        'page' => 'hoo-location-add',
        'action' => 'create',
        'action-display' => 'Add'
      );

    }

    $view->render( $view_options );
  }

  public function add_action_links( $links ) {

    return array_merge(
      array(
        'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ),
      $links );

  }
  

  public function ajax_location_order() {

    $locations_order = $_POST['location'];
    
    foreach( $locations_order as $position => $location_id ) {
      $location = $this->entity_manager->find( '\Hoo\Model\Location', $location_id );
      $location->position = $position;
      $this->entity_manager->flush();
    }

    wp_send_json_success();
    exit;
  }

  public static function get_page_url() {

  }
}

?>

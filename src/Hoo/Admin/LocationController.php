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
    'index' => array(
      'parent' => 'hoo',
      'permissions' => 'manage_options',
      'menu_title' => 'Locations',
      'slug' => 'hoo'
    ),
    'add' => array(
      'parent' => 'hoo',
      'permissions' => 'manage_options',
      'menu_title' => 'Add New Location',
      'slug' => 'hoo-location-add'
    ),
    'edit' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Edit Location',
      'slug' => 'hoo-location-edit'
    )
  );

  const SLUG = 'hoo-location';

  public function __construct($entity_manager) {
    $this->entity_manager = $entity_manager;

    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    $this->init_hooks();
  }

  public function add_menu_pages() {
    foreach ( $this->sub_pages as $sub_page => $options ) {
      add_submenu_page(
        $options['parent'],
        __( $options['menu_title'], 'hoo-location' ),
        __( $options['menu_title'], 'hoo-location' ),
        $options['permissions'],
        $options['slug'],
        array( $this, $sub_page ) );
    }
  }


  public function enqueue_scripts() {
    $current_screen = get_current_screen();

    // only enqueue for location pages
    if ( preg_match( '/hoo(-location)?/i', $current_screen->id ) ) {

      wp_enqueue_style( 'location-admin' );

      wp_enqueue_script( 'location-delete' );
      wp_enqueue_script( 'location-order' );
    }
  }

  public function init_hooks() {

    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    add_action( 'wp_ajax_location_order', array( $this, 'ajax_location_order' ) );
    add_action( 'wp_ajax_location_delete', array( $this, 'ajax_location_delete' ) );

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

    $location_info_fields = new View( 'admin/location/form_info_fields' );
    $location_publish_fields = new View( 'admin/location/form_publish_fields' );
    $location_address_fields = new View( 'admin/location/form_address_fields' );


    add_meta_box(
      'location-publish',
      'Publish',
      array( $location_publish_fields, 'render_metabox' ),
      $_GET['page'],
      'side',
      'high',
      array( 'location' => $location ) );

    $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
    $parent_locations = $locations_repo->findBy( array(), array( 'position' => 'asc' ) );
    $parent_locations = array_filter( $parent_locations, function( $p_location ) use ( $location ) { return $location->id != $p_location->id;  } ); // can't be own parent :D
    add_meta_box(
      'location-info',
      'Location Info',
      array( $location_info_fields, 'render_metabox' ),
      $_GET['page'],
      'normal',
      'high',array( 'location' => $location, 'parent-locations' => $parent_locations ) );

    add_meta_box(
      'location-address',
      'Location Address',
      array( $location_address_fields, 'render_metabox' ),
      $_GET['page'],
      'normal',
      'high',array( 'location' => $location ) );

  }

  public function edit() {

    $page = 'hoo-location-edit';

    $view = new View( 'admin/location/location' );
    $view_options = array(
      'title' => 'Edit a Location',
      'action' => 'update',
      'page' => 'hoo-location-edit',
      'columns' => 2 );

    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_REQUEST['location_id'] );
    $this->entity_manager->persist( $location );

    switch( $_POST['action'] ) {
      case 'update':
        $location_data = $_REQUEST['location'];

        // update associations first
        $location->address->fromArray( $location_data['address'] );
        $location->parent = $this->entity_manager->find( '\Hoo\Model\Location', $location_data['parent'] );

        // don't pass association data to fromArray method for location
        unset( $location_data['address'] ); unset( $location_data['parent'] );

        // set main location data now
        $location = $location->fromArray( $location_data );

        $view_options['location'] = $location;
        $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Location updated' );
        $this->entity_manager->flush();
        $this->add_meta_boxes( $location );
        break;

      case 'delete':

        $location_id = $_POST['location_id'];

        $location = $this->entity_manager->find( '\Hoo\Model\Location', $location_id );
        $this->entity_manager->persist( $location );

        $location->remove();
        $location->flush();

        $view_options = array(
          'locations-table' => $locations_table,
          'notification' => array( 'type' => 'updated', 'message' => 'Location Added' )
        );
        $view = new View( 'admin/location/index' );
      default:
        $this->add_meta_boxes( $location );
    }

    $view_options['location'] = $location;
    $view->render( $view_options );

  }

  public function add() {

    if ( $_REQUEST['action'] == 'create' ) {
      $location_data = $_REQUEST['location'];

      $location_data['address'] = new Address( $location_data['address'] );
      $location_data['parent'] =  $this->entity_manager->find( '\Hoo\Model\Location', $location_data['parent'] );

      $location = new Location( $location_data );
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
      $location = new Location();
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

  public function ajax_location_delete() {
    $location_id = $_POST['location_id'];

    $location = $this->entity_manager->find( '\Hoo\Model\Location', $location_id );
    $this->entity_manager->remove( $location );
    $this->entity_manager->flush();

    wp_send_json_success();
    exit;
  }

  public static function get_page_url() {

  }
}

?>

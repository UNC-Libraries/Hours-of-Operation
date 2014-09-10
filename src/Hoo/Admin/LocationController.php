<?php

namespace Hoo\Admin;

use \Hoo\Model\Location;
use \Hoo\Model\Address;
use \Hoo\View;

defined( 'ABSPATH' ) or die();

class LocationController {
  protected $screen_hook_suffix = null;

  private $actions = array( 'add', 'create', 'edit', 'update', 'delete' );

  const SLUG = 'hoo-location';

  public function __construct($entity_manager) {
    $this->entity_manager = $entity_manager;

    $this->init_hooks();


  }

  public function route() {
    if ( in_array( $_REQUEST['action'], $this->actions ) ) {
      $this->$_REQUEST['action']();
    } else {
      $this->index();
    }
  }
  public function init_hooks() {

    wp_enqueue_style(
      LocationController::SLUG . '-admin-styles',
      HOO__PLUGIN_URL . 'assets/css/admin.css',
      array(),
      HOO_VERSION);
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

  public function edit() {

    $view = new View( 'admin/location/edit' );
    
    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_REQUEST['location_id'] );

    $view->render(
      array(
        'title' => 'Edit a Location',
        'location' => $location, 
        'action' => 'update',
        'action-display' => 'Update'
      )
    );
  }
  
  public function create() {

    $location = new Location();
    $location = $location->fromArray( $_REQUEST['location'] );

    $this->entity_manager->persist( $location );
    $this->entity_manager->flush();
    
  }

  public function add( $location = null) {
    if( empty( $location ) ) {
      $location = new Location();
    } 
    $address = new Address();

    $view = new View( 'admin/location/add' );

    $view->render(
      array(
        'title' => 'Add a Location',
        'location' => $location,
        'action' => 'create',
        'action-display' => 'Add'
      )
    );

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

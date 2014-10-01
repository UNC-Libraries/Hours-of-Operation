<?php 

namespace Hoo\Admin;

use \Hoo\Model\Event;
use \Hoo\View;
use \Hoo\Utils;

defined( 'ABSPATH' ) or die();

class EventController {
  private $sub_pages = array(
    'index' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Hours Events',
      'slug' => 'hoo-location-events'
    ),
    'add' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Add New Location Event',
      'slug' => 'hoo-location-event-add'
    ),
    'edit' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Edit Location Event',
      'slug' => 'hoo-location-event-edit'
    )
  );
  
  public function __construct( $entity_manager ) {
    $this->entity_manager = $entity_manager;
   
    $this->init_hooks();
  }
  
  public function init_hooks() {
    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
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
  
  public function index() {
    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );

    $view_options = array( 'title' => sprintf( '%s Hours Events', $location->name ) );

    $events_table = new EventList( $this->entity_manager, $location );

    $events_table->prepare_items();
    $view_options['events-table'] = $events_table;

    $view = new View( 'admin/events/index' );
    $view->render( $view_options );
  }
}

 ?>

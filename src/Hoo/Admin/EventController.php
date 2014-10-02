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
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
  }
  
  public function enqueue_scripts() {
    $current_screen = get_current_screen();

    // only enqueue for location pages
    if ( preg_match( '/hoo-location(-event)?/i', $current_screen->id ) ) {
      wp_localize_script( 'init-postbox', 'HOO', array( 'page' => $_REQUEST['page'] ) );

      wp_enqueue_style( 'datetime-picker' );

      wp_enqueue_script( 'full-calendar' );
      wp_enqueue_script( 'event-edit' );
      wp_enqueue_script( 'init-postbox' );
    }
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
  
  public function add_meta_boxes( $page, $event ) {
    $event_details_fields = new View( 'admin/event/form_details_fields' );
    $event_publish_fields = new View( 'admin/event/form_publish_fields' );
    $event_general_fields = new View( 'admin/event/form_general_fields' );
    
    add_meta_box( 'event-publish',
                  'Publish',
                  array( $event_publish_fields, 'render_metabox' ),
                  $page,
                  'side',
                  'high',
                  array( 'event' => $event ) );

    $category_repo = $this->entity_manager->getRepository( '\Hoo\Model\Category' );
    $categories = $category_repo->findAll();
    add_meta_box( 'event-general',
                  'General',
                  array( $event_general_fields, 'render_metabox' ),
                  $page,
                  'normal',
                  'high',
                  array( 'event' => $event,
                         'event-categories' => $categories ) );

    add_meta_box( 'event-details',
                  'Details',
                  array( $event_details_fields, 'render_metabox' ),
                  $page,
                  'normal',
                  'high',
                  array( 'event' => $event ) );

  }
  
  public function index() {
    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );

    $view_options = array( 'title' => sprintf( '%s Hours Events', $location->name ) );

    $events_table = new EventList( $this->entity_manager, $location );

    $events_table->prepare_items();
    $view_options['events-table'] = $events_table;

    $view = new View( 'admin/event/index' );
    $view->render( $view_options );
  }
  
  public function add() {
    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );

    $view = new View( 'admin/event/event' );
    $view_options = array( 'page' => 'hoo-location-event-add',
                           'columns' => 2 );

    if ( $_POST['action'] == 'create' ) {
      //stub
    } else {
      $event = new Event();

      $this->add_meta_boxes( $view_options['page'], $event );
      $view_options = array_merge( $view_options, array( 'title' => sprintf( 'Add an Hours Event for <em>%s</em>', $location->name ),
                                                         'event' => $event,
                                                         'action' => 'create',
                                                         'action-display' => 'Add' ) );
    }
    $view->render( $view_options );
  }
}
 ?>

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

    // enqueue edit/add page specific js
    if ( preg_match( '/hoo-location-event-(add|edit)?/i', $current_screen->id ) ) {
      wp_enqueue_script( 'event-edit' );
      wp_enqueue_style( 'jquery-ui' );
      wp_enqueue_style( 'full-calendar' );
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

  public function add_meta_boxes( $event ) {
    $event_details_fields = new View( 'admin/event/form_details_fields' );
    $event_publish_fields = new View( 'admin/event/form_publish_fields' );
    $event_general_fields = new View( 'admin/event/form_general_fields' );

    add_meta_box( 'event-publish',
                  'Publish',
                  array( $event_publish_fields, 'render_metabox' ),
                  $_GET['page'],
                  'side',
                  'high',
                  array( 'event' => $event ) );

    $category_repo = $this->entity_manager->getRepository( '\Hoo\Model\Category' );
    $categories = $category_repo->findAll();
    add_meta_box( 'event-general',
                  'General',
                  array( $event_general_fields, 'render_metabox' ),
                  $_GET['page'],
                  'normal',
                  'high',
                  array( 'event' => $event,
                         'event-categories' => $categories ) );

    add_meta_box( 'event-details',
                  'Details',
                  array( $event_details_fields, 'render_metabox' ),
                  $_GET['page'],
                  'normal',
                  'high',
                  array( 'event' => $event ) );

  }

  public function index() {
    $location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );

    $view = new View( 'admin/event/index' );
    $view_options = array( 'title' => sprintf( '%s Hours Events', $location->name ) );

    $events_table = new EventList( $this->entity_manager, $location );

    $events_table->prepare_items();
    $view_options['events-table'] = $events_table;

    $view->render( $view_options );
  }

  public function add() {

    if ( $_POST['action'] == 'create' ) {
      $event_data = $_POST['event'];

      $event_data['location'] = $this->entity_manager->find( '\Hoo\Model\Location', $event_data['location'] );
      $event_data['category'] = $this->entity_manager->find( '\Hoo\Model\Category', $event_data['category'] );
      $event_data['start'] = new \Datetime( $event_data['start'] );
      $event_data['end'] = new \Datetime( $event_data['end'] );

      $event = new Event( $event_data );
      $this->entity_manager->persist( $event );
      $this->entity_manager->flush();

      wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s', 'hoo-location-events', $even_data['location']->id ) ) );
      exit;
    } else {
      $location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );
      $event = new Event();

      $view_options = array( 'page' => 'hoo-location-event-add',
                             'columns' => 2 );

      $this->add_meta_boxes( $view_options['page'], $event );
      $view_options = array_merge( $view_options, array( 'title' => sprintf( 'Add an Hours Event for <em>%s</em>', $location->name ),
                                                         'event' => $event,
                                                         'location' => $location,
                                                         'action' => 'create',
                                                         'action-display' => 'Add' ) );

      $view = new View( 'admin/event/event' );
      $view->render( $view_options );
    }

  }
}
?>

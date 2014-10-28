<?php

namespace Hoo\Admin;

use \Hoo\Model\Event;
use \Hoo\View;
use \Hoo\Utils;

use \Recurr\Rule as RRule;
use \Recurr\Transformer\ArrayTransformer as RRuleTransformer;
use \Recurr\Transformer\Constraint\BetweenConstraint;
use \Recurr\Transformer\Constraint\BeforeConstraint;
use Doctrine\Common\Collections\Criteria as Criteria;

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

    add_action( 'wp_ajax_location_events', array( $this, 'ajax_location_events' ) );
    add_action( 'wp_ajax_location_event_delete', array( $this, 'ajax_location_event_delete' ) );
  }

  public function enqueue_scripts() {
    $current_screen = get_current_screen();

    wp_enqueue_script( 'event-delete' );

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
    $view_options = array( 'title' => sprintf( '%s Hours Events', $location->name ),
                           'add-new-page' => sprintf( 'hoo-location-event-add&location_id=%s', $_GET['location_id'] ) );

    $events_table = new EventList( $this->entity_manager, $location );

    $events_table->prepare_items();
    $view_options['events-table'] = $events_table;

    $view->render( $view_options );
  }

  public function edit() {

    switch( $_POST['action'] ) {
      case 'update':
        $event_data = $_POST['event'];

        $current_tz = new \DateTimeZone( get_option( 'timezone_string' ) );
        $utc_tz = new \DateTimeZone( 'UTC' );
        $start = new \Datetime( $event_data['start'], $current_tz );
        $end = new \Datetime( $event_data['end'], $current_tz );
        $start->setTimezone( $utc_tz );
        $end->setTimezone( $utc_tz );

        $event = $this->entity_manager->find( '\Hoo\Model\Event', $event_data['id'] );
        $event_data['category'] = $this->entity_manager->find( '\Hoo\Model\Category', $event_data['category'] );
        $event_data['location'] = $this->entity_manager->find( '\Hoo\Model\Location', $event_data['location'] );
        $event_data['start'] = $start;
        $event_data['end'] = $end;
        $event = $event->fromArray( $event_data );

        $this->entity_manager->persist( $event );
        $this->entity_manager->flush();

        wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&event_id=%s', 'hoo-location-event-edit', $event_data['id'] ) ) );
        exit;
      case 'delete':
        $event_data = $_POST['event'];

        $event = $this->entity_manager->find( '\Hoo\Model\Event', $event_data['id'] );
        $this->entity_manager->persist( $location );

        $location->remove();
        $location->flush();

        wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s', 'hoo-location-events', $even_data['location']->id ) ) );
        exit;
      default:
        $event = $this->entity_manager->find( '\Hoo\Model\Event', $_GET['event_id'] );

        $view = new View( 'admin/event/event' );

        $view_options = array( 'title' => sprintf( 'Edit %s', $event->label ),
                               'event' => $event,
                               'action' => 'update',
                               'page' => $_GET['page'],
                               'columns' => 2 );

        $this->add_meta_boxes( $event );

        $view->render( $view_options );
    }
  }

  public function add() {

    if ( $_POST['action'] == 'create' ) {
      $event_data = $_POST['event'];

      $current_tz = new \DateTimeZone( get_option( 'timezone_string' ) );
      $utc_tz = new \DateTimeZone( 'UTC' );
      $start = new \Datetime( $event_data['start'], $current_tz );
      $end = new \Datetime( $event_data['end'], $current_tz );
      $start->setTimezone( $utc_tz );
      $end->setTimezone( $utc_tz );

      $event_data['location'] = $this->entity_manager->find( '\Hoo\Model\Location', $event_data['location'] );
      $event_data['category'] = $this->entity_manager->find( '\Hoo\Model\Category', $event_data['category'] );
      $event_data['start'] = $start;
      $event_data['end'] = $end;

      $event = new Event( $event_data );
      $this->entity_manager->persist( $event );
      $this->entity_manager->flush();

      wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s', 'hoo-location-events', $event_data['location']->id ) ) );
      exit;
    } else {
      $event = new Event();
      $event->location = $this->entity_manager->find( '\Hoo\Model\Location', $_GET['location_id'] );


      $view_options = array( 'page' => 'hoo-location-event-add',
                             'columns' => 2 );

      $this->add_meta_boxes( $event );
      $view_options = array_merge( $view_options, array( 'title' => sprintf( 'Add an Hours Event for <em>%s</em>', $location->name ),
                                                         'event' => $event,
                                                         'action' => 'create',
                                                         'action-display' => 'Add' ) );

      $view = new View( 'admin/event/event' );
      $view->render( $view_options );
    }

  }

  public function ajax_location_event_delete() {
    $event_id = $_POST['event_id'];

    $event = $this->entity_manager->find( '\Hoo\Model\Event', $event_id );
    $this->entity_manager->remove( $event );
    $this->entity_manager->flush();

    wp_send_json_success();
    exit;
  }

  public function ajax_location_events() {
    // params passed by fullcalendar 
    $location_id = $_GET['location_id'];
    $cal_start = new \Datetime( $_GET['start'] );
    $cal_end = new \DateTime( $_GET['end'] );

    $tz = new \DateTimeZone( get_option( 'timezone_string') );

    $events_repo = $this->entity_manager->getRepository( '\Hoo\Model\Event' );
    $events = $events_repo->findBy( array( 'location' => $location_id ) );

    $rrule_transformer = new RRuleTransformer();

    $event_instances = array();
    foreach( $events as $event ) {
      $event->start->setTimeZone( $tz );
      $event->end->setTimeZone( $tz );
      $rrule = new RRule( $event->recurrence_rule, $event->start, $event->end, get_option( 'timezone_string' ) );
      $cal_range = new BetweenConstraint( $cal_start, $cal_end, $tz ) ;
      
      foreach( $rrule_transformer->transform( $rrule, nil, $cal_range )->toArray() as $recurrence ) {
        $event_instances[] = array( 'id' => $event->id,
                                    'title' => Utils::format_time( $recurrence->getStart(), $recurrence->getEnd() ),
                                    'start' => $recurrence->getStart()->format( \DateTime::ISO8601 ),
                                    'end' => $recurrence->getEnd()->format( \DateTime::ISO8601 ),
                                    'color' => $event->category->color );
      }
    }

    wp_send_json( $event_instances );
    exit;
  }
}
?>

<?php

namespace Hoo\Admin;

use Hoo\Model\Event;
use Hoo\Model\Category;
use Hoo\View;
use Hoo\Utils;

use Recurr\Rule as RRule;
use Recurr\Transformer\ArrayTransformer as RRuleTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Doctrine\Common\Collections\Criteria as Criteria;

defined( 'ABSPATH' ) or die();

class EventController {
    private $entity_manager = null;

    private $sub_pages = array(
        'index' => array(
            'parent' => null,
            'permissions' => 'edit_pages',
            'menu_title' => 'Hours Events',
            'slug' => 'hoo-location-events'
        ),
        'add' => array(
            'parent' => null,
            'permissions' => 'edit_pages',
            'menu_title' => 'Add New Location Event',
            'slug' => 'hoo-location-event-add'
        ),
        'edit' => array(
            'parent' => null,
            'permissions' => 'edit_pages',
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

        add_action( 'wp_ajax_hour_events', array( $this, 'ajax_hour_events' ) );
        add_action( 'wp_ajax_nopriv_hour_events', array( $this, 'ajax_hour_events' ) );

        add_action( 'wp_ajax_location_events', array( $this, 'ajax_location_events' ) );
        add_action( 'wp_ajax_location_event_delete', array( $this, 'ajax_location_event_delete' ) );
    }

    public function enqueue_scripts( $page ) {
        if ( preg_match( '/hoo-location-event/i', $page ) ) {
            wp_enqueue_script( 'event-delete' );
        }

        // enqueue edit/add page specific js
        if ( preg_match( '/hoo-location-event-(add|edit)?/i', $page ) ) {
            wp_enqueue_style( 'full-calendar' );
            wp_enqueue_script( 'event-edit' );
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

        $category_repo = $this->entity_manager->getRepository( 'Hoo\Model\Category' );
        $categories = $category_repo->findAll();
        add_meta_box( 'event-general',
                      'General',
                      array( $event_general_fields, 'render_metabox' ),
                      $_GET['page'],
                      'normal',
                      'high',
                      array( 'event' => $event,
                             'event-categories' => $categories ) );

        $freq_values = array( 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Custom' );
        add_meta_box( 'event-details',
                      'Details',
                      array( $event_details_fields, 'render_metabox' ),
                      $_GET['page'],
                      'normal',
                      'high',
                      array( 'event' => $event,
                             'freq_values' => $freq_values,
                             'event-categories' => $categories,
                             'freq_units' => array( 'HOURLY' => 'hour', 'DAILY' => 'day', 'WEEKLY' => 'week' ),
                             'cust_freq_values' => array_slice( $freq_values, 0, -1 ) ) );

    }

    public function index() {
        $location = $this->entity_manager->find( 'Hoo\Model\Location', $_GET['location_id'] );

        $view = new View( 'admin/event/index' );
        $view_options = array( 'title' => sprintf( '%s Hours Events', $location->name ),
                               'location-id' => $location->id,
                               'page' => $_GET['page'],
                               'add-new-page' => sprintf( 'hoo-location-event-add&location_id=%s', $_GET['location_id'] ) );

        $events_table = new EventList( $this->entity_manager, $location );

        $events_table->prepare_items();

        if ( isset ( $_GET['updated'] ) )
            $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Event Added' );
        $view_options['events-table'] = $events_table;

        $view->render( $view_options );
    }

    public function edit() {
        $current_tz = new \DateTimeZone( get_option( 'timezone_string' ) );

        switch( isset( $_POST['action'] ) && $_POST['action'] ) {
            case 'update':
                $event = $this->entity_manager->find( 'Hoo\Model\Event', $_POST['event']['id'] );
                $event->fromParams( $_POST, $this->entity_manager );
                $this->entity_manager->persist( $event );
                $this->entity_manager->flush();

                wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&event_id=%s&updated=2', 'hoo-location-event-edit', $_POST['event']['id'] ) ) );
                exit;
            case 'delete':
                $event = $this->entity_manager->find( 'Hoo\Model\Event', $_POST['event']['id'] );
                $this->entity_manager->persist( $event );

                $event->remove();
                $event->flush();

                wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s', 'hoo-location-events', $event_data['location']->id ) ) );
                exit;
            default:

                $event = $this->entity_manager->find( 'Hoo\Model\Event', $_GET['event_id'] );
                $event->recurrence_rule = new RRule( $event->recurrence_rule, $event->start, $event->end, get_option( 'timezone_string' ) );

                $view = new View( 'admin/event/event' );

                $view_options = array( 'title' => sprintf( 'Edit %s', $event->title ),
                                       'event' => $event,
                                       'action' => 'update',
                                       'page' => $_GET['page'],
                                       'add-new-page' => sprintf( 'hoo-location-event-add&location_id=%s', $event->location->id ),
                                       'breadcrumbs' => array( 'Locations' => 'hoo',
                                                               sprintf( '%s Hours', $event->location->name ) => sprintf( '%s&location_id=%s', 'hoo-location-events', $event->location->id ),
                                                               $event->title => null ),

                                       'columns' => 2 );

                if ( isset ( $_GET['updated'] ) )
                    $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Event Updated' );
                
                $this->add_meta_boxes( $event );

                $view->render( $view_options );
        }
    }

    public function add() {

        if ( isset( $_POST['action'] ) && $_POST['action'] == 'create' ) {
            $event = new Event( $_POST, $this->entity_manager );
            $this->entity_manager->persist( $event );
            $this->entity_manager->flush();

            wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s&updated=1', 'hoo-location-events', $event->location->id ) ) );
            exit;
        } else {
            $event = new Event();
            $event->location = $this->entity_manager->find( 'Hoo\Model\Location', $_GET['location_id'] );

            $view_options = array( 'page' => 'hoo-location-event-add',
                                   'columns' => 2 );

            $this->add_meta_boxes( $event );
            $view_options = array_merge( $view_options, array( 'title' => sprintf( 'Add an Hours Event for <em>%s</em>', $event->location->name ),
                                                               'event' => $event,
                                                               'action' => 'create',
                                                               'breadcrumbs' => array( 'Locations' => 'hoo',
                                                                                       sprintf( '%s Hours', $event->location->name ) => sprintf( '%s&location_id=%s', 'hoo-location-events', $event->location->id ) ),
                                                               'add-new-page' => sprintf( 'hoo-location-event-add&location_id=%s', $_GET['location_id'] ),
                                                               'action-display' => 'Add' ) );

            $view = new View( 'admin/event/event' );
            $view->render( $view_options );
        }

    }

    public function ajax_location_event_delete() {
        $event_id = $_POST['event_id'];

        $event = $this->entity_manager->find( 'Hoo\Model\Event', $event_id );
        $this->entity_manager->remove( $event );
        $this->entity_manager->flush();

        wp_send_json_success();
        exit;
    }

    public function ajax_location_events() {
        $location_id = $_GET['event']['location'];

        $location_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
        $location = $location_repo->find( $location_id );

        wp_send_json( $location->get_fullcalendar_events( $_GET, $this->entity_manager ) );
    }

    public function ajax_hour_events() {
        $location_id = $_GET['location_id'];

        $location_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
        $location = $location_repo->find( $location_id );

        wp_send_json( $location->get_fullcalendar_events( $_GET, $this->entity_manager, false ) );
    }
}
?>

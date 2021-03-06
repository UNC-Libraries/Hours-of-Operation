<?php

namespace Hoo\Admin;

use Hoo\Model\Location;
use Hoo\Model\Address;
use Hoo\View;
use Hoo\Utils;

defined( 'ABSPATH' ) or die();

class LocationController {
    private $entity_manager = null;

    protected $screen_hook_suffix = null;

    private $sub_pages = array(
        'index' => array(
            'parent' => 'hoo',
            'permissions' => 'edit_posts',
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


    public function enqueue_scripts( $page ) {

        // only enqueue for location pages
        if ( preg_match( '/hoo(-location)?/i', $page ) ) {
            wp_enqueue_style( 'location-admin' );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'location-image' );

            if ( Utils::check_user_role( 'administrator' ) ) { // only enqueue if we need to
                wp_enqueue_script( 'location-visibility' );
                wp_enqueue_script( 'location-delete' );
                wp_enqueue_script( 'location-order' );
            }
        }
    }

    public function init_hooks() {

        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_location_is_visible', array( $this, 'ajax_location_is_visible' ) );
        add_action( 'wp_ajax_location_order', array( $this, 'ajax_location_order' ) );
        add_action( 'wp_ajax_location_delete', array( $this, 'ajax_location_delete' ) );

    }

    public function index() {
        $locations_table = new LocationList( $this->entity_manager );
        $locations_table->prepare_items();

        $view_options = array( 'title'           => 'Locations',
                               'locations-table' => $locations_table,
                               'add-new-page'    => 'hoo-location-add' );

        if ( isset ( $_GET['updated'] ) )
            $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Location Added' );

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

        $locations_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
        $parent_locations = $locations_repo->findBy( array(), array( 'position' => 'asc' ) );
        $parent_locations = array_filter( $parent_locations, function( $p_location ) use ( $location ) { return $location->id != $p_location->id;  } ); // can't be own parent :D
        $wp_editor_options = array( 'textarea_name' => 'location[description]',
                                    'textarea_rows' => 5,
                                    'teeny'         => true,
                                    'media_buttons' => false );
        add_meta_box(
            'location-info',
            'Location Info',
            array( $location_info_fields, 'render_metabox' ),
            $_GET['page'],
            'normal',
            'high',array( 'location' => $location,
                          'parent-locations' => $parent_locations,
                          'wp_editor_options' => $wp_editor_options ) );
        add_meta_box(
            'location-address',
            'Location Address',
            array( $location_address_fields, 'render_metabox' ),
            $_GET['page'],
            'normal',
            'high',array( 'location' => $location ) );

    }

    public function edit() {
        $view = new View( 'admin/location/location' );
        $view_options = array(
            'title' => 'Edit a Location',
            'action' => 'update',
            'page' => 'hoo-location-edit',
            'add-new-page' => sprintf( 'hoo-location-add' ),
            'breadcrumbs' => array( 'Locations' => 'hoo',
                                    'Edit a Location' => 'hoo-location-edit' ),
            'columns' => 2 );


        switch( isset( $_POST['action'] ) && $_POST['action'] ) {
            case 'update':
                $location = $this->entity_manager->find( 'Hoo\Model\Location', $_POST['location']['id'] );
                $this->entity_manager->persist( $location );
                $location->fromParams( $_POST, $this->entity_manager );

                $this->entity_manager->flush();
                wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s&location_id=%s&updated=2', 'hoo-location-edit', $_POST['location']['id'] ) ) );
                exit;

            case 'delete':
                $location_id = $_POST['location_id'];

                $location = $this->entity_manager->find( 'Hoo\Model\Location', $location_id );
                $this->entity_manager->persist( $location );

                $location->remove();
                $location->flush();

                wp_safe_redirect( admin_url( 'admin.php?page=hoo&updated=2' ) );
                exit;
            default:
                $location = $this->entity_manager->find( 'Hoo\Model\Location', $_GET['location_id'] );

                $this->add_meta_boxes( $location );
                if ( isset ( $_GET['updated'] ) )
                    $view_options['notification'] = array( 'type' => 'updated', 'message' => 'Location Updated' );

                $view_options['location'] = $location;
                $view->render( $view_options );
        }


    }

    public function add() {

        if ( isset( $_POST['action'] ) && $_POST['action'] == 'create' ) {
            try {
                $location = new Location( $_POST, $this->entity_manager );
                $this->entity_manager->persist( $location );
                $this->entity_manager->flush();

                wp_safe_redirect( admin_url( 'admin.php?page=hoo&updated=1' ) );
                exit();
            } catch ( Exception $e ) {
            }
        } else {
            $location = new Location();
        }

        $view = new View( 'admin/location/location' );

        $this->add_meta_boxes( $location );
        $view_options = array(
            'title' => 'Add a Location',
            'columns' => 2,
            'location' => $location,
            'page' => 'hoo-location-add',
            'add-new-page' => sprintf( 'hoo-location-add' ),
            'breadcrumbs' => array( 'Locations' => 'hoo',
                                    'Add a Location' => 'hoo-location-add' ),
            'action' => 'create',
            'action-display' => 'Add'
        );


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
            $location = $this->entity_manager->find( 'Hoo\Model\Location', $location_id );
            $location->position = $position;
            $this->entity_manager->flush();
        }

        wp_send_json_success();
        exit;
    }

    public function ajax_location_delete() {
        $location_id = $_POST['location_id'];

        $location = $this->entity_manager->find( 'Hoo\Model\Location', $location_id );
        $this->entity_manager->remove( $location );
        $this->entity_manager->flush();

        wp_send_json_success();
        exit;
    }

    public function ajax_location_is_visible() {
        $location_id = $_POST['location_id'];
        $checked = $_POST['checked'] === 'true' ? true : false;

        $location = $this->entity_manager->find( 'Hoo\Model\Location', $location_id );
        $location->is_visible = $checked;
        $this->entity_manager->flush();

        wp_send_json_success();
        exit;
    }

    public static function get_page_url() {

    }
}

?>

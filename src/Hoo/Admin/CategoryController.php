<?php

namespace Hoo\Admin;

use \Hoo\Model\Category;
use \Hoo\View;

defined( 'ABSPATH' ) or die();

class CategoryController {

    private $sub_pages = array(
        'index' => array(
            'parent' => 'hoo',
            'permissions' => 'manage_options',
            'menu_title' => 'Categories',
            'slug' => 'hoo-category'
        ),
        'add' => array(
            'parent' => 'hoo',
            'permissions' => 'manage_options',
            'menu_title' => 'Add New Category',
            'slug' => 'hoo-category-add'
        ),
        'edit' => array(
            'parent' => null,
            'permissions' => 'manage_options',
            'menu_title' => 'Edit Category',
            'slug' => 'hoo-category-edit'
        )
    );

    const SLUG = 'hoo-category';

    public function __construct($entity_manager) {
        $this->entity_manager = $entity_manager;

        $this->init_hooks();
    }

    public function add_menu_pages() {
        foreach ( $this->sub_pages as $sub_page => $options ) {
            add_submenu_page(
                $options['parent'],
                __( $options['menu_title'], 'hoo-category' ),
                __( $options['menu_title'], 'hoo-category' ),
                $options['permissions'],
                $options['slug'],
                array( $this, $sub_page ) );
        }
    }

    public function enqueue_scripts( $page ) {

        wp_enqueue_script( 'category-delete' );
        wp_enqueue_script( 'category-visibility' );
        wp_enqueue_script( 'category-order' );

        // only enqueue for category pages
        if ( preg_match( '/hoo-category-(edit|add)?/i', $page ) ) {
            wp_enqueue_script( 'category-color-picker' );
            wp_enqueue_style( 'category-color-picker' );
        }
    }

    public function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_category_is_visible', array( $this, 'ajax_category_is_visible' ) );
        add_action( 'wp_ajax_category_order', array( $this, 'ajax_category_order' ) );
        add_action( 'wp_ajax_category_delete', array( $this, 'ajax_category_delete' ) );
    }

    public function index() {
        $view_options = array( 'title' => 'Categories' );

        $categories_table = new CategoryList( $this->entity_manager );

        $categories_table->prepare_items();
        $view_options['categories-table'] = $categories_table;
        $view_options['add-new-page'] = 'hoo-category-add';

        $view = new View( 'admin/category/index' );
        $view->render( $view_options );

    }

    private function add_meta_boxes( $category ) {

        $category_info_fields = new View( 'admin/category/form_info_fields' );
        $category_publish_fields = new View( 'admin/category/form_publish_fields' );

        add_meta_box('category-publish',
                     'Publish',
                     array( $category_publish_fields, 'render_metabox' ),
                     $_GET['page'],
                     'side',
                     'high',
                     array( 'category' => $category ) );

        add_meta_box( 'category-info',
                      'Category Info',
                      array( $category_info_fields, 'render_metabox' ),
                      $_GET['page'],
                      'normal',
                      'high',array( 'category' => $category ) );

    }

    public function edit() {
        $category = $this->entity_manager->find( '\Hoo\Model\Category', $_GET['category_id'] );
        $view = new View( 'admin/category/category' );
        $view_options = array('title'   => sprintf( 'Edit %s Category', $category->name ),
                              'action'  => 'update',
                              'page'    => 'hoo-category-edit',
                              'breadcrumbs'    => array( 'Categories'    => 'hoo-category',
                                                         $category->name => null ),
                              'columns' => 2 );

        $this->entity_manager->persist( $category );

        switch( isset( $_POST['action'] ) && $_POST['action'] ) {
            case 'update':
                $category_data = $_POST['category'];

                // set main category data now
                $category = $category->fromArray( $category_data );

                $view_options['category'] = $category;
                $view_options['notification'] = array( 'type' => 'updated', 'message' => 'category updated' );
                $this->entity_manager->flush();
                $this->add_meta_boxes( $category );
                break;

            case 'delete':

                $category_id = $_POST['category_id'];

                $category = $this->entity_manager->find( '\Hoo\Model\category', $category_id );
                $this->entity_manager->persist( $category );

                $category->remove();
                $category->flush();

                $view_options = array( 'categories-table' => $categories_table,
                                       'notification'     => array( 'type' => 'updated', 'message' => 'category Added' ) );
                $view = new View( 'admin/category/index' );
            default:
                $this->add_meta_boxes( $category );
        }

        $view_options['category'] = $category;
        $view->render( $view_options );

    }

    public function add() {

        if ( isset( $_POST['action'] ) && $_POST['action'] == 'create' ) {
            $category_data = $_POST['category'];

            $category = new Category( $category_data );
            $this->entity_manager->persist( $category );
            $this->entity_manager->flush();

            $categories_table = new CategoryList( $this->entity_manager );
            $categories_table->prepare_items();

            $view_options = array( 'title'            => 'Categories',
                                   'categories-table' => $categories_table,
                                   'notification'     => array( 'type' => 'updated', 'message' => 'Category Added' )
            );

            $view = new View( 'admin/category/index' );

        } else {
            $category = new Category();
            $view = new View( 'admin/category/category' );

            $this->add_meta_boxes( $category );
            $view_options = array( 'title'          => 'Add a Category',
                                   'columns'        => 2,
                                   'category'       => $category,
                                   'page'           => 'hoo-category-add',
                                   'add-new-page'   => sprintf( 'hoo-category-add' ),
                                   'breadcrumbs'    => array( 'Categories'     => 'hoo-category',
                                                              'Add a Location' => 'hoo-category-add' ),
                                   'action'         => 'create',
                                   'action-display' => 'Add'
            );

        }

        $view->render( $view_options );
    }

    public function add_action_links( $links ) {

        return array_merge( array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ),
            $links );

    }


    public function ajax_category_order() {

        $categories_order = $_POST['category'];

        foreach( $categories_order as $priority => $category_id ) {
            $category = $this->entity_manager->find( '\Hoo\Model\Category', $category_id );
            $category->priority = $priority;
            $this->entity_manager->flush();
        }

        wp_send_json_success();
        exit;
    }

    public function ajax_category_delete() {
        $category_id = $_POST['category_id'];

        $category = $this->entity_manager->find( '\Hoo\Model\Category', $category_id );
        $this->entity_manager->remove( $category );
        $this->entity_manager->flush();

        wp_send_json_success();
        exit;
    }

    public function ajax_category_is_visible() {
        $category_id = $_POST['category_id'];
        $checked = $_POST['checked'] === 'true' ? true : false;

        $category = $this->entity_manager->find( '\Hoo\Model\Category', $category_id );
        $category->is_visible = $checked;
        $this->entity_manager->flush();

        wp_send_json_success();
        exit;
    }
}

?>

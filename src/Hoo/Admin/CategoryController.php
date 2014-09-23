<?php

namespace Hoo\Admin;

use \Hoo\Model\Category;
use \Hoo\View;

defined( 'ABSPATH' ) or die();

class CategoryController {
  protected $screen_hook_suffix = null;

  private $actions = array( 'add', 'create', 'edit', 'update', 'delete' );

  private $sub_pages = array(
    'add' => array(
      'parent' => 'hoo-category',
      'permissions' => 'manage_options',
      'menu_title' => 'Add New Category'
    ),
    'edit' => array(
      'parent' => null,
      'permissions' => 'manage_options',
      'menu_title' => 'Edit Category'
    )
  );

  const SLUG = 'hoo-category';

  public function __construct($entity_manager) {
    $this->entity_manager = $entity_manager;

    wp_register_style( 'category-admin', HOO__PLUGIN_URL . 'assets/css/admin.css', array(), HOO_VERSION );

    wp_register_script( 'init-postbox', HOO__PLUGIN_URL . 'assets/js/init_postbox.js', array( 'postbox' ) );

    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    $this->init_hooks();

  }

  public function add_menu_pages() {

    $this->screen_hook_suffix = add_menu_page(
      __( 'Hours of Operation', 'hoo-category' ),
      __( 'Hours of Operation', 'hoo-category' ),
      'manage_options',
      'hoo-category',
      array( $this, 'index' ),
      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );

    foreach ( $this->sub_pages as $sub_page => $options ) {
      add_submenu_page(
        $options['parent'],
        __( $options['menu_title'], 'hoo-category' ),
        __( $options['menu_title'], 'hoo-category' ),
        $options['permissions'],
        "hoo-category-$sub_page",
        array( $this, $sub_page ) );


    }
  }


  public function enqueue_scripts() {

    wp_localize_script( 'init-postbox', 'HOO', array( 'page' => $_REQUEST['page'] ) );

    wp_enqueue_style( 'category-admin' );
    wp_enqueue_script( 'init-postbox' );

  }

  public function init_hooks() {

  }

  public function index() {
    $view_options = array( 'title' => 'Categories' );

    $categories_table = new CategoryList( $this->entity_manager );

    $categories_table->prepare_items();
    $view_options['categories-table'] = $categories_table;

    $view = new View( 'admin/category/index' );
    $view->render( $view_options );

  }

  private function add_meta_boxes( $category ) {

    $category_info_fields = new View( 'admin/partials/category_form_category_info_fields' );
    $category_publish_fields = new View( 'admin/partials/category_form_publish_fields' );
    $category_address_fields = new View( 'admin/partials/category_form_address_fields' );

    add_meta_box(
      'category-publish',
      'Publish',
      array( $category_publish_fields, 'render_metabox' ),
      'hoo-category-edit',
      'side',
      'high',
      array( 'category' => $category ) );

    add_meta_box(
      'category-info',
      'category Info',
      array( $category_info_fields, 'render_metabox' ),
      'hoo-category-edit',
      'normal',
      'high',array( 'category' => $category ) );

    add_meta_box(
      'category-address',
      'category Address',
      array( $category_address_fields, 'render_metabox' ),
      'hoo-category-edit',
      'normal',
      'high',array( 'category' => $category ) );

  }

  public function edit() {


    $view = new View( 'admin/category/category' );
    $view_options = array(
      'title' => 'Edit a category',
      'action' => 'update',
      'page' => 'hoo-category-edit',
      'columns' => 2 );

    $category = $this->entity_manager->find( '\Hoo\Model\category', $_REQUEST['category_id'] );

    if ( $_REQUEST['action'] == 'update' ) {
      $category = $category->fromArray( $_REQUEST['category'] );
      $view_options['category'] = $category;
      $view_options['notification'] = array( 'type' => 'updated', 'message' => 'category updated' );
      $this->entity_manager->flush();
    } else {
      $view_options['category'] = $category;
    }

    $this->add_meta_boxes( $category );

    $view->render( $view_options );
  }

  public function create() {

    $category = new category();
    $category = $category->fromArray( $_REQUEST['category'] );

    $this->entity_manager->persist( $category );
    $this->entity_manager->flush();

  }

  public function add() {
    $category = new category();

    if ( $_REQUEST['action'] == 'create' ) {
      $category = $category->fromArray( $_REQUEST['category'] );
      $this->entity_manager->persist( $category );
      $this->entity_manager->flush();

      $categories_table = new CategoryList( $this->entity_manager );
      $categories_table->prepare_items();

      $view_options = array(
        'categories-table' => $categories_table,
        'notification' => array( 'type' => 'updated', 'message' => 'category Added' )
      );

      $view = new View( 'admin/category/index' );

    } else {

      $view = new View( 'admin/category/category' );

      $this->add_meta_boxes( $category );
      $view_options = array(
        'title' => 'Add a category',
        'columns' => 2,
        'category' => $category,
        'page' => 'hoo-category-add',
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

  public static function get_page_url() {

  }
}

?>

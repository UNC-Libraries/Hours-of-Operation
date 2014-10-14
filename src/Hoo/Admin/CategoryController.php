<?php

namespace Hoo\Admin;

use \Hoo\Model\Category;
use \Hoo\View;

defined( 'ABSPATH' ) or die();

class CategoryController {
  protected $screen_hook_suffix = null;

  private $actions = array( 'add', 'create', 'edit', 'update', 'delete' );

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

    add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

  public function enqueue_scripts() {

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'category-color-picker' );
  }

  public function init_hooks() {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
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
      'Category Info',
      array( $category_info_fields, 'render_metabox' ),
      'hoo-category-edit',
      'normal',
      'high',array( 'category' => $category ) );

  }

  public function edit() {


    $view = new View( 'admin/category/category' );
    $view_options = array(
      'title' => 'Edit a Category',
      'action' => 'update',
      'page' => 'hoo-category-edit',
      'columns' => 2 );

    $category = $this->entity_manager->find( '\Hoo\Model\Category', $_REQUEST['category_id'] );
    $this->entity_manager->persist( $category );

    switch( $_POST['action'] ) {
      case 'update':
        $category_data = $_REQUEST['category'];

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

        $view_options = array(
          'categories-table' => $categories_table,
          'notification' => array( 'type' => 'updated', 'message' => 'category Added' )
        );
        $view = new View( 'admin/category/index' );
      default:
        $this->add_meta_boxes( $category );
    }

    $view_options['category'] = $category;
    $view->render( $view_options );

  }

  public function add() {

    if ( $_REQUEST['action'] == 'create' ) {
      $category_data = $_REQUEST['category'];

      $category = new Category( $category_data );
      $this->entity_manager->persist( $category );
      $this->entity_manager->flush();

      $categories_table = new CategoryList( $this->entity_manager );
      $categories_table->prepare_items();

      $view_options = array(
        'categories-table' => $categories_table,
        'notification' => array( 'type' => 'updated', 'message' => 'Category Added' )
      );

      $view = new View( 'admin/category/index' );

    } else {
      $category = new Category();
      $view = new View( 'admin/category/category' );

      $this->add_meta_boxes( $category );
      $view_options = array(
        'title' => 'Add a Category',
        'columns' => 2,
        'category' => $category,
        'page' => 'hoo-category-add',
        'action' => 'create',
        'action-display' => 'Add'
      );

    }

    $view->render( $view_options );
  }

  public function enqueue_color_picker( $hook_suffix ) {
  
    //Access the global $wp_version variable to see which version of WordPress is installed.
    global $wp_version;
 
    //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
    if ( 3.5 <= $wp_version ){
        //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
        $picker = 'wp-color-picker';
        wp_enqueue_style( $picker );
        wp_enqueue_script( $picker );

    }

    //If the WordPress version is less than 3.5 load the older farbtasic color picker.
    else {
        //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
        $picker = 'farbtastic';
        wp_enqueue_style( $picker );
        wp_enqueue_script( $picker );
    }

    //Load our custom javascript file
    wp_enqueue_script( 'category-color-picker', plugins_url('color-picker.js', __FILE__ ), array( $picker ), false, true );
  
    }


  public static function get_page_url() {

  }
}

?>

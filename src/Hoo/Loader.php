<?php

namespace Hoo;

use \Hoo\Admin\LocationController;
use \Doctrine\ORM\Tools\Setup as ORMSetup;
use \Doctrine\ORM\EntityManager;

class Loader {

  const SLUG = 'hoo';
  private $tables = array(
    'hoo_locations'  => 'Hoo\Model\Location',
    'hoo_addresses'  => 'Hoo\Model\Address',
    'hoo_events'     => 'Hoo\Model\Event',
    'hoo_categories' => 'Hoo\Model\Category'
  );

  /**
     Responsible for setting up database access and choosing correct controller
   */
  public function __construct() {

    $db_params = array(
      'driver' => 'pdo_mysql',
      'user' => DB_USER,
      'password' => DB_PASSWORD,
      'host' => DB_HOST,
      'dbname' => DB_NAME );

    $is_dev_mode = true;

    $config = ORMSetup::createAnnotationMetadataConfiguration(array( HOO__PLUGIN_DIR . 'Hoo/Model' ), $is_dev_mode, null, null, false );
    $entity_manager = EntityManager::create( $db_params, $config );

    $this->entity_manager = $entity_manager;

    $this->shortcode = new Shortcode( $entity_manager );

    if ( is_admin() ) {
      $this->init_admin_hooks();
      $this->init_controllers();
    }

    $this->init_public_hooks();
  }

  /**
     activate the plugin

     load the model and create the db schema from annotations
     @return void
   */
  public function activate() {
    $schema_tool = new \Doctrine\ORM\Tools\SchemaTool( $this->entity_manager );
    $schema_manager = $this->entity_manager->getConnection()->getSchemaManager();

    foreach ( $this->tables as $table => $class_name ) {
      $class = $this->entity_manager->getClassMetadata( $class_name );

      if ( $schema_manager->tablesExist( array( $table ) ) ) {
        // update schema?
      }
      else {
        $schema_tool->createSchema( array( $class ) );
      }

    }
  }

  public function deactivate() {
  }


  /**
     register all the global admin hooks like adding the admin menus
     @return void
   */
  private function init_admin_hooks() {
    global $wp_version;
    // let's register some scripts/styles
    wp_register_style( 'jquery-ui', HOO__PLUGIN_URL . 'assets/css/jquery-ui.css' );
    wp_register_style( 'full-calendar', HOO__PLUGIN_URL . 'assets/css/fullcalendar.min.css' );

    wp_enqueue_script( 'init-postbox', HOO__PLUGIN_URL . 'assets/js/init_postbox.js', array( 'postbox' ) );

    // category stuff
    $color_picker = 3.4 <= $wp_version ? 'wp-color-picker' : 'farbtastic';
    wp_register_script( 'category-color-picker', HOO__PLUGIN_URL . 'assets/js/color-picker.js', array( $color_picker ) );
    wp_register_style( 'category-color-picker', NULL, array( $color_picker ) );

    // location stuff
    wp_register_script( 'location-order', HOO__PLUGIN_URL . 'assets/js/location-order.js', array( 'jquery-ui-sortable' ) );
    wp_register_script( 'location-delete', HOO__PLUGIN_URL . 'assets/js/location-delete.js', array( 'jquery' ) );
    wp_register_script( 'location-image', HOO__PLUGIN_URL . 'assets/js/location-image.js', array( 'media-upload', 'thickbox' ) );

    // category stuff
    wp_register_script( 'category-color-picker', HOO__PLUGIN_URL . 'assets/js/color-picker.js', array( 'jquery' ) );
    wp_register_script( 'category-order', HOO__PLUGIN_URL . 'assets/js/category-order.js', array( 'jquery-ui-sortable' ) );
    wp_register_script( 'category-delete', HOO__PLUGIN_URL . 'assets/js/category-delete.js', array( 'jquery' ) );

    // event stuff
    wp_register_script( 'moment', HOO__PLUGIN_URL . 'assets/js/vendor/moment.min.js' );
    wp_register_script( 'full-calendar', HOO__PLUGIN_URL . 'assets/js/vendor/fullcalendar.min.js', array( 'jquery', 'moment' ) );
    wp_register_script( 'jquery-timepicker-addon',
                        HOO__PLUGIN_URL . 'assets/js/vendor/jquery-ui-timepicker-addon.js',
                        array("jquery-ui-core",            //UI Core - do not remove this one
                              "jquery-ui-slider",
                              "jquery-ui-datepicker" ) );

    wp_register_script( 'event-edit', HOO__PLUGIN_URL . 'assets/js/event-edit.js', array( 'jquery-timepicker-addon', 'full-calendar' ) );
    wp_register_script( 'event-delete', HOO__PLUGIN_URL . 'assets/js/event-delete.js', array( 'jquery' ) );



    wp_enqueue_style(
      'hoo-admin',
      HOO__PLUGIN_URL . 'assets/css/admin.css',
      array( 'jquery-ui' ),
      HOO_VERSION);

    add_action( 'admin_menu', array( $this, 'add_menu' ) );

    $plugin_basename = HOO__PLUGIN_DIR . SLUG;
    add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

    add_action( 'init', array( $this, 'output_buffer' ) );

  }

  public function output_buffer() {
    ob_start();
  }

  public function add_menu() {
    add_menu_page(
      __( 'Locations', 'hoo' ),
      __( 'Hours of Operation', 'hoo' ),
      'manage_options',
      'hoo',
      array( $this->location_controller, 'index' ),
      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );
  }

  public function init_controllers() {
    $controller_classes = array( 'LocationController', 'CategoryController', 'EventController' );
    foreach ( $controller_classes as $class_name ) {
      $property_name = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $class_name ) ); // convert to snake

      $class_name = "\Hoo\Admin\\$class_name"; // need namespace I guess
      $this->$property_name = new $class_name( $this->entity_manager );
    }


  }

  private function init_public_hooks() {

    wp_enqueue_script( 'hoo', HOO__PLUGIN_URL . 'assets/js/hoo.js', array( 'jquery' ) );
    wp_localize_script( 'hoo', 'HOO', array( 'page'     => $_GET['page'],
                                             'ajaxurl'  => admin_url( 'admin-ajax.php' ), // need for frontpage ajax
                                             'timezone' => get_option( 'timezone_string' ) ) );
    // shortcode stuff
    wp_register_style( 'shortcode-main', HOO__PLUGIN_URL . 'assets/css/shortcode-main.css');
    wp_register_script( 'g-api', sprintf( 'http://google.com/jsapi?key=%s', 'ABQIAAAAoRs91XgpKw60K4liNrOHoBStNMhZCa0lqZKLUDgzjZGRsKl38xSnSmVmaulnWVdBLItzW4KsddHCzA' ) ); // TODO: make plugin settings for this
    wp_register_script( 'g-maps', 'http://maps.googleapis.com/maps/api/js?senesor=false', array( 'g-api', 'jquery' ) );
    wp_register_script( 'shortcode-main', HOO__PLUGIN_URL . 'assets/js/shortcode-main.js', array( 'g-maps', 'jquery', 'jquery-ui-tabs', 'jquery-effects-slide', 'jquery-effects-fade' ) );
  }
}
?>

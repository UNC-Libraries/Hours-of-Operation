<?php

namespace Hoo;

use \Hoo\Admin\LocationController;
use \Doctrine\ORM\Tools\Setup as ORMSetup;
use \Doctrine\ORM\EntityManager;

class Loader {

    const SLUG = 'hoo';

    private $tables = array(
        'wp_hoo_locations'  => 'Hoo\Model\Location',
        'wp_hoo_addresses'  => 'Hoo\Model\Address',
        'wp_hoo_events'     => 'Hoo\Model\Event',
        'wp_hoo_categories' => 'Hoo\Model\Category'
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

        // check if DB_HOST is a socket
        // TODO:  is this the best way to do this?
        $socket = explode( ':', DB_HOST )[1];
        if ( file_exists( $socket ) ) {
            $db_params['host'] = 'localhost';
            $db_params['unix_socket'] = $socket;
        }

        $is_dev_mode = true;

        $config = ORMSetup::createAnnotationMetadataConfiguration(array( HOO__PLUGIN_DIR . 'Hoo/Model' ), $is_dev_mode, null, null, false );
        $entity_manager = EntityManager::create( $db_params, $config );

        $this->entity_manager = $entity_manager;


        add_action( 'init', array( $this, 'init_hooks' ) );
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

    private function strip_wordpress_slashes_from_gpc() {
        // apparently wordpress ignores magic quote settings
        $_POST    = array_map( 'stripslashes_deep', $_POST );
        $_GET     = array_map( 'stripslashes_deep', $_GET );
        $_COOKIE  = array_map( 'stripslashes_deep', $_COOKIE );
        $_REQUEST = array_map( 'stripslashes_deep', $_REQUEST );
    }

    public function init_hooks() {
        $this->strip_wordpress_slashes_from_gpc();

        wp_enqueue_script( 'hoo', HOO__PLUGIN_URL . 'assets/js/hoo.js');
        wp_localize_script( 'hoo', 'HOO', array( 'ajaxurl'  => admin_url( 'admin-ajax.php' ), // need for frontpage ajax
                                                 'timezone' => get_option( 'timezone_string' ) ) );

        if ( is_admin() ) {
            $this->register_admin_scripts();
            $this->init_admin_hooks();
            $this->init_controllers();
        } else {
            $this->register_shortcode_scripts();
            $this->shortcode = new Shortcode( $this->entity_manager );
        }
        ob_start();
    }

    /**
       register all the global admin hooks like adding the admin menus
       @return void
     */
    public function init_admin_hooks() {


        wp_enqueue_style('hoo-admin', HOO__PLUGIN_URL . 'assets/css/admin.css', array( 'jquery-ui' ), HOO_VERSION);
        add_action( 'admin_menu', array( $this, 'add_menu' ) );


        $plugin_basename = HOO__PLUGIN_DIR . $this::SLUG;
        add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

    }
    public function add_menu() {
        add_menu_page(__( 'Locations', 'hoo' ),
                      __( 'Hours of Operation', 'hoo' ),
                      'edit_pages',
                      'hoo',
                      array( $this->location_controller, 'index' ),
                      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );
    }

    public function init_controllers() {
        $controller_classes = array( 'LocationController', 'CategoryController', 'EventController', 'ShortcodeController' );
        foreach ( $controller_classes as $class_name ) {
            $property_name = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $class_name ) ); // convert to snake

            $class_name = "\Hoo\Admin\\$class_name"; // need namespace I guess
            $this->$property_name = new $class_name( $this->entity_manager );
        }


    }

    public function register_admin_scripts() {

        wp_enqueue_script( 'init-postbox', HOO__PLUGIN_URL . 'assets/js/init_postbox.js', array( 'postbox' ) );

        wp_register_script( 'validation', HOO__PLUGIN_URL . 'assets/js/vendor/jquery.validate.min.js', array( 'jquery' ) );
        wp_register_style( 'jquery-ui', HOO__PLUGIN_URL . 'assets/css/jquery-ui.css' );
        wp_register_style( 'full-calendar', HOO__PLUGIN_URL . 'assets/css/fullcalendar.min.css', array( 'jquery-ui' ) );

        // location stuff
        wp_register_script( 'location-visibility', HOO__PLUGIN_URL . 'assets/js/location-visibility.js', array( 'jquery' ) );
        wp_register_script( 'location-order', HOO__PLUGIN_URL . 'assets/js/location-order.js', array( 'jquery-ui-sortable' ) );
        wp_register_script( 'location-delete', HOO__PLUGIN_URL . 'assets/js/location-delete.js', array( 'jquery' ) );
        wp_register_script( 'location-image', HOO__PLUGIN_URL . 'assets/js/location-image.js', array( 'media-upload', 'thickbox' ) );

        // category stuff
        wp_register_script( 'category-visibility', HOO__PLUGIN_URL . 'assets/js/category-visibility.js', array( 'jquery' ) );
        wp_register_script( 'category-order', HOO__PLUGIN_URL . 'assets/js/category-order.js', array( 'jquery-ui-sortable' ) );
        wp_register_script( 'category-delete', HOO__PLUGIN_URL . 'assets/js/category-delete.js', array( 'jquery' ) );

        global $wp_version;
        $color_picker = 3.4 <= $wp_version ? 'wp-color-picker' : 'farbtastic';
        wp_register_script( 'category-color-picker', HOO__PLUGIN_URL . 'assets/js/color-picker.js', array( $color_picker ) );
        wp_register_style( 'category-color-picker', NULL, array( $color_picker ) );

        // event stuff
        wp_register_script( 'moment', HOO__PLUGIN_URL . 'assets/js/vendor/moment.min.js' );
        wp_register_script( 'full-calendar', HOO__PLUGIN_URL . 'assets/js/vendor/fullcalendar.min.js', array( 'jquery', 'moment' ) );
        wp_register_script( 'jquery-timepicker-addon',
                            HOO__PLUGIN_URL . 'assets/js/vendor/jquery-ui-timepicker-addon.js',
                            array("jquery-ui-core",            //UI Core - do not remove this one
                                  "jquery-ui-slider",
                                  "jquery-ui-datepicker" ) );

        wp_register_script( 'event-edit', HOO__PLUGIN_URL . 'assets/js/event-edit.js', array( 'validation', 'jquery-timepicker-addon', 'full-calendar' ) );
        wp_register_script( 'event-delete', HOO__PLUGIN_URL . 'assets/js/event-delete.js', array( 'jquery' ) );

        wp_register_script( 'hoo-shortcodes', HOO__PLUGIN_URL . 'assets/js/hoo-shortcodes.js', array( 'jquery' ) );


    }
    public function register_shortcode_scripts() {

        wp_register_style( 'jquery-ui', HOO__PLUGIN_URL . 'assets/css/jquery-ui.css' );
        wp_register_style( 'full-calendar', HOO__PLUGIN_URL . 'assets/css/fullcalendar.min.css', array( 'jquery-ui' ) );
        wp_register_style( 'shortcode-main', HOO__PLUGIN_URL . 'assets/css/shortcode-main.css', array( 'full-calendar' ) );

        wp_register_script( 'moment', HOO__PLUGIN_URL . 'assets/js/vendor/moment.min.js' );
        wp_register_script( 'full-calendar', HOO__PLUGIN_URL . 'assets/js/vendor/fullcalendar.min.js', array( 'jquery', 'moment' ) );
        wp_register_script( 'g-api', sprintf( 'http://google.com/jsapi?key=%s', 'ABQIAAAAoRs91XgpKw60K4liNrOHoBStNMhZCa0lqZKLUDgzjZGRsKl38xSnSmVmaulnWVdBLItzW4KsddHCzA' ) ); // TODO: make plugin settings for this
        wp_register_script( 'g-maps', 'http://maps.googleapis.com/maps/api/js?senesor=false', array( 'g-api', 'jquery' ) );
        wp_register_script( 'shortcode-main', HOO__PLUGIN_URL . 'assets/js/shortcode-main.js', array( 'g-maps', 'jquery', 'jquery-ui-tabs', 'jquery-effects-slide', 'jquery-effects-fade', 'full-calendar' ) );
    }
}
?>

<?php

namespace Hoo\Admin;

defined( 'ABSPATH' ) or die();

class Controller {

  protected static $instance = null;
  protected $screen_hook_suffix = null;

  public function __construct() {
//    $plugin = HoO::get_instance();
    $this->plugin_slug = 'hoo';
    

    $this->init_hooks();

  }

  public function init_hooks() {
    // style
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    // menus
    add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

  //  $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
   // add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
  }


  public function add_admin_menu() {

    $this->screen_hook_suffix = add_menu_page(
      __( 'Hours of Operation', $this->plugin_slug ),
      __( 'Hours of Operation', $this->plugin_slug ),
      'manage_options',
      $this->plugin_slug,
      array( $this, 'display_admin_page' ),
      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );
  }

  public function add_action_links( $links ) {

    return array_merge(
      array(
        'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
      ),
      $links
    );

  }

  public function enqueue_admin_styles() {
    if ( ! isset( $this->screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $this->screen_hook_suffix == $screen->id ) {
      wp_enqueue_style(
        $this->plugin_slug . '-admin-styles',
        HOO__PLUGIN_URL . 'assets/css/admin.css',
        array(),
        \Hoo\Controller::VERSION );
    }
  }

  public function enqueue_admin_scripts() {
  }

  public function display_admin_page() {
    include_once( HOO__PLUGIN_VIEWS_DIR . 'admin/index.php');
  }
}

?>

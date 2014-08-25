<?php 

class HoO {

  const VERSION = '0.0.1';

  protected $plugin_slug = 'hoo';
  protected static $instance = null;

  private function __construct() {
    self::init_hooks();
  }
  
  public function init_hooks() {
  }
  
  public static function activate() {
    // set some things up :) 
  }
  
  public static function deactivate() {
    // tidy up
  }
  
  public function get_plugin_slug() {
    return $this->plugin_slug;
  }

  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}

?>

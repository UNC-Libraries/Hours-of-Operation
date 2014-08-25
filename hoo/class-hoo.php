<?php

include 'class-hoo-db.php';

class HoO {

  const VERSION = '0.0.1';

  protected static $instance = null;
  protected $plugin_slug = 'hoo';

  private function __construct() {
    self::init_hooks();

  }

  public function init_hooks() {
  }

  public static function activate() {
    HoO_DB::create_tables();
  }

  public static function deactivate() {
    // tidy up
  }

  function public get_plugin_slug() {
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

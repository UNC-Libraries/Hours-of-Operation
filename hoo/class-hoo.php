<?php

require_once 'includes/class-hoo-db.php';
require_once 'includes/class-hoo-ical.php';

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
    HoO_DB::create_tables();
  }

  public static function deactivate() {
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

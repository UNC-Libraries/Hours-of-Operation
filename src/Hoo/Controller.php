<?php

namespace Hoo;

class Controller {

  const VERSION = '0.0.1';

  protected $plugin_slug = 'hoo';

  public static function activate() {
    HoO_DB::create_tables();
  }

  public static function deactivate() {
  }

  public function __construct() {
    $this->init_hooks();
  }

  public function init_hooks() {
  }

  public function get_plugin_slug() {
    return $this->plugin_slug;
  }
}

?>

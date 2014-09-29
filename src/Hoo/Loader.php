<?php


namespace Hoo;

use \Hoo\Admin\LocationController;
use \Doctrine\ORM\Tools\Setup as ORMSetup;
use \Doctrine\ORM\EntityManager;

class Loader {

  const SLUG = 'hoo';
  private $tables = array(
    'hoo_locations' => 'Hoo\Model\Location',
    'hoo_addresses' => 'Hoo\Model\Address',
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


    if ( is_admin() ) {

    wp_enqueue_style(
      LocationController::SLUG . '-admin-styles',
      HOO__PLUGIN_URL . 'assets/css/admin.css',
      array(),
      HOO_VERSION);

      $this->location_controller = new LocationController( $entity_manager );
      $this->init_admin_hooks();

    } else {
      $this->init_public_hooks();

    }


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

    $plugin_basename = HOO__PLUGIN_DIR . SLUG;
    add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

  }


  private function init_public_hooks() {
  }

}

?>

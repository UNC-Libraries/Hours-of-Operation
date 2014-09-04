<?php


namespace Hoo;

use \Hoo\Admin\LocationController;
use \Doctrine\ORM\Tools\Setup as ORMSetup;
use \Doctrine\ORM\EntityManager;

class Loader {

  const SLUG = 'hoo';
  
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

    // @TODO: check each table one at a time?
    if ( $schema_manager->tablesExist( array( 'hoo_locations', 'hoo_addresses' ) ) ) {
      // update schema?
    }
    else {
      $entities = array(
        $this->entity_manager->getClassMetadata( '\Hoo\Model\Location' ),
        $this->entity_manager->getClassMetadata( '\Hoo\Model\Address' )
      );

      $schema_tool->createSchema( $entities );
    }

  }

  public function deactivate() {
  }

  
  /**
     register all the global admin hooks like adding the admin menus
     @return void
  */
  private function init_admin_hooks() {

    // menus
    add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

    $plugin_basename = HOO__PLUGIN_DIR . SLUG;
    add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

  }

  
  /**
     add the menus to the admin section
     @return void
   */
  public function add_admin_menus() {
    $this->screen_hook_suffix = add_menu_page(
      __( 'Hours of Operation', Loader::SLUG ),
      __( 'Hours of Operation', Loader::SLUG ),
      'manage_options',
      Loader::SLUG,
      array( $this->location_controller, 'index' ),
      HOO__PLUGIN_URL . 'assets/images/hoo-20.png' );

    add_submenu_page(
      Loader::SLUG,
      __( 'Add New', LocationController::SLUG . '-add' ),
      __( 'Add New', LocationController::SLUG . '-add' ),
      'manage_options',
      LocationController::SLUG . '-add',
      array($this->location_controller, 'add'));

  }


  private function init_public_hooks() {
  }

}

?>

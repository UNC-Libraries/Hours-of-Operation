<?php

namespace Hoo;

class Activator {

  public function __construct($entity_manager) {
    $this->entity_manager = $entity_manager;
  }

  public function activate() {
    $schema_tool = new \Doctrine\ORM\Tools\SchemaTool( $this->entity_manager );
    $schema_manager = $this->entity_manager->getConnection()->getSchemaManager();

    // @TODO: check each table one at a time?
    if ( $schema_manager->tablesExist( array( 'hoo_locations', 'hoo_addresses' ) ) ) {
      // update schema?
    }
    else {
      $entities = array(
        $this->entity_manager->getClassMetadata( '\\Hoo\\Model\\Location' ),
        $this->entity_manager->getClassMetadata( '\\Hoo\\Model\\Address' )
      );

      $schema_tool->createSchema( $entities );
    }

  }

  public function deactivate() {
  }
}

?>

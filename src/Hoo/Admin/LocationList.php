<?php

namespace Hoo\Admin;

class LocationList extends \WP_List_Table {

  public function __construct( $entity_manager ) {
    $this->entity_manager = $entity_manager;

    parent::__construct( array(
      'singular' => 'wp_list_text_link',
      'plural'   => 'wp_list_text_links',
      'ajax'     => false
    ) );
  }

  public function get_columns() {
    return array(
      'name' => __( 'Name' ),
      'position' => __( 'Position' ),
      'updated_at' => __( 'Modified Date' ),
      'is_visible' => __( 'Visible?' )
    );
  }

  public function prepare_items() {
    $current_screen = get_current_screen();


    /**
       TODO: pagination
     */


    // register columns
    $columns = $this->get_columns();
    $this->_column_headers = array( $columns, array(), array() );

    // fetch locations
    $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
    $locations = $locations_repo->findAll();

    $this->items = $locations;
  }

  public function column_name( $location ) {
    $actions = array(
      'edit' => sprintf( '<a href=?page=%s&action=%s&location_id=%s>Edit</a>', 'hoo-location', 'edit', $location->id ),
      'delete' => sprintf( '<a href=?page=%s&action=%s&location_id%s>Delete</a>', 'hoo-location', 'delete', $location->id )
    );

    return sprintf( '%1$s %2$s', $location->name, $this->row_actions( $actions ) );
  }

  public function column_default( $location, $column_name ) {
    return $location->$column_name;
  }

  public function no_items() {
    __( ' There are no locations :( ' );
  }
}

?>

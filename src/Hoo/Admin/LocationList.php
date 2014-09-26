<?php

namespace Hoo\Admin;

class LocationList extends \WP_List_Table {

  public function __construct( $entity_manager ) {
    $this->entity_manager = $entity_manager;
    

    parent::__construct( array(
      'singular' => 'location',
      'plural'   => 'locations',
      'ajax'     => false
    ) );
  }
  
  public function get_columns() {
    return array(
      'name' => __( 'Name' ),
      'updated_at' => __( 'Modified Date' ),
      'is_visible' => __( 'Visible?' ),
      'position' => __( 'Position' )
    );
  }

  public function get_sortable_columns() {
    return array(
      'name' => array( 'name', false ),
      'updated_at' => array( 'updated_at', false ),
      'position' => array( 'position', true )
    );
  }

  public function prepare_items() {
    $current_screen = get_current_screen();

    // register columns
    $columns = $this->get_columns();
    $this->_column_headers = array(
      $this->get_columns(),
      array(),
      $this->get_sortable_columns()
    );

    // fetch locations
    $order_by = isset( $_REQUEST['orderby'] ) ? array( $_REQUEST['orderby'] => $_REQUEST['order'] ) : array( 'position' => 'asc' );
    $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
    $locations = $locations_repo->findBy( array(), $order_by );

    $this->items = $locations;
  }

  public function column_name( $location ) {
    $actions = array(
      'edit' => sprintf( '<a href=?page=%s&location_id=%s>Edit</a>', 'hoo-location-edit', $location->id ),
      'delete' => sprintf( '<a href=?page=%s&action=%s&location_id=%s class="location-delete">Delete</a>', 'hoo-location-edit', 'delete', $location->id )
    );

    return sprintf( '%1$s %2$s', $location->name, $this->row_actions( $actions ) );
  }

  public function column_updated_at( $location ) {
    return $location->updated_at->format( 'F j, Y g:i a' );
  }

  public function column_is_visible( $location ) {
    $checked = $location->is_visible ? 'checked' : '';
    return sprintf( '<input type="checkbox" value="%s" %s/>', $location->is_visible, $checked );
  }

  public function column_default( $location, $column_name ) {
    return $location->$column_name;
  }

  public function single_row( $item ) {
    static $alternate = '';
    $alternate = ( $alternate == '' ? ' alternate' : '' );
    
    $row_class = sprintf( ' class="list-item%s"', $alternate );
    $row_id = sprintf( ' id="location_%s"', $item->id );

    echo sprintf( '<tr %s %s>', $row_id, $row_class );
    $this->single_row_columns( $item );
    echo '</tr>';

  }
  
  public function no_items() {
    _e( ' There are no locations.  <a href="?page=hoo-location-add">Click Here</a> to add a location!' );
  }
}

?>

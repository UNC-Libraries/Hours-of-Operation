<?php

namespace Hoo\Admin;

use Hoo\Utils;

class EventList extends \WP_List_Table {
  public function __construct( $entity_manager, $location ) {
    $this->entity_manager = $entity_manager;
    $this->location = $location;

    parent::__construct( array( 'singular' => 'event',
                                'plural'   => 'events',
                                'ajax'     => false ) );
  }

  public function get_columns() {
    return array( 'title'    => __( 'Title / Label' ),
                  'category' => __( 'Category' ),
                  'start'    => __( 'Start' ),
                  'end'      => __( 'End' ) );
  }

  public function get_sortable_columns() {
    return array( 'title' => array( 'title', false ),
                  'category' => array( 'category', false ),
                  'start' => array( 'start', false),
                  'end'   => array( 'end', false) );
  }

  public function prepare_items() {
    // register columns
    $this->_column_headers = array( $this->get_columns(),
                                    array(),
                                    $this->get_sortable_columns() );

    // fetch events
    $order_by = isset( $_GET['orderby'] ) ? array( $_GET['orderby'] => $_GET['order'] ) : array( 'start' => 'desc' );
    $events_repo = $this->entity_manager->getRepository( '\Hoo\Model\Event' );
    $events = $events_repo->findBy( array( 'location' => $this->location->id ), $order_by );

    $this->items = $events;
  }

  public function column_title( $event ) {
    $actions = array(
      'edit' => sprintf( '<a href="?page=%s&event_id=%s">Edit</a>', 'hoo-location-event-edit', $event->id ),
      'delete' => sprintf( '<a href="?page=%s&action=%s&event_id=%s" class="event-delete">Delete</a>', 'hoo-location-event-edit', 'delete', $event->id )
    );

    return sprintf( '%1$s %2$s', $event->title, $this->row_actions( $actions ) );
  }
  
  public function column_category( $event ) {
    return $event->category->name;
  }
  
  public function column_start( $event ) {
    return $event->start->format( 'Y-m-d h:i');
  }

  public function column_end( $event ) {
    return $event->end->format( 'Y-m-d h:i');
  }
  
  public function column_default( $event, $column_name ) {
    return $event->$column_name;
  }

  public function single_row( $item ) {
    static $alternate = '';
    $alternate = ( $alternate == '' ? ' alternate' : '' );
    
    $row_class = sprintf( ' class="list-item%s"', $alternate );
    $row_id = sprintf( ' id="event_%s"', $item->id );

    echo sprintf( '<tr %s %s>', $row_id, $row_class );
    $this->single_row_columns( $item );
    echo '</tr>';

  }
  
  public function no_items() {
    _e( sprintf( 'There are no hour events.  <a href="?page=hoo-location-event-add&location_id=%s">Click Here</a> to add hours!', 
                 $this->location->id ) );
  }
}

?>

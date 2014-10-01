<?php

namespace Hoo\Admin;

class EventList extends \WP_List_Table {
  public function __construct( $entity_manager, $location ) {
    $this->entity_manager = $entity_manager;
    $this->location = $location;

    parent::__construct( array( 'singular' => 'event',
                                'plural'   => 'events',
                                'ajax'     => false ) );
  }

  public function get_columns() {
    return array( 'label'    => __( 'Title / Label' ),
                  'start'    => __( 'Start' ),
                  'end'      => __( 'End' ),
                  'category' => __( 'Category' ));
  }

  public function get_sortable_columns() {
    return array( 'label' => array( 'label', false ),
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
    $events = $events_repo->findBy( array( 'location' => $location->id ), $order_by ) ;

    $this->items = $events;
  }
  
  public function no_items() {
    _e( sprintf( 'There are no hour events.  <a href="?page=hoo-location-event-add&location_id=%s">Click Here</a> to add hours!', 
                 $this->location->id ) );
  }
}

?>

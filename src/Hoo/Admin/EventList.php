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
                      'category' => __( 'Category'  ),
                      'start'    => __( 'Starts' ),
                      'end'      => __( 'Ends'),
                      'updated_at' => __( 'Modified Date' ) );
    }

    public function get_sortable_columns() {
        return array( 'title' => array( 'e.title', false ),
                      'category' => array( 'c.name', false ),
                      'start'    => array('e.start', false),
                      'updated_at' => array( 'e.updated_at', false ) );
    }

    public function prepare_items() {
        // register columns
        $this->_column_headers = array( $this->get_columns(),
                                        array(),
                                        $this->get_sortable_columns() );

        // fetch events
        $events_repo = $this->entity_manager->getRepository( '\Hoo\Model\Event' );

        if ( isset( $_GET['orderby'] ) ) {
            $order_by = $_GET['orderby']; $order = $_GET['order'];
        }else {
            $order_by = 'e.updated_at'; $order = 'desc';
        }

        if ( isset( $_GET['s'] ) ) {
            $events = $this->entity_manager->createQueryBuilder()
                                           ->select( array( 'e', 'c' ) )
                                           ->from( '\Hoo\Model\Event', 'e')
                                           ->join( 'e.category', 'c' )
                                           ->where( 'e.location = :location' )
                                           ->andWhere( 'e.title LIKE :search')
                                           ->orWhere( 'c.name LIKE :search' )
                                           ->setParameter( 'location', $_GET['location_id'] )
                                           ->setParameter( 'search', '%'. $_GET['s'] . '%' )
                                           ->orderBy( $order_by, $order )
                                           ->getQuery()
                                           ->getResult();

        } else {
            $events = $this->entity_manager->createQueryBuilder()
                                           ->select( array( 'e', 'c') )
                                           ->from( '\Hoo\Model\Event', 'e')
                                           ->where( 'e.location = :location' )
                                           ->join( 'e.category', 'c' )
                                           ->setParameter( 'location', $_GET['location_id'] )
                                           ->orderBy( $order_by, $order)
                                           ->getQuery()
                                           ->getResult();
        }

        $this->items = $events;
    }

    public function column_title( $event ) {
        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&event_id=%s">Edit</a>', 'hoo-location-event-edit', $event->id ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&event_id=%s" class="event-delete">Delete</a>', 'hoo-location-event-edit', 'delete', $event->id )
        );

        return sprintf( '%1$s %2$s', $event->title, $this->row_actions( $actions ) );
    }

    public function column_start( $event ) {
        return $event->start->format( 'F j, Y' );
    }

    public function column_end( $event ) {
        $rrule = $event->recurrence_rule;
        if ( empty( $rrule ) ) {
            return $event->end->format( 'F j, Y' );
        } else {
            if ( preg_match( '/UNTIL=(\w+)(;|$)/', $event->recurrence_rule, $matches ) ) {
                $end = new \DateTime( $matches[1], new \DateTimeZone( get_option( 'timezone_string' ) ) );
                return $end->format( 'F j, Y' );
            } else {
                return '∞';
            }
        }
    }

    public function column_category( $event ) {
        return $event->category->name;
    }

    public function column_updated_at( $event ) {
        return $event->updated_at->format( 'F j, Y g:i a' );
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

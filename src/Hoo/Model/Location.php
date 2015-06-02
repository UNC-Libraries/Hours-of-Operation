<?php

namespace Hoo\Model;

use Hoo\Utils;
use Hoo\Model\Address;
use Doctrine\ORM\Mapping as ORM;

use Recurr\Rule as RRule;
use Recurr\Transformer\ArrayTransformer as RRuleTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Doctrine\Common\Collections\Criteria as Criteria;

/**
 * @ORM\Entity
 * @ORM\Table(name="wp_hoo_locations")
 * @ORM\HasLifecycleCallbacks()
 */
class Location {

    /**
     *   @ORM\Id
     *   @ORM\Column(type="integer")
     *   @ORM\GeneratedValue(strategy="AUTO")
     **/
    protected $id;

    /** @ORM\Column(type="string", length=256) **/
    protected $name;

    /** @ORM\Column(name="alternate_name", type="string", length=256, nullable=true) **/
    protected $alternate_name;

    /** @ORM\Column(type="string", length=256, nullable=true) **/
    protected $url;

    /** @ORM\Column(type="string", length=256, nullable=true) **/
    protected $phone;

    /** @ORM\Column(type="text", nullable=true) **/
    protected $description;

    /** @ORM\Column(type="string", length=256, nullable=true) **/
    protected $notice;

    /** @ORM\Column(name="handicap_accessible", type="boolean", options={"default" = 1}) **/
    protected $is_handicap_accessible = true;

    /** @ORM\Column(type="string", length=256, nullable=true) **/
    protected $handicap_link;

    /** @ORM\Column(name="is_visible", type="boolean", options={"default" = 1}) **/
    protected $is_visible = true;

    /** @ORM\Column(name="position", type="integer", options={"default" = 0}) **/
    protected $position = 0;

    /** @ORM\Column(name="image", type="string", nullable=true) **/
    protected $image = 0;

    /** @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"}, fetch="EAGER") **/
    protected $address;

    /** @ORM\OneToMany(targetEntity="Location", mappedBy="parent") **/
    protected $sublocations;

    /**
     *  @ORM\ManyToOne(targetEntity="Location", inversedBy="sublocations")
     **/
    protected $parent;

    /** @ORM\OneToMany(targetEntity="Event", mappedBy="location", fetch="EAGER") **/
    protected $events;

    /** @ORM\Column(name="created_at", type="datetime") **/
    private $created_at;

    /** @ORM\column(name="updated_at", type="datetime") **/
    private $updated_at;

    public function to_api_response() {
        $attributes = array( 'id', 'name', 'url', 'phone', 'notice', 'is_handicap_accessible', 'handicap_link' );

        return array_reduce( $attributes,
                             function( $attrs, $attr ) { $attrs[$attr] = $this->$attr; return $attrs; },
                             array() );
    }

    public function get_weekly_hours() {
        $start = new \DateTime( 'sunday last week' );
        $end = new \DateTime( 'sunday this week' );
        $interval = new \DateInterval( 'P1D' );

        $event_instances = $this->get_event_instances( $start, $end );

        $weekdays = new \DatePeriod( $start, $interval, $end );
        $weekdays = iterator_to_array( $weekdays );
        $weekly_events = array();

        foreach ( $weekdays as $day_number => $day ) {
            // TODO: refactor as this is kind of ugly
            $day_instance = isset( $event_instances[ $day->format( 'Y-m-d' ) ] ) ? $event_instances[ $day->format( 'Y-m-d' ) ] : false;
            $prev_day = isset( $weekdays[ $day_number - 1 ] ) && isset( $event_instances[ $weekdays[ $day_number - 1 ]->format( 'Y-m-d' ) ] ) ?
                        $weekdays[ $day_number - 1 ] :
                        false;
            $next_day = isset( $weekdays[ $day_number + 1 ] ) && isset( $event_instances[ $weekdays[ $day_number + 1 ]->format( 'Y-m-d' ) ] ) ?
                        $weekdays[ $day_number + 1 ] :
                        false;

            if ( $day_instance ) {
                if ( $day_instance->is_closed ) {
                    $weekly_events[] = 'Closed';
                } elseif ( $day_instance->is_all_day ) {
                    $weekly_events[] = '24 Hours';
                } elseif ( ( $prev_day && $event_instances[ $prev_day->format( 'Y-m-d' ) ]->is_all_day ) &&
                    ( $next_day && $event_instances[ $next_day->format( 'Y-m-d' ) ]->is_all_day ) ) {
                    $weekly_events[] = sprintf( '%s - %s', $day_instance->start->format( 'h:i a' ), $day_instance->end->format( 'h:i a' ) );
                } elseif ( $prev_day && $event_instances[ $prev_day->format( 'Y-m-d' ) ]->is_all_day  ) {
                    $weekly_events[] = sprintf( '24 Hours - %s', $day_instance->end->format( 'h:i a' ) );
                } elseif ( $next_day && $event_instances[ $next_day->format( 'Y-m-d' ) ]->is_all_day ) {
                    $weekly_events[] = sprintf( '%s - 24 Hours', $day_instance->start->format( 'h:i a' ) );
                } else {
                    $weekly_events[] = sprintf( '%s - %s', $day_instance->start->format( 'h:i a' ), $day_instance->end->format( 'h:i a' ) );
                }
            } else {
                $weekly_events[] = 'N/A';
            }
        }

        // go through each day in the week and group according to the text in $weekly_events
        $hours = array();
        $day = 0;
        while ( $day < 7 ) {
            $start = $day; $end = $day;
            // keep going until the event hours are not the same
            while ( $end < 7 && ( $weekly_events[ $start ] == $weekly_events[ $end ] ) ) {
                $end++;
            };

            if ( 1 == $end - $start ) { // not consecutive, just print the value
                $dow_text = date( 'l', strtotime( "sunday last week +$start days" ) );
                $hours[ $dow_text ] = $weekly_events[ $day ];
            } else {
                $start_dow_text = date( 'D', strtotime( "sunday last week +$start days" ) );
                $end_dow_text = date( 'D', strtotime( sprintf( 'sunday last week +%s days', $end - 1 ) ) );
                $range_text = sprintf( '%s - %s', $start_dow_text, $end_dow_text );
                $hours[ $range_text ] = $weekly_events[ $day ];
            }
            $day = $end++;
        }
        return $hours;
    }

    public function get_hours_for_date( $start ) {
        $end = new \DateTime( $start->format( 'Y-m-d' ) );
        $end->modify( '+1 day' );
        $events = $this->get_event_instances( $start, $end );
        return ( 1 <= count( $events ) ) ? reset( $events ) : null;
    }

    public function is_open() {
        $now = new \DateTime();
        $now_start = new \DateTime( date( 'Y-m-d' ) );
        $now_ymd = $now_start->format( 'Y-m-d' );
        $now_end = new \DateTime( $now_start->format( 'Y-m-d' ) );
        $now_end->modify( '+1 day' );
        $event_instances = $this->get_event_instances( $now_start, $now_end );

        if ( count ( $event_instances ) > 0 ) {
            // special cases
            if ( $event_instances[ $now_ymd ]->is_all_day )
                return '24 hours';
            elseif ( $event_instances[ $now_ymd ]->is_closed )
                return false;

            return ( $now >= $event_instances[ $now_ymd ]->start && $now <= $event_instances[ $now_ymd ]->end ) ? $event_instances[ $now_ymd ]->end : false;
        }
        return null;
    }

    public function google_map_url() {
        $ll = implode( ',', array( $this->address->lat, $this->address->lon ) );
        $url_template = 'http://google.com/maps/@%s,18z';

        return sprintf( $url_template, $ll );
    }

    public function get_event_instances( $start = null, $end = null, $only_visible = true, $entity_manager = null ) {
        $event_instances = array();
        $rrule_transformer = new RRuleTransformer();

        $this->events = $only_visible ?
                        $this->events->filter( function( $event ) { return $event->is_visible; } ) :
                        $this->events;

        // we are creating a new event so make one and add it to the list
        if ( isset( $_GET['event']['id'] ) && empty( $_GET['event']['id'] ) ) {
            $new_event = new Event( $_GET, $entity_manager );
            $this->events->add( $new_event );
        }

        foreach( $this->events as $event ) {
            // update the event being edited
            if ( isset( $_GET['event']['id'] ) && $_GET['event']['id'] == $event->id ) {
                $event->fromParams( $_GET, $entity_manager );
            }

            if ( $event->is_recurring ) {

                // TODO: I believe this is a bug, need to see how or why this is already an object
                if ( is_object( $event->recurrence_rule ) ) {
                    $event->recurrence_rule = new RRule( $event->recurrence_rule->getString(), $event->start, $event->end );
                } else {
                    $event->recurrence_rule = new RRule( $event->recurrence_rule, $event->start, $event->end );
                }


                // if we have a start and end date add a between contraint, otherwise get all events
                if ( $start && $end ) {
                    $range = new BetweenConstraint( $start, $end, true ) ;
                    $recurrences = $rrule_transformer->transform( $event->recurrence_rule, 60, $range )->toArray();
                } else {
                    $recurrences = $rrule_transformer->transform( $event->recurrence_rule, 60 )->toArray();
                }

                foreach ( $recurrences as $recur ) {
                    $tmp_event = clone $event;
                    $tmp_event->start = $recur->getStart();
                    $tmp_event->end = $recur->getEnd();
                    $tmp_ymd = $tmp_event->start->format( 'Y-m-d' );
                    $tmp_priority = $tmp_event->category->priority;

                    // check priority
                    if ( array_key_exists( $tmp_ymd, $event_instances ) ) {
                        if ( $tmp_priority > $event_instances[ $tmp_ymd ]->category->priority ) {
                            $event_instances[ $tmp_ymd ] = $tmp_event;
                        }
                    } else {
                        $event_instances[ $tmp_ymd ] = $tmp_event;
                    }
                }
            } elseif ( ( $start <= $event->start && $end >= $event->end ) || ( $start->format( 'Y-m-d' ) == $event->start->format( 'Y-m-d' ) && $event->is_all_day ) ) {
                $event_ymd = $event->start->format( 'Y-m-d' );
                $event_priority = $event->category->priority;

                // check priority
                if ( array_key_exists( $event_ymd, $event_instances ) ) {
                    if ( $event_priority > $event_instances[ $event_ymd ]->category->priority ) {
                        $event_instances[ $event_ymd ] = $event;
                    }
                } else {
                    $event_instances[ $event_ymd ] = $event;
                }
            }
        }
        return $event_instances;
    }

    public function get_preview_events( $entity_manager, $with_title = false ) {
        $cal_start = new \Datetime( $_GET['start'] );
        $cal_end = new \DateTime( $_GET['end'] );
        $preview_events = array();

        $events = $this->get_event_instances( $cal_start, $cal_end, false, $entity_manager );
        ksort( $events );
        $events = array_values( $events );

        foreach ( $events as $index => $event ) {

            $prev_event = ( isset( $events[ $index - 1 ] ) && $event->start->diff( $events[ $index - 1 ]->start )->days <= 1 ) ?
                          $events[ $index - 1 ] :
                          null;
            $next_event = ( isset( $events[ $index + 1 ] ) && $event->start->diff( $events[ $index + 1 ]->start )->days <= 1 ) ?
                          $events[ $index + 1] :
                          null;

            $preview_events[] = array( 'id' => $event->id,
                                       'title' => $event->format_title( $prev_event, $next_event, $with_title ),
                                       'start' => $event->start->format( \DateTime::ISO8601 ),
                                       'end' => $event->end->format( \DateTime::ISO8601 ),
                                       'color' => $event->category->color );
        }
        return $preview_events;
    }

    public function get_fullcalendar_events( $params, $entity_manager, $with_title = true ) {
        // TODO: convert this to how the is_open/get_instances works
        $rrule_transformer = new RRuleTransformer();
        $cal_start = new \Datetime( $params['start']);
        $cal_end = new \DateTime( $params['end']);

        $event_instances = array();
        $event_dates = array();

        if ( isset( $params['event']['id'] ) && empty( $params['event']['id'] ) ) {
            $current_event = new Event();
            $this->events->add( $current_event );
        }

        foreach( $this->events as $event ) {
            if ( isset( $params['event']['id'] ) && $params['event']['id'] == $event->id ) {
                $event->fromParams( $params, $entity_manager );
            } else {
                if ( $event->is_recurring ) {
                    $event->recurrence_rule = new RRule( $event->recurrence_rule, $event->start, $event->end );
                } else {
                    $event->recurrence_rule = new RRule( null, $event->start, $event->end );
                }
            }
            if ( ! $event->is_visible ) continue;

            // get all recurrences
            $cal_range = new BetweenConstraint( $cal_start, $cal_end, true ) ;
            $recurrences = $rrule_transformer->transform( $event->recurrence_rule, 60, $cal_range)->toArray();
            foreach( $recurrences as $recurrence ) {
                $event_instances[] = array( 'event' => $event, 'recurrence' => $recurrence );
            }
        }

        $event_instances = Utils::remove_overlapping_events( $event_instances );

        // TODO: refactor the 24 hours / formatting as it is gross
        $events = array();
        foreach( $event_instances as $instance ) {
            $prev_all_day = Utils::prev_was_all_day( $instance, $event_instances );
            $next_all_day = Utils::next_is_all_day( $instance, $event_instances );

            if ( $prev_all_day && $next_all_day ) {
                $title = $with_title ? $instance['event']->title . "\n" : '';
                $title .= Utils::format_time( $instance['recurrence']->getStart(), $instance['recurrence']->getEnd() );
            } elseif ( $prev_all_day ) {
                $title = sprintf( "%s24 Hours -\n%s", $with_title? $instance['event']->title . "\n" : '', Utils::format_time( $instance['recurrence']->getEnd() ) );
            } elseif ( $next_all_day )  {
                $title = sprintf( "%s%s -\n24 Hours", $with_title? $instance['event']->title . "\n" : '', Utils::format_time( $instance['recurrence']->getStart() ) );
            } elseif ( $instance['event']->is_all_day ) {
                $title = sprintf( "%sOpen\n24 Hours", $with_title ? $instance['event']->title . "\n" : '');
            } elseif ( $instance['event']->is_closed ) {
                $title = sprintf( "%sClosed", $with_title ? $instance['event']->title . "\n" : '');
            } else {
                $title = $with_title ? $instance['event']->title . "\n" : '';
                $title .= Utils::format_time( $instance['recurrence']->getStart(), $instance['recurrence']->getEnd() );
            }

            $events[] = array( 'id' => $instance['event']->id,
                               'title' => $title,
                               'start' => $instance['recurrence']->getStart()->format( \DateTime::ISO8601 ),
                               'end' => $instance['recurrence']->getEnd()->format( \DateTime::ISO8601 ),
                               'color' => $instance['event']->category->color  );
        }
        return $events;
    }

    public static function get_location_by_id_or_shortname( $id_or_name, $entity_manager ) {
        $locations_repo = $entity_manager->getRepository( 'Hoo\Model\Location' );

        $qb = $entity_manager->createQueryBuilder();
        try {
            $location = $qb->select( array( 'location' ) )
                           ->from( 'Hoo\Model\Location', 'location' )
                           ->where( $qb->expr()->orX(
                               $qb->expr()->eq( 'location.id', ':id_or_name' ),
                               $qb->expr()->like( 'location.alternate_name', ':id_or_name' ) ) )
                           ->setParameter( 'id_or_name', $id_or_name )
                           ->getQuery()->getSingleResult();
        } catch ( \Doctrine\ORM\NoResultException $e)  {
            return false;
        }
        return $location;
    }

    public static function get_visible_locations( $entity_manager ) {
        $locations_repo = $entity_manager->getRepository( 'Hoo\Model\Location' );
        $locations = $locations_repo->findBy( array( 'parent' => null, 'is_visible' => true ), array( 'position' => 'asc' ) );

        // quick hack to put the sublocations under the parent
        return array_reduce(
            $locations,
            function( $locations, $location ) {
                if ( $location->address && $location->address->lat && $location->address->lon ) {
                    $locations[] = $location;
                    foreach( $location->sublocations->toArray() as $sub ) {
                        $locations[] = $sub;
                    }
                }
                return $locations;
            },
            array() );
    }

    /** @ORM\PrePersist **/
    public function set_alternate_name() {
        if ( empty( $this->alternate_name ) ){
            $this->alternate_name = sanitize_title( $this->name );
        } else {
            $this->alternate_name = sanitize_title( $this->alternate_name );
        }
    }

    /** @ORM\PrePersist **/
    public function set_created_at() {
        $datetime = new \DateTime();
        $this->updated_at = $datetime;
        $this->created_at = $datetime;
    }

    /** @ORM\PreUpdate **/
    public function set_updated_at() {
        if ( empty( $this->alternate_name ) ){
            $this->alternate_name = sanitize_title( $this->name );
        } else {
            $this->alternate_name = sanitize_title( $this->alternate_name );
        }
        $this->updated_at = new \DateTime();
    }

    public function __toString(){
        return $this->name;
    }

    public function fromParams( $data, $entity_manager ) {
        $location_data = $data['location'];
        $location_data['address'] = new Address( $location_data['address'] );
        $location_data['parent'] = $entity_manager->find( '\Hoo\Model\Location', $location_data['parent'] );

        $location_data['is_handicap_accessible'] = isset( $location_data['is_handicap_accessible'] ) && $location_data['is_handicap_accessible'];

        return $this->fromArray( $location_data );
    }
    public function fromArray( $data ) {
        foreach ( $data as $property => $value ) {
            if ( property_exists( $this, $property ) ) {
                $this->$property = $value;
            } else {
                trigger_error( "Can't access property " . get_class( $this ) . ':' . $property, E_USER_ERROR );
            }
        }

        return $this;
    }

    /**
       a little getter/setter magic
     */
    public function __get( $property ) {
        if ( property_exists( $this, $property ) ){
            return $this->$property;
        }
    }

    public function __set( $property, $value ) {
        if ( property_exists( $this, $property ) ) {
            $this->$property = $value;
        } else {
            trigger_error( "Can't access property " . get_class( $this ) . ':' . $property, E_USER_ERROR );
        }

        return $this; // allow chaining

    }

    public function __isset( $property ) {
        return property_exists( $this, $property ) && isset( $this->$property );
    }

    public function __construct( $initial_values = array(), $entity_manager = null ) {
        if ( $initial_values )  {
            $this->fromParams( $initial_values, $entity_manager );
        } else {
            $this->address = new Address();
            $this->sublocations = new \Doctrine\Common\Collections\ArrayCollection();
        }
    }
}

?>

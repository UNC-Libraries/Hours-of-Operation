<?php

namespace Hoo\Model;

use \Hoo\Utils;
use Hoo\Model\Address;
use \Doctrine\ORM\Mapping as ORM;

use \Recurr\Rule as RRule;
use \Recurr\Transformer\ArrayTransformer as RRuleTransformer;
use \Recurr\Transformer\Constraint\BetweenConstraint;
use \Recurr\Transformer\Constraint\BeforeConstraint;
use Doctrine\Common\Collections\Criteria as Criteria;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoo_locations")
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
       @ORM\ManyToOne(targetEntity="Location", inversedBy="sublocations")
     **/
    protected $parent;

    /** @ORM\OneToMany(targetEntity="Event", mappedBy="location", fetch="EAGER") **/
    protected $events;

    /** @ORM\Column(name="created_at", type="datetime") **/
    private $created_at;

    /** @ORM\column(name="updated_at", type="datetime") **/
    private $updated_at;

    public function get_hours( \DateTime $start, \DateTime $end) {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );

        $rrule_transformer = new RRuleTransformer();

        $event_instances = array();
        $event_dates = array();

        foreach( $this->events as $event ) {
            $rrule = new RRule( $event->recurrence_rule, $event->start, $event->end );

            $event->start->setTimeZone( $tz );
            $event->end->setTimeZone( $tz );
            $rrule->setTimezone( get_option( 'timezone_string' ) );
            $cal_range = new BetweenConstraint( $start, $end, $tz, true ) ;
            foreach( $rrule_transformer->transform( $rrule, null, $cal_range )->toArray() as $recurrence ) {
                $event_instances[] = array( 'id' => $event->id,
                                            'title' => $event->title,
                                            'open' => $recurrence->getStart(),
                                            'close' => $recurrence->getEnd(),

                                            // the two are here solely for priority filtering and are removed before sending
                                            'priority' => $event->category->priority ? $event->category->priority : 0,
                                            'date' => $recurrence->getStart()->format( 'Y-m-d' ) );
            }
        }

        foreach( $event_instances as &$event_instance ) {
            if ( ! isset( $event_dates[ $event_instance['date'] ] ) ) {
                $event_dates[ $event_instance['date'] ] =& $event_instance;
            } elseif ( $event_dates[ $event_instance['date'] ]['priority'] > $event_instance['priority'] )
                $event_dates[ $event_instance['date'] ] =& $event_instance;
        }

        $event_instances = array_values ( $event_dates );
        return $event_instances;
    }


    public function get_hours_for_date( $start ) {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );

        $start = new \DateTime( date( $start ? $start : 'Y-m-d' ) );
        $end = new \DateTime( $start->format( 'Y-m-d' ) );
        $end->modify( '+1 day' );

        $start->setTimeZone( $tz ); $end->setTimeZone( $tz );

        $hours = $this->get_hours( $start, $end );

        $current_event = isset( $hours[0] ) ? $hours[0] : null;

        unset( $current_event['priority'] ); unset( $current_event['date'] );

        return $current_event;
    }

    public function is_open() {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );
        $now = new \DateTime( null, $tz );

        $now_start = new \DateTime( date( 'Y-m-d' ), $tz );
        $now_end = new \DateTime( $now_start->format( 'Y-m-d' ), $tz );
        $now_end->modify( '+1 day' );
        $event_instances = $this->get_event_instances( $now_start, $now_end );

        if ( count ( $event_instances ) > 0 ) {
            // special cases
            if ( $event_instances[0]->is_all_day )
                return '24 hours';
            elseif ( $event_instances[0]->is_closed )
                return false;

            return ( $now >= $event_instances[0]->start && $now <= $event_instances[0]->end ) ? $event_instances[0]->end : false;
        } 
        return null;
    }

    public function google_map_url() {
        $ll = implode( ',', array( $this->address->lat, $this->address->lon ) );
        $name = implode( '+', explode( ' ', $this->name ) );
        $url_template = 'http://maps.google.com/maps?q=%s+(%s)&z=16&ll=%s&iwloc=A';

        return sprintf( $url_template, $ll, $name, $ll );
    }

    public function get_event_instances( $start = null, $end = null ) {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );
        $event_instances = array();
        $rrule_transformer = new RRuleTransformer();

        foreach( $this->events as $event ) {
            if ( $event->is_recurring ) {
                $event->recurrence_rule = new RRule( $event->recurrence_rule, $event->start, $event->end );

                $event->start->setTimeZone( $tz );
                $event->end->setTimeZone( $tz );
                $event->recurrence_rule->setTimezone( get_option( 'timezone_string' ) );

                if ( $start && $end ) {
                    $range = new BetweenConstraint( $start, $end, true ) ;
                    $recurrences = $rrule_transformer->transform( $event->recurrence_rule, $range )->toArray();
                } else {
                    $recurrences = $rrule_transformer->transform( $event->recurrence_rule )->toArray();
                }

                foreach ( $recurrences as $recur ) {
                    $tmp_event = clone $event;
                    $tmp_event->start = $recur->getStart();
                    $tmp_event->end = $recur->getEnd();
                    $event_instances[] = $tmp_event;
                }
            } else {
                $event_instances[] = $event;
            }
        }
        
        return $event_instances;
    }


    public function get_fullcalendar_events( $params, $entity_manager, $with_title = true ) {
        $rrule_transformer = new RRuleTransformer();
        $tz = new \DateTimeZone( get_option( 'timezone_string') );
        $utc_tz = new \DateTimeZone( 'UTC' );

        $cal_start = new \Datetime( $params['start'], $tz );
        $cal_end = new \DateTime( $params['end'], $tz );

        $event_instances = array();
        $event_dates = array();

        if ( empty( $params['event']['id'] ) ) {
            $current_event = new Event();
            $this->events->add( $current_event );
        }

        foreach( $this->events as $event ) {
            
            if ( isset( $params['event']['id'] ) && $params['event']['id'] == $event->id ) {
                $event->fromParams( $params, $entity_manager );
            } else {
                if ( $event->is_recurring ) {
                    $event->recurrence_rule = new RRule( $event->recurrence_rule, $event->start, $event->end, get_option( 'timezone_string' ) );
                } else {
                    $event->recurrence_rule = new RRule( null, $event->start, $event->end, get_option( 'timezone_string' ) );
                }

            }
            if ( ! $event->is_visible ) continue;

            $event->start->setTimeZone( $tz );
            $event->end->setTimeZone( $tz );
            $event->recurrence_rule->setTimezone( get_option( 'timezone_string' ) );

            // get all recurrences
            $cal_range = new BetweenConstraint( $cal_start, $cal_end, true ) ;
            $recurrences = $rrule_transformer->transform( $event->recurrence_rule, $cal_range)->toArray();
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
            }
            elseif ( $prev_all_day ) {
                $title = sprintf( "%s24 Hours\n%s", $with_title? $instance['event']->title . "\n" : '', Utils::format_time( $instance['recurrence']->getEnd() ) );
            } elseif ( $next_all_day )  {
                $title = sprintf( "%s%s\n24 Hours", $with_title? $instance['event']->title . "\n" : '', Utils::format_time( $instance['recurrence']->getStart() ) );
            } elseif ( $instance['event']->is_all_day ) {
                $title = sprintf( "%sOpen\n24 Hours", $with_title ? $instance['event']->title . "\n" : '');
            } elseif ($instance['event']->is_closed ) {
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

    public static function get_visible_locations( $entity_manager ) {
        $locations_repo = $entity_manager->getRepository( '\Hoo\Model\Location' );
        $locations = $locations_repo->findBy( array( 'parent' => null, 'is_visible' => true ), array( 'position' => 'asc' ) );

        // quick hack to put the sublocations under the parent
        $locations = array_reduce(

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

        return $locations;
    }

    /** @ORM\PrePersist **/
    public function set_created_at() {
        $datetime = new \DateTime();
        $this->updated_at = $datetime;
        $this->created_at = $datetime;
    }

    /** @ORM\PreUpdate **/
    public function set_updated_at() {
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

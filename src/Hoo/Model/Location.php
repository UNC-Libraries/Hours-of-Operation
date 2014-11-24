<?php

namespace Hoo\Model;

use \Hoo\Utils;
use Doctrine\ORM\Mapping as ORM;

use \Recurr\Rule as RRule;
use \Recurr\Transformer\ArrayTransformer as RRuleTransformer;
use \Recurr\Transformer\Constraint\BetweenConstraint;
use \Recurr\Transformer\Constraint\BeforeConstraint;
use Doctrine\Common\Collections\Criteria as Criteria;

/**
   @ORM\Entity
   @ORM\Table(name="hoo_locations")
   @ORM\HasLifecycleCallbacks()
 */
class Location {

    /**
       @ORM\Id
       @ORM\Column(type="integer")
       @ORM\GeneratedValue
     */
    protected $id;

    /**
       @ORM\Column(type="string", length=256)
     */
    protected $name;

    /**
       @ORM\Column(name="alternate_name", type="string", length=256, nullable=true)
     */
    protected $alternate_name;

    /** @ORM\Column(type="string", length=256, nullable=true) */
    protected $url;

    /** @ORM\Column(type="string", length=256, nullable=true) */
    protected $phone;

    /** @ORM\Column(type="text", nullable=true) */
    protected $description;

    /** @ORM\Column(name="handicap_accessible", type="boolean", options={"default" = 1}) */
    protected $is_handicap_accessible = true;

    /** @ORM\Column(name="is_visible", type="boolean", options={"default" = 1}) */
    protected $is_visible = true;

    /** @ORM\Column(name="position", type="integer", options={"default" = 0}) */
    protected $position = 0;

    /** @ORM\Column(name="image", type="string", nullable=true) */
    protected $image = 0;

    /**
       @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"}, fetch="EAGER")
     */
    protected $address;

    /**
       @ORM\OneToMany(targetEntity="Location", mappedBy="parent")
     */
    protected $sublocations;

    /**
       @ORM\ManyToOne(targetEntity="Location", inversedBy="sublocations")
       @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /** @ORM\OneToMany(targetEntity="Event", mappedBy="location") */
    protected $events;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $created_at;

    /** @ORM\column(name="updated_at", type="datetime") */
    private $updated_at;

    public function get_hours( \DateTime $start, \DateTime $end) {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );

        $rrule_transformer = new RRuleTransformer();

        $event_instances = array();
        $event_dates = array();
        foreach( $this->events as $event ) {
            $event->start->setTimeZone( $tz );
            $event->end->setTimeZone( $tz );
            $rrule = new RRule( $event->recurrence_rule, $event->start, $event->end, get_option( 'timezone_string' ) );
            $cal_range = new BetweenConstraint( $start, $end, $tz ) ;

            foreach( $rrule_transformer->transform( $rrule, nil, $cal_range )->toArray() as $recurrence ) {
                $event_instances[] = array( 'id' => $event->id,
                                            'title' => $event->title,
                                            'open' => $recurrence->getStart()->format( \DateTime::ISO8601 ),
                                            'close' => $recurrence->getEnd()->format( \DateTime::ISO8601 ),

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

    public function current_hours() {
        $tz = new \DateTimeZone( get_option( 'timezone_string') );
        $hours = $this->get_hours( new \DateTime( '-1 day', $tz ), new \DateTime( 'now', $tz ) );

        $current_event = $hours[0];
        unset( $current_event['priority'] ); unset( $current_event['date'] );
        
        return $current_event;
    }

    /** @ORM\PrePersist */
    public function set_created_at() {
        $datetime = new \DateTime();
        $this->updated_at = $datetime;
        $this->created_at = $datetime;
    }

    /** @ORM\PreUpdate */
    public function set_updated_at() {
        $this->updated_at = new \DateTime();
    }

    public function __toString(){
        return $this->name;
    }

    /**

     */
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

    public function __construct( $initial_values = array() ) {
        foreach ( $initial_values as $property => $value ) {
            if ( property_exists( $this, $property ) ) {
                $this->$property = $value;
            }

            $this->sublocations = new \Doctrine\Common\Collections\ArrayCollection();
        }
    }
}

?>

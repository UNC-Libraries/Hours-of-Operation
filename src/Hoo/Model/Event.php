<?php
namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;
use \Recurr\Rule as RRule;
use Hoo\Model\Category;
use \Hoo\Utils;

/**
 *  @ORM\Entity
 *  @ORM\Table(name="hoo_events")
 *  @ORM\HasLifecycleCallbacks()
 */
class Event {
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="Location", inversedBy="events") */
    private $location;

    /** @ORM\Column(type="string", length=256) */
    protected $title;

    /** @ORM\Column(type="datetime") */
    protected $start;

    /** @ORM\Column(type="datetime") */
    protected $end;


    /** @ORM\ManyToOne(targetEntity="Category", fetch="EAGER") */
    protected $category;

    /** @ORM\Column(type="boolean", options={"default"=0}) */
    protected $is_recurring = false;

    /** @ORM\Column(type="boolean", options={"default"=0}) */
    protected $is_custom_rrule = false;

    /** @ORM\Column(type="boolean", options={"default"=0}) */
    protected $is_all_day = false;

    /** @ORM\Column(type="boolean", options={"default"=0}) */
    protected $is_closed = false;

    /** @ORM\Column(type="boolean", options={"default=1"}) */
    protected $is_visible = true;

    /** @ORM\Column(type="text", nullable=true) */
    protected $recurrence_rule;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $created_at;

    /** @ORM\column(name="updated_at", type="datetime") */
    private $updated_at;

    public function fromParams( $params, $entity_manager ) {
        $current_tz = new \DateTimeZone( get_option( 'timezone_string' ) );
        $utc_tz = new \DateTimeZone( 'UTC' );

        $rrule = new RRule();
        $rrule->setTimezone( get_option( 'timezone_string' ) );
        $event_data = $params['event'];

        $event_data['is_all_day'] = isset( $event_data['is_all_day'] ) && $event_data['is_all_day'];
        $event_data['is_closed'] = isset( $event_data['is_closed'] ) && $event_data['is_closed'];

        if ( $event_data['is_all_day'] || $event_data['is_closed'] ) {
            $start = new \Datetime( $params['event_start_date'], $current_tz );
            $end =   new \Datetime( $params['event_start_date'], $current_tz );
        } else {
            $start = new \Datetime( $event_data['start'], $current_tz );
            $end =   new \Datetime( $event_data['end'], $current_tz );
        }

        $rrule->setStartDate( $start );
        $rrule->setEndDate( $end );
        if ( isset( $params['event_recurrence_rule_custom'] ) ) {

            if ( isset( $params['event_recurrence_rule_custom']['BYDAY'] ) )
                $rrule->setByDay( $params['event_recurrence_rule_custom']['BYDAY'] );

            if ( ! empty( $params['event_recurrence_rule_custom']['UNTIL'] ) ) {
                $until = clone $start;
                $date = explode( '-', $params['event_recurrence_rule_custom']['UNTIL'] );
                $until->setDate( intval($date[0]), intval($date[1]), intval($date[2]) );
                $rrule->setUntil( $until );
            }

            if ( isset(  $params['event_recurrence_rule_custom']['INTERVAL'] ) )
                $rrule->setInterval( $params['event_recurrence_rule_custom']['INTERVAL'] );
        }

        switch( $event_data['recurrence_rule'] ) {
            case 'CUSTOM':
                // freq rules are in custom fields
                $event_data['is_recurring'] = true;
                $event_data['is_custom_rrule'] = true;
                $rrule->setFreq( $params['event_recurrence_rule_custom']['FREQ'] );
                break;
            case 'NONE':
                $event_data['is_recurring'] = false;
                $event_data['is_custom_rrule'] = false;
                $rrule->setCount( 1 );
                break;
            default:
                // freq value is sitting in the recurrence_rule field
                $event_data['is_recurring'] = true;
                $event_data['is_custom_rrule'] = false;
                $rrule->setFreq( $event_data['recurrence_rule'] );
        }
        $start->setTimezone( $utc_tz );
        $end->setTimezone( $utc_tz );
        $rrule->setTimezone( 'UTC' );


        if ( empty( $event_data['title'] ) )
            $event_data['title'] = 'New Event';

        if ( empty( $event_data['category'] ) ) {
            $event_data['category'] = new Category( array( 'name' => 'None',
                                                           'color' => '#ddd000',
                                                           'priority' => 9999999999999 ) );
        } else {
            $event_data['category'] = $entity_manager->find( '\Hoo\Model\Category', intval( $event_data['category'] ) );
        }

        $event_data['location'] = $entity_manager->find( '\Hoo\Model\Location', intval( $event_data['location'] ) );
        $event_data['start'] = $start;
        $event_data['end'] = $end;
        $event_data['recurrence_rule'] = $rrule;

        $this->fromArray( $event_data );

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function rrule_to_string() {
        // only save recurrence rule if we are actually recurring
        if ( $this->is_recurring ) {
            $this->recurrence_rule = $this->recurrence_rule->getString();
        } else {
            $this->recurrence_rule = null;
        }
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

    public function __construct( $initial_values = array(), $entity_manager = null) {
        if ( $initial_values )  {
            $this->fromParams( $initial_values, $entity_manager );
        } else {
            $this->category = new Category();
            $this->recurrence_rule = new RRule( array( 'FREQ' => 'DAILY', 'COUNT' => '1' ) );
            $this->start = new \DateTime('now', new \DateTimeZone( get_option( 'timezone_string' ) ) );
            $this->end = new \DateTime( '+1 hour', new \DateTimeZone( get_option( 'timezone_string' ) )  );
        }
    }

    public function __toString(){
        return $this->title;
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
}
?>

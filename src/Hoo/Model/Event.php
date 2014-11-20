<?php
namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;
use \Hoo\Utils;

/**
   @ORM\Entity
   @ORM\Table(name="hoo_events")
   @ORM\HasLifecycleCallbacks()
 */
class Event {
    /**
       @ORM\Id
       @ORM\Column(type="integer")
       @ORM\GeneratedValue
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


    /** @ORM\ManyToOne(targetEntity="Category") */
    protected $category;

    /** @ORM\Column(type="boolean", options={"default"=0}) */
    protected $is_all_day = false;

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

        $event_data = $params['event'];

        switch( $event_data['recurrence_rule'] ) {
            case 'CUSTOM':
                $custom_rr = $params['event_recurrence_rule_custom'];
                $event_data['recurrence_rule'] = UTILS::rrules_to_str( $custom_rr );
                break;
            case 'NONE':
                $event_data['recurrence_rule'] = '';
                break;
            default:
                $event_data['recurrence_rule'] =  strtoupper( sprintf( 'FREQ=%s', $event_data['recurrence_rule'] ) );
        }

        $event_start_dt = sprintf( '%s %s', $params['event_start_date'], $params['event_start_time'] );
        $event_end_dt = sprintf( '%s %s', $params['event_start_date'], $params['event_end_time'] );
        $start = new \Datetime( $event_start_dt, $current_tz );
        $end = new \Datetime( $event_end_dt, $current_tz );
        $start->setTimezone( $utc_tz );
        $end->setTimezone( $utc_tz );

        $event = $entity_manager->find( '\Hoo\Model\Event', $event_data['id'] );
        $event_data['category'] = $entity_manager->find( '\Hoo\Model\Category', $event_data['category'] );
        $event_data['location'] = $entity_manager->find( '\Hoo\Model\Location', $event_data['location'] );
        $event_data['start'] = $start;
        $event_data['end'] = $end;

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
            $this->start = new \DateTime();
            $this->end = new \DateTime( '+1 hour' );
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

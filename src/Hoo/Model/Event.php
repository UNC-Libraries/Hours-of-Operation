<?php
namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;
use \Recurr\Rule as RRule;
use Hoo\Model\Category;
use \Hoo\Utils;

/**
 *  @ORM\Entity
 *  @ORM\Table(name="wp_hoo_events")
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

    public function to_api_response() {
        $no_hours = $this->is_all_day || $this->is_closed;
        return array( 'category'   => $this->category->name,
                      'is_all_day' => $this->is_all_day,
                      'is_closed'  => $this->is_closed,
                      'open'       => $no_hours ? null : $this->start,
                      'close'      => $no_hours ? null : $this->end );
    }

    public function format_title( $prev_event, $next_event, $with_title ) {
        if ( $prev_event && $prev_event->is_all_day && $next_event && $next_event->is_all_day ) {
            $title = "{$this->start->format( 'g:i a')}\n{$this->end->format( 'g:i a')}";
        } elseif ( $prev_event && $prev_event->is_all_day ) {
            $title = "24 Hours - \n{$this->end->format( 'g:i a' )}";
        } elseif ( $next_event && $next_event->is_all_day ) {
            $title = "{$this->start->format( 'g:i a' )} - \n24 Hours";
        } else {
            $title = "{$this->start->format( 'g:i a')}\n{$this->end->format( 'g:i a')}";
        }

        if ( $this->is_all_day ) {
            $title = "Open\n24 Hours";
        } elseif ( $this->is_closed ) {
            $title = 'Closed';
        }

        return ( $with_title ? "{$this->title}\n" : '' ) . $title;
    }

    public function fromParams( $params, $entity_manager ) {
        $rrule = new RRule();
        $event_data = $params['event'];

        $event_data['is_visible'] = isset( $event_data['is_visible'] ) && $event_data['is_visible'];
        $event_data['is_all_day'] = isset( $event_data['is_all_day'] ) && $event_data['is_all_day'];
        $event_data['is_closed'] = isset( $event_data['is_closed'] ) && $event_data['is_closed'];

        if ( $event_data['is_all_day'] || $event_data['is_closed'] ) {
            $start = new \Datetime( $params['event_start_date'] );
            $end =   new \Datetime( $params['event_start_date'] );
        } else {
            $start = new \Datetime( $event_data['start'] );
            $end =   new \Datetime( $event_data['end'] );
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

        return $this->fromArray( $event_data );
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

        // end is set the same as start so add a day
        if ( $this->is_all_day || $this->is_closed ) {
            $this->end->modify('+1 day');
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
            $this->start = new \DateTime( '8 am');
            $this->end = new \DateTime( '9 pm' );
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

    public function __isset( $property ) {
        return property_exists( $this, $property ) && isset( $this->$property );
    }
}
?>

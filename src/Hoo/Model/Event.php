<?php
namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;

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
  
  public function __construct( $initial_values = array() ) {

    if ( $initial_values )  {
      foreach ( $initial_values as $property => $value ) {
        if ( property_exists( $this, $property ) ) {
          $this->$property = $value;
        }
      }
    } else {
      $this->start = new \Datetime();
      $this->end = new \Datetime('+1 Day');
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

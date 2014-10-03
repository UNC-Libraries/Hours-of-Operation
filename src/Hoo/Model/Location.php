<?php

namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;

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
  private $id;

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

  /**
     @ORM\OneToOne(targetEntity="Address", cascade={"persist", "remove"})
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

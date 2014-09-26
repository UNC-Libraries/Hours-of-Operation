<?php

namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;

/**
   @ORM\Entity
   @ORM\Table(name="hoo_addresses")
   @ORM\HasLifecycleCallbacks
 */

class Address {

  /**
     @ORM\Id
     @ORM\Column(type="integer")
     @ORM\GeneratedValue
   */
  protected $id;

  /**
     @ORM\Column(name="line1", type="string", length=256, nullable=true)
   */
  protected $line1;

  /**
     @ORM\Column(name="line2", type="string", length=256, nullable=true)
   */
  protected $line2;

  /**
     @ORM\Column(name="line3", type="string", length=256, nullable=true)
   */
  protected $line3;

  /**
     @ORM\Column(name="city", type="string", length=256, nullable=true)
   */
  protected $city;

  /**
     @ORM\Column(name="state", type="string", length=256, nullable=true)
   */
  protected $state;

  /**
     @ORM\Column(name="zip", type="string", length=256, nullable=true)
   */
  protected $zip;

  /** @ORM\Column(type="string", length=256, nullable=true) */
  protected $lat;

  /** @ORM\Column(type="string", length=256, nullable=true) */
  protected $lon;

  /**
     @ORM\Column(name="created_at", type="datetime", nullable=true)
   */
  private $created_at;

  /**
     @ORM\Column(name="updated_at", type="datetime")
   */
  private $updated_at;

  public function fromArray( $data ) {
    foreach ( $data as $property => $value ) {
      $this->$property = $value;
    }

    return $this;
  }

  /**
     @ORM\PrePersist
   */
  public function set_created_at() {
    $datetime = new \DateTime();
    $this->updated_at = $datetime;
    $this->created_at = $datetime;
  }

  /**
     @ORM\PostUpdate
   */
  public function set_updated_at() {
    $this->updated_at = new \DateTime();
  }

  /**
     a little getter/setter magic
     doctrine wants protected properties
     this tries to preserve accessibility a little bit by making private non-accessible
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

  public function __toString(){
    return "";
  }

  public function __construct( $initial_values = array() ) {
    foreach ( $initial_values as $property => $value ) {
      if ( property_exists( $this, $property ) ) {
        $this->$property = $value;
      }
    }
  }
}

?>

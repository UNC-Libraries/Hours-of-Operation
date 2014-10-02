<?php

namespace Hoo\Model;

use Doctrine\ORM\Mapping as ORM;

/**
   @ORM\Entity
   @ORM\Table(name="hoo_categories")
   @ORM\HasLifecycleCallbacks()
 */
class Category {

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

  /** @ORM\Column(type="string", length=256) */
  protected $color;

  /** @ORM\Column(type="integer") */
  protected $priority;


  /** @ORM\Column(type="text", nullable=true) */
  protected $description;

    /** @ORM\Column(name="is_visible", type="boolean", options={"default" = 1}) */
  protected $is_visible = true;

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
    return $this->title;
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
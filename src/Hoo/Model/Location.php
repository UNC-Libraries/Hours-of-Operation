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
     @ORM\Column(name="alternate_name", type="string", length=256)
   */
  protected $alternate_name;

  /** @ORM\Column(type="string", length=256, nullable=true) */
  protected $url;

  /** @ORM\Column(type="string", length=256, nullable=true) */
  protected $phone;

  /** @ORM\Column(type="text", nullable=true) */
  protected $description;

  /** @ORM\Column(name="handicap_accessible", type="boolean", nullable=true, options={"default" = 1}) */
  protected $is_handicap_accessible = true;

  /** @ORM\Column(name="is_visible", type="boolean", nullable=true, options={"default" = 1}) */
  protected $is_visible = true;

  /** @ORM\Column(name="position", type="integer", nullable=true) */
  protected $position;

  /**
     @ORM\OneToOne(targetEntity="Address", cascade={"all"})
     @ORM\joinColumn(name="location_id", referencedColumnName="id")
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

  /** @ORM\PostUpdate */
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
      switch( $property ) {
        case 'address':
          $address = new Address();
          $address = $address->fromArray( $value );

          $this->address = $address;
          break;
          
        default: 
          $this->$property = $value;
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
    // can't set private properties
    $protected = \ReflectionProperty::IS_PROTECTED;
    $reflector = new \ReflectionClass( $this );
    $protected_props = $reflector->getProperties( $protected );

    foreach( $protected_props as $prop ) {
      if ( $prop->getName() == $property ) {
        $this->$property = $value;
      }
    }
    if ( isset ( $this->$property ) ) {
      return $this; // allow chaining
    } else {
      trigger_error( "Can't access property " . get_class( $this ) . ':' . $property, E_USER_ERROR );
    }

  }

  public function __construct() {
    $this->sublocations = new \Doctrine\Common\Collections\ArrayCollection();
  }
}

?>

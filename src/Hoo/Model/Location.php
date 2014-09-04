<?php

namespace Hoo\Model;

use \Doctrine\ORM\Mapping as ORM;

/** 
   @ORM\Entity
   @ORM\Table(name="hoo_locations")
   @ORM\HasLifecycleCallbacks
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
  protected $alternateName;

  /** @ORM\Column(type="string", length=256) */
  protected $url;

  /** @ORM\Column(type="string", length=256) */
  protected $phone;

  /** @ORM\Column(type="decimal", precision=18, scale=15) */
  protected $lat;

  /** @ORM\Column(type="decimal", precision=18, scale=15) */
  protected $lon;

  /** @ORM\Column(type="text") */
  protected $description;

  /** @ORM\Column(name="handicap_accessible", type="boolean") */
  protected $isHandicapAccessible;

  /**
     @ORM\Column(name="address_id", type="integer") 
     @ORM\OneToOne(targetEntity="Address")
     @ORM\joinColumn(name="location_id", referencedColumnName="id")
   */
  protected $address;
  
  /** @ORM\OneToMany(targetEntity="Location", mappedBy="parent") */
  protected $sublocations;

  /**
     @ORM\ManyToOne(targetEntity="Location", inversedBy="sublocations")
     @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
   */
  private $parent;

  /** @ORM\Column(name="created_at", type="datetime") */
  private $createdAt;

  /** @ORM\column(name="updated_at", type="datetime") */
  private $updatedAt;

  /** @ORM\PrePersist */
  public function set_created_at() {
    $this->createdAt = new \DateTime();
  }

  /** @ORM\PostUpdate */
  public function set_updated_at() {
    $this->updatedAt = new \DateTime();
  }

  public function __toString(){
    return $this->name;
  }
  
  /**
     a little getter/setter magic
     doctrine wants protected properties
     this tries to preserve accessibility a little bit by making private non-accessible
   */
  public function __get( $property ) {
    // can't get private properties
    $protected = \ReflectionProperty::IS_PROTECTED;
    $reflector = new \ReflectionClass( $this );
    $protected_props = $reflector->getProperties( $protected );

    foreach( $protected_props as $prop ) {
      if ( $prop->getName() == $property ) {
        return $this->$property;
      }
    }
    trigger_error( "Can't access property " . get_class( $this ) . ':' . $property, E_USER_ERROR );
    
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

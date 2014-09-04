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
  private $id;

  /** 
    @ORM\Column(name="line1", type="string", length=256) 
  */
  protected $line1;

  /** 
    @ORM\Column(name="line2", type="string", length=256) 
  */
  protected $line2;

  /** 
    @ORM\Column(name="line3", type="string", length=256) 
  */
  protected $line3;

  /** 
    @ORM\Column(name="city", type="string", length=256) 
  */
  protected $city;

  /** 
    @ORM\Column(name="state", type="string", length=256) 
  */
  protected $state;

  /** 
    @ORM\Column(name="zip", type="string", length=256) 
  */
  protected $zip;

  /** 
    @ORM\Column(name="created_at", type="datetime") 
  */
  private $createdAt;

  /** 
    @ORM\Column(name="updated_at", type="datetime") 
  */
  private $updatedAt;

  /** 
    @ORM\PrePersist 
  */
  public function set_created_at() {
    $this->createdAt = new \DateTime();
  }

  /** 
    @ORM\PostUpdate 
  */
  public function set_updated_at() {
    $this->updatedAt = new \DateTime();
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

  public function __toString(){
    return $this->name;
  }
}

?>

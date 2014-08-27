<?php

namespace Hoo\Model;

/** @Entity @Table(name="hoo_locations") @HasLifecycleCallbacks */
class Location {

  /** @Id @Column(type="integer") @GeneratedValue */
  private $id;

  /** @Column(type="string", length=256) */
  protected $name;

  /** @Column(name="alternate_name", type="string", length=256) */
  protected $alternateName;

  /** @Column(type="string", length=256) */
  protected $url;

  /** @Column(type="string", length=256) */
  protected $phone;

  /** @Column(type="decimal", precision=18, scale=15) */
  protected $lat;

  /** @Column(type="decimal", precision=18, scale=15) */
  protected $lon;

  /** @Column(type="text") */
  protected $description;

  /** @Column(name="handicap_accessible", type="boolean") */
  protected $isHandicapAccessible;

  /**
     @Column(name="address_id", type="integer") 
     @OneToOne(targetEntity="Address")
     @joinColumn(name="location_id", referencedColumnName="id")
   */
  protected $address;
  
  /** @OneToMany(targetEntity="Location", mappedBy="parent") */
  protected $sublocations;

  /**
     @ManyToOne(targetEntity="Location", inversedBy="sublocations")
     @JoinColumn(name="parent_id", referencedColumnName="id")
   */
  protected $parent;

  /** @Column(name="created_at", type="datetime") */
  private $createdAt;

  /** @column(name="updated_at", type="datetime") */
  private $updatedAt;

  /** @PrePersist */
  public function set_created_at() {
    $this->createdAt = new \DateTime();
  }

  /** @PostUpdate */
  public function set_updated_at() {
    $this->updatedAt = new \DateTime();
  }

  public function __toString(){
    return $this->name;
  }
  
  public function __construct() {
    $this->sublocations = new \Doctrine\Common\Collections\ArrayCollection();
  }
}

?>

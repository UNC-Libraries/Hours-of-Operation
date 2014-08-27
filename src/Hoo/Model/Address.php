<?php

namespace Hoo\Model;

/** @Entity @Table(name="hoo_addresses") @HasLifecycleCallbacks */
class Address {

  /** @Id @Column(type="integer") @GeneratedValue */
  private $id;

  /** @Column(name="line1", type="string", length=256) */
  protected $line1;

  /** @Column(name="line2", type="string", length=256) */
  protected $line2;

  /** @Column(name="line3", type="string", length=256) */
  protected $line3;

  /** @Column(name="city", type="string", length=256) */
  protected $city;

  /** @Column(name="state", type="string", length=256) */
  protected $state;

  /** @Column(name="zip", type="string", length=256) */
  protected $zip;

  /** @Column(name="created_at", type="datetime") */
  private $createdAt;

  /** @Column(name="updated_at", type="datetime") */
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
}

?>

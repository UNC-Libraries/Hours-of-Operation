<?php

defined( 'ABSPATH' ) or die();

class HoO_DB {
  
  public static function create_tables() {
    global $wpdb;

    $locations_sql = <<<SQL
    CREATE TABLE IF NOT EXISTS hoo_locations (
    `id` INT(11) NOT NULL auto_increment,
    `name` VARCHAR(256) NOT NULL,
    `alternate_name` VARCHAR(256),
    `url` VARCHAR(256),
    `phone` VARCHAR(256),
    `lat` DECIMAL(18,15) NOT NULL,
    `lon` DECIMAL(18,15) NOT NULL,
    `description` TEXT NOT NULL,
    `handicap_accessible` BOOLEAN,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`) );
SQL;

    $addresses_sql = <<<SQL
    CREATE TABLE IF NOT EXISTS hoo_addresses (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `location_id` INT(11) NOT NULL,
    `line1` VARCHAR(256),
    `line2` VARCHAR(256),
    `line3` VARCHAR(256),
    `city` VARCHAR(256),
    `state` VARCHAR(256),
    `zip` VARCHAR(256),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`location_id`) REFERENCES hoo_locations(`id`));
SQL;

    foreach( array( $locations_sql, $addresses_sql ) as $table ) {
      $status = $wpdb->query( $table );
    }
  }
  
  public static function locations( $id = null ) {
    global $wpdb;
    
    $query = <<<SQL
    SELECT * FROM `hoo_locations` hl
    JOIN `hoo_addresses` ha ON hl.id = ha.id
SQL;

    if ( $id ) {
      $query .= ' WHERE `hl`.`id` = %s';
      return $wpdb->get_row( $wpdb->prepare( $query, $id ), ARRAY_A );
    }
    else {
      return $wpdb->get_results( $query, ARRAY_A );
    }
  }
}
?>

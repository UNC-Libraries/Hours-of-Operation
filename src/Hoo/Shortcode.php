<?php

namespace Hoo;

class Shortcode {
  public function __construct( $em ) {
    $this->entity_manager = $em;

    add_shortcode( 'hoo', array( $this, 'hoo' ) );
  }

  public function hoo( $attributes ) {
    $attributes = shortcode_atts( array( 'widget' => 'full' ), $attribtues, 'hoo' );

    if( method_exists( $this, $attributes['widget'] ) ) {
      $this->$attributes['widget']();
    } else {
      return 'bad widget attribute!!';
    }
  }

  public function full() {
    $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
    $locations = $locations_repo->findBy( array(), array( 'position' => 'asc' ) );
    $view = new View( 'shortcode/location' );
    $view->render( array( 'locations' => $locations,
                          'now' => new \DateTime( null, new \DateTimeZone( get_option( 'timezone_string' ) ) )) );
  }
}
?>

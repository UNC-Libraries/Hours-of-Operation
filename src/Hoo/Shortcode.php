<?php

namespace Hoo;

class Shortcode {
  public function __construct( $em ) {
    $this->entity_manager = $em;

    add_shortcode( 'hoo', array( $this, 'hoo' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'init' ) );
  }

  public function hoo( $attributes ) {
    $attributes = shortcode_atts( array( 'widget' => 'full' ), $attribtues, 'hoo' );

    if( method_exists( $this, $attributes['widget'] ) ) {
      $this->$attributes['widget']();
    } else {
      return 'bad widget attribute!!';
    }
  }

  public function init() {
    global $post;

    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'hoo' ) ) {
      wp_enqueue_style( 'shortcode-main' );
      wp_enqueue_script( 'shortcode-main' );
      wp_enqueue_script( 'hoo-map');
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

<?php

namespace Hoo;
use Hoo\Model\Location;

class Shortcode {
    public function __construct( $em ) {
        $this->entity_manager = $em;

        add_shortcode( 'hoo', array( $this, 'hoo' ) );

        /*
           HACK:
           Registers the hoo-api shortcode. However, the template_redirect callback should intercept
           and check for the shortcode before wp can render anything
           hoo_api will send some json and exit
           TODO: find a better way to do the api? :D
         */
        add_shortcode( 'hoo-api', array( $this, 'hoo_api' ) );
        add_action( 'template_redirect', array( $this, 'hoo_api' ) );
    }

    public function enqueue_script( $widget ) {
        switch( $widget ){
            case 'full':
                wp_enqueue_style( 'shortcode-main' );
                wp_enqueue_script( 'shortcode-main' );
                break;
            default:
        }
    }
    public function hoo_api() {
        global $post;

        // /if the page containts the hoo-api shortcode send json and exit :}
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'hoo-api' ) ) {
            $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );

            if ( isset( $_GET['location_id'] ) ) {
                $location = $locations_repo->findBy( array( 'id' => $_GET['location_id'], 'is_visible' => true ) )[0];
                $hours = $location->get_hours_for_date( $_GET['date'] );
                wp_send_json( $hours );
            }
            exit;
        }
    }

    public function hoo( $attributes ) {
        $attributes = shortcode_atts( array( 'widget' => 'full', 'header' => null, 'tagline' => null ), $attributes, 'hoo' );


        if ( method_exists( $this, $attributes['widget'] ) && 'hoo_api' != $attributes['widget'] ) {
            $this->enqueue_script( $attributes['widget'] );
            $this->$attributes['widget']( $attributes['header'], $attributes['tagline']);
        } else {
            return 'bad widget attribute!!';
        }
    }

    public function full( $header, $tagline ) {
        $locations = Location::get_visible_locations( $this->entity_manager );


        $view = new View( 'shortcode/location' );
        $view->render( array( 'locations' => $locations,
                              'header' => $header,
                              'tagline' => $tagline,
                              'now' => new \DateTime() ) );
    }
}
?>

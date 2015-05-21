<?php

namespace Hoo;
use Hoo\Model\Location;
use Hoo\Model\Category;

class Shortcode {
    private static $valid_widgets = array( 'full', 'today',  'weekly' );
    private static $valid_widget_attributes = array( 'header' => array( 'full', 'weekly' ),
                                                     'location' => array( 'weekly', 'today' ),
                                                     'tagline' => array( 'full' ) );

    static public function available_widgets() {
        return self::$valid_widgets;
    }

    static public function valid_widget_attributes() {
        return self::$valid_widget_attributes;
    }

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
        add_action( 'wp', array( $this, 'api_headers' ) );
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

    public function api_headers() {
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'hoo-api' ) ) {
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Headers: *' );
            header( 'Content-Type: application/json' );
        }
    }

    public function hoo_api() {
        global $post;

        // /if the page contains the hoo-api shortcode send json and exit :}
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'hoo-api' ) ) {

            $locations_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
            $json_response = array();
            $date = isset( $_GET['date'] ) ? new \DateTime( $_GET['date'] ) : new \DateTime ( date( 'Y-m-d' ) );

            if ( isset( $_GET['location_id'] ) ) {
                $location = $locations_repo->findOneBy( array( 'id' => $_GET['location_id'], 'is_visible' => true ) );
                $hours = $location->get_hours_for_date( $date );

                $json_response['location'] = $location->to_api_response();
                $json_response['location']['address'] = $location->address->to_api_response();
                $json_response['hours'] = $hours ? $hours->to_api_response() : null;

            } else {
                foreach ( $locations_repo->findBy( array( 'is_visible' => true ) ) as $location ) {
                    $hours = $location->get_hours_for_date( $date );
                    $json_response[]['location'] = $location->to_api_response();
                    $json_response[]['location']['address'] = $location->address->to_api_response();
                    $json_response[]['hours'] = $hours ? $hours->to_api_response() : null;
                }
            }

            wp_send_json( $json_response );
            exit;
        }
    }

    public function hoo( $attributes ) {
        $attributes = shortcode_atts( array( 'widget' => 'full', 'header' => null, 'tagline' => null, 'location' => null ), $attributes, 'hoo' );

        if ( method_exists( $this, $attributes['widget'] ) && in_array( $attributes['widget'], self::$valid_widgets ) ) {
            $this->enqueue_script( $attributes['widget'] );
            return $this->$attributes['widget']( $attributes );
        } else {
            return 'bad widget attribute!!';
        }
    }

    public function full( $attributes ) {
        $locations = Location::get_visible_locations( $this->entity_manager );
        $categories = Category::get_visible_categories( $this->entity_manager );

        // get weekly hours for mobile view
        $locations_hours = array();
        foreach( $locations as $location ) {
            $locations_hours[] = array( 'location' => $location, 'hours' => $location->get_weekly_hours() );
        }

        $view = new View( 'shortcode/location' );
        return $view->fetch( array( 'locations' => $locations_hours,
                                    'categories' => $categories,
                                    'header' => $attributes['header'],
                                    'tagline' => $attributes['tagline'],
                                    'now' => new \DateTime() ) );
    }

    public function today( $attributes ) {
        $locations_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
        $location = isset( $attributes['location'] ) ? $locations_repo->findOneBy( array( 'id' => $attributes['location'], 'is_visible' => true ) ) : null;

        if ( $location ) {
            $view = new View( 'shortcode/today' );
            return $view->fetch( array( 'current_hours' => $location->is_open() ) );
        } else {
            return '';
        }
    }

    public function weekly( $attributes ) {
        $locations_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
        $locations = isset( $attributes['location'] ) ?
                     $locations_repo->findBy( array( 'id' => $attributes['location'], 'is_visible' => true ) ) :
                     $locations_repo->findBy( array( 'is_visible' => true ) );
        $view = new View( 'shortcode/weekly' );
        $locations_hours = array();

        foreach( $locations as $location ) {
            $locations_hours[] = array( 'location' => $location, 'hours' => $location->get_weekly_hours() );
        }

        return $view->fetch( array( 'header' => $attributes['header'],
                                    'locations' => $locations_hours ) );
    }
}
?>

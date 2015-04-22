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

        // /if the page containts the hoo-api shortcode send json and exit :}
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'hoo-api' ) ) {

            $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
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


        $view = new View( 'shortcode/location' );
        return $view->fetch( array( 'locations' => $locations,
                                    'categories' => $categories,
                                    'header' => $attributes['header'],
                                    'tagline' => $attributes['tagline'],
                                    'now' => new \DateTime() ) );
    }

    public function today( $attributes ) {
        $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
        $location = isset( $attributes['location'] ) ? $locations_repo->findOneBy( array( 'id' => $attributes['location'], 'is_visible' => true ) ) : null;

        if ( $location ) {
            $view = new View( 'shortcode/today' );
            return $view->fetch( array( 'current_hours' => $location->is_open() ) );
        } else {
            return '';
        }
    }

    public function weekly( $attributes ) {
        $locations_repo = $this->entity_manager->getRepository( '\Hoo\Model\Location' );
        $location = isset( $attributes['location'] ) ? $locations_repo->findOneBy( array( 'id' => $attributes['location'], 'is_visible' => true ) ) : null;

        if ( $location ) {
            $start = new \DateTime( 'sunday last week' );
            $end = new \DateTime( 'sunday this week' );
            $interval = new \DateInterval( 'P1D' );

            $event_instances = $location->get_event_instances( $start, $end );

            $weekdays = new \DatePeriod( $start, $interval, $end );
            $weekly_events = array();

            foreach ( $weekdays as $day ) {
                $weekly_events[] = isset( $event_instances[ $day->format( 'Y-m-d' ) ] ) ?
                                   sprintf( '%s - %s',
                                            $event_instances[ $day->format( 'Y-m-d' ) ]->start->format( 'h:i a' ),
                                            $event_instances[ $day->format( 'Y-m-d' ) ]->end->format( 'h:i a' ) ) :
                                   'N/A';
            }

            $hours = array();
            $day = 0;
            while ( $day < 7 ) {
                $start = $day; $end = $day;
                while ( $end < 7 && ( $weekly_events[ $start ] == $weekly_events[ $end ] ) ) {
                    $end++;
                };

                if ( 1 == $end - $start ) {
                    $dow_text = date( 'l', strtotime( "sunday last week +$start days" ) );
                    $hours[ $dow_text ] = $weekly_events[ $day ];
                } else {
                    $start_dow_text = date( 'D', strtotime( "sunday last week +$start days" ) );
                    $end_dow_text = date( 'D', strtotime( sprintf( 'sunday last week +%s days', $end - 1 ) ) );
                    $range_text = sprintf( '%s - %s', $start_dow_text, $end_dow_text );
                    $hours[ $range_text ] = $weekly_events[ $day ];
                }
                $day = $end++;
            }


            $view = new View( 'shortcode/weekly' );
            return $view->fetch( array( 'location' => $location,
                                        'header' => $attributes['header'],
                                        'hours'    => $hours ) );
        } else {
            return '';
        }
    }
}
?>

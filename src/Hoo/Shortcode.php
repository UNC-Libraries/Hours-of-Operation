<?php

namespace Hoo;
use Hoo\Model\Location;
use Hoo\Model\Category;

class Shortcode {
    private static $valid_widgets = array( 'full', 'full-list-only', 'today',  'weekly' );
    private static $valid_widget_attributes = array( 'header' => array( 'full', 'full-list-only', 'weekly' ),
                                                     'full_widget_url' => array( 'full-list-only' ),
                                                     'location' => array( 'weekly', 'today' ),
                                                     'tagline' => array( 'full', 'full-list-only' ) );

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
            case 'full-list-only':
                wp_enqueue_style( 'shortcode-full-list-only' );
                break;
            default:
                wp_enqueue_style( 'shortcode-full-list-only' );
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
                $qb = $this->entity_manager->createQueryBuilder();
                try {
                    $location = $qb->select( array( 'location' ) )
                                   ->from( 'Hoo\Model\Location', 'location' )
                                   ->where( $qb->expr()->orX(
                                       $qb->expr()->eq( 'location.id', ':id_or_name' ),
                                       $qb->expr()->like( 'location.alternate_name', ':id_or_name' ) ) )
                                   ->setParameter( 'id_or_name', $_GET['location_id'] )
                                   ->getQuery()->getSingleResult();
                } catch ( \Doctrine\ORM\NoResultException $e)  {
                    wp_send_json_error( 'Not Found' );
                }

                $hours = $location->get_hours_for_date( $date );

                $json_response['location'] = $location->to_api_response();
                $json_response['location']['address'] = $location->address->to_api_response();
                $json_response['hours'] = $hours ? $hours->to_api_response() : null;
                $json_response['weekly'] = $location->get_weekly_hours();

            } else {
                $locations_repo = $this->entity_manager->getRepository( 'Hoo\Model\Location' );
                foreach ( $locations_repo->findBy( array( 'is_visible' => true ) ) as $location ) {
                    $hours = $location->get_hours_for_date( $date );
                    $json_response[]['location'] = $location->to_api_response();
                    $json_response[]['location']['address'] = $location->address->to_api_response();
                    $json_response[]['hours'] = $hours ? $hours->to_api_response() : null;
                    $json_response[]['weekly'] = $location->get_weekly_hours();
                }
            }

            wp_send_json( $json_response );
            exit;
        }
    }

    public function hoo( $attributes ) {
        // NOTE: attributes have to be snake case...
        $attributes = shortcode_atts( array( 'widget' => 'full', 'header' => null, 'tagline' => null, 'location' => null, 'full_widget_url' => null), $attributes, 'hoo' );
        /* NOTE: the widget attribute has to be a valid method name.
           this does a string replace of '-' -> '_' to conenience.
         */
        $method = str_replace( '-', '_', $attributes['widget'] );

        if ( method_exists( $this, $method ) && in_array( $attributes['widget'], self::$valid_widgets ) ) {
            $this->enqueue_script( $attributes['widget'] );
            return $this->$method( $attributes );
        } else {
            return 'bad widget attribute!!';
        }
    }

    public function full( $attributes ) {
        $locations = Location::get_visible_locations( $this->entity_manager );

        // get weekly hours for mobile view
        $locations_hours = array();
        foreach( $locations as $location ) {
            $locations_hours[] = array( 'location' => $location, 'hours' => $location->get_weekly_hours() );
        }

        $view_options = array( 'locations' => $locations_hours,
                               'header' => $attributes['header'],
                               'tagline' => $attributes['tagline'],
                               'now' => new \DateTime() );

        if ( isset ( $attributes['list-only'] ) && $attributes['list-only'] ) {
            $view_options['list-only'] = true;
            $view_options['full-widget-url'] = $attributes['full_widget_url'];
        } else {
            $view_options['categories']  = Category::get_visible_categories( $this->entity_manager );
            $view_options['list-only'] = false;
        }


        $view = new View( 'shortcode/full' );
        return $view->fetch( $view_options );
    }

    public function full_list_only( $attributes ) {
        $attributes['list-only'] = true;
        return $this->full( $attributes );
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

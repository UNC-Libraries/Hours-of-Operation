<?php

namespace Hoo;


class Utils {
    static public function check_user_role( $role, $user_id = null ){

        $user = is_numeric( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();

        if ( empty( $user ) ) {
            return false;
        }

        return array_key_exists( $role, (array) $user->allcaps );
    }

    static public function format_time( \DateTime $start, \DateTime $end = null) {
        $start_format = ( $start->format('i') == '00' ? 'g ' : 'g:i ' ) . 'a';

        if ( ! is_null( $end ) ) {
            $end_format = ( $end->format('i') == '00' ? 'g ' : 'g:i ' ) . 'a';
            return sprintf( "%s \n-\n %s", $start->format( $start_format), $end->format( $end_format ) );
        }

        return $start->format( $start_format );

    }

    static public function is_open( $hours ) {
        $now = new \DateTime();
        return $now >= new \DateTime( $recurrence['open'] ) && $now <= new \DateTime( $recurrence['close'] );
    }

    static public function remove_overlapping_events( $event_instances ) {
        $event_dates = array();
        foreach( $event_instances as &$event_instance ) {
            $cur_date = $event_instance['recurrence']->getStart()->format( 'Y-m-d' );
            $cur_date_priority = $event_instance['event']->category->priority;

            if ( array_key_exists( $cur_date, $event_dates ) ) {
                $priority = $event_dates[ $cur_date ]['event']->category->priority;

                if ( $cur_date_priority > $priority ) {
                    $event_dates[ $cur_date ] =& $event_instance;
                }
            } else {
                $event_dates[ $cur_date ] =& $event_instance;
            }
        }
        ksort( $event_dates );
        return array_values( $event_dates );
    }

    static public function prev_was_all_day( $cur, $instances ) {
        $index = 1;
        while ( $index < count( $instances ) ) {
            if ( $instances[ $index ] == $cur ) {
                $prev = $instances[$index - 1];
                break;
            }
            $index++;
        }

        if ( isset( $prev ) && $prev['event']->is_all_day ) {
            if ( $cur['event']->is_all_day ) return false;

            $cur_date = new \DateTime( $cur['recurrence']->getStart()->format('Y-m-d') );
            $prev_date = new \DateTime( $prev['recurrence']->getStart()->format('Y-m-d') );

            return $cur_date->diff( $prev_date )->d == 1;
        }
        return false;
    }

    static public function next_is_all_day( $cur, $instances ) {
        $num_instances = count( $instances );
        $index = 0;
        if ( $num_instances == 1 ) return false;
        while ( $index < $num_instances - 1 ) {
            if ( $instances[ $index ] == $cur ) {
                $next = $instances[ $index + 1 ];
                break;
            }
            $index++;
        }

        if ( isset( $next ) && $next['event']->is_all_day ) {
            if ( $cur['event']->is_all_day ) return false;
            $cur_date = new \DateTime( $cur['recurrence']->getStart()->format('Y-m-d') );
            $next_date = new \DateTime( $next['recurrence']->getStart()->format('Y-m-d') );

            return $cur_date->diff( $next_date )->d == 1;
        }
        return false;
    }
}

?>

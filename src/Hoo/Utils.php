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

    static public function str_to_rrules ( $str ) {
        $rrules = array();

        foreach( explode( ';', $str ) as $rule ) {
            list( $rule_name, $rule_value ) = split( '=', $rule );

            if( preg_match( '/^BY/', $rule_name ) || preg_match( '/,/', $rule_value ) ) {
                $rule_value = explode( ',', $rule_value );
            }
            $rrules[ $rule_name ] = $rule_value;
        }
        return $rrules;
    }

    static public function rrules_to_str( $rrules ) {
        end( $rrules ); $last = key( $rrules );
        $rrule_str = '';
        foreach( $rrules as $name => $rule_part ) {
            if ( is_array( $rule_part ) ) {
                $rrule_str .= sprintf( '%s=%s', $name, join( ',', $rule_part ) );
            } else {
                $rrule_str .= sprintf( '%s=%s', $name, $rule_part );
            }
            $rrule_str .= $name == $last ? '' : ';' ;
        }
        return strtoupper( $rrule_str );
    }

    static public function remove_overlapping_events( $event_instances ) {
        $event_dates = array();
        foreach( $event_instances as &$event_instance ) {
            $date = $event_instance['recurrence']->getStart()->format( 'Y-m-d' );
            if ( ! isset( $event_dates[ $date ] ) ) {
                $event_dates[ $date ] =& $event_instance;
            } elseif ( $event_dates[ $date ]['event']->category->priority > $event_instance['event']->category->priority )
                $event_dates[ $date ] =& $event_instance;
        }
        ksort( $event_dates );
        return array_values( $event_dates );
    }

    private function prev_was_all_day( $cur, $instances ) {
        // get array pointer to correct item
        while ( $val = current( $instances ) ) {
            if ( $val == $cur ) {
                $prev = prev( $instances );
                break;
            }
            next( $instances );
        }

        if ( $prev && $prev['event']->is_all_day ) {
            if ( $cur['event']->is_all_day ) return false;

            return $cur['recurrence']->getStart()->diff( $prev['recurrence']->getStart() ) == 1;
        }
        return false;
    }

    private function next_is_all_day( $cur, $instances ) {
        // get array pointer to correct item
        while ( $val = current( $instances ) ) {
            if ( $val == $cur ) {
                $next = next( $instances );
                break;
            }
            next( $instances );
        }

        if ( $next && $next['event']->is_all_day ) {
            if ( $cur['event']->is_all_day ) return false;

            return $cur['recurrence']->getStart()->diff( $next['recurrence']->getStart() ) == 1;
        }
        return false;
    }

    static public function event_instances_to_fullcalendar ( $event_instances ) {
        // TODO: refactor the 24 hours / formatting as it is gross
        $events = array();
        foreach( $event_instances as $index => $instance ) {
            if ( $instance['event']->is_all_day ) {
                $title = 'Open 24 Hours';
            } elseif ( Utils::prev_was_all_day( $instance, $event_instances ) )  {
                $title = sprintf( "24 Hours\n-\n%s", Utils::format_time( $instance['recurrence']->getEnd() ) );
            } elseif ( Utils::next_is_all_day( $instance, $event_instances ) )  {
                $title = sprintf( "%s\n-\n24 Hours", Utils::format_time( $instance['recurrence']->getStart() ) );
            } else {
                $title = sprintf( "%s\n%s", $instance['event']->title,
                                  Utils::format_time( $instance['recurrence']->getStart(), $instance['recurrence']->getEnd() ) );
            }


            $events[] = array( 'id' => $instance['event']->id,
                               'title' => $title,
                               'start' => $instance['recurrence']->getStart()->format( \DateTime::ISO8601 ),
                               'end' => $instance['recurrence']->getEnd()->format( \DateTime::ISO8601 ),
                               'color' => $instance['event']->category->color  );
        }
        return $events;
    }
}

?>

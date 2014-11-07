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

  static public function is_open( $recurrence ) {
    $now = new \DateTime();
    return $now >= $recurrence->getStart() && $now <= $recurrence->getEnd();
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
}

?>

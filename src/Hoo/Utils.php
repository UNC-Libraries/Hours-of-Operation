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
  
  static public function format_as_time_range( $start, $end ) {
    $start_format = ( $start->format('i') == '00' ? 'g ' : 'g:i ' ) . 'a';
    $end_format = ( $end->format('i') == '00' ? 'g ' : 'g:i ' ) . 'a';
    
    return sprintf( '%s - %s', $start->format( $start_format), $end->format( $end_format ) );
  }
}

?>

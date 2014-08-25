<?php 
/*
 * Hours of Operation
 *
 * @package   HoO
 * @author    UNCLibraries
 * @license   GPL-3.0+
 * @link      http://library.unc.edu
 * @copyright 2014 UNCLibraries
 *
 * @wordpress-plugin
 * Plugin Name:       Hours of Operation
 * Plugin URI:        @TODO
 * Description:       Allows you to manage the Hours of Operations for your business or institution
 * Version:           0.0.1
 * Author:            @TODO
 * Author URI:        @TODO
 * Text Domain:       hoo-locale
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: @TODO
 */

// quit if called directly
if ( !function_exists( 'add_action' )) {
  echo "Hi!  I'm goin going to quit now.  Bye!";
  exit;
}

define( 'HOO_VERSION', '0.0.1' );
define( 'HOO__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) . 'hoo/' );
define( 'HOO__PLUGIN_ADMIN_DIR', plugin_dir_path( __FILE__ ) . 'hoo-admin/' );

// public facing app
require_once( HOO__PLUGIN_DIR . 'class-hoo.php' );

register_activation_hook( __FILE__, array( 'HoO', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'HoO', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'HoO', 'get_instance' ) );

if ( is_admin() ) {
  require_once ( HOO__PLUGIN_ADMIN_DIR . 'class-hoo-admin.php' );
  add_action( 'plugins_loaded', array( 'HoO_Admin', 'get_instance' ) );
}

?>

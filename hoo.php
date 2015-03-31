<?php
/*
 * Hours of Operation
 *
 * @package   HoO
 * @author    UNC Libraries
 * @license   GPL-3.0+
 * @link      http://library.unc.edu
 * @copyright 2014 UNC Libraries
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
defined( 'ABSPATH' ) or die();

define( 'HOO_VERSION', '0.0.1' );
define( 'HOO__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOO__PLUGIN_ASSETS_URL', HOO__PLUGIN_URL . 'assets/');
define( 'HOO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once( HOO__PLUGIN_DIR . 'vendor/autoload.php' );
date_default_timezone_set( get_option( 'timezone_string' ) );

$loader = new Hoo\Loader();

register_activation_hook( __FILE__, array( $loader, 'activate' ) );
register_deactivation_hook( __FILE__, array( $loader, 'deactivate' ) );


?>

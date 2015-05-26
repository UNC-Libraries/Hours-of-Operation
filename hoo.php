<?php
/*
 * Hours of Operation
 *
 * @package   HoO
 * @author    UNC Libraries
 * @license   GPL-2.0
 * @link      http://library.unc.edu
 * @copyright 2015 UNC Libraries
 *
 * @wordpress-plugin
 * Plugin Name:       Hours of Operation
 * Plugin URI:        @TODO
 * Description:       Allows you to manage the Hours of Operations for your business or institution
 * Version:           0.0.1
 * Author:            @TODO
 * Author URI:        @TODO
 * Text Domain:       hoo-locale
 * License:           GPL-2.0
 * License URI:       http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: @TODO

   Copyright 2015  UNC Libraries  (email : erhart@unc.edu)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


 */

// quit if called directly
defined( 'ABSPATH' ) or die();

define( 'HOO_VERSION', '0.0.1' );
define( 'HOO__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOO__PLUGIN_ASSETS_URL', HOO__PLUGIN_URL . 'assets/');
define( 'HOO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once( HOO__PLUGIN_DIR . 'vendor/autoload.php' );
date_default_timezone_set( get_option( 'timezone_string' ) );

add_action( 'plugins_loaded', array( 'Hoo\Loader', 'init' ) );

register_activation_hook( __FILE__, array( 'Hoo\Loader', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Hoo\Loader', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'Hoo\Loader', 'uninstall' ) );

?>

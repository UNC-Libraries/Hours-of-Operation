<?php

namespace Hoo\Admin;

use Hoo\View;
use Hoo\Model\Location;
use Hoo\Shortcode;

class ShortcodeController {
    public function __construct( $entity_manager ) {
        $this->entity_manager = $entity_manager;

        $this->init_hooks();
    }

    public function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function add_menu_page() {
        add_submenu_page( 'hoo',
                          __( 'Shortcodes', 'hoo-shortcodes' ),
                          __( 'Shortcodes', 'hoo-shortcodes' ),
                          'manage_options',
                          'hoo-shortcodes',
                          array( $this, 'index' ) );
    }

    public function enqueue_scripts( $page ) {
        if ( preg_match( '/hoo-shortcodes/i', $page ) ) {
            wp_enqueue_script( 'hoo-shortcodes' );
        }
    }

    public function index() {
        $view = new View( 'admin/shortcodes/index' );
        $view->render( array( 'title' => 'Shortcodes',
                              'locations' => Location::get_visible_locations( $this->entity_manager ),
                              'available_widgets' => Shortcode::available_widgets(),
                              'valid_widget_attributes' => Shortcode::valid_widget_attributes() ) );
    }
}

?>

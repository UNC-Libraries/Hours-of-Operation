<?php

namespace Hoo\Admin;

use Hoo\Utils;

class CategoryList extends \WP_List_Table {

  public function __construct( $entity_manager ) {
    $this->entity_manager = $entity_manager;

    parent::__construct( array(
      'singular' => 'wp_list_text_link',
      'plural'   => 'wp_list_text_links',
      'ajax'     => false
    ) );
  }

  public function get_columns() {
    return array(
      'name' => __( 'Name' ),
      'updated_at' => __( 'Modified Date' ),
      'priority' => __( 'Priority' )
    );
  }

  public function get_sortable_columns() {
    return array(
      'name' => array( 'name', false ),
      'updated_at' => array( 'updated_at', false ),
      'priority' => array( 'priority', true )
    );
  }

  public function prepare_items() {
    $current_screen = get_current_screen();

    // register columns
    $columns = $this->get_columns();
    $this->_column_headers = array(
      $this->get_columns(),
      array(),
      $this->get_sortable_columns()
    );

    // fetch categories
    $order_by = isset( $_REQUEST['orderby'] ) ? array( $_REQUEST['orderby'] => $_REQUEST['order'] ) : array( 'priority' => 'asc' );
    $categories_repo = $this->entity_manager->getRepository( '\Hoo\Model\Category' );
    $categories = $categories_repo->findBy( array(), $order_by );

    $this->items = $categories;
  }

  public function column_name( $category ) {
    $actions = array(
      'edit' => sprintf( '<a href=?page=%s&category_id=%s>Edit</a>', 'hoo-category-edit', $category->id ),
      'delete' => sprintf( '<a href=?page=%s&action=%s&category_id=%s>Delete</a>', 'hoo-category-edit', 'delete', $category->id )
    );

    return sprintf( '%1$s %2$s', $category->name, $this->row_actions( $actions ) );
  }

  public function column_updated_at( $category ) {
    return $category->updated_at->format( 'F j, Y g:i a' );
  }

  public function column_is_visible( $category ) {
    $checked = $category->is_visible ? 'checked' : '';
    return sprintf( '<input type="checkbox" value="%s" %s/>', $category->is_visible, $checked );
  }

  public function column_default( $category, $column_name ) {
    return $category->$column_name;
  }

  public function single_row( $item ) {
    static $alternate = '';
    $alternate = ( $alternate == '' ? ' alternate' : '' );
    
    $row_class = sprintf( ' class="list-item%s"', $alternate );
    $row_id = sprintf( ' id="category_%s"', $item->id );

    echo sprintf( '<tr %s %s>', $row_id, $row_class );
    $this->single_row_columns( $item );
    echo '</tr>';

  }

  public function no_items() {
    _e( ' There are no categories.  <a href="?page=hoo-category-add">Click Here</a> to add a category!' );
  }
}

?>

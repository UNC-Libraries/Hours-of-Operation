<?php

$this->set_layout( 'admin' );
$this->capture();

?>

<form id="category-form" name="category_form" class="metabox-form" action="<?php echo esc_url( sprintf( '?page=%s&action=%s&category_id=%s', $this['page'], $this['action'], $this['category']->id ) )?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $this['action'] ?>"/>
  <?php 

  wp_nonce_field( $this['action'] );
  wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
  wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

  ?>

  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-<?php echo $this['columns'] ?>">

      <div id="post-body-content">
        <div id="titlediv">
          <div id="titlewrap">
            <label for="title" class="screen-reader-text" id="title-prompt-text">Title</label>
            <input type="text"
                   name="category[name]"
                   id="title"
                   autocomplete="off"
                   placeholder="Name"
                   value="<?php echo $this['category']->name ?>"/>
          </div>
        </div>
      </div> <!-- /post-body-content -->

      <div id="postbox-container-1" class="postbox-container">
        <?php do_meta_boxes( $this['page'], 'side', null ) ?>
      </div>
      
      <div id="postbox-container-2" class="postbox-container">
        <?php do_meta_boxes( $this['page'], 'normal', null) ?>
      </div>
    </div>
  </div>
</form>
<?php $this->end_capture( 'body' ); ?>

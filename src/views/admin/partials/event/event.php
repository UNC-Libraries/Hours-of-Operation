<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<form id="event-form" name="event_form" action="<?php echo esc_url( sprintf( '?page=%s&action=%s', 'hoo-location-event-edit', $this['action'] ) )  ?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $this['action'] ?>"/>

  <?php
  wp_nonce_field( $this['action'] );
  wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
  wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
  ?>

  <div id="poststuff">
    <p>hi</p>
    <div id="post-body" class="metabox-holder columns-<?php echo $this['columns'] ?>">

      <div id="postbox-container-1" class="postbox-container">
        <?php do_meta_boxes( 'hoo-location-event-add', 'side', null ) ?>
      </div>

      <div id="postbox-container-2" class="postbox-container">
        hi
        <?php do_meta_boxes( 'hoo-location-event-add', 'normal', null) ?>
      </div>
    </div>
  </div>
</form>

<?php $this->end_capture( 'body' ); ?>

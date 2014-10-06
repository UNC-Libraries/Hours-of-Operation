<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<form id="event-form" name="event_form" class="metabox-form" action="<?php echo esc_url( sprintf( '?page=%s&action=%s', $this['page'], $this['action'] ) )  ?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $this['action'] ?>"/>
  <input type="hidden" name="event[id]" value"<?php echo $this['action'] ?>"/>
  <input type="hidden" name="event[location]" value="<?php echo $this['location']->id ?>"/>

  <?php
  wp_nonce_field( $this['action'] );
  wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
  wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
  ?>

  <div id="poststuff">
    <div id="post-body" class="location-name metabox-holder columns-<?php echo $this['columns'] ?>">

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

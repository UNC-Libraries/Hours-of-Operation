<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<form id="event-form" name="event_form" class="metabox-form" action="<?php echo esc_url( sprintf( '?page=%s', $this['page'] ) )  ?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $this['action'] ?>"/>
  <input type="hidden" name="event[id]" id="event_id" value="<?php echo $this['event']->id ?>"/>
  <input type="hidden" name="event[location]" id="event_location" value="<?php echo $this['event']->location->id ?>"/>

  <?php
  wp_nonce_field( $this['action'] );
  wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
  wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
  ?>

  <div id="poststuff">
    <div id="post-body" class="event-name metabox-holder columns-<?php echo $this['columns'] ?>">

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

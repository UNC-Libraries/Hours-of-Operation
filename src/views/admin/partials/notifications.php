<?php if ( isset( $this['notification'] ) ) :?>
  <div class="below-h2 <?php echo $this['notification']['type'] ?>">
    <p><?php _e( $this['notification']['message'], 'hoo-location' ) ?></p>
  </div>
<?php endif ?>

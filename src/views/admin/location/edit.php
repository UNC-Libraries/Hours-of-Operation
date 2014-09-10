<?php 
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="location">
  <?php $this->include_file( 'admin/partials/location_form' ) ?>
</div>

<?php $this->end_capture( 'body' ); ?>

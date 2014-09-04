<?php
$this->set_layout( 'admin' );
$this->capture();
?>


<h3>Add a Location</h3>

<div class="location">
  <?php $this->include_file( 'partials/location_form' ) ?>
</div>

<?php $this->end_capture( 'body'  ); ?>

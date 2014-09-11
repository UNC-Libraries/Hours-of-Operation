<?php 
$this->set_layout( 'admin' );
$this->capture();
?>

<?php $this->include_file( 'admin/partials/location_form' ) ?>

<?php $this->end_capture( 'body' ); ?>

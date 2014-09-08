<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="locations">
  <?php $this['locations-table']->display(); ?>
</div>

<?php $this->end_capture( 'body'  ); ?>

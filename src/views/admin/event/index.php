<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="events">
  <?php $this['events-table']->display(); ?>
</div>

<?php $this->end_capture( 'body'  ); ?>

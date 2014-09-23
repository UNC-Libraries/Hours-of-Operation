<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="categories">
  <?php $this['categories-table']->display(); ?>
</div>

<?php $this->end_capture( 'body'  ); ?>

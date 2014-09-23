<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="category">
  <?php $this->include_file( 'admin/partials/category_form' ) ?>
</div>

<?php $this->end_capture( 'body'  ); ?>

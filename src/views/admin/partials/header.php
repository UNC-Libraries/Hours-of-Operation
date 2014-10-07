<div class="hoo-head">
  <a href="?page=<?php echo isset( $this['hoobert-link'] ) ? $this['hoobert-link'] : 'hoo' ?>">
    <img src="http://library.dev/wp-content/plugins/hoo/assets/images/hoo-100.png"/></a>
    <h2 id="wphead">
      <?php echo $this['title'] ?>
      <?php if ( isset( $this['add-new-page'] ) ) : ?>
        <a class="add-new-h2" href="?page=<?php echo $this['add-new-page'] ?>">Add New</a>
      <?php endif ?>
    </h2>
    <?php $this->include_file( 'admin/partials/notifications' ) ?>
</div>

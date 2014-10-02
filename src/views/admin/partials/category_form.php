<form id="category-form" name="category_form" action="<?php echo esc_url( '?page=hoo-category-edit&action=' . $this['action'] )?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $this['action'] ?>"/>
  <?php wp_nonce_field( $this['action'] );
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
  ?>
  
  
         
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-<?php echo $this['columns'] ?>">

      <div id="post-body-content">
        <div id="titlediv">
          <div id="titlewrap">
            <label for="title" class="screen-reader-text" id="title-prompt-text">Name</label>
            <input type="text"
                   name="category[name]"
                   id="title"
                   autocomplete="off"
                   placeholder="Name"
                   value="<?php echo $this['category']->name ?>"/>
          </div>
        </div>
      </div> <!-- /post-body-content -->
      
      <div id="postbox-container-1" class="postbox-container">
        <?php do_meta_boxes( '', 'category', null ) ?>
      </div>
      
      
    </div>
  </div>
</form>

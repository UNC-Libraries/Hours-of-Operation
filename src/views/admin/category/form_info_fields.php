<ul>
    <li>
      <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
        <div class="form-item form-item-textfield">
          <label for="title" class="wpt-form-label wpt-form-textfield-label">Title</label>
          <input type="text"
                 name="category[name]"
                 id="title"
                 autocomplete="off"
                 placeholder="Name"
                 value="<?php echo $this['category']->name ?>"/>
        </div>
      </div>
    </li>
    <li>
    <div class="description wpt-form-description wpt-form-description-textarea description-textarea">
      <div class="form-item form-item-textarea">
        <label for="category_description" class="wpt-form-label wpt-form-textarea-label">Description</label>
        <textarea name="category[description]" id="category_description" tabindex="4"><?php echo $this['category']->description ?></textarea>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="category_color" class="wpt-form-label wpt-form-textfield-label">Color</label>
        <input type="text" 
               name="category[color]"
               id="category_color"
               class="wpt-form-textfield form-textfield textfield category-color-field"
               value="<?php echo $this['category']->color ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="category_priority" class="wpt-form-label wpt-form-textfield-label">Priority</label>
        <input type="text"
               name="category[priority]"
               id="category_url"
               class="wpt-form-textfield form-textfield textfield"
               value="<?php echo $this['category']->priority ?>"/>
      </div>
    </div>
  </li>
</ul>
<ul>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_name" class="wpt-form-label wpt-form-textfield-label">Title / Label</label>
        <input type="text"
               name="event[name]"
               id="event_name"
               class="wpt-form-textfield form-textfield textfield"
               value="<?php echo $this['event']->name ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="category">
      <div class="form-item form-item-select">
        <label for="event_category" class="wpt-form-label wpt-form-select-label">Category</label>
        <select name="event[category]">
          <?php foreach( $this['event-categories'] as $category ) : ?>
            <option value="">Select a Category</option>
            <option value="<?php echo $category->id ?>" <?php echo $this['event']->category == $category ? 'selected' : '' ?>>
              <?php echo $category->name ?>
            </option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
  </li>
</ul>

<ul>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="location_url" class="wpt-form-label wpt-form-textfield-label">URL</label>
        <input type="text"
               name="location[url]"
               id="location_url"
               class="wpt-form-textfield form-textfield textfield"
               value="<?php echo $this['location']->url ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="location_phone" class="wpt-form-label wpt-form-textfield-label">Phone #</label>
        <input type="text"
               name="location[phone]"
               id="location_url"
               class="wpt-form-textfield form-textfield textfield"
               value="<?php echo $this['location']->phone ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="description wpt-form-description wpt-form-description-textarea description-textarea">
      <div class="form-item form-item-textarea">
        <label for="location_description" class="wpt-form-label wpt-form-textarea-label">Description</label>
        <textarea name="location[description]" id="location_description" tabindex="4">
          <?php echo $this['location']->description ?>
        </textarea>
      </div>
    </div>
  </li>
  <li>
    <div class="parent">
      <div class="form-item form-item-select">
        <label for="location_parent" class="wpt-form-label wpt-form-select-label">Parent Location</label>
        <select name="location[parent]">
          <option value="">None</option>
          <?php foreach( $this['parent-locations'] as $parent_location ) : ?>
            <option value="<?php echo $parent_location->id ?>" <?php echo $this['location']->parent == $parent_location ? 'selected' : '' ?>>
              <?php echo $parent_location->name ?>
            </option>
          <? endforeach ?>
        </select>
      </div>
    </div>
  </li>
</ul>

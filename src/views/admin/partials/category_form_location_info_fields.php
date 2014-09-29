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
</ul>
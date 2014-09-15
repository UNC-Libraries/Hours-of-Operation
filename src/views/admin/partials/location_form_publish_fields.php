<div class="submitbox" id="submitpost">
  <div id="minor-publishing">
    <ul>
      <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
          <div class="form-item form-item-textfield">
            <label for="location_position">Position</label>
            <input name="location[position]"
                   type="text"
                   size="4"
                   id="location_position"
                   value="<?php echo $this['location']->position ?>">
          </div>
        </div>
      </li>
      <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
          <div class="form-item form-item-textfield">
            <label for="location_is_visible">Visible</label>
            <input type="checkbox"
                   name="location[is_visible]"
                   id="location_is_visible"
                   value="<?php echo $this['location']->is_visible ?>"
            <?php echo $this['location']->is_visible ? 'checked' : '' ?>/>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div id="major-publishing-actions">
    <div id="publishing-action">
      <input type="submit"
             name="location_submit"
             id="location_submit"
             class="button button-primary button-large"
             value="Publish" />
    </div>
    <div class="clear"></div>
  </div>
</div>

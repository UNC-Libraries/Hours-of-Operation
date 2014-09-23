<div class="submitbox" id="submitpost">
  <div id="minor-publishing">
    <ul>
      <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
          <div class="form-item form-item-textfield">
            <label for="category_position">Priority</label>
            <input name="category[priority]"
                   type="text"
                   size="4"
                   id="category_priority"
                   value="<?php echo $this['category']->priority ?>">
          </div>
        </div>
      </li>
      <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
          <div class="form-item form-item-textfield">
            <label for="category_is_visible">Visible</label>
            <input type="hidden" name="category[is_visible]" value="0"/>
            <input type="checkbox"
                   name="category[is_visible]"
                   id="category_is_visible"
                   value="<?php echo $this['category']->is_visible ?>"
            <?php echo $this['category']->is_visible ? 'checked' : '' ?>/>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div id="major-publishing-actions">
    <div id="publishing-action">
      <input type="submit"
             name="category_submit"
             id="category_submit"
             class="button button-primary button-large"
             value="Publish" />
    </div>
    <div class="clear"></div>
  </div>
</div>

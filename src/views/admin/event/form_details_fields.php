<ul>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_start" class="wpt-form-label wpt-form-textfield-label">Start</label>
        <?php $this['event']->start->setTimezone( new \DateTimeZone( get_option( 'timezone_string' ) ) ) ?>
        <input type="datetime"
               name="event[start]"
               id="event_start"
               class="wpt-form-textfield form-textfield textfield datetimefield"
               value="<?php echo $this['event']->start->format( 'Y-m-d H:i' ) ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_end" class="wpt-form-label wpt-form-textfield-label">End</label>
        <?php $this['event']->end->setTimezone( new \DateTimeZone( get_option( 'timezone_string' ) ) ) ?>
        <input type="datetime"
               name="event[end]"
               id="event_end"
               class="wpt-form-textfield form-textfield textfield datetimefield"
               value="<?php echo $this['event']->end->format( 'Y-m-d H:i' )?>">
      </div>
    </div>
  </li>
  <li>
    <div class="repeat-options">
      <div class="form-item form-item-select">
        <label for="event_repeats" class="wpt-form-label wpt-form-select-label">Repeats</label>
        <select id="event_repeats" name="event_repeats[frequency]">
          <option value="">None</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="yearly">Yearly</option>
          <option value="custom">Custom</option>
        </select>
      </div>
    </div>
  </li>
  <li>
    <fieldset>
      <legend>Preview</legend>
      <div id="preview_calendar"></div>
    </fieldset>
  </li>
</ul>

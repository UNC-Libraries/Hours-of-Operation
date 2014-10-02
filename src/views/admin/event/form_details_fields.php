<ul>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_start" class="wpt-form-label wpt-form-textfield-label">Start</label>
        <input type="text"
               name="event[start]"
               id="event_start"
               class="wpt-form-textfield form-textfield textfield datetimefield"
               value="<?php echo $this['event']->start->format( 'Y-m-d h:i' ) ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_end" class="wpt-form-label wpt-form-textfield-label">End</label>
        <input type="text"
               name="event[end]"
               id="event_end"
               class="wpt-form-textfield form-textfield textfield datetimefield"
               value="<?php echo $this['event']->end->format( 'Y-m-d h:i' ) ?>"/>
      </div>
    </div>
  </li>
</ul>

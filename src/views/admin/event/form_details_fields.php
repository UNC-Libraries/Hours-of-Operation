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
    <div class="form-item form-item-select">
      <label for="event_recurrence_rule" class="wpt-form-label wpt-form-select-label">Repeats</label>
      <select id="event_recurrence_rule" name="event_recurrence_rule[frequency]">
        <option value="">None</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
        <option value="custom">Custom</option>
      </select>
    </div>
  </li>
  <li>
    <div id="rrule-custom-container" class="js-wpt-field wpt-field wp-textfield">
      <label for="event_recurrence_rule_custom" class="wpt-form-label wpt-form-select-label">Frequency</label>
      <select id="event_recurrence_rule_custom" name="event_recurrence_rule_custom[frequency]">
        <option value="daily" data-unit="day">Daily</option>
        <option value="weekly" data-unit="week">Weekly</option>
        <option value="monthly" data-unit="month">Monthly</option>
        <option value="yearly" data-unit="year">Yearly</option>
      </select>

      <div class="interval wpt-field wp-textfield">
        <div class="form-item form-item-textfield">
          <label for="event_recurrence_rule_custom_interval"></label>
          Every
          <input type="text"
                 id="event_recurrence_rule_custom_interval"
                 name="event_recurrence_rule_custom[interval]"
                 class="wpt-form-textfield form-textfield textfield"
                 value="1"/>
          <span id="interval-unit"> day</span>(s)</p>
        </div>
      </div>

      <div class="rrule-custom weekly">
        <?php foreach ( array( 'MO' => 'Monday' , 'TU' => 'Tuesday', 'WE' => 'Wednesday', 'TH' => 'Thursday', 'FR' => 'Friday', 'SA' => 'Saturday' ) as $abbrv => $full) : ?>
          <label for="<?php sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?> class="wpt-form-label wpt-form-checkbox-label">
            <?php echo $full ?>
          </label>
          <input type="checkbox"
                 id="<?php sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?>"
                 name="event_recurrence_rule_custom[byday][]"
                 value="<?php echo $day ?>"/>
        <?php endforeach ?>
      </div>

      <div class="rrule-custom monthly">
        <p>hi</p>
      </div>

      <div class="rrule-custom yearly">
        <?php foreach( range( 1, 12 ) as $month ): ?>
          <label for="<?php sprintf( 'event_recurrence_rule_custom_bymonth_%s', $month ) ?>">
            <?php echo \DateTime::createFromFormat( '!m', $month )->format( 'M' ) ?>
          </label>
          <input type="checkbox"
                 id="<?php sprintf( 'event_recurrence_rule_custom_bymonth_%s', $month ) ?>"
                 name="event_recurrence_rule_custom[bymonth][]"
                 value="<?php echo $month ?>"/>
        <?php endforeach ?>
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

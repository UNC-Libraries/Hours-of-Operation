<ul>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_start" class="wpt-form-label wpt-form-textfield-label">Start Date</label>
        <input type="text"
               name="event_start_date"
               id="event_start_date"
               class="wpt-form-textfield form-textfield textfield date"
               value="<?php echo $this['event']->start->format( 'Y-m-d' ) ?>"/>
      </div>
    </div>
  </li>
  <li>
    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
      <div class="form-item form-item-textfield">
        <label for="event_end" class="wpt-form-label wpt-form-textfield-label">Hours</label>
        <input type="text"
               name="event_start_time"
               id="event_start_time"
               class="wpt-form-textfield form-textfield textfield time"
               value="<?php echo $this['event']->start->format( 'h:i A' )?>">
        <span>-</span>
        <input type="text"
               name="event_end_time"
               id="event_end_time"
               class="wpt-form-textfield form-textfield textfield time"
               value="<?php echo $this['event']->end->format( 'h:i A' )?>">
      </div>
    </div>
  </li>
  <li>
    <?php $is_custom = count( $this['event']->recurrence_rule ) > 1; ?>
    <div class="form-item form-item-select">
      <label for="event_recurrence_rule" class="wpt-form-label wpt-form-select-label">Repeats</label>
      <select id="event_recurrence_rule" name="event[recurrence_rule]" class="hoo-rrule">
        <option value="NONE"
          <?php if ( empty( $this['event']->recurrence_rule ) ) echo  'selected' ?>>None
        </option>
        <?php $rule = $is_custom ? 'CUSTOM' : $this['event']->recurrence_rule['FREQ']  ?>
        <?php foreach ( $this['freq_values'] as $freq_value ) : ?>
          <option value="<?php echo strtoupper( $freq_value ) ?>"
            <?php if ( strtoupper( $freq_value ) == $rule ) echo 'selected' ?>><?php echo $freq_value  ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </li>
  <li>
    <div id="rrule-custom-container" class="js-wpt-field wpt-field wp-textfield <?php if ( ! $is_custom ) echo 'is-hidden' ?>">
      <label for="event_recurrence_rule_custom" class="wpt-form-label wpt-form-select-label">Frequency</label>
      <select id="event_recurrence_rule_custom" name="event_recurrence_rule_custom[freq]" class="hoo-rrule">
        <?php foreach ( $this['cust_freq_values'] as $freq_value ) : ?>
          <option value="<?php echo strtoupper( $freq_value ) ?>"
            <?php if ( strtoupper( $freq_value ) == $this['event']->recurrence_rule['FREQ'] ) echo 'selected' ?>><?php echo $freq_value  ?></option>
        <?php endforeach ?>
      </select>

      <div class="rrule-custom interval wpt-field wp-textfield">
        <div class="form-item form-item-textfield">
          <label for="event_recurrence_rule_custom_interval"></label>
          Every
          <input type="text"
                 id="event_recurrence_rule_custom_interval"
                 name="event_recurrence_rule_custom[interval]"
                 class="wpt-form-textfield form-textfield textfield hoo-rrule"
                 value="<?php echo isset( $this['event']->recurrence_rule['INTERVAL'] ) ? $this['event']->recurrence_rule['INTERVAL'] : 1 ?>"/>
          <span id="interval-unit"> <?php echo $this['freq_units'][ $this['event']->recurrence_rule['FREQ'] ] ?></span>(s)</p>
        </div>
      </div>

      <div class="rrule-custom weekly <?php if ( ! ( $is_custom && $this['event']->recurrence_rule['FREQ'] == 'WEEKLY' ) ) echo 'is-hidden' ?>">
        <?php foreach ( array( 'MO' => 'Monday', 'TU' => 'Tuesday', 'WE' => 'Wednesday', 'TH' => 'Thursday', 'FR' => 'Friday', 'SA' => 'Saturday' ) as $abbrv => $full) : ?>
          <label for="<?php echo sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?>" class="wpt-form-label wpt-form-checkbox-label">
            <?php echo $full ?>
          </label>
          <input type="checkbox"
                 id="<?php sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?>"
                 class="hoo-rrule"
                 name="event_recurrence_rule_custom[byday][]"
                 value="<?php echo $abbrv ?>"
          <?php echo ( isset( $this['event']->recurrence_rule['BYDAY'] ) && in_array( $abbrv, $this['event']->recurrence_rule['BYDAY'] ) ) ? 'checked' : ''  ?>/>
        <?php endforeach ?>
      </div>

      <div class="rrule-custom monthly <?php if ( ! ( $is_custom && $this['event']->recurrence_rule['FREQ'] == 'MONTHLY' ) ) echo 'is-hidden' ?>">
        <p>hi</p>
      </div>

      <div class="rrule-custom yearly <?php if ( ! ( $is_custom && $this['event']->recurrence_rule['FREQ'] == 'MONTHLY' ) ) echo 'is-hidden' ?>">
        <?php foreach( range( 1, 12 ) as $month ): ?>
          <label for="<?php sprintf( 'event_recurrence_rule_custom_bymonth_%s', $month ) ?>">
            <?php echo \DateTime::createFromFormat( '!m', $month )->format( 'M' ) ?>
          </label>
          <input type="checkbox"
                 id="<?php sprintf( 'event_recurrence_rule_custom_bymonth_%s', $month ) ?>"
                 class="hoo-rrule"
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

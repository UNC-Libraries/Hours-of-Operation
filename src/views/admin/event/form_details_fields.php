<ul>
    <li>
        <label for="event_is_all_day" class="wpt-form-label wpt-form-textfield-label">Closed</label>
        <input type="checkbox"
               name="event[is_closed]"
               id="event_is_closed"
               class="wpt-form-checkbox form-checkbox checkbox"
               value="1"
               <?php if ( $this['event']->is_closed ) echo  'checked' ?>/>
    </li>
    <li>
        <label for="event_is_all_day" class="wpt-form-label wpt-form-textfield-label">24 Hours</label>
        <input type="checkbox"
               name="event[is_all_day]"
               id="event_is_all_day"
               class="wpt-form-checkbox form-checkbox checkbox"
               value="1"
               <?php if ( $this['event']->is_all_day ) echo  'checked' ?>/>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield date-field <?php if ( ! ($this['event']->is_closed || $this['event']->is_all_day ) ) echo 'is-hidden' ?>">
            <div class="form-item form-item-textfield">
                <label for="event_start_date" class="wpt-form-label wpt-form-textfield-label">Date</label>
                <input type="text"
                       name="event_start_date"
                       id="event_start_date"
                       class="wpt-form-textfield form-textfield textfield date"
                       value="<?php echo $this['event']->start->format( 'Y-m-d' ) ?>"
                       required />
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield datetime-field <?php if ( $this['event']->is_closed || $this['event']->is_all_day ) echo 'is-hidden' ?>"">
            <div class="form-item form-item-textfield">
                <label for="event_end" class="wpt-form-label wpt-form-textfield-label">Hours</label>
                <input type="text"
                       name="event[start]"
                       id="event_start"
                       class="wpt-form-textfield form-textfield textfield datetime"
                       value="<?php echo $this['event']->start->format( 'Y-m-d h:i a' ) ?>"
                       required />
                -
                <input type="text"
                       name="event[end]"
                       id="event_end"
                       class="wpt-form-textfield form-textfield textfield datetime"
                       value="<?php echo $this['event']->end->format( 'Y-m-d h:i a' ) ?>"
                       required />
            </div>
        </div>
    </li>
    <li>
        <div class="form-item form-item-select">
            <label for="event_recurrence_rule" class="wpt-form-label wpt-form-select-label">Repeats</label>
            <select id="event_recurrence_rule" name="event[recurrence_rule]" class="hoo-rrule">
                <option value="NONE"
                        <?php if ( ! $this['event']->is_recurring ) echo  'selected' ?>>None
                </option>
                <?php
                if ( $this['event']->is_custom_rrule ) {
                    $rule = 'CUSTOM';
                } else if ( $this['event']->is_recurring && ! $this['event']->is_custom_rule ) {
                    $rule = $this['event']->recurrence_rule->getFreqAsText();
                }
                ?>
                <?php foreach ( $this['freq_values'] as $freq_value ) : ?>
                    <option value="<?php echo strtoupper( $freq_value ) ?>"
                            <?php if ( isset( $rule ) && strtoupper( $freq_value ) == $rule ) echo 'selected' ?>>
                        <?php echo $freq_value  ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
    </li>
    <li>
        <div id="rrule-until" class="form-item rrule-custom until wpt-field wp-textfield <?php if ( ! $this['event']->is_recurring ) echo 'is-hidden' ?>">
            <label for="event_recurrence_rule_custom_until" class="wpt-form-label">Until</label>
            <input type="text"
                   id="event_recurrence_rule_custom_until"
                   name="event_recurrence_rule_custom[UNTIL]"
                   class="wpt-form-textfield form-textfield textfield hoo-rrule date"
                   value="<?php if ( $this['event']->is_recurring && $this['event']->recurrence_rule->getUntil() ) echo $this['event']->recurrence_rule->getUntil()->format( 'Y-m-d' ) ?>"/>
        </div>
    </li>
    <li>
        <div id="rrule-custom-container" class="js-wpt-field wpt-field wp-textfield <?php if ( ! $this['event']->is_custom_rrule ) echo 'is-hidden' ?>">
            <label for="event_recurrence_rule_custom" class="wpt-form-label wpt-form-select-label">Frequency</label>
            <select id="event_recurrence_rule_custom" name="event_recurrence_rule_custom[FREQ]" class="hoo-rrule">
                <?php foreach ( $this['cust_freq_values'] as $freq_value ) : ?>
                    <option value="<?php echo strtoupper( $freq_value ) ?>"
                            data-freq-unit="<?php echo $this['freq_units'][ strtoupper( $freq_value ) ] ?>""
                            <?php if ( strtoupper( $freq_value ) == $this['event']->recurrence_rule->getFreqAsText() ) echo 'selected' ?>><?php echo $freq_value  ?></option>
                <?php endforeach ?>
            </select>

            <div class="rrule-custom interval wpt-field wp-textfield">
                <div class="form-item form-item-textfield">
                    <label for="event_recurrence_rule_custom_interval" class="wpt-form-label">Every</label>
                    <input type="text"
                           id="event_recurrence_rule_custom_interval"
                           name="event_recurrence_rule_custom[INTERVAL]"
                           class="wpt-form-textfield form-textfield textfield hoo-rrule"
                           value="<?php echo $this['event']->is_custom_rrule ? $this['event']->recurrence_rule->getInterval() : 1 ?>"/>
                    <span id="interval-unit">
                        <?php echo $this['event']->is_custom_rrule ? $this['freq_units'][ $this['event']->recurrence_rule->getFreqasText() ] : 'day' ?>
                    </span>(s)
                </div>
            </div>

            <div class="rrule-custom weekly <?php if ( ! ( $this['event']->is_custom_rrule && $this['event']->recurrence_rule->getFreqAsText() == 'WEEKLY' ) ) echo 'is-hidden' ?>">
                <?php foreach ( array( 'SU' => 'Sunday', 'MO' => 'Monday', 'TU' => 'Tuesday', 'WE' => 'Wednesday', 'TH' => 'Thursday', 'FR' => 'Friday', 'SA' => 'Saturday' ) as $abbrv => $full) : ?>
                    <label for="<?php echo sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?>" class="wpt-form-label wpt-form-checkbox-label">
                        <input type="checkbox"
                           id="<?php sprintf( 'event_recurrence_rule_custom_byday_%s', $abbrv ) ?>"
                           class="hoo-rrule"
                           name="event_recurrence_rule_custom[BYDAY][]"
                           value="<?php echo $abbrv ?>"
                           <?php if ( $this['event']->is_custom_rrule && $this['event']->recurrence_rule->getByDay() && in_array( $abbrv, $this['event']->recurrence_rule->getByDay() ) ) echo 'checked' ?>/>
                        <?php echo $full ?>
                    </label>
                <?php endforeach ?>
            </div>
        </div>
    </li>
    <li>
        <fieldset>
            <legend>Preview</legend>
            <div id="preview-legend">
                <ul>
                    <?php foreach( $this['event-categories'] as $category ) : ?>
                        <li>
                            <span><?php echo $category->name ?></span>
                            <div class="preview-block" style="background-color: <?php echo $category->color ?>;"></div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <div id="preview_calendar"></div>
        </fieldset>
    </li>
</ul>

<div class="submitbox" id="submitpost">
    <div id="minor-publishing">
        <ul>
            <li>
                <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                    <div class="form-item form-item-textfield">
                        <label for="event_is_visible">Visible</label>
                        <input type="hidden" name="event[is_visible]" value="0"/>
                        <input type="checkbox"
                               name="event[is_visible]"
                               id="event_is_visible"
                               class="wpt-form-checkbox form-checkbox checkbox"
                               value="1"
                        <?php echo $this['event']->is_visible ? 'checked' : '' ?>/>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div id="major-publishing-actions">
        <div id="publishing-action">
            <input type="submit"
                   name="event_submit"
                   id="event_submit"
                   class="button button-primary button-large"
                   value="Publish" />
        </div>
        <div class="clear"></div>
    </div>
</div>

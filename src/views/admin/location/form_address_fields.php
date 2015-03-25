<ul>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line1">Line 1</label>
                <input type="text" name="location[address][line1]" id="address_line1" size="60" maxlength="256 "value="<?php echo $this['location']->address->line1 ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line2">Line 2</label>
                <input type="text" name="location[address][line2]" id="address_line2" size="60" maxlangth="256" value="<?php echo $this['location']->address->line2 ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line2">Line 3</label>
                <input type="text" name="location[address][line3]" id="address_line3" size="60" maxlangth="256" value="<?php echo $this['location']->address->line2 ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line3">City</label>
                <input type="text" name="location[address][city]" id="address_city" size="60" maxlangth="256" value="<?php echo $this['location']->address->city ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line3">State</label>
                <input type="text" name="location[address][state]" id="address_state" size="2" maxlength="256" value="<?php echo $this['location']->address->state ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_line3">Zip</label>
                <input type="text" name="location[address][zip]" id="address_zip" size="20" maxlength="256" value="<?php echo $this['location']->address->zip ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_lat">Latitude</label>
                <input type="text"
                       name="location[address][lat]"
                       id="address_lat"
                       size="20"
                       maxlength="256"
                       value="<?php echo $this['location']->address->lat ?>">
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="address_lon">Longitude</label>
                <input type="text" name="location[address][lon]" id="address_lon" size="20" maxlength="256" value="<?php echo $this['location']->address->lon ?>"/>
            </div>
        </div>
    </li>
</ul>

<ul>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="location_url" class="wpt-form-label wpt-form-textfield-label">URL</label>
                <input type="url"
                       name="location[url]"
                       id="location_url"
                       class="wpt-form-textfield form-textfield textfield"
                       size="60"
                       maxlength="256"
                       value="<?php echo $this['location']->url ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
            <div class="form-item form-item-textfield">
                <label for="location_phone" class="wpt-form-label wpt-form-textfield-label">Phone Number</label>
                <input type="tel"
                       name="location[phone]"
                       id="location_phone"
                       class="wpt-form-textfield form-textfield textfield"
                       size="20"
                       maxlength="256"
                       value="<?php echo $this['location']->phone ?>"/>
            </div>
        </div>
    </li>
    <li>
        <div class="parent">
            <div class="form-item form-item-select">
                <label for="location_parent" class="wpt-form-label wpt-form-select-label">Parent Location</label>
                <select name="location[parent]">
                    <option value="">None</option>
                    <?php foreach( $this['parent-locations'] as $parent_location ) : ?>
                        <option value="<?php echo $parent_location->id ?>" <?php echo $this['location']->parent == $parent_location ? 'selected' : '' ?>>
                            <?php echo $parent_location->name ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </li>
    <li>
        <label for="location_is_handicap_accessible" class="wpt-form-label wpt-form-checkbox-label">Handicap Accessible?</label>
        <input type="checkbox"
               name="location[is_handicap_accessible]"
               id="location_is_handicap_accessible"
               class="wpt-form-checkbox form-checkbox checkbox" 
               value="1"
               <?php if ( $this['location']->is_handicap_accessible ) echo 'checked' ?>/>
    </li>
    <li>
        <label for="location_handicap_link" class="wpt-form-label wpt-form-textfield-label">Handicap Access Link</label>
        <input type="url"
               name="location[handicap_link]"
               id="location_handicap_link"
               class="wpt-form-textfield form-textfield textfield" 
               value="<?php echo $this['location']->handicap_link ?>"/>
    </li>
    <li>
        <label for="location_notice" class="wpt-form-label wpt-form-textfield-label">* Notice</label>
        <input type="text"
               name="location[notice]"
               id="location_notice"
               class="wpt-form-textfield form-textfield textfield" 
               size="60"
               maxlength="256"
               value="<?php echo $this['location']->notice ?>"/>
    </li>
    <li>
        <div class="form-item">
            <label for="location_image" class="">Image</label>
            <input type="hidden"
                   name="location[image]"
                   id="location_image"
                   class="wpt-form-filefield form-filefield filefield"
                   value="<?php echo $this['location']->image ?>"/>
            <button id="location_upload_image_button" type="button">Select an Image</button>
            <img id="location_image_thumb" src="<?php echo $this['location']->image ? $this['location']->image : HOO__PLUGIN_URL . 'assets/images/hoo-no-image-selected.png' ?>" width="120"/>
        </div>
    </li>
    <li>
        <?php wp_editor( $this['location']->description, 'location_description', $this['wp_editor_options'] ) ?>
    </li>
</ul>

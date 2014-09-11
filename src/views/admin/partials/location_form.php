<form id="location-form" name="location_form" action="<?php echo esc_url( '?page=hoo-location&action=' . $this['action'] )?>" method="POST">
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">

      <div id="post-body-content">
        <div id="titlediv">
          <div id="titlewrap">
            <label for="title" class="screen-reader-text" id="title-prompt-text">Name</label>
            <input type="text"
                   name="location[name]"
                   id="title"
                   autocomplete="off"
                   placeholder="Name"
                   value="<?php echo $this['location']->name ?>"/>
          </div>
        </div>
      </div>

      <div id="postbox-container-1" class="postbox-container">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">

          <div class="postbox">
            <div id="location-attributes" class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle"><span>Publish</span></h3>
            <div class="inside">
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
                           value="<?php echo sprintf( '%s Location', $this['action-display'] ) ?>" />
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="postbox-container-2" class="postbox-container">

        <div class="meta-box-sortables ui-sortable">

          <div id="location-info" class="postbox">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle"><span>Location Info</span></h3>

            <div class="inside">
              <ul>
                <li>
                  <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                    <div class="form-item form-item-textfield">
                      <label for="location_url" class="wpt-form-label wpt-form-textfield-label">URL</label>
                      <input type="text"
                             name="location[url]"
                             id="location_url"
                             class="wpt-form-textfield form-textfield textfield"
                             value="<?php echo $this['location']->url ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                    <div class="form-item form-item-textfield">
                      <label for="location_phone" class="wpt-form-label wpt-form-textfield-label">Phone #</label>
                      <input type="text"
                             name="location[phone]"
                             id="location_url"
                             class="wpt-form-textfield form-textfield textfield"
                             value="<?php echo $this['location']->phone ?>"/>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="description wpt-form-description wpt-form-description-textarea description-textarea">
                    <div class="form-item form-item-textarea">
                      <label for="location_description" class="wpt-form-label wpt-form-textarea-label">Description</label>
                      <textarea name="location[description]" id="location_description" tabindex="4">
                        <?php echo $this['location']->description ?>
                      </textarea>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="meta-box-sortables ui-sortable">
            <div class="postbox">
              <div class="handlediv" title="Click to toggle"><br></div>
              <h3 class="hndle"><span>Address</span></h3>

              <div class="inside">
                <ul>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line1">Line 1</label>
                        <input type="text" name="location[address][line1]" id="address_line1" value="<?php echo $this['location']->address->line1 ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line2">Line 2</label>
                        <input type="text" name="location[address][line2]" id="address_line2" value="<?php echo $this['location']->address->line2 ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line2">Line 3</label>
                        <input type="text" name="location[address][line3]" id="address_line3" value="<?php echo $this['location']->address->line2 ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line3">City</label>
                        <input type="text" name="location[address][city]" id="address_city" value="<?php echo $this['location']->address->city ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line3">State</label>
                        <input type="text" name="location[address][state]" id="address_state" value="<?php echo $this['location']->address->state ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_line3">Zip</label>
                        <input type="text" name="location[address][zip]" id="address_zip" value="<?php echo $this['location']->address->zip ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_lat">Lattitude</label>
                        <input type="text" name="location[address][lat]" id="address_lat" value="<?php echo $this['location']->address->lat ?>"/>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="js-wpt-field wpt-field js-wpt-textfield wpt-textfield">
                      <div class="form-item form-item-textfield">
                        <label for="address_lon">Longitude</label>
                        <input type="text" name="location[address][lon]" id="address_lon" value="<?php echo $this['location']->address->lon ?>"/>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

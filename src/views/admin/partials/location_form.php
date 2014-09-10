<form id="location_form" name="location_form" action="<?php echo esc_url( '?page=hoo-location&action=' . $this['action'] )?>" method="POST">
  <ul>
    <li>
      <label for="location_name">Name</label>
      <input type="text" name="location[name]" id="location_name" value="<?php echo $this['location']->name ?>"/>
    </li>
    <li>
      <label for="location_alternate_name">Alternate Name</label>
      <input type="text" name="location[alternate_name]" id="location_alternate_name" value="<?php echo $this['location']->alternate_name ?>"/>
    </li>
    <li>
      <label for="location_url">URL</label>
      <input type="text" name="location[url]" id="location_url" value="<?php echo $this['location']->url ?>"/>
    </li>
    <li>
      <label for="location_phone">Phone #</label>
      <input type="text" name="location[phone]" id="location_phone" value="<?php echo $this['location']->phone ?>"/>
    </li>
    <li>
      <label for="location_is_handicap_accissible">Handicap Accessible</label>
      <input type="checkbox" 
             name="location[is_handicap_accessible]" 
             id="location_is_handicap_accessible"
             value="<?php echo $this['location']->is_handicap_accessible ?> "
             <?php echo $this['location']->is_handicap_accessible ? 'checked' : '' ?>/>
    </li>
    <li>
      <label for="location_is_visible">Visible</label>
      <input type="checkbox"
             name="location[is_visible]"
             id="location_is_visible"
             value="<?php echo $this['location']->is_visible ?>"
             <?php echo $this['location']->is_visible ? 'checked' : '' ?>/>
    </li>
    <li>
      <fieldset>
        <legend>Address</legend>
        <ul>
          <li>
            <label for="address_line1">Line 1</label>
            <input type="text" name="location[address][line1]" id="address_line1" value="<?php echo $this['location']->address->line1 ?>"/>
          </li>
          <li>
            <label for="address_line2">Line 2</label>
            <input type="text" name="location[address][line2]" id="address_line2" value="<?php echo $this['location']->address->line2 ?>"/>
          </li>
          <li>
            <label for="address_line2">Line 3</label>
            <input type="text" name="location[address][line3]" id="address_line3" value="<?php echo $this['location']->address->line2 ?>"/>
          </li>
          <li>
            <label for="address_line3">City</label>
            <input type="text" name="location[address][city]" id="address_city" value="<?php echo $this['location']->address->city ?>"/>
          </li>
          <li>
            <label for="address_line3">State</label>
            <input type="text" name="location[address][state]" id="address_state" value="<?php echo $this['location']->address->state ?>"/>
          </li>
          <li>
            <label for="address_line3">Zip</label>
            <input type="text" name="location[address][zip]" id="address_zip" value="<?php echo $this['location']->address->zip ?>"/>
          </li>
          <li>
            <label for="address_lat">Lattitude</label>
            <input type="text" name="location[address][lat]" id="address_lat" value="<?php echo $this['location']->address->lat ?>"/>
          </li>
          <li>
            <label for="address_lon">Longitude</label>
            <input type="text" name="location[address][lon]" id="address_lon" value="<?php echo $this['location']->address->lon ?>"/>
          </li>
        </ul>
      </fieldset>
    </li>
    <li>
      <label for="location_description">Description</label>
      <textarea name="location[description]" id="location_description"  tabindex="4"><?php echo $this['location']->description ?></textarea>
    </li>
  </ul>

  <?php submit_button( sprintf( '%s Location', $this['action-display'] ), 'primary', 'location_submit' )  ?>

</form>

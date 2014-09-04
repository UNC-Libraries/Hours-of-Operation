<form id="location_form" name="location_form" action="<?php echo esc_url ( 'hi' )?>" method="POST">
  <ul>
    <li>
      <label for="location_name">Name</label>
      <input type="text" name="location_name" id="location_name" value="<?php echo $this['location']->name ?>"/>
    </li>
    <li>
      <label for="location_alternate_name">Alternate Name</label>
      <input type="text" name="location_alternate_name" id="location_alternate_name" value="<?php echo $this['location']->alternameName ?>"/>
    </li>
    <li>
      <label for="location_url">URL</label>
      <input type="text" name="location_url" id="location_url" value="<?php echo $this['location']->url ?>"/>
    </li>
    <li>
      <label for="location_phone">Phone #</label>
      <input type="text" name="location_phone" id="location_phone" value="<?php echo $this['location']->phone ?>"/>
    </li>
    <li>
      <label for="location_lat">Latitude</label>
      <input type="text" name="location_lat" id="location_lat" value="<?php echo $this['location']->lat ?>"/>
    </li>
    <li>
      <label for="location_lon">Longitude</label>
      <input type="text" name="location_lon" id="location_lon" value="<?php echo $this['location']->lon ?>"/>
    </li>
    <li>
      <label for="location_is_handicap_accissible">Handicap Accessible</label>
      <input type="text" name="location_is_handicap_accessible" id="location_is_handicap_accessible" value="<?php echo $this['location']->isHandicapAccessible ?>"/>
    </li>
    <li>
      <label for="location_description">Description</label>
      <textarea name="location_description" id="location_description"  tabindex="4"><?php echo $this['location']->description ?></textarea>
    </li>
  </ul>

</form>

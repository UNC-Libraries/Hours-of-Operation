<ul id="locations-list">
  <li class="list-header">
    <div class="location-name">
      <span>Location Name</span>
    </div>
    <div class="location-status">
      <span>
        at <?php echo $this['now']->format( 'h:i a') ?>
      </span>
    </div>
  </li>
  <?php $this->include_file( 'shortcode/location_list_rows' ) ?>
</ul>

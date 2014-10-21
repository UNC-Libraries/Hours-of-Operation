<ul id="locations-list">
  <li class="list-header">
    <span class="location-name">Location Name</span>
    <span classs="location-status"><?php echo $this['now']->format( 'h:i a') ?></span>
  </li>
  <?php $this->include_file( 'shortcode/location_list_rows' ) ?>
</ul>

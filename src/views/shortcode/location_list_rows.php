<li class="hoo-main-list-header">
  <a href="#hoo-map">
    <span class="location-name">
      Location Name
    </span>
    <span class="location-status">
      @ <?php echo $this['now']->format( 'h:i a' ) ?>
    </span>
  </a>
</li>
<?php foreach( $this['locations'] as $location ) : ?>
  <li>
    <a href="#location-<?php echo $location->id ?>">
      <span class="location-name">
        <?php echo $location->name ?>
      </span>
      <span class="location-status">
        <?php echo $location->is_open() ? 'Open' : 'Closed' ?>
      </span>
    </a>
  </li>
<?php endforeach ?>

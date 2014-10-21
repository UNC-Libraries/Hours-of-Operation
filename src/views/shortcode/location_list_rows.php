<?php foreach( $this['locations'] as $location ) : ?>
  <li class="location-row">
    <a href="#" data-panel="panel-<?php echo $location->id ?>">
      <span class="location-name">
        <?php echo $location->name ?>
      </span>
      <span class="location-status">
        <?php echo $location->is_open() ? 'Open' : 'Closed' ?>
      </span>
    </a>
  </li>
<?php endforeach ?>

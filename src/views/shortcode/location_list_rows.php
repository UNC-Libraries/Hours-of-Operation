<?php foreach( $this['locations'] as $location ) : ?>
  <li class="location-row" data-panel="panel-<?php echo $location->id ?>">
    <div class="location-name">
      <span>
        <a href="#location">
          <?php echo $location->name ?>
        </a>
      </span>
    </div>
    <div class="location-status">
      <p>
        <?php echo $location->is_open() ? 'Open' : 'Closed' ?>
      </p>
      <p>
        until 12 PM
      </p>
    </div>
  </li>
<?php endforeach ?>

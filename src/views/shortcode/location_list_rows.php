<?php foreach( $this['locations'] as $location ) : ?>
  <?php $current_hours = $location->get_hours_for_date( null ); ?>
  <li class="location-row" data-panel="panel-<?php echo $location->id ?>" data-lat="<?php echo $location->address->lat ?>" data-lon="<?php echo $location->address->lon ?>" data-id="<?php echo $location->id ?>">
    <div class="location-name">
      <span>
        <a href="#location">
          <?php echo $location->name ?>
        </a>
      </span>
    </div>
    <div class="location-status">
      <?php if ( is_null( $current_hours ) ) : ?>
        <p>N/A</p>
      <?php elseif ( \Hoo\Utils::is_open( $current_hours ) ) : ?>
        <p>Open</p>
        <p>Until <?php echo \Hoo\Utils::format_time( $current_hours->getEnd() )?> </p>
      <?php else : ?>
        <p>Closed</p>
      <?php endif ?>
    </div>
  </li>
<?php endforeach ?>

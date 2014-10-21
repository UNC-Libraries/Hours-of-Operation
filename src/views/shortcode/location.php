<div id="hoo-main">
  <div id="location-list-container">
    <?php $this->include_file( 'shortcode/location_list' ) ?>
  </div>

  <div id="panel-container">
    <?php foreach( $this['locations'] as $location ) : ?>
      <div id="panel-<?php echo $location->id ?>" class="panel">
        <?php echo $location->description ?>
      </div>
    <?php endforeach ?>

    <div id="hoo-map" class="">
      <div id="map-canvas"></div>
    </div>
  </div>
</div>

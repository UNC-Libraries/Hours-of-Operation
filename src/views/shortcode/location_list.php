<ul id="locations-list">
  <?php $this->include_file( 'shortcode/location_list_rows' ) ?>
</ul>
<div id="hoo-map"></div>
<?php foreach( $this['locations'] as $location ) : ?>
  <div id="location-<?php echo $location->id ?>">
    <?php echo $location->description ?>
  </div>
<?php endforeach ?>

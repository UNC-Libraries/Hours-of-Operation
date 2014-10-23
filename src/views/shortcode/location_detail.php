<?php foreach( $this['locations'] as $location ) : ?>
  <div id="panel-<?php echo $location->id ?>" class="panel">
    <div class="location-detail">
      <?php if ( $location->url ) : ?>
        <h4><span><a href="<?php echo $location->url ?>"><?php echo $location->name ?></a></span></h4>
        <p><a href="<?echo $location->url ?>"><?php echo $location->url ?></a></p>
      <?php else :?>
        <h4><span><?php echo $location->name ?></span></h4>
      <?php endif ?>

      <div class="location-description">
        <?php echo $location->description ?>
      </div>

      <div class="location-address">

      </div>
    </div>
  </div>
<?php endforeach ?>

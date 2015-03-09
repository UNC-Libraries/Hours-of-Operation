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

            <div class="hours-calendar" data-location-id="<?php echo $location->id ?>"></div>

            <div class="address-picture-wrapper">
                <?php if ( $location->image ) : ?>
                    <div class="location-image">
                        <img src="<?php echo $location->image ?>"/>
                    </div>
                <?php endif ?>
                <?php if ( $location->address ) : ?>
                    <div class="location-address">
                        <ul>
                            <li>Address</li>
                        </ul>
                            <li><?php echo $location->address->line1 ?></li>
                            <li><?php echo $location->address->line2 ?></li>
                            <li><?php echo $location->address->line3 ?></li>
                            <li><?php echo sprintf( '%s, %s',  $location->address->city, $location->address->state ) ?></li>
                            <li><?php echo $location->address->zip ?></li>
                        <?php if ( $location->is_handicap_accessible ) : ?>
                            <span>Disability Access</span>
                        <?php endif ?>
                    </div>
                <?php endif ?>
                <div class="location-phone">
                    <ul>
                        <li>Phone</li>
                        <li><?php echo $location->phone ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

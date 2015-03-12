<?php foreach( $this['locations'] as $location ) : ?>
    <div id="panel-<?php echo $location->id ?>" class="panel">
        <div class="location-detail">
            <?php if ( $location->url ) : ?>
                <h4><span><a href="<?php echo $location->url ?>"><?php echo $location->name ?></a></span></h4>
                <p><a href="<?echo $location->url ?>"><?php echo $location->url ?></a></p>
            <?php else :?>
                <h4><span><?php echo $location->name ?></span></h4>
            <?php endif ?>

            <?php if ($location->description ) : ?>
                <div class="location-description">
                    <?php echo $location->description ?>
                </div>
            <?php endif ?>

            <div class="hours-calendar" data-location-id="<?php echo $location->id ?>"></div>

            <div class="contact">
                <div class="address-picture-wrapper">
                    <?php if ( $location->image ) : ?>
                        <div class="location-image">
                            <img src="<?php echo $location->image ?>"/>
                        </div>
                    <?php endif ?>
                </div>
                <div class="location-address">
                    <h5>Address</h5>
                    <ul>
                        <li><?php if ( $location->address->line1 ) echo $location->address->line1 ?></li>
                        <li><?php if ( $location->address->line2 ) echo $location->address->line2 ?></li>
                        <li><?php if ( $location->address->line2 ) echo $location->address->line3 ?></li>
                        <li><?php if ( $location->address->line2 ) echo sprintf( '%s, %s',  $location->address->city, $location->address->state ) ?></li>
                        <li><?php if ( $location->address->line2 ) echo $location->address->zip ?></li>
                    </ul>
                </div>
                <?php if ( $location->is_handicap_accessible ) : ?>
                    <div class="handicap">
                        <span>Disability Access</span>
                    </div>
                <?php endif ?>
                <?php if ( $location->phone ) : ?>
                    <div class="location-phone">
                        <h5>Phone</h5>
                        <ul>
                            <li><?php echo $location->phone ?></li>
                        </ul>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach ?>

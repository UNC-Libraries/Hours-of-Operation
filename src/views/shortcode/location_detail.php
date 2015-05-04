<?php foreach( $this['locations'] as $location_data ) : ?>
    <div id="panel-<?php echo $location_data['location']->id ?>" class="panel">
        <div class="content-wrapper">
            <div class="location-detail">
                <h2 class="post-entry">
                    <span class="location-name">
                        <?php if ( isset( $location_data['location']->url ) ) : ?>
                            <a href="<?php echo $location_data['location']->url ?>"><?php echo $location_data['location']->name ?></a>
                        <?php else :?>
                            <?php echo $location_data['location']->name ?>
                        <?php endif ?>
                    </span>
                    <span class="close-panel">&#x21e6;</span>
                </h2>

                <?php if ( ! empty( $location_data['location']->notice ) ) : ?>
                    <div class="location-notice">
                        <p>
                            <?php echo $location_data['location']->notice ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="hours-calendar" data-location-id="<?php echo $location_data['location']->id ?>"></div>

                <div id="category-legend">
                    <ul>
                        <?php foreach( $this['categories'] as $category ) : ?>
                            <li>
                                <div class="legend-wrapper">
                                    <div class="preview-block" style="background-color: <?php echo $category->color ?>;"></div>
                                    <span><?php echo $category->name ?></span>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>

                <h3 id="location-header"><?php echo $location_data['location']->name ?></h3>

                <?php if ( isset( $location_data['location']->url ) ) : ?>
                    <p id="location-url"><a href="<?php echo $location_data['location']->url ?>"><?php echo $location_data['location']->url ?></a></p>
                <?php endif ?>

                <?php if ( isset( $location_data['location']->description ) ) : ?>
                    <div id="location-description">
                        <?php echo $location_data['location']->description ?>
                    </div>
                <?php endif ?>

                <div id="contact">
                    <div id="address-picture-wrapper">
                        <?php if ( isset( $location_data['location']->image ) ) : ?>
                            <div id="location-image">
                                <img src="<?php echo $location_data['location']->image ?>"/>
                            </div>
                        <?php endif ?>
                    </div>
                    <div id="location-address">
                        <h5>Address</h5>
                        <ul>
                            <a href="<?php echo $location_data['location']->google_map_url() ?>" title="View on Google Maps">
                                <li><?php if ( isset( $location_data['location']->address->line1 ) ) echo $location_data['location']->address->line1 ?></li>
                                <li><?php if ( isset( $location_data['location']->address->line2 ) ) echo $location_data['location']->address->line2 ?></li>
                                <li><?php if ( isset( $location_data['location']->address->line2 ) ) echo $location_data['location']->address->line3 ?></li>
                                <li><?php if ( isset( $location_data['location']->address->line2 ) ) echo sprintf( '%s, %s',  $location_data['location']->address->city, $location_data['location']->address->state ) ?></li>
                                <li><?php if ( isset( $location_data['location']->address->line2 ) ) echo $location_data['location']->address->zip ?></li>
                            </a>
                        </ul>
                    </div>
                    <?php if ( isset( $location_data['location']->is_handicap_accessible ) ) : ?>
                        <div id="location-handicap">
                            <?php if ( isset( $location_data['location']->handicap_link ) ) : ?>
                                <a href="<?php echo $location_data['location']->handicap_link ?>">
                            <?php endif ?>
                            <img src="<?php echo HOO__PLUGIN_URL . 'assets/images/wheelchair-32.png' ?>"/>
                            <?php if ( isset( $location_data['location']->handicap_link ) ) : ?>
                                </a>
                            <?php endif ?>
                            <?php if ( isset( $location_data['location']->handicap_link ) ) : ?>
                                <a href="<?php echo $location_data['location']->handicap_link ?>">
                            <?php endif ?>
                            <span>Disability Access Information</span>
                            <?php if ( isset( $location_data['location']->handicap_link ) ) : ?>
                                </a>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                    <?php if ( isset( $location_data['location']->phone ) ) : ?>
                        <div id="location-phone">
                            <h5>Phone</h5>
                            <ul>
                                <li><?php echo $location_data['location']->phone ?></li>
                            </ul>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<div id="hoo-<?php echo $this['list-only'] ? 'full-list-only' : 'main' ?>">
    <?php if ( isset( $this['header'] ) ) : ?>
        <h1 id="header">
            <?php echo $this['header'] ?>
        </h1>
    <?php endif ?>

    <?php if ( isset( $this['tagline'] ) ) : ?>
        <div id="tagline">
            <p><?php echo $this['tagline'] ?></p>
        </div>
    <?php endif ?>

    <?php if ( count( $this['locations'] ) > 0 ) : ?>
        <div id="location-list-container">
            <?php $this->include_file( 'shortcode/_full_location_list' ) ?>
        </div>

        <?php if ( ! $this['list-only'] ) : ?>
            <div id="panel-container" class="desktop-only">
                <?php $this->include_file( 'shortcode/_full_location_detail' ) ?>

                <div id="hoo-map">
                    <div id="map-canvas"></div>
                </div>
            </div>
        <?php endif ?>
    <?php else : ?>
        <p>No complete locations to display.</p>
    <?php endif ?>
</div>

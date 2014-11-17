<div id="hoo-main">
    <?php if ( count( $this['locations'] ) > 0 ) : ?>
    <div id="location-list-container">
        <?php $this->include_file( 'shortcode/location_list' ) ?>
    </div>

    <div id="panel-container">
        <?php $this->include_file( 'shortcode/location_detail' ) ?>

        <div id="hoo-map">
            <div id="map-canvas"></div>
        </div>
    </div>
    <?php else : ?>
    <p>No complete locations to display.</p>
    <?php endif ?>
</div>

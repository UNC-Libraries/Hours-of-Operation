<div id="hoo-main">
    <?php if ( isset( $this['header'] ) ) : ?>
        <h1 id="header">
            <?php echo $this['header'] ?>
    </h2>
    <?php endif ?>

    <?php if ( isset( $this['tagline'] ) ) : ?>
        <div id="tagline">
            <p><?php echo $this['tagline'] ?></p>
        </div>
    <?php endif ?>

    <?php if ( count( $this['locations'] ) > 0 ) : ?>
        <div class="table-wrapper">
            <div id="location-list-container">
                <?php $this->include_file( 'shortcode/location_list' ) ?>
            </div>

            <div id="panel-container">
                <?php $this->include_file( 'shortcode/location_detail' ) ?>

                <div id="hoo-map">
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <p>No complete locations to display.</p>
    <?php endif ?>
</div>

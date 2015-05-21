<div id="hoo-full-list-only" class="list-only">
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
    <?php else : ?>
        <p>No complete locations to display.</p>
    <?php endif ?>
</div>

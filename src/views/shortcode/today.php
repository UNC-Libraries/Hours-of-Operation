<span class="hoo-today">
    <?php if ( is_null( $this['current_hours'] ) ) : ?>
        <span class="na">N/A</span>
    <?php elseif ( is_object( $this['current_hours'] ) ) : ?>
        <span class="open">Open</span>
        <span class="until">Until <?php echo \Hoo\Utils::format_time( $this['current_hours'] )?> </span>
    <?php elseif ( is_string( $this['current_hours'] ) ) : ?>
        <span class="open">Open</span>
        <span class="all-day"><?php echo $this['current_hours'] ?></span>
    <?php else : ?>
        <span class="closed">Closed</span>
    <?php endif ?>
</span>

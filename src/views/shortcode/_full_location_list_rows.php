<?php foreach( $this['locations'] as $location_data ) : ?>
    <?php $current_hours = $location_data['location']->is_open(); ?>
    <tr class="location-row"
        data-panel="panel-<?php echo $location_data['location']->id ?>"
        data-lat="<?php echo $location_data['location']->address->lat ?>"
        data-lon="<?php echo $location_data['location']->address->lon ?>"
        data-id="<?php echo $location_data['location']->id ?>">

        <td class="location-name<?php if ( $location_data['location']->parent ) echo ' child' ?>">
            <a href="#<?php echo $location_data['location']->alternate_name ?>">
                <?php echo $location_data['location']->name ?>
            </a>
        </td>
        <td class="location-status">
            <?php if ( is_null( $current_hours ) ) : ?>
                <span class="na">N/A</span>
            <?php elseif ( is_object( $current_hours ) ) : ?>
                <span class="open">Open</span>
                <span class="until">Until <?php echo \Hoo\Utils::format_time( $current_hours )?> </span>
            <?php elseif ( is_string( $current_hours ) ) : ?>
                <span class="open">Open</span>
                <span class="all-day"><?php echo $current_hours ?></span>
            <?php else : ?>
                <span class="closed">Closed</span>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>

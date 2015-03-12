<?php foreach( $this['locations'] as $location ) : ?>
    <?php $current_hours = $location->is_open(); ?>
    <tr class="location-row" data-panel="panel-<?php echo $location->id ?>" data-lat="<?php echo $location->address->lat ?>" data-lon="<?php echo $location->address->lon ?>" data-id="<?php echo $location->id ?>">
        <td class="location-name">
            <a href="#location">
                <?php echo $location->name ?>
            </a>
        </td>
        <td class="location-status">
            <?php if ( is_null( $current_hours ) ) : ?>
                <span class="na">N/A</span>
            <?php elseif ( $current_hours ) : ?>
                <span class="open">Open</span>
                <span class="until">Until <?php echo \Hoo\Utils::format_time( $current_hours )?> </span>
            <?php else : ?>
                <span class="closed">Closed</span>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>

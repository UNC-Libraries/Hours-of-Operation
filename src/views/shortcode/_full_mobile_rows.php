<?php foreach( $this['locations'] as $location_data ) : ?>
    <?php $current_hours = $location_data['location']->is_open(); ?>
    <tr>
        <th>
            <a href="<?php echo $location_data['location']->url ?>">
                <?php echo $location_data['location']->name ?>
            </a>
        </th>
        <th class="location-status">
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
        </th>
    </tr>
    <?php foreach( $location_data['hours'] as $weekday => $cur_hours ) : ?>
        <tr>
            <td><?php echo $weekday ?></td>
            <td><?php echo $cur_hours ?></td>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>

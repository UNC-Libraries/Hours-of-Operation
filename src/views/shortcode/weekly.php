<div id="hoo-weekly">
    <table class="weekly-hours">
        <?php if ( isset( $this['header'] ) ) : ?>
            <caption><?php echo $this['header'] ?></caption>
        <?php endif ?>

        <?php foreach( $this['locations'] as $location_data ) : ?>
            <tr>
                <th colspan="2">
                    <a href="<?php echo $location_data['location']->url ?>">
                        <?php echo $location_data['location']->name ?>
                    </a>
                </th>
            </tr>
            <?php foreach( $location_data['hours'] as $weekday => $cur_hours ) : ?>
                <tr>
                    <td><?php echo $weekday ?></td>
                    <td><?php echo $cur_hours ?></td>
                </tr>
            <?php endforeach ?>
        <?php endforeach ?>
    </table>
</div>

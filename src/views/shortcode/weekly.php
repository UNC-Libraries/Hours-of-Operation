<div id="hoo-weekly">
    <table class="weekly-hours">
        <?php if ( isset( $this['header'] ) ) : ?>
            <caption><?php echo $this['header'] ?></caption>
        <?php endif ?>

        <?php foreach( $this['hours'] as $weekday => $cur_hours ) : ?>
            <tr>
                <td><?php echo $weekday ?></td>
                <td><?php echo $cur_hours ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>

<table id="locations-list">
    <thead>
        <tr class="list-header">
            <th class="location-name">Location Name</th>
            <th class="location-status"><?php echo $this['now']->format( 'h:i a') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $this->include_file( 'shortcode/location_list_rows' ) ?>
    </tbody>
</table>

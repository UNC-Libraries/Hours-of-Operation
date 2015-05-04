<table id="locations-list">
    <thead>
        <tr class="list-header">
            <th class="desktop-only location-name">Location Name</th>
            <th class="mobile-only location-name"></th>
            <th class="location-status"><?php echo $this['now']->format( 'h:i a') ?></th>
        </tr>
    </thead>
    <tbody class="desktop-only">
        <?php $this->include_file( 'shortcode/location_list_rows' ) ?>
    </tbody>
    <tbody class="mobile-only">
        <?php $this->include_file( 'shortcode/_full_mobile_rows' ) ?>
    </tbody>
</table>
<div class="location-list-footer"></div>

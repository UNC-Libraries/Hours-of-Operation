<table id="widet-locations" cellspacing="0">
  <thead>
    <tr>
      <th class="location-name">Location Name</th>
      <th class="location-status">
        <span>At <?php echo $this['now']->format( 'h:i a' ) ?></span>
      </th>
    </tr>
  </thead>

  <?php $this->include_file( 'shortcode/location_list_rows' ) ?>

  <tr>
    <tfoot>
      <tr>
        <td></td>
        <td></td>
      </tr>
    </tfoot>
  </tr>
</table>

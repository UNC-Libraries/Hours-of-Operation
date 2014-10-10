<?php foreach( $this['locations'] as $location ) : ?>
  <tr class="location">
    <td class="location-name">
      <span>
        <?php echo $location->name ?>
      </span>
    </td>
    <td class="location-status">
      <span>
        <?php echo $location->is_open() ? 'Open' : 'Closed' ?>
      </span>
    </td>
  </tr>
<?php endforeach ?>

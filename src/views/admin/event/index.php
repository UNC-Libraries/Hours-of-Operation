<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<div class="events">
    <form method="GET">
        <input type="hidden" name="page" value="<?php echo $this['page'] ?>"/>
        <input type="hidden" name="location_id" value="<?php echo $this['location-id'] ?>"/>
        <?php $this['events-table']->search_box( 'Search', 'search_title' ) ?>
        <?php $this['events-table']->display(); ?>
    </form>
</div>

<?php $this->end_capture( 'body'  ); ?>

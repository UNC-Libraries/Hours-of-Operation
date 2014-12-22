<div class="hoo-head">
    <span>HoO</span>
    <a href="?page=<?php echo isset( $this['hoobert-link'] ) ? $this['hoobert-link'] : 'hoo' ?>">
        <img src="<?php echo HOO__PLUGIN_URL . 'assets/images/hoo-100.png' ?>"/>
    </a>
    <h2 id="wphead">
        <?php echo $this['title'] ?>
        <?php if ( isset( $this['add-new-page'] ) ) : ?>
            <a class="add-new-h2" href="?page=<?php echo $this['add-new-page'] ?>">Add New</a>
        <?php endif ?>
    </h2>
    <?php if ( isset( $this['breadcrumbs'] ) ) : ?>
    <ol class="breadcrumbs">
        <?php $last_crumb = end( $this['breadcrumbs'] ); reset( $this['breadcrumbs'] ); ?>
        <?php foreach( $this['breadcrumbs'] as $title => $url ) : ?>
            <?php  if ( $last_crumb == $url ) : ?>
                <li class="active"><?php echo $title ?></li>
            <?php else : ?>
                <li><a href="admin.php?page=<?php echo $url ?>"><?php echo $title ?></a></li>
            <?php endif ?>
        <?php endforeach ?>
    </ol>
    <?php endif ?>
    <?php $this->include_file( 'admin/partials/notifications' ) ?>
</div>

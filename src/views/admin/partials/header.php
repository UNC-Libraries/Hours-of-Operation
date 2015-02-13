<div class="hoo-head">
    <div class="hoo-logo">
        <span>HoO</span>
        <a href="?page=<?php echo isset( $this['hoobert-link'] ) ? $this['hoobert-link'] : 'hoo' ?>">
            <img src="<?php echo HOO__PLUGIN_URL . 'assets/images/hoo-100.png' ?>"/>
        </a>
    </div>
    <div class="hoo-title">
        <?php if ( isset( $this['breadcrumbs'] ) ) : ?>
        <ol class="breadcrumbs">
            <?php $last_crumb = count( $this['breadcrumbs'] ) - 1; $titles = array_keys( $this['breadcrumbs'] ); ?>
            <?php for( $index = 0; $index < $last_crumb ; $index++) : ?>
                <li><a href="admin.php?page=<?php echo $this['breadcrumbs'][ $titles[ $index ] ] ?>"><?php echo $titles[ $index ] ?></a> | </li>
            <?php endfor ?>
                <li class="active"><?php echo $titles[ $index ] ?></li>
        </ol>
        <?php endif ?>
        <h2 id="wphead">
            <?php echo $this['title'] ?>
            <?php if ( isset( $this['add-new-page'] ) ) : ?>
                <a class="add-new-h2" href="?page=<?php echo $this['add-new-page'] ?>">Add New</a>
            <?php endif ?>
        </h2>
        <?php $this->include_file( 'admin/partials/notifications' ) ?>
    </div>
</div>

<?php
$this->set_layout( 'admin' );
$this->capture();
?>

<form action="" name="shortcodes_form"  class="metabox-form">
    <div id="poststuff">
        <div id="post-body" class="hoo-shortcodes metabaox-holder columns-1">
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h3><span>Hoo Shortcode</span></h3>
                    <div class="inside">
                        <ul>
                            <li>
                                <div class="widget-choice">
                                    <select id="hoo_widget" name="widget" class="shortcode_attribute" data-valid-widgets="full weekly today"required>
                                        <option value="">Select a Widget</option>
                                        <?php foreach( $this['available_widgets'] as $widget ) : ?>
                                            <option value="<?php echo $widget ?>">
                                                <?php echo $widget ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <input type="text"
                                       name="header"
                                       id="hoo_header"
                                       class="form-textfield textfield wpt-form-textfield shortcode_attribute"
                                       value=""
                                       placeholder="Header"
                                       data-valid-widgets="full weekly"
                                       disabled />
                            </li>
                            <li>
                                <input type="text"
                                       name="tagline"
                                       id="hoo_tagline"
                                       value=""
                                       placeholder="Tagline"
                                       data-valid-widgets="full"
                                       class="shortcode_attribute"
                                       disabled />
                            </li>
                            <li>
                                <select id="hoo_location" name="location" data-valid-widgets="today weekly" class="shortcode_attribute" disabled>
                                    <option value="">Location</option>
                                    <?php foreach( $this['locations'] as $location ) : ?>
                                        <option value="<?php echo $location->id ?>">
                                            <?php echo $location->name ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </li>
                        </ul>
                        <pre class="shortcode"><code>[hoo]</code> </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $this->end_capture( 'body' ) ?>

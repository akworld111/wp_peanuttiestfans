<?php
/**
 * Vars:
 * $section_key
 * $section_title
 * $fields_html
 */
?>
<div class="meta-box-sortables col-lg-24 FV_config__section FV_config__section_<?php echo esc_attr($section_key); ?>">
    <div class="postbox">
        <h3 class="hndle">
            <span><?php echo $section_title; ?></span>
        </h3>
        <div class="inside">
            <div class="row">
                <?php echo $fields_html; ?>
            </div><!-- /.row -->
        </div><!-- /.postbox -->
    </div><!-- /.inside -->
</div><!-- /.meta-box-sortables -->
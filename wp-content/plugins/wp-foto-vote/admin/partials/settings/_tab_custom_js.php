<table class="form-table">

    <!-- ============ Custom CSS BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="2"><h3><?php _e('Custom JavaScript', 'fv') ?></h3></td>
        <td colspan="1">
            <?php _e( sprintf('Some useful JS examples can be found <a href="%s" target="_blank">here</a>.', 'https://github.com/max-kk/wp-foto-vote-dev-lib/tree/master/js_public'), 'fv') ?>
            Plugins like "Wordfence"/"iThemes Security" can block saving this JS code due to security reasons.
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('For all website pages', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Loads on all pages. If Cache is enabled - clear it after Save.', 'fv') ); ?>
        <td>
            <code>&lt;script&gt;</code>
            <br/>
            <textarea id="fv-custom-js" name="fv-custom-js" class="large-text" rows="5" cols="70"><?php echo get_option('fv-custom-js', ''); ?></textarea> <br/>
            <code>&lt;/script&gt;</code>
            <br/>
            <small><?php _e('You may add custom JS here. This JS code will be added to each page! So make sure that you can\'t add it to any of specific pages (Gallery or Single View).', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('For Contest Gallery page', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Loads only on Page with Contest Gallery. If Cache is enabled - clear it after Save.', 'fv') ); ?>
        <td>
            <code>&lt;script&gt;</code>
            <br/>
            <textarea id="fv-custom-js-gallery" name="fv-custom-js-gallery" class="large-text" rows="5" cols="70"><?php echo get_option('fv-custom-js-gallery', ''); ?></textarea>
            <code>&lt;/script&gt;</code>
            <br/>
            <small><?php _e( 'You may add custom JS for only pages with contest Galley here.', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('For Contest Single View page', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Loads only on contest Single View Page. If Cache is enabled - clear it after Save.', 'fv') ); ?>
        <td>
            <code>&lt;script&gt;</code>
            <br/>
            <textarea id="fv-custom-js-single" name="fv-custom-js-single" class="large-text" rows="5" cols="70"><?php echo get_option('fv-custom-js-single', ''); ?></textarea> <br/>
            <code>&lt;/script&gt;</code>
            <br/>
            <small><?php _e( 'You may add custom JS for only contest Single View pages here.', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('For Upload page', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Loads only on Page with contest Upload form. If Cache is enabled - clear it after Save.', 'fv') ); ?>
        <td>
            <code>&lt;script&gt;</code>
            <br/>
            <textarea id="fv-custom-js-upload" name="fv-custom-js-upload" class="large-text" rows="5" cols="70"><?php echo get_option('fv-custom-js-upload', ''); ?></textarea> <br/>
            <code>&lt;/script&gt;</code>
            <br/>
            <small><?php _e( 'You may add custom JS for only contest Upload pages here. Note - frequently upload form is displayed on gallery page.', 'fv') ?></small>
        </td>
    </tr>


</table>
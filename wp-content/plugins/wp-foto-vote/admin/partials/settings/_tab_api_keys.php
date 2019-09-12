<table class="form-table">

    <!-- ============ FB API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Facebook APP ID', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row" style="vertical-align: middle;"><?php _e('Facebook api key (for improve sharing & social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('If you want, that Facebook use true image<br/> on share, you need register as developer.', 'fv') ); ?>
        <td>
            <input name="fotov-fb-apikey"  class="all-options"  type="text" value="<?php echo get_option('fotov-fb-apikey', ''); ?>"/>
            <small><?php _e('How to get FB key - ', 'fv') ?> <a href="http://wp-vote.net/create-facebook-app-id/" target="_blank">http://wp-vote.net/create-facebook-app-id/</a></small>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row" style="vertical-align: middle;"><?php _e('Facebook api secret (for social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('To Enable Simple Social login you need add your api secret.', 'fv') ); ?>
        <td>
            <input name="fv-fb-secret"  class="all-options" type="password" value="<?php echo get_option('fv-fb-secret', ''); ?>"/>
        </td>
    </tr>

    
    <!-- ============ ReCaptcha API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('ReCaptcha api keys', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('ReCaptcha api key', 'fv') ?>:</th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('If you want use reCAPTCHA.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fv[recaptcha-key]" class="all-options" type="text" value="<?php echo ( isset($settings['recaptcha-key']) ) ? $settings['recaptcha-key'] : ''; ?>"/>
            <small><?php _e('How to get ReCaptcha key - ', 'fv') ?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank">https://www.google.com/recaptcha/admin#list</a>, https://developers.google.com/recaptcha/docs/start</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('ReCaptcha secret key', 'fv') ?>:</th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('Needs for verify response.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="password" name="fv[recaptcha-secret-key]" class="all-options" type="text" value="<?php echo ( isset($settings['recaptcha-secret-key']) ) ? $settings['recaptcha-secret-key'] : ''; ?>"/>
            <small><?php _e('How to get ReCaptcha secret key - ', 'fv') ?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank">https://www.google.com/recaptcha/admin#list</a>, https://developers.google.com/recaptcha/docs/start</small>
        </td>
    </tr>

    <!-- ============ Disqus API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Disqus settings (comments on single view)', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Your site shortname in Disqus', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code('For make Disqus comments working'); ?>
        <td>
            <input name="fv[ds-slug]" class="all-options" type="text" value="<?php echo fv_setting('ds-slug'); ?>"/><code>.disqus.com</code><br/>
            <small><?php _e('Where to find your site shortname:', 'fv') ?> <a href="https://monosnap.com/file/EAL0RKjzbAy11d4gL9cn5abQZ6tt23" target="_blank">https://monosnap.com/file/EAL0RKjzbAy11d4gL9cn5abQZ6tt23</a></small>
        </td>
    </tr>

    <!-- ============ Vkotakte API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Vkotakte settings', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Your site APP ID (for comments & social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code('For make Vkotakte comments working'); ?>
        <td>
            <input name="fv[vk-app-id]" class="all-options" type="text" value="<?php echo fv_setting('vk-app-id'); ?>"/> (numeric)<br/>
            <small><?php _e('Where to find your site APP ID:', 'fv') ?> <a href="https://vk.com/apps?act=manage" target="_blank">https://vk.com/apps?act=manage</a>, <a href="https://monosnap.com/file/VG5vg0ggeYShxXshZLAjWgRocb4lbx" target="_blank">Example</a></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Your site APP Secret (for social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code('For make Vkotakte social login working'); ?>
        <td>
            <input name="fv[vk-app-secret]" class="all-options" type="password" value="<?php echo fv_setting('vk-app-secret'); ?>"/>
        </td>
    </tr>

    <!-- ============ Google API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Google settings', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Your site APP ID (for social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code('For make Google social login working'); ?>
        <td>
            <input name="fv[gp-app-id]" class="all-options" type="text" value="<?php echo fv_setting('gp-app-id'); ?>"/> (numeric)<br/>
            <small><?php _e('Where to find your site APP ID:', 'fv') ?> <a href="https://developers.google.com/identity/sign-in/web/sign-in" target="_blank">https://developers.google.com/identity/sign-in/web/sign-in</a></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Your site APP Secret (for social login)', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code('For make Google social login working'); ?>
        <td>
            <input name="fv[gp-app-secret]" class="all-options" type="password" value="<?php echo fv_setting('gp-app-secret'); ?>"/>
        </td>
    </tr>

</table>
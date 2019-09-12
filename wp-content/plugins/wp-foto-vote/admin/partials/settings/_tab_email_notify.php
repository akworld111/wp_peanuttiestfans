<table class="form-table">

    <!-- ============ Admin BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Email notify', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3">
            <br/>
            From version 2.2.600 notifications must be configured in
            <a href="<?php echo admin_url('edit.php?post_type=notification'); ?>">Notifications menu</a>.
            <br/><br/>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify users from email', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('If not set, wordpress send from email<br/> like wordpress@domain.com', 'fv') ); ?>
        <td>
            <input name="fotov-users-notify-from-mail" value="<?php echo get_option('fotov-users-notify-from-mail', ''); ?>"/>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify users from name', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('If not set - will be like `Wordpress`', 'fv') ); ?>
        <td>
            <input name="fotov-users-notify-from-name" value="<?php echo get_option('fotov-users-notify-from-name', ''); ?>"/>
        </td>
    </tr>

    <!-- ============ Users BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Log emails', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Log all emails', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Log emails for debug', 'fv') ); ?>
        <td>
            <?php
            add_thickbox();
            $plugin_name = 'log-emails';
            $install_link = esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=600&height=550' ) );
            ?>
            To enable logging, please install this plugin - <a href="<?php echo $install_link ?>" class="thickbox" title="More info about Log Emails">Log Emails</a>
        </td>
    </tr>


</table>
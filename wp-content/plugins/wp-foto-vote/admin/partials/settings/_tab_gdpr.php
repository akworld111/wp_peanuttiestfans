<table class="form-table">

    <!-- ============ Leaders Vote ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('GDPR (sensitive data rotating and other tweaks)', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3">
            <br/>
            This options will not make your website 100% GDPR compatible without other changes but you can configure how long store users sensitive information.
            <br/><br/>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Require user to agree with your Privacy Policy before vote?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('When user click on \'Vote\' button - will be displayed modal with your text and checkbox.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv-vote-show-privacy-modal" <?php echo checked(get_option('fv-vote-show-privacy-modal', false), 'on'); ?>/> <?php _e('Enable', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('After what time delete voting log?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('When contest ends - votes log will be erased after indicated time.', 'fv') ); ?>
        <td>
            <select name="fv-erase-votes-log">
                <option value="0" <?php selected( get_option('fv-erase-log-in', 0), 0 ); ?>>Do not erase</option>
                <option value="7" <?php selected( get_option('fv-erase-log-in', 3), 0 ); ?>>7 days</option>
                <option value="30" <?php selected( get_option('fv-erase-log-in', 3), 0 ); ?>>30 days</option>
                <option value="60" <?php selected( get_option('fv-erase-log-in', 3), 0 ); ?>>60 days</option>
                <option value="90" <?php selected( get_option('fv-erase-log-in', 3), 0 ); ?>>90 days</option>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Remove competitors IP?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('After specified time Competitors IP will be removed.', 'fv') ); ?>
        <td>
            <select name="fv-erase-competitors-ip">
                <option value="0" <?php selected( get_option('fv-erase-competitor-ip', 0), 0 ); ?>>Do not erase</option>
                <option value="30" <?php selected( get_option('fv-erase-competitor-ip', 0), 0 ); ?>>After 7 days when contest ends</option>
                <option value="7" <?php selected( get_option('fv-erase-competitor-ip', 0), 0 ); ?>>After 30 days when contest ends</option>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Subscribe data rotation', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('How long rotate subscribers data', 'fv') ); ?>
        <td>
            <select name="fv-reminder-to-erase-subscribers">
                <option value="0" <?php selected( get_option('fv-reminder-to-erase-subscribers', 0), 0 ); ?>>Do not reminder</option>
                <option value="7" <?php selected( get_option('fv-reminder-to-erase-subscribers', 0), 7 ); ?>>Reminder me after 7 days when contest ends</option>
                <option value="30" <?php selected( get_option('fv-reminder-to-erase-subscribers', 0), 30 ); ?>>Reminder me after 30 days when contest ends</option>
            </select>
        </td>
    </tr>

</table>
<table class="form-table">

    <tr valign="top">
        <th scope="row"><?php _e('Restrict users to vote for own photos?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Prevent users to vote for their own uploaded photos.', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[restrict-vote-for-own]', fv_setting('restrict-vote-for-own', false) ); ?> <?php _e('Yes', 'fv') ?>
            <small><?php _e('(work with only logged-in users, so recommended require login to vote)', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Rating stars count (if contest voting type = "Rating")', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Starts count.', 'fv') ); ?>
        <td>
            <input type="number" name="fv[rate-stars-count]" value="<?php echo fv_setting('rate-stars-count', 5); ?>" min="5" max="10" step="1"/> <br/>
            <small><?php _e("Do not change if have active contests! Please enter valid value between 5 and 10.", "fv") ?></small>
        </td>
    </tr>
<!--
    <tr valign="top">
        <th scope="row"><?php _e('Voting icon', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Starts count.', 'fv') ); ?>
        <td>
            <input type="radio" name="fv[voting-icon]" value="like" <?php checked( fv_setting('voting-icon', 'like'), 'like' ); ?>/> <span class="typcn typcn-heart-full-outline"></span>
            <small>heart</small>
            <br/>
            <input type="radio" name="fv[voting-icon]" value="star" <?php checked( fv_setting('voting-icon', 'like'), 'star' ); ?>/> <span class="typcn typcn-star-full-outline"></span>
            <small>star</small>
        </td>
    </tr>
-->
    <tr valign="top">
        <th scope="row"><?php _e('Use fast voting option?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Will be used SHORTINIT wordpress feature, that allows decrease memory and sql usage into 30-200%.', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[fast-ajax]', fv_setting('fast-ajax', true) ); ?> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>Decreased memory and sql usage to about 50-300%. Disable if experiencing issues with voting.</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable anti fraud system?', 'fv') ?> (beta):</th>
        <?php echo fv_get_td_tooltip_code( __('After enabling this, you can see in Votes Log fraud score', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[anti-fraud]" <?php checked( fv_setting('anti-fraud', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will add at least one query to voting process. <a target="_blank" href="http://docs.wp-vote.net/#anti-fraud-system">Read more</a>)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Save reCAPTCHA result in session?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('If you enable this, than user need math reCAPTCHA once in 30 minutes. Can be not complete secure, but more accessible to users and less server load.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[recaptcha-session]" <?php checked( fv_setting('recaptcha-session', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will save reCAPTCHA result in a session for 30 mins.)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable voting debug?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Save all Unsuccessful Voting attempts to later inspect it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[debug-vote]" <?php checked( fv_setting('debug-vote', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will save all unsuccessful voting attempts with all data to the debug log.
                Please don't forget disable this, for do not pollute the log.)</small>
        </td>
    </tr>

    <!-- ============ Social counter ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Email subscribe Vote security', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable mail verification?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('User will receive email with verification link.', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[mail-verify]', fv_setting('mail-verify', true) ); ?> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(don't forget to edit the confirmation mail in Notification menu)</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Enable reCaptcha in Subscribe window?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('For enable additional security', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[recaptcha-for-subscribe]', fv_setting('recaptcha-for-subscribe') ); ?> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <!-- ============ Social Login ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Social Login Vote security (recommended count 4-5)', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Social networks, on using Social Login vote security?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how social network user can use for vote.', 'fv') ); ?>
        <td class="socials">
            <span><?php _e('Facebook', 'fv') ?>:</span>
            <?php fv_admin_echo_switch_toggle( 'fv[voting-slogin-fb]', fv_setting('voting-slogin-fb', true) ); ?>
            <?php _e('Show', 'fv') ?> <small>(api keys required)</small><br />

            <span><?php _e('Google+', 'fv') ?>:</span>
            <?php fv_admin_echo_switch_toggle( 'fv[voting-slogin-gp]', fv_setting('voting-slogin-gp') ); ?>
            <?php _e('Show', 'fv') ?> <small>(api keys required)</small><br />

            <span><?php _e('Vkontake', 'fv') ?>:</span>
            <?php fv_admin_echo_switch_toggle( 'fv[voting-slogin-vk]', fv_setting('voting-slogin-vk') ); ?>
            [Russian] <?php _e('Show', 'fv') ?> <small>(api keys required)</small><br />
        </td>
    </tr>

</table>
<table class="form-table">


    <!-- ============ popup social buttons after voting ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Social / Sharing', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('FB sharing dialog on click to *plugin FB share button*', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select preferred FB sharing dialog.', 'fv') ); ?>
        <td>
            <select name="fv[fb-dialog]" class="form-control">
                <option value="share" <?php selected( fv_setting('fb-dialog', 'feed'), 'share' ); ?>>[Share] Allow share to: user feed, groups, user pages, friend feed</option>
                <option value="feed" <?php selected( fv_setting('fb-dialog', 'feed'), 'feed' ); ?>>[Feed] Allow share to user feed (required FB app id)</option>
            </select>
            <br/><small>
                <a href="https://developers.facebook.com/docs/sharing/reference/share-dialog" target="_blank">Share</a> -
                <?php _e('Give to user more sharing options, but take sharing data from Open Graph, so can use incorrect info.', 'fv') ?>
            </small>
            <br/><small>
                <a href="https://developers.facebook.com/docs/sharing/reference/feed-dialog/v2.6" target="_blank">Feed</a> -
                <?php _e('Allow share just to user feed, but always share correct data that passed from plugin.', 'fv') ?>
            </small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Hide all social sharing buttons in popup after voting?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you don\'t want see a social buttons after voting - check it', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[voting-noshow-social]" <?php checked( fv_setting('voting-noshow-social', false) ); ?>/> <?php _e('Hide all', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Or hide specified networks:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how social buttons do not show after voting', 'fv') ); ?>
        <td class="socials">
            <div>
                <span><?php _e('Vkontake', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-vk]', fv_setting('voting-noshow-vk') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Facebook', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-fb]', fv_setting('voting-noshow-fb') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Twitter', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-tw]', fv_setting('voting-noshow-tw') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Odnoklasniki', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-ok]', fv_setting('voting-noshow-ok') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Google+', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-gp]', fv_setting('voting-noshow-gp') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Pinterest', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-pi]', fv_setting('voting-noshow-pi') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Whatsapp (only on mobile/tablet)', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-whatsapp]', fv_setting('voting-noshow-whatsapp') ); ?> Hide it
            </div>
        </td>
    </tr>

    <!-- ============ Social counter ============ -->
    <tr valign="top" class="no-padding">
        <td ><h3><?php _e('Public social counter', 'fv') ?></h3></td>
        <td colspan="2">
            <em>If enabled - near public share photo button will be added shares count [<a href="https://yadi.sk/i/3DACRA5ixKtkV" target="_blank">https://yadi.sk/i/3DACRA5ixKtkV</a>]</em>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable social counter?', 'fv') ?> (beta):</th>
        <?php echo fv_get_td_tooltip_code( __('After enabling this each photo will have counter with total sharing count.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[soc-counter]" <?php checked( fv_setting('soc-counter', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(Please disable not used networks, because getting data from all networks increases user PC loading)</small>
            <br/><small>Note: this option now good work with 'Ajax' or 'Infinity loading' pagination</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Count social shares from:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - from what social networks count shares', 'fv') ); ?>
        <td class="socials">
            <span><?php _e('Facebook', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-fb]" <?php checked( fv_setting('soc-counter-fb', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query for all photos)</small><br />

            <span><?php _e('Twitter', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-tw]" disabled/> <?php _e('Count', 'fv') ?>
                - <a target="_blank" href="https://twittercommunity.com/t/a-new-design-for-tweet-and-follow-buttons/52791">Not supported by Twitter now</a> <br />

            <span><?php _e('Google+', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-gp]" disabled/> <?php _e('Count', 'fv') ?>
            - <a target="_blank" href="https://warfareplugins.com/google-plus-share-counts/">Deprecated since August 2017</a><br />

            <span><?php _e('Pinterest', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-pi]" <?php checked( fv_setting('soc-counter-pi', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Vkontake', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-vk]" <?php checked( fv_setting('soc-counter-vk', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Odnoklasniki', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-ok]" <?php checked( fv_setting('soc-counter-ok', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Mail.ru', 'fv') ?>(мой мир):</span>
            <input type="checkbox" name="fv[soc-counter-mailru]" <?php checked( fv_setting('soc-counter-mailru', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query for all photos)</small><br />
        </td>
    </tr>

    <!-- ============ FB API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Facebook tweaks', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Load Facebook assets and init in header or footer?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('To fix issues with some social plugins you can load FB assets in page header.', 'fv') ); ?>
        <td>
            <select name="fv-fb-assets-position">
                <option value="footer" <?php selected('footer', get_option('fv-fb-assets-position', 'footer') ); ?>>in footer</option>
                <option value="head" <?php selected('head', get_option('fv-fb-assets-position', 'footer') ); ?>>in head</option>
            </select>
            &nbsp; <small><?php _e('By default sets in Footer, but if you have problems with FB sharing after configure FV App Id, change to Head.', 'fv') ?></small>
        </td>
    </tr>
</table>
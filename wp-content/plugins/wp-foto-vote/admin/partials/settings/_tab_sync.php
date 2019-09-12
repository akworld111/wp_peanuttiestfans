<table class="form-table">

    <!-- ============ Sync BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('WP Sync', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Automatically create page/post for contest?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('When you click "New contest" - new page/post will be automatically created', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv-sync[on]', fv_setting('on', false) ); ?> <?php _e('Yes', 'fv') ?>
            <br/>
            <small>This option allows visitors to find your contest photos via search engines, but  be sure that:
                <br/>1. You don't delete the contest after it ends
                <br/>2. Photos have descriptions, since search engines don't like empty pages.
            </small>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Single page contest block design', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select how your contest block will looks.', 'fv') ); ?>
        <td>
            <select name="fv[single-theme]" class="form-control">
                <?php foreach (fv_get_themes_arr() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( fv_setting('single-theme', 'pinterest'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Comments on Single photo page:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('What comments types show', 'fv') ); ?>
        <td class="socials">
            <div>
                <span><?php _e('Facebook', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[single-fb-comments]', fv_setting('single-fb-comments') ); ?> Enable <small>(required "FB APP ID")</small>
            </div>
            <div>
                <span><?php _e('Wordpress', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[single-wp-comments]', fv_setting('single-wp-comments') ); ?> Enable <small>(not work with Cloudinary Addon and other CDN)</small>
            </div>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo Heading template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-head-tpl]" class="large-text" value="<?php echo fv_setting('single-head-tpl', '{name}'); ?>"/> <br/>
            <small>You can use <code>{name}</code>, <code>{contest_name}</code>, <code>{description}</code>, <code>{meta_field_key}</code></small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Single photo description template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-desc-tpl]" class="large-text" value="<?php echo fv_setting('single-desc-tpl', '{description}'); ?>"/> <br/>
            <small>You can use <code>{name}</code>, <code>{description}</code>, <code>{full_description}</code>, <code>{meta_field_key}</code></small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Photo Url', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single image preview mode?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you don\'t want use lightbox or want show more details<br/> - use this option.', 'fv') ); ?>
        <td>
            <select name="fv[single-link-mode]" class="form-control">
                <option value="lightbox" <?php selected( fv_setting('single-link-mode', 'mixed'), 'lightbox' ); ?>>[lightbox] Always open in lightbox (not recommended if used pagination)</option>
                <option value="mixed" <?php selected( fv_setting('single-link-mode', 'mixed'), 'mixed' ); ?>>[mixed] Show photos in lightbox but share single photo url (recommended)</option>
                <option value="direct" <?php selected( fv_setting('single-link-mode', 'mixed'), 'direct' ); ?>>[direct] Force show photos in single page</option>
            </select><br/>
            <small><?php _e('Lightbox mode has two problems: 1) Pagination (image can be moved to another page, for example from Page 1 to Page 2), 
            and with sharing photo URL directly in social networks (For example - Facebook can\'t fetch correct image info, and 
            could show the same info for all photos.) You will have better results if sharing with the Photo Contest plugin sharing buttons.', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Page used for showing single photo?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If did\'t want use lightbox or want add more description<br/> about photo - use this option.', 'fv') ); ?>
        <td>
            <?php
            $current_single_page_id = fv_setting('single-page', '');
            ?>
            <input type="hidden" name="fv[single-page]" class="fv-posts-hidden-value" value="<?php echo $current_single_page_id; ?>"/>
            <select name="fv[single-page]" class="fv-posts-dropdown" disabled="disabled">
                <option value=""><?php echo esc_attr( __( 'None selected' ) ); ?></option>
                <?php
                if ( $current_single_page_id ) {
                    $current_single_page = get_post($current_single_page_id);
                    echo '<option value="' , $current_single_page->ID , '" selected="selected">' , $current_single_page->post_title , '</option>';
                }
                ?>
            </select>
            <a href="#0" class="fv-init-posts-dropdown" data-what-get="pages" data-post-id="<?php echo $current_single_page_id; ?>">edit</a>
            <br/><small><?php _e('Please make sure that selected page content contains shortcode <code>[fv]</code>'); ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo url prefix?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <?php echo home_url('/'); ?><input name="fv[single-permalink]" value="<?php echo fv_setting('single-permalink', 'contest-photo') ?>"/>/123/
            <br/><small>Please use just letters[a-z,A-Z] and dash[-].</small><br/>
            <small>Result will be: <code>www.site.com/contest-photo/123/</code> (123 used for example).</small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Photo Meta', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Allow search engines (Google, etc) index single photos?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Do not restrict search engines index single photo page.', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[single-allow-index]', fv_setting('single-allow-index', false) ); ?> <?php _e('Yes', 'fv') ?>
            <br/><small>This option allows visitors to find your contest photos via search engines, but  be sure that:
                <br/>1. You don't delete the contest after it ends
                <br/>2. Photos have descriptions, since search engines don't like empty pages.
            </small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo Meta Title format?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-title]" class="large-text" value="<?php echo fv_setting('single-title', '{name} - {contest_name}'); ?>"/> <br/>
            <small>Recommended size < 70 chars. You can use <code>{name}</code>,
                <code>{description}</code>, <code>{social_description}</code>,
                <code>{contest_name}</code>, <code>{contest_social_title}</code></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo Meta Description format?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-meta-description]" class="large-text" value="<?php echo fv_setting('single-meta-description', '{name} - {description}'); ?>"/> <br/>
            <small>Recommended size < 160 chars. You can use <code>{name}</code>, <code>{description}</code>,
                <code>{social_description}</code>, <code>{contest_name}</code>, <code>{contest_social_description}</code></small>
        </td>
    </tr>

</table>
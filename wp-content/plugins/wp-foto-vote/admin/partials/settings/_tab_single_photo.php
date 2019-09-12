<table class="form-table">

    <!-- ============ Design BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Design', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Single page contest block design', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select how your contest block will looks.', 'fv') ); ?>
        <td>
            <select name="fv[single-theme]" class="form-control">
                <?php foreach (FV_Skins::i()->getSingleViewList() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( fv_setting('single-theme', 'pinterest'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
            <a href="https://wp-vote.net/doc/skins-and-templates/" target="_blank">how to customize</a>
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
            <div>
                <span><?php _e('Vkontakte', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[single-vk-comments]', fv_setting('single-vk-comments') ); ?> Enable <small>(require VK app id)</small>
            </div>
            <div>
                <span><?php _e('Disqus', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[single-ds-comments]', fv_setting('single-ds-comments') ); ?> Enable <small>(require Disqus app id)</small>
            </div>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo Heading template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-head-tpl]" class="large-text" type="text" value="<?php echo esc_attr( fv_setting('single-head-tpl', '{name}') ); ?>"/> <br/>
            <small>
                <a href="https://wp-vote.net/doc/customizing-template-tags/" target="_blank"><span class="typcn typcn-social-youtube"></span> How to customize.</a>
                You can use <code>{name}</code>, <code>{contest_name}</code>, <code>{description}</code>,
                <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>phone</strong>}</code>).
            </small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Single photo description template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <textarea name="fv[single-desc-tpl]" class="large-text" rows="4"><?php echo esc_attr( fv_setting('single-desc-tpl', '{description}') ); ?></textarea> <br/>
            <small>
                <a href="https://wp-vote.net/doc/customizing-template-tags/" target="_blank"><span class="typcn typcn-social-youtube"></span> How to customize.</a>
                You can use <code>{ID}</code>, <code>{name}</code>, <code>{description}</code>, <code>{full_description}</code>, <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>website</strong>}</code>),
                <code>{categories_comma_separated}</code>, <code>{category_first}</code> and <a href="https://wp-vote.net/doc/customizing-template-tags/#conditional_tags" target="_blank">Conditional Tags</a>.

            </small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Allow users to view photos on moderation?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Useful if you want to redirect user into Single View page or use link in Email. Users are not allowed to vote.', 'fv') ); ?>
        <td>
            <select name="fv[single-preview-onmoderation]" class="form-control">
                <option value="restrict" <?php selected( fv_setting('single-preview-onmoderation', 'restrict'), 'restrict' ); ?>>Do not allow</option>
                <option value="allow" <?php selected( fv_setting('single-preview-onmoderation', 'allow'), 'allow' ); ?>>Allow</option>
                <option value="allow_logged_in" <?php selected( fv_setting('single-preview-onmoderation', 'allow_logged_in'), 'allow_logged_in' ); ?>>Only for logged in</option>
            </select><br/>
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
        <?php echo fv_get_td_tooltip_code( __('Path to Single Photo? Global for all contest images.', 'fv') ); ?>
        <td>
            <?php echo home_url('/'); ?><input name="fv[single-permalink]" type="text" value="<?php echo fv_setting('single-permalink', 'contest-photo') ?>"/>/123/
            <br/><small>Please use just letters[a-z,A-Z] and dash[-].</small><br/>
            <small>Result will be: <code>www.site.com/contest-photo/123/</code> (123 used for example).</small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Photo OG Meta tags', 'fv') ?></h3></td>
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
            <input name="fv[single-title]" class="large-text" type="text" value="<?php echo esc_attr( fv_setting('single-title', '{name} - {contest_name}') ); ?>"/> <br/>
            <small>Recommended size < 70 chars. You can use <code>{name}</code>,
                <code>{description}</code>, <code>{social_description}</code> (from competitor data),
                <code>{contest_name}</code>, <code>{contest_social_title}</code>,
                <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>website</strong>}
            </small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Single photo Meta Description format?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[single-meta-description]" class="large-text" type="text" value="<?php echo esc_attr( fv_setting('single-meta-description', '{name} - {description}') ); ?>"/> <br/>
            <small>Recommended size < 160 chars. You can use <code>{name}</code>, <code>{description}</code>,
                <code>{social_description}</code> (from competitor data), <code>{contest_name}</code>, <code>{contest_social_description}</code>,
                <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>website</strong>}
            </small>
        </td>
    </tr>

</table>
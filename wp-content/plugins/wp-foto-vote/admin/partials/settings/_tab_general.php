<table class="form-table">

    <!-- ============ Design BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Competitors list settings', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Select skin for contest gallery', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Select how your contest block will looks.', 'fv') ); ?>
        <td>
            <select name="fv[theme]" class="form-control">
                <?php foreach (FV_Skins::i()->getList() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( fv_setting('theme', 'pinterest'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
             <a href="https://wp-vote.net/doc/skins-and-templates/" target="_blank">how to customize</a>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Contest gallery block width (min. 180 px.)', 'fv') ?> </th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('Change to fit the width of the voting blocks your site', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="number" name="fotov-block-width" value="<?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH); ?>" min="0" max="1000"/> px.
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Thumbnail retrieving type:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Function, than used for retrieving thumbnail image.', 'fv') ); ?>
        <td>
            <select name="fv[thumb-retrieving]" class="form-control">
                <option value="plugin_default" <?php selected( fv_setting('thumb-retrieving', 'plugin_default'), 'plugin_default' ); ?>>Plugin default (1 sql query per image)</option>
                <option value="wordpress_default" <?php selected( fv_setting('thumb-retrieving', 'plugin_default'), 'wordpress_default' ); ?>>Wordpress default (2 sql queries per image)</option>
            </select>
            <br/><small>If you have some problems with "Plugin default" try "Wordpress" </small>
            <br/><small><strong>Note:</strong> If you installed and activated
                <a href="https://jetpack.me/support/photon/" target="_blank">Jetpack + Photon module</a>,
                than it will be used by default.</small>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('List thumbnail image size <small>(Changes on the fly: each change creates a new file for each contestant. Because of this, do not change very often.)</small>', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Thumbnails size in photos list, better not much more than `Contest block size`', 'fv') ); ?>
        <td> width: <input type="number" name="fotov-image-width" value="<?php echo get_option('fotov-image-width', 330); ?>" min="0" max="1200"/> px. /
            height: <input type="number" name="fotov-image-height" value="<?php echo get_option('fotov-image-height', 666); ?>" min="0" max="1200"/> px. /
            hard crop: <input type="checkbox" name="fotov-image-hardcrop" <?php echo checked( get_option('fotov-image-hardcrop', false), 'on' ); ?>/>
            <br/>
            <small><?php _e('Hard crop means that the thumbnail size will be equal entered dimension (if checked) or proportional (if unchecked) using the larger dimension.', 'fv'); ?><br/>
            <?php _e('If you want to fit just one proportion, set up one side to 0 or 777 (for ex.). (Example: 280 x 0 (or 280 * 777) will not crop the image by height, ideal for Pinterest theme.)', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('List photo Heading template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <input name="fv[list-head-tpl]" class="large-text" type="text" value="<?php echo esc_attr( fv_setting('list-head-tpl', '{name}') ); ?>"/> <br/>
            <small>
                <a href="https://wp-vote.net/doc/customizing-template-tags/" target="_blank"><span class="typcn typcn-social-youtube"></span> How to customize.</a>
                You can use <code>{name}</code>, <code>{description}</code>, <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>phone</strong>}</code>).
            </small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('List photo description template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('.', 'fv') ); ?>
        <td>
            <textarea name="fv[list-desc-tpl]" class="large-text" rows="4"><?php echo esc_attr( fv_setting('list-desc-tpl', '{description}') ); ?></textarea> <br/>
            <small>
                <a href="https://wp-vote.net/doc/customizing-template-tags/" target="_blank"><span class="typcn typcn-social-youtube"></span> How to customize.</a>
                You can use <code>{ID}</code>, <code>{name}</code>, <code>{description}</code>, <code>{full_description}</code>, <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>website</strong>}</code>),
                <code>{categories_comma_separated}</code>, <code>{category_first}</code> and <a href="https://wp-vote.net/doc/customizing-template-tags/#conditional_tags" target="_blank">Conditional Tags</a>.
            </small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Author', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Display competitor author?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Will de displayed like "Max Smith"', 'fv') ); ?>
        <td>
            <select name="fv-display-author" class="form-control">
                <option value="" <?php selected( get_option('fv-display-author', false), '' ); ?>>Do not display</option>
                <option value="link" <?php selected( get_option('fv-display-author', false), 'link' ); ?>>As link (to WP or Buddypress profile)</option>
                <option value="text" <?php selected( get_option('fv-display-author', false), 'text' ); ?>>As text</option>
            </select>

            <small><?php _e('Work just in Pinterest & Flickr skins for now.', 'fv'); ?></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Display competitor author avatar (WP or Buddypress, if installed)?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Gravatar or from Buddypress', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv-display-author-avatar" <?php checked( get_option('fv-display-author-avatar'), 'on' ); ?>/> <?php _e('Yes, please', 'fv') ?>
        </td>
    </tr>


    <tr valign="top" class="no-padding">
        <td colspan="3">
            <hr/>
            <input type="submit" class="button-primary" value="<?php _e('Save all Changes', 'fv') ?>" />
            <span class="spinner"></span>
            &nbsp;<small>Click save here if you don't want to scroll to the bottom</small><br>
            <hr/>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable lazy load images?', 'fv') ?></th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('If you enable this, will be loaded first 3 images, <br/>other on page scrolling', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fv[lazy-load]" <?php echo ( fv_setting('lazy-load') ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(Does not work in Fashion Theme)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Hide votes count?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you don`t want show to users votes count, check it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[hide-votes]" <?php echo ( fv_setting('hide-votes') ) ? 'checked' : ''; ?>/> <?php _e('Hide', 'fv') ?>
            <small>(Don`t forget also remove votes count from the option "Lightbox title format")</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable cache support?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you using cache plugins, after refresh page votes will not changes.<br/> For fix this plugin will AJAX update votes on page after it loaded.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[cache-support]" <?php echo ( fv_setting('cache-support') ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp; <small>(Does not work, if in wp-config.php not defined "WP_CACHE")</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable Cloudflare Rocket Loader support?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you using Cloudflare Rocket Loader some of plugin script can work incorrect. Enabled this for fix issues.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[rocketscript-support]" <?php echo ( fv_setting('rocketscript-support') ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp; <small>(about <a href="https://support.cloudflare.com/hc/en-us/articles/200168056-What-does-Rocket-Loader-do-" target="_blank">Rocket Loader</a>)</small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Pagination', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Break photos into pages', 'fv') ); ?>
        <td>
            <input type="number" name="fv[pagination]" value="<?php echo fv_setting('pagination', 0); ?>" min="0" max="200"/>
            <?php _e('Per page', 'fv') ?>
            &nbsp;<small>(if < 6, pagination will be disabled)</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Pagination type', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Pagination type - simply or ajax', 'fv') ); ?>
        <td>
            <select name="fv[pagination-type]">
                <option value="default" <?php selected( fv_setting('pagination-type', 'default'), 'default' ); ?>>default</option>
                <option value="ajax" <?php selected( fv_setting('pagination-type', 'default'), 'ajax' ); ?>>ajax</option>
                <option value="infinite" <?php selected( fv_setting('pagination-type', 'default'), 'infinite' ); ?>>Button Infinite scroll</option>
                <option value="infinite-auto" <?php selected( fv_setting('pagination-type', 'default'), 'infinite-auto' ); ?>>Auto Infinite scroll [beta]</option>
            </select>
            &nbsp;<small>Ajax works faster (without refreshing the page), but can cause problems with some WP themes.</small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3">
            <hr/>
            <input type="submit" class="button-primary" value="<?php _e('Save all Changes', 'fv') ?>" />
            <span class="spinner"></span>
            &nbsp;<small>Click save here if you don't want to scroll to the bottom</small><br>
            <hr/>
        </td>
    </tr>

    <!-- ============ Lightbox BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Lightbox settings', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Disable plugin lightbox?', 'fv') ?></th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('If you have some conflicts with you default lightbox(image preview plugin), check this', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-voting-no-lightbox" <?php echo ( get_option('fotov-voting-no-lightbox', false) ) ? 'checked' : ''; ?>/> <?php _e('Disable', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Lightbox title format?', 'fv') ?></th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('You can change text, that shows in lightbox', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fv[lightbox-title-format]" class="large-text" type="text" value="<?php echo ( isset($settings['lightbox-title-format']) ) ? esc_attr( $settings['lightbox-title-format'] ) : '{name} <br/>{votes}'; ?>"/> <br/>
            <small>
                You can use <code>{name}</code>, <code>{description}</code>, <code>{full_description}</code>,
                <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>phone</strong>}</code>).
            </small>
        </td>
    </tr>

    <!-- ============ Custom CSS BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Custom CSS', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Custom CSS styles', 'fv') ?>:</th>
        <td class="fv-tooltip">
            <div class="box" title="<?php _e('If you want to add some extra styles,<br/> use this textarea. <br/>If plugin will be disabled - this css will be not printed.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <textarea id="fotov-custom-css" name="fotov-custom-css" class="large-text" rows="5" cols="70"><?php echo get_option('fotov-custom-css', ''); ?></textarea> <br/>
            <small><?php _e('You could add custom styles here. Press"Ctrl + Space" to show autocomplete. This CSS code will be added to each page, so if you have a big code, it\'s better to place the code into (child) theme styles.css', 'fv') ?></small>
        </td>
    </tr>


</table>
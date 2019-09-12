<table class="form-table">

    <!-- ============ Photos BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Photos', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('On deleting contest / single contest photo Delete image from Hosting & Media library?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you not need photos after contest, check this. Also applies to s3 / Cloudinary.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv-image-delete-from-hosting" <?php echo checked(get_option('fv-image-delete-from-hosting', false), 'on'); ?>/> <?php _e('Enable', 'fv') ?>
        </td>
    </tr>

    <!-- ============ Photos BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Uninstall', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Delete all plugin & addons data on plugin deactivation?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you want to fully remove plugin from your website - check this option before deleting it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv-full-uninstall" <?php echo checked(get_option('fv-full-uninstall', false), 'on'); ?>/> <?php _e('Yes', 'fv') ?>
            <br/><small>It's recommended to remove all contests manually before, to avoid out of memory or timeout errors due too many actions required. Contest images can be not removed if option above is not activated.</small>
        </td>
    </tr>

    <!-- ============ EXPORT ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Export to CSV', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Export delimiter', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('String delimiter in CSV file', 'fv') ); ?>

        <td>
            <select name="fv-export-delimiter">
                <option value=";" <?php selected(';', get_option('fv-export-delimiter', ';') ); ?>>; (recommended for Excel)</option>
                <option value="\t" <?php selected('\t', get_option('fv-export-delimiter', ';') ); ?>>tab</option>
                <option value="," <?php selected(',', get_option('fv-export-delimiter', ';') ); ?>>,</option>
                <option value="," <?php selected(':', get_option('fv-export-delimiter', ';') ); ?>>:</option>
            </select>
            &nbsp; <small><?php _e('What is this - ', 'fv') ?> <a href="http://en.wikipedia.org/wiki/Delimiter-separated_values" target="_blank">Delimiter-separated values - WIKI</a></small>
        </td>
    </tr>


    <!-- ============ CAPABILITY  ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Capability', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Needed capability to fully manage contests', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Capability, required to fully manage contests<br/> (photos, translations, forms, settings).', 'fv') ); ?>

        <td>
            <select name="fv-needed-capability">
                <option value="manage_options" <?php selected('manage_options', get_option('fv-needed-capability', 'manage_options') ); ?>>manage_options (administrator+)</option>
                <option value="edit_pages" <?php selected('edit_pages', get_option('fv-needed-capability', 'manage_options') ); ?>>edit_pages (administrator+, editor+) = default</option>
                <option value="edit_posts" <?php selected('edit_posts', get_option('fv-needed-capability', 'manage_options') ); ?>>edit_posts (administrator+, editor+, author+)</option>
                <option value="install_plugins" <?php selected('install_plugins', get_option('fv-needed-capability', 'manage_options') ); ?>>install_plugins (administrator+, editor+, contributor+)</option>
                <option value="moderate_comments" <?php selected('moderate_comments', get_option('fv-needed-capability', 'manage_options') ); ?>>moderate_comments (administrator+, editor+)</option>
            </select>
            <br/><small><?php _e('More about roles and capabilities - ', 'fv') ?> <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"><?php _e('Capability vs. Role Table', 'fv') ?></a></small>
        </td>
    </tr>


    <tr valign="top" class="important">
        <th scope="row"><?php _e('Needed capability to moderate new entries', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('For example if you wish grant access for \'editor\' role to only Moderation page.', 'fv') ); ?>

        <td>
            <select name="fv[moderator-required-caps]">
                <option value="edit_pages" <?php selected('edit_pages', fv_setting('moderator-required-caps', 'manage_options') ); ?>>edit_pages (administrator+, editor+) = default</option>
                <option value="manage_options" <?php selected('manage_options', fv_setting('moderator-required-caps', 'manage_options') ); ?>>manage_options (administrator+)</option>
                <option value="edit_posts" <?php selected('edit_posts', fv_setting('moderator-required-caps', 'manage_options') ); ?>>edit_posts (administrator+, editor+, author+)</option>
                <option value="install_plugins" <?php selected('install_plugins', fv_setting('moderator-required-caps', 'manage_options') ); ?>>install_plugins (administrator+, editor+, contributor+)</option>
                <option value="moderate_comments" <?php selected('moderate_comments', fv_setting('moderator-required-caps', 'manage_options') ); ?>>moderate_comments (administrator+, editor+)</option>
            </select>
            <br/><small><?php _e('More about roles and capabilities - ', 'fv') ?> <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"><?php _e('Capability vs. Role Table', 'fv') ?></a></small>
        </td>
    </tr>

    <!-- ============ Not compiled BLOCK ============ -->
    <tr valign="top">
        <th scope="row"><?php _e('Remove all "new-line" codes in html?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you contest layout looks broken - try this option. Or can be used for little decrease page size.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[remove-newline]" <?php echo checked(fv_setting('remove-newline', false)); ?>/> <?php _e('Yes', 'fv') ?>
            <br/><small>Example, when you need enable it: <a target="_blank" href="https://yadi.sk/i/6Ycoqwugk7nDy">https://yadi.sk/i/6Ycoqwugk7nDy</a></small>
        </td>
    </tr>

    <!-- ============ Not compiled BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Debug Scripts & styles', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Load not minimized scripts and styles?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Can be used Developers for debug, not recommended for most users.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[not-compiled-assets]" <?php echo checked(fv_setting('not-compiled-assets', false)); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>



    <!-- ============ Addons support ============ -->
    <tr valign="top" class="no-padding" style="display: none;">
        <td colspan="3"><h3><?php _e('Addons support', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important" style="display: none;">
        <th scope="row"><?php _e('Disable addons support?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Can be used for little decrease server loading. But all addons will stop work (like circled countdown).', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[disable-addons-support]" <?php echo checked(fv_setting('disable-addons-support', false)); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top" style="display: none;">
        <th scope="row"><?php _e('Enable SQL debug?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Will save all plugin SQL queries.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[debug-sql]" <?php checked( fv_setting('debug-sql', false) ); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

</table>
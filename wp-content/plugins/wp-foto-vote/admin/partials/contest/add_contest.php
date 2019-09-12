<div class="wrap" id="fv-help-wrap">
    <?php do_action('fv_admin_notices'); ?>
    <h1>
        <?php _e('Add new contest', 'fv') ?>
    </h1>
    <p>
        If you select "Create new post/page for this contest?" option then new page/post will be created
        with specified Title & Content [fv id="CREATED_CONTEST_ID"]
    </p>
    <form action="<?php echo admin_url( 'admin.php?page=fv&action=create'); ?>" method="POST">

        <table class="form-table">

            <tr valign="top">
                <th scope="row"><?php _e('Contest title', 'fv') ?>:</th>
                <td>
                    <input type="text" name="name" value="New contest"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Display upload form before contest?', 'fv'); ?></th>
                <td>
                    <select id="upload_enable" name="upload_enable" class="form-control">
                        <option value="1" > <?php _e('Yes', 'fv') ?> <?php _e('["Upload dates" must be active]', 'fv') ?></option>
                        <option value="0" > <?php _e('No', 'fv') ?> <?php _e('[You can still use "Upload shortcode"]', 'fv') ?></option>
                    </select>
                    <small><?php _e('You still can use "Upload shortcode" to display form on another page.', 'fv'); ?></small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Display countdown?', 'fv') ?></th>
                <td>
                    <select id="timer" name="timer" class="form-control">
                        <option value="no" > <?php _e('Do not display', 'fv') ?></option>
                        <?php foreach ($countdowns as $key => $countdown_title): ?>
                            <option value="<?php echo $key ?>"><?php echo $countdown_title ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <td colspan="2"><hr></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Create new post/page for this contest?', 'fv') ?></th>
                <td>
                    <?php echo fv_admin_get_switch_toggle( 'create_post', true ); ?> <?php _e('Yes', 'fv') ?>
                </td>
            </tr>

            <tr valign="top" class="post-settings">
                <th scope="row"><?php _e('Create page or post?', 'fv') ?></th>
                <td>
                    <select name="post_type" class="fv-post-type">
                        <option value="page">Page</option>
                        <option value="post">Post</option>
                        <?php foreach ($post_types as $post_type): ?>
                            <option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr valign="top" class="post-settings">
                <th scope="row"><?php _e('Page/post status', 'fv') ?></th>
                <td>
                    <select name="post_status" id="post_status">
                        <option value="publish">Publish</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending Review</option>
                    </select>
                </td>
            </tr>

            <tr valign="top" class="post-settings post-category" style="display: none;">
                <th scope="row"><?php _e('Post category', 'fv') ?></th>
                <td>
                    <?php wp_dropdown_categories( array('hierarchical'=>1, 'hide_empty'=>0) ); ?>
                </td>
            </tr>

            <tr valign="top" class="post-settings">
                <th scope="row"><?php _e('Page/post title', 'fv') ?>:</th>
                <td>
                    <input type="text" name="post_title" value="New contest"/>
                </td>
            </tr>

            <tr valign="top" class="post-settings">
                <th scope="row"><?php _e('Featured image', 'fv') ?>:</th>
                <td>
                    <input type="hidden" id="fv_cover_image" name="cover_image">
                    <input type="hidden" id="fv_cover_image_url">
                    <button type="button" class="button button-primary button-large" value="Upload Image"
                            onclick="fv_wp_media_upload('#fv_cover_image_url', '#fv_cover_image', '#cover-image-thumb')">
                        Select featured image
                    </button>
                    <img src="<?php echo FV::$ASSETS_URL ?>img/no-photo.png" alt="" id="cover-image-thumb" height="31">
                </td>
            </tr>

        </table>
        
        <button class="button button-primary button-large"> >> Create contest << </button>
    </form>

</div>
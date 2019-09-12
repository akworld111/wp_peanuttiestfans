<div class="wrap" id="fv-license-wrap">
    <?php do_action('fv_admin_notices'); ?>
    <?php
    if ( isset($_GET['updated']) ) :
        echo '<div id="setting-error-settings_updated" class="updated settings-error">
                <p><strong>' . __('License details have been updated.', 'fv') . '</strong></p>
             </div>';
    endif;
    ?>
    <h1><span class="dashicons dashicons-post-status"></span> <?php _e('License', 'fv') ?></h1>

    <form action="<?php echo admin_url('admin.php?page=fv-license'); ?>" method="POST">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Key for automatically updating plugin', 'fv') ?>:</th>
                <td class="fv-tooltip">
                    <div class="box" title="<?php _e('Key, taken you at plugin purchase', 'fv') ?>">
                        <span class="dashicons dashicons-info"></span>
                    </div>
                </td>
                <td>
                    <input name="update-key" class="all-options" value="<?php echo $key; ?>"/>
                    <button type="button " class="button button-primary button-large">Save key or Refresh it details</button>
                </td>
            </tr>

        </table>
        <input type="hidden" name="action" value="fv-update-key"/>
        <?php wp_nonce_field('fv-update-key-nonce'); ?>
    </form>

    <h2><?php _e('License details', 'fv') ?></h2>
    <strong><?php _e('Key status: ', 'fv') ?></strong>
    <?php echo isset($key_details['status']) ? fv_get_update_key_status_as_text($key_details['status']) : 'not set'; ?>

    <?php if ( isset($key_details['status']) && $key_details['status'] == 2 ):
        echo '<br/>', '<strong>' ,__('Thanks for keep license active!', 'fv'), '</strong>';
    elseif ( isset($key_details['status']) && $key_details['status'] == 4 ):
        ?>
        <a href="http://wp-vote.net/extending-a-license/?license_key=<?php echo $key; ?>" target="_blank">Extend license >></a>
        <?php
    endif;
    ?>
    <br/><br/><strong><?php _e('Key expiration: ', 'fv') ?></strong>
    <?php echo isset($key_details['expiration']) ? $key_details['expiration'] : 'not set'; ?>
    <br/><small>You can keep using WP Foto Vote even after the license expires (however you won’t benefit from support anymore after license expiration & getting updates).</small>

    <?php echo isset($key_details['last_update']) ? '<br/><br/><strong>Last details update:</strong> ' . $key_details['last_update'] : ''; ?>

    <h2><?php _e('Addons', 'fv') ?></h2>
    <?php

    //var_dump($response_arr);
    $title = '';
    if (!empty($response_arr) && is_array($response_arr)) : foreach ($response_arr['products'] as $product) :
        $title = !empty( $product->info->title ) ? $product->info->title : '-';
        ?>
        <div class="fv-extension">
            <h3 class="fv-extension-title"><?php echo $title; ?></h3>
            <a href="<?php echo !empty( $product->info->link ) ? $product->info->link : '#'; ?>&utm_source=license-page&amp;utm_medium=plugin&amp;utm_campaign=licensePage&amp;utm_content=<?php echo urlencode($title); ?>" title="<?php echo $title; ?>">
                <img width="290" class="wp-post-image" src="<?php echo !empty( $product->info->thumbnail ) ? $product->info->thumbnail : FV::$ASSETS_URL . 'img/no-photo.png'; ?>" alt="<?php echo $title; ?>">
            </a>
            <p><?php echo !empty( $product->info->excerpt ) ? $product->info->excerpt : ''; ?></p>
            <a href=<?php echo !empty( $product->info->link ) ? $product->info->link : '#'; ?>&utm_source=license-page&amp;utm_medium=plugin&amp;utm_campaign=licensePage&amp;utm_content=<?php echo urlencode($title); ?>" title="<?php echo $title; ?>" class="button-secondary">Get this Extension</a>
        </div>
    <?php endforeach; endif; ?>
</div>
<style>
    #fv-license-wrap .fv-extension {
        background: #fff;
        border: 1px solid #ccc;
        float: left;
        padding: 14px 0;
        position: relative;
        margin: 0 15px 15px 0;
        width: 285px;
        height: 285px;
    }
    #fv-license-wrap .fv-extension h3 {
        font-size: 15px;
        margin: 0 0 10px;
        padding: 0 14px;
    }
    #fv-license-wrap .fv-extension p {
        padding: 0 14px;
    }
    #fv-license-wrap .fv-extension .button-secondary {
        position: absolute;
        bottom: 14px;
        left: 14px;
    }
    #fv-license-wrap .fv-extension .wp-post-image {
        width: 100%;
        height: auto;
    }    
</style>
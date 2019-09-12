<div class="wrap" id="fv-help-wrap">
    <?php do_action('fv_admin_notices'); ?>
    <h1><span class="dashicons dashicons-editor-help"></span> <?php _e('Have a question about setting up or a trouble with using WP Foto Vote? ', 'fv') ?></h1>
    <p>
        At this page you can send support request. Reply will come to email, specified in form.
    </p>
    <!--<form action="<?php echo admin_url('admin.php?page=fv-help'); ?>" method="POST" id="fv-get-help-form">-->
    <form action="https://wp-vote.net/ticket-form/" method="POST" id="fv-get-help-form">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Get reply to Email', 'fv') ?>:</th>
                <td>
                    <input type="email" name="stg_ticket_email" class="large-text" required/>
                    <small>"gmail" or "hotmail" is a good choise, because sometime our messages is not delivered to "domain" related emails like "me@site.com"</small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Subject', 'fv') ?>:</th>
                <td>
                    <input name="stg_ticket_subject" class="large-text" required/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Messsage', 'fv') ?>:</th>
                 <td>
                     <textarea name="stg_ticket_message" class="large-text" style="min-height: 120px;" placeholder="Hi Max,<?php echo PHP_EOL; ?>I had a troubles with voting on this page http://xxxx.com<?php echo PHP_EOL; ?>Tom"></textarea>
                     <br/>For attach screenshot please use (just remove sensitive information before post): <a href="https://snag.gy" target="_blank">https://snag.gy/</a>, https://prnt.sc/, Dropbox, Yandex Disk, etc
                     <br/>If you have Voting / Upload troubles - please also paste "Debug log" content from <a href="<?php echo admin_url('admin.php?page=fv-debug'); ?>" target="_blank">Debug page</a>
                     <br/>Also you can attach link to contest page for speed up examination of the issues.
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Extra data, that will be attached to request', 'fv') ?>:</th>
                <td>
                    <?php
                    $single_page_id = fv_setting('single-page', '');
                    global $wpdb;
                    ?>
                    <textarea name="stg_ticket_message_details" class="large-text" readonly style="min-height: 260px;">
Home URL: <?php echo home_url(), PHP_EOL; ?>
Site URL: <?php echo site_url(), PHP_EOL; ?>

FV Version: <?php echo esc_html( FV::VERSION ), PHP_EOL; ?>
FV License key: <?php echo get_option('fv-update-key', ''), PHP_EOL; ?>
FV Database Version: <?php echo esc_html( FV_DB_VERSION ), PHP_EOL; ?>
FV Fast Voting (setting): <?php echo (fv_setting('fast-ajax'))? 'yes' : 'no';echo PHP_EOL; ?>
FV Log File Writable: <?php echo @fopen( FV_LOG_FILE, 'a' ) ? '&#10004;' : '&#10005;'; echo PHP_EOL;  ?>
FV "cache-support": <?php echo fv_setting('cache-support') ? '&#10004;' : '&#10005;'; echo PHP_EOL;  ?>
FV Single Page: <?php echo $single_page_id, ' # ', get_permalink($single_page_id), PHP_EOL; ?>

WP Version: <?php bloginfo('version');echo PHP_EOL; ?>
WP Multisite: <?php if ( is_multisite() ) echo '&#10004;'; else echo '&ndash;';echo PHP_EOL; ?>
WP Memory Limit: <?php echo WP_MEMORY_LIMIT, PHP_EOL; ?>
WP Debug Mode: <?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '&#10004;', PHP_EOL; else echo '&ndash;', PHP_EOL; ?>
WP_CACHE: <?php if ( defined('WP_CACHE') && WP_CACHE ) echo '&#10004;', PHP_EOL; else echo '&ndash;', PHP_EOL; ?>
Server Info: <?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] );echo PHP_EOL; ?>
PHP Version: <?php echo function_exists( 'phpversion' ) ? esc_html( phpversion() ) : "Couldn't determine PHP version because phpversion() doesn't exist.";echo PHP_EOL; ?>
MySQL Version: <?php echo $wpdb->db_version(), PHP_EOL; ?>

<?php if ( function_exists( 'ini_get' ) ) : ?>
PHP Memory Limit: <?php echo ini_get('memory_limit'), PHP_EOL; ?>
PHP Post Max Size: <?php echo ini_get('post_max_size'), PHP_EOL; ?>
PHP Time Limit: <?php echo ini_get('max_execution_time'), PHP_EOL; ?>
PHP Max Input Vars: <?php echo ini_get('max_input_vars'), PHP_EOL; ?>
<?php endif; ?>
Max Upload Size: <?php echo size_format( wp_max_upload_size() ), PHP_EOL; ?>

== Active plugins: ==
<?php
$active_plugins = (array) get_option( 'active_plugins', array() );

if ( is_multisite() ) {
    $network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
    $active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
}

$plugin_name = '';
$plugin_data = array();
$dirname = '';
foreach ( $active_plugins as $plugin ) {

    $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
    $dirname = dirname($plugin);

    if (!empty($plugin_data['Name'])) {
        // Link the plugin name to the plugin url if available.
        $plugin_name = esc_html($plugin_data['Name']);
    }
    echo $plugin_name, ' # ', esc_html($plugin_data['Version']), PHP_EOL;
}
?>
                    </textarea>

                    <br/>

                    <button type="submit" class="button button-primary button-large">Send</button>
                </td>
            </tr>

        </table>
        <input type="hidden" name="stg_saveTicket" value="1"/>
        <input type="hidden" name="stg_siteUrl" value="<?php echo home_url(); ?>"/>

    </form>
</div>
<style>
    #fv-help-wrap h1 .dashicons {
        line-height: 1;
        font-size: 24px;
        vertical-align: middle;
        width: 24px;
        height: 24px;
    }
</style>
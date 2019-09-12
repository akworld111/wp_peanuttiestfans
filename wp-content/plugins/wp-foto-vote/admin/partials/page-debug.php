<style>
    .CodeMirror {
        height: 590px !important;
    }

    .fv_status_table {
        margin: 10px 0;
    }
    table.fv_status_table td:first-child {
        width: 28%;
    }
    table.fv_status_table td {
        padding: 9px;
        font-size: 1.1em;
    }
    table.fv_status_table td.tooltip {
        width: 1em;
    }
    #debug-report textarea {
        font-family: monospace;
        width: 100%;
        margin: 0;
        height: 300px;
        padding: 10px 20px;
        -moz-border-radius: 0;
        -webkit-border-radius: 0;
        border-radius: 0;
        resize: none;
        font-size: 12px;
        line-height: 20px;
        outline: 0;
    }
</style>
<script>
    FvLib.addHook('doc_ready', function() {
        // load css editor for additional CSS styles editor
//        jQuery( '.formatted-log').each(function(key, element) {
//            CodeMirror.fromTextArea(
//                element,
//                {
//                    lineNumbers: true,
//                    mode: "text/x-textile",
//                    lineNumbers: true,
//                    readOnly: true
//                    //viewportMargin: 'Infinity'
//                }
//            );
//        });
//

        jQuery( 'a.debug-report' ).click( function() {

            var report = '';

            jQuery( '#status thead, #status tbody' ).each(function(){

                if ( jQuery( this ).is('thead') ) {

                    var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
                    report = report + "\n### " + jQuery.trim( label ) + " ###\n\n";

                } else {

                    jQuery('tr', jQuery( this ) ).each(function(){

                        var label       = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
                        var the_name    = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML
                        var the_value   = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
                        var value_array = the_value.split( ', ' );

                        if ( value_array.length > 1 ) {

                            // If value have a list of plugins ','
                            // Split to add new line
                            var output = '';
                            var temp_line ='';
                            jQuery.each( value_array, function( key, line ){
                                temp_line = temp_line + line + '\n';
                            });

                            the_value = temp_line;
                        }

                        report = report + '' + the_name + ': ' + the_value + "\n";
                    });

                }
            });

            try {
                jQuery( "#debug-report" ).slideDown();
                jQuery( "#debug-report textarea" ).val( report ).focus().select();
                jQuery( this ).fadeOut();
                return false;
            } catch( e ){
                console.log( e );
            }

            return false;
        });

    });

</script>
<div class="wrap">
    <!-- DEBUG LOG -->

    <form role="form" method="post" id="fv-log">
        <?php
        $file = file_get_contents(FV_LOG_FILE);
        ?>
        <br/>
        <?php _e('Debug Log:', 'fv') ?> / <small>Total log size: <?php echo round(filesize(FV_LOG_FILE) / 1024, 2), ' KB'; ?> [please clear Log bigger than 1-2 MB!]</small>
        <br/><br/>
        <textarea cols="100" rows="40" class="formatted-log" id="formatted-log"><?php echo $file; ?></textarea>

        <div id="clear-action">
            <input type="hidden" name="action" value="clear"/>
            <input name="save" type="submit" class="button button-primary button-large" id="clear"
                   value="<?php _e('Clear log', 'fv') ?>">
        </div>

        <?php wp_nonce_field('fv_clear_log'); ?>
    </form>

    <?php do_action('fv/admin/debug_extra'); ?>

    <!-- DEBUG INFO -->

    <div class="bs-callout bs-callout-warning">
        <p>Please copy and paste this information when contacting support: </p>
        <p class="submit">
            <a href="#0" class="button-primary debug-report">Get System Report</a>
        </p>
        <div id="debug-report" style="display: none;">
            <textarea readonly="readonly"></textarea>
            <p class="submit">Use Ctrl + C for copy.</p>
        </div>
    </div>

    <table class="fv_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="WordPress Environment"><?php _e( 'WordPress Environment', 'fv' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Home URL"><?php _e( 'Home URL', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The URL of your site\'s homepage.', 'fv' ) ); ?>
            <td><?php echo home_url(); ?></td>
        </tr>
        <tr>
            <td data-export-label="Site URL"><?php _e( 'Site URL', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The root URL of your site.', 'fv' ) ); ?></td>
            <td><?php echo site_url(); ?></td>
        </tr>
        <tr>
            <td data-export-label="FV Version"><?php _e( 'FV Version', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The version of WP Foto Vote installed on your site.', 'fv' ) ); ?></td>
            <td><?php echo esc_html( FV::VERSION ); ?></td>
        </tr>
        <tr>
            <td data-export-label="FV Database Version"><?php _e( 'FV Database Version', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The version of WP Foto Vote database.', 'fv' ) ); ?></td>
            <td><?php echo esc_html( FV_DB_VERSION ); ?></td>
        </tr>
        <tr>
            <td data-export-label="FV Fast Voting"><?php _e( 'FV Fast Voting', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'Fast Voting can cause some problems, but allows increase voting speed. Can be disabled at *Photo contest => Settings => Voting*', 'fv' ) ); ?></td>
            <td><?php echo (fv_setting('fast-ajax'))? '<mark class="yes">yes</mark>' : 'no'; ?></td>
        </tr>
        <tr>
            <td data-export-label="Log File Writable"><?php _e( 'Log File Writable', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'Several WP Foto Vote extensions can write logs which makes debugging problems easier. The directory must be writable for this to happen.', 'fv' ) ); ?></td>
            <td><?php
                if ( @fopen( FV_LOG_FILE, 'a' ) ) {
                    echo '<mark class="yes">' . '&#10004; <code>' . FV_LOG_FILE . '</code></mark> ';
                } else {
                    printf( '<mark class="error">' . '&#10005; ' . __( 'To allow logging, make <code>%s</code> writable.', 'fv' ) . '</mark>', FV_LOG_FILE );
                }
                ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Version"><?php _e( 'WP Version', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The version of WordPress installed on your site.', 'fv' ) ); ?></td>
            <td><?php bloginfo('version'); ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Multisite"><?php _e( 'WP Multisite', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'fv' ) ); ?></td>
            <td><?php if ( is_multisite() ) echo '&#10004;'; else echo '&ndash;'; ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Memory Limit"><?php _e( 'WP Memory Limit', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'fv' ) ); ?></td>
            <td><?php
                echo WP_MEMORY_LIMIT;
                ?></td>
        </tr>
        <tr>
            <td data-export-label="WP Debug Mode"><?php _e( 'WP Debug Mode', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'fv' ) ); ?></td>
            <td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . '&#10004;' . '</mark>'; else echo '<mark class="no">' . '&ndash;' . '</mark>'; ?></td>
        </tr>
        <tr>
            <td data-export-label="Language"><?php _e( 'Language', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The current language used by WordPress. Default = English', 'fv' ) ); ?></td>
            <td><?php echo get_locale() ?></td>
        </tr>
        </tbody>
    </table>

    <table class="fv_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Server Environment"><?php _e( 'Server Environment', 'fv' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Server Info"><?php _e( 'Server Info', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'Information about the web server that is currently hosting your site.', 'fv' ) ); ?></td>
            <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
        </tr>
        <tr>
            <td data-export-label="PHP Version"><?php _e( 'PHP Version', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The version of PHP installed on your hosting server.', 'fv' ) ); ?></td>
            <td><?php
                // Check if phpversion function exists
                if ( function_exists( 'phpversion' ) ) {
                    $php_version = phpversion();

                    if ( version_compare( $php_version, '5.3', '<' ) ) {
                        echo '<mark class="error">' . sprintf( __( '%s - We recommend a minimum PHP version of 5.4. See: <a href="%s" target="_blank">How to update your PHP version</a>', 'fv' ), esc_html( $php_version ), 'http://docs.woothemes.com/document/how-to-update-your-php-version/' ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
                    }
                } else {
                    _e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'fv' );
                }
                ?></td>
        </tr>
        <?php if ( function_exists( 'ini_get' ) ) : ?>
            <tr>
                <td data-export-label="PHP Post Max Size"><?php _e( 'PHP Post Max Size', 'fv' ); ?>:</td>
                <?php echo fv_get_td_tooltip_code( esc_attr__( 'The largest filesize that can be contained in one post.', 'fv' ) ); ?></td>
                <td><?php echo ini_get('post_max_size'); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Memory Limit"><?php _e( 'PHP Memory Limit', 'fv' ); ?>:</td>
                <?php echo fv_get_td_tooltip_code( esc_attr__( 'Max size of hosting memory, that can be used for script.', 'fv' ) ); ?></td>
                <td><?php echo ini_get('memory_limit'); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Time Limit"><?php _e( 'PHP Time Limit', 'fv' ); ?>:</td>
                <?php echo fv_get_td_tooltip_code( esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'fv' ) ); ?></td>
                <td><?php echo ini_get('max_execution_time'); ?> s</td>
            </tr>
            <tr>
                <td data-export-label="PHP Max Input Vars"><?php _e( 'PHP Max Input Vars', 'fv' ); ?>:</td>
                <?php echo fv_get_td_tooltip_code( esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'fv' ) ); ?></td>
                <td><?php echo ini_get('max_input_vars'); ?></td>
            </tr>
            <tr>
                <td data-export-label="SUHOSIN Installed"><?php _e( 'SUHOSIN Installed', 'fv' ); ?>:</td>
                <?php echo fv_get_td_tooltip_code( esc_attr__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself. If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'fv' ) ); ?></td>
                <td><?php echo extension_loaded( 'suhosin' ) ? '&#10004;' : '&ndash;'; ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td data-export-label="MySQL Version"><?php _e( 'MySQL Version', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The version of MySQL installed on your hosting server.', 'fv' ) ); ?></td>
            <td>
                <?php
                /** @global wpdb $wpdb */
                global $wpdb;
                echo $wpdb->db_version();
                ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Max Upload Size"><?php _e( 'Max Upload Size', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The largest filesize that can be uploaded to your WordPress installation.', 'fv' ) ); ?></td>
            <td><?php echo size_format( wp_max_upload_size() ); ?></td>
        </tr>
        <tr>
            <td data-export-label="Default Timezone is UTC"><?php _e( 'Default Timezone is UTC', 'fv' ); ?>:</td>
            <?php echo fv_get_td_tooltip_code( esc_attr__( 'The default timezone for your server.', 'fv' ) ); ?></td>
            <td><?php
                $default_timezone = date_default_timezone_get();
                if ( 'UTC' !== $default_timezone ) {
                    echo '<mark class="error">' . '&#10005; ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'fv' ), $default_timezone ) . '</mark>';
                } else {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } ?>
            </td>
        </tr>
        <?php
        $posting = array();

        // fsockopen/cURL
        $posting['fsockopen_curl']['name'] = 'fsockopen/cURL';
        $posting['fsockopen_curl']['help'] = esc_attr__( 'Payment gateways can use cURL to communicate with remote servers to authorize payments, other plugins may also use it when communicating with remote services.', 'fv' );

        if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
            $posting['fsockopen_curl']['success'] = true;
        } else {
            $posting['fsockopen_curl']['success'] = false;
            $posting['fsockopen_curl']['note']    = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'fv' ). '</mark>';
        }

        // GZIP
        $posting['gzip']['name'] = 'GZip';
        $posting['gzip']['help'] = esc_attr__( 'GZip (gzopen) is used to open the GEOIP database from MaxMind.', 'fv' );

        if ( is_callable( 'gzopen' ) ) {
            $posting['gzip']['success'] = true;
        } else {
            $posting['gzip']['success'] = false;
            $posting['gzip']['note']    = sprintf( __( 'Your server does not support the <a href="%s">gzopen</a> function - this is required to use the GeoIP database from MaxMind. The API fallback will be used instead for geolocation.', 'fv' ), 'http://php.net/manual/en/zlib.installation.php' ) . '</mark>';
        }

        // WP Remote Post Check
        $posting['wp_remote_post']['name'] = __( 'Remote Post', 'fv');
        $posting['wp_remote_post']['help'] = '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'PayPal uses this method of communicating when sending back transaction information.', 'fv' ) ;

        $response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
            'sslverify'  => false,
            'timeout'    => 60,
            'user-agent' => 'FV/' . FV::VERSION,
            'body'       => array(
                'cmd'    => '_notify-validate'
            )
        ) );

        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
            $posting['wp_remote_post']['success'] = true;
        } else {
            $posting['wp_remote_post']['note']    = __( 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider.', 'fv' );
            if ( is_wp_error( $response ) ) {
                $posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Error: %s', 'fv' ), sanitize_text_field( $response->get_error_message() ) );
            } else {
                $posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Status code: %s', 'fv' ), sanitize_text_field( $response['response']['code'] ) );
            }
            $posting['wp_remote_post']['success'] = false;
        }

        foreach ( $posting as $post ) {
            $mark = ! empty( $post['success'] ) ? 'yes' : 'error';
            ?>
            <tr>
                <td data-export-label="<?php echo esc_html( $post['name'] ); ?>"><?php echo esc_html( $post['name'] ); ?>:</td>
                <?php echo isset( $post['help'] ) ? fv_get_td_tooltip_code( $post['help'] ) : '' ; ?></td>
                <td>
                    <mark class="<?php echo $mark; ?>">
                        <?php echo ! empty( $post['success'] ) ? '&#10004 ok' : '&#10005'; ?>
                        <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
                    </mark>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>

    <?php //FvFunctions::dump( get_option('fv') ); ?>
    <?php //FvFunctions::dump( get_option('fv_addons_settings') ); ?>
</div>
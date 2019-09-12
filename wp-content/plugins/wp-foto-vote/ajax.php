<?php
if ( !isset($_REQUEST['action']) ) {
    die('=00=');
}

$fv_action = $_REQUEST['action'];

if ( !in_array( $fv_action, array('vote', 'fv_is_subscribed', 'fv_subscribe_verify') ) ) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error - action not defined', true, 500);
    die('=01=');
}

//make sure we skip most of the loading which we might not need
//http://core.trac.wordpress.org/browser/branches/3.4/wp-settings.php#L99
define('SHORTINIT', true);

//mimic the actuall admin-ajax
define('DOING_AJAX', true);

// This include gives us all the WordPress functionality
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once( $parse_uri[0] . 'wp-load.php' );


//============ Show errors !
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

try {
    //Typical headers
    header('Content-Type: text/html');
    send_nosniff_header();

    //Disable caching
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');

    require_once( ABSPATH . WPINC . '/l10n.php' );
    //Include only the files and function we need
    global $wp_version;
    if ( $wp_version  < 4.4 ) {

        require( ABSPATH . WPINC . '/formatting.php' );
        require( ABSPATH . WPINC . '/capabilities.php' );
        //require( ABSPATH . WPINC . '/query.php' );
        //require( ABSPATH . WPINC . '/date.php' );
        require( ABSPATH . WPINC . '/class-wp-roles.php' );
        require( ABSPATH . WPINC . '/class-wp-role.php' );
        require( ABSPATH . WPINC . '/class-wp-role.php' );

        require( ABSPATH . WPINC . '/user.php' );
        require( ABSPATH . WPINC . '/meta.php' );
        require( ABSPATH . WPINC . '/general-template.php' );
        require( ABSPATH . WPINC . '/link-template.php' );
        //require( ABSPATH . WPINC . '/post.php' );
        require( ABSPATH . WPINC . '/kses.php' );
        require( ABSPATH . WPINC . '/http.php' );
        require( ABSPATH . WPINC . '/class-http.php' );

        require( ABSPATH . WPINC . '/l10n.php' );
    } elseif ( $wp_version >= 4.4 ) {
        require( ABSPATH . WPINC . '/class-wp-walker.php' );
        require( ABSPATH . WPINC . '/class-wp-ajax-response.php' );
        require( ABSPATH . WPINC . '/formatting.php' );
        require( ABSPATH . WPINC . '/capabilities.php' );
        require( ABSPATH . WPINC . '/class-wp-roles.php' );
        require( ABSPATH . WPINC . '/class-wp-role.php' );

        require( ABSPATH . WPINC . '/class-wp-user.php' );
        require( ABSPATH . WPINC . '/query.php' );
        //require( ABSPATH . WPINC . '/date.php' );
        require( ABSPATH . WPINC . '/template.php' );
        require( ABSPATH . WPINC . '/user.php' );
        require( ABSPATH . WPINC . '/class-wp-user-query.php' );
        require( ABSPATH . WPINC . '/meta.php' );
        require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
        require( ABSPATH . WPINC . '/general-template.php' );
        require( ABSPATH . WPINC . '/link-template.php' );
        require( ABSPATH . WPINC . '/post.php' );
        require( ABSPATH . WPINC . '/class-wp-post.php' );
        require( ABSPATH . WPINC . '/rewrite.php' );
        require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
        require( ABSPATH . WPINC . '/kses.php' );
        require( ABSPATH . WPINC . '/http.php' );
        require( ABSPATH . WPINC . '/class-http.php' );
        require( ABSPATH . WPINC . '/class-wp-http-streams.php' );
        require( ABSPATH . WPINC . '/class-wp-http-curl.php' );
        require( ABSPATH . WPINC . '/class-wp-http-proxy.php' );
        require( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
        require( ABSPATH . WPINC . '/class-wp-http-encoding.php' );
        require( ABSPATH . WPINC . '/class-wp-http-response.php' );
        require_once( ABSPATH . WPINC . '/class-wp-http-requests-response.php' );

        require_once( ABSPATH . WPINC . '/rest-api.php' );
        require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php' );
        require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php' );
        require_once( ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php' );

        require_once(ABSPATH . '/wp-admin/includes/class-wp-screen.php');   // For Math Captcha !!
    }
    if ( $wp_version >= 4.6 ) {
        require_once(ABSPATH . WPINC . '/canonical.php');
        require_once(ABSPATH . WPINC . '/class-wp-metadata-lazyloader.php');
    }

    if ( $wp_version > 4.7 ) {
        require_once( ABSPATH . WPINC . '/class-wp-http-requests-hooks.php' );
    }
    if ( version_compare($wp_version, '4.7.3', '>') ) {
        require_once( ABSPATH . WPINC . '/class-wp-session-tokens.php' );
        require_once( ABSPATH . WPINC . '/class-wp-user-meta-session-tokens.php' );

        require_once( ABSPATH . WPINC . '/class-wp-query.php' );
    }

    // Define constants
    wp_plugin_directory_constants();
    wp_cookie_constants();
    // Define and enforce our SSL constants
    wp_ssl_constants();

    wp_register_plugin_realpath( WP_PLUGIN_DIR . '/wp-foto-vote/wp-foto-vote.php' );

    global $wp_plugin_paths;
    $wp_plugin_paths = array();

    //and do your stuff

    if ( file_exists(WP_PLUGIN_DIR . '/wp-math-captcha/wp-math-captcha.php') ) {
        include(WP_PLUGIN_DIR . '/wp-math-captcha/wp-math-captcha.php');
    }
    require( 'wp-foto-vote.php' );

    require( ABSPATH . WPINC . '/pluggable.php' );
    require( ABSPATH . WPINC . '/pluggable-deprecated.php' );

    // Define constants which affect functionality if not already defined.
    wp_functionality_constants();

    // Add magic quotes and set up $_REQUEST ( $_GET + $_POST )
    wp_magic_quotes();

    if ( !fv_is_fast_ajax_enabled() ) {
        die('hehe');
    }

    
    if ( 'vote' == $fv_action ) {
        do_action('wp_ajax_nopriv_vote');

    } elseif( 'fv_is_subscribed' == $fv_action ) {
        do_action('wp_ajax_nopriv_fv_is_subscribed');

    } elseif( 'fv_subscribe_verify' == $fv_action ) {
        do_action('wp_ajax_nopriv_fv_subscribe_verify');

    }

} catch (Exception $e) {
    fv_log( 'FV fast AJAX error: ', $e->getMessage() );
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}
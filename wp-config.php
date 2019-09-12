<?php
# Database Configuration
define( 'DB_NAME', 'wp_peanuttiestfan' );
define( 'DB_USER', 'peanuttiestfan' );
define( 'DB_PASSWORD', 'r-9H9GSizhpICQYxHuyS' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '?[L(,-Bo =CeK,W0|P(@P=:Br];v.*?B!Sd eOoL3Aa;ZlWM6eCFaLa iO+v@Byg');
define('SECURE_AUTH_KEY',  'rh_K}WhQqs+s(LN6T_bTV}g+#)B#>.1?:r4RmPm(;H&,W%<wFX+});dfc*L|XD9L');
define('LOGGED_IN_KEY',    '##r.(&a8l%#r`!eP/S}L+3F-h@C!O`$Yxgun7NZ%lIN{z]I+{!ireS1Rg/ WzxP2');
define('NONCE_KEY',        'mIT`fh=:8)uBv+p-NFfOJr%#sRG;}v~(eJB[};m <4j@*0kE8`hHhD7pgBc5Ryy?');
define('AUTH_SALT',        'n:MpqV?7@ ;s8ot#+WCbX3]6N5Y-!/O(KPuK;PUw<bi{76/BhnW(3F9 /A-$iElX');
define('SECURE_AUTH_SALT', '<H|=SZ/ds#,?e,U,|/J2OJKudAss7o &`_:{>M`Is|<}d2I:EIce`?_gtv8nsp/n');
define('LOGGED_IN_SALT',   'g(0@_q%JMecK1V(oWKcK|qqEeB4X(KG7D ZXa}.a{eFQ9QCF+C2}h$cuxd:Q5};R');
define('NONCE_SALT',       'h[7:0!]!il>iTR5@IQcNAQhYbL2|S.WLs/B^2Uxu.;GuI-f&t/XKj-9*H|k3Muu*');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'peanuttiestfan' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', 'bbf0440cf633e29a11d10d22c236af27603d134d' );

define( 'WPE_CLUSTER_ID', '100727' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'peanuttiestfans.com', 1 => 'peanuttiestfan.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-100727', );

$wpe_special_ips=array ( 0 => '104.199.118.66', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', __DIR__ . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}

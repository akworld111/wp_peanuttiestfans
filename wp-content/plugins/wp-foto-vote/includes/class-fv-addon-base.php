<?php

defined('ABSPATH') or die("No script kiddies please!");
/**
 * Uses for create addons functionality
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
abstract class FvAddonBase {

    /**
     * Addon slug (like UAR)
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $slug;

    /**
     * Addon slug for using Translation functions like `_e()` or `__()`
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $mu_slug;

    /**
     * Addon name (like UploadAgreeRules)
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $name;

    /**
     * Addon version
     *
     * @since 2.2.508
     *
     * @var string
     */
    public $version;

/*
    Add this later WHEN @filter photos@ && @Popup Promo@ && @Video Addons Will be fixed@
    public $addonUrl;
    public $addonDir;
*/
    /**
     * All addon settings
     *
     * @since 2.2.083
     *
     * @var object
     */
    protected static $addonsSettings;
    
    /**
     * Required Main Plugin Version
     *
     * @since 2.2.411
     *
     * @var string
     */
    protected $required_version;

    /**
     * @var array
     * @since 2.2.504
     */
    public $minimal_addon_versions = array (
        'video' => 1.20,
        'wpml'  => 0.7,
        's3'    => 0.6,
        'cloudi'=> 0.4,
        'ig'    => 1.20,
        'ccm'    => 0.2,       // CoutdownMinimalCircle
        'gall-pro'  => 0.7,
        'pfa'       => 2.00,        // Pay Fro Action
    );

    /**
     * @var array
     * @since 2.2.504
     */
    public $about_last_addon_versions = array (
        'ig'        => 1.40,
        'li'        => 1.70,       // Lightbox Inspired
        'bp'        => 0.6,        // Buddypress
        'wpml'      => 0.8,        // wpml
        'fblike'    => 0.6,        // fblike
        'upl'       => 0.82,        // User Photos Lite
        's3'        => 0.6,        // AWS S3
        'frontend-manage' => 0.2,
        'gall-pro'  => 0.7,
        'video'     => 1.21,
        'fph'       => 0.41,        // filter-photos
        'cloudi'    => 0.5,        // cloudinary
        'cc'        => 0.4,        // Coutdown Circle
        'pfa'       => 2.00,        // Pay Fro Action
    );

    /**
     * Constructor. Loads the class.
     * And performs the necessary actions
     * - add_filter to add Addon settings section
     * - add_filter to add addon into main plugin Addon array
     *
     * @since 2.2.083
     */
    protected function __construct($name, $slug, $api_ver = 'api_v1', $main_file_path = false) {
        $is_admin = is_admin();

        if ( function_exists('get_class') ) {
            $addon_name = get_class($this);
        } else {
            $addon_name = $name;
        }

        if ( $api_ver == 'api_v1' ) {
            if ( !$is_admin ) {
                return;
            }
            wp_add_notice('Waring! Addon "' . $addon_name . '" used old API, that now is not supported. Please contact to support for update it! [http://wp-vote.net/contact-us/]', 'warning');
            return false;
        }

        // TO Avoid issues with a PHP 5.2
        if ( version_compare(phpversion(), '5.3.0', '>') ) :

            if ( isset($this->minimal_addon_versions[$slug]) && defined("$addon_name::VER") && version_compare($addon_name::VER, $this->minimal_addon_versions[$slug]) === -1 ) {
                if ( !$is_admin ) {
                    return;
                }

                wp_add_notice(
                    sprintf(
                        'WP Foto Vote Waring! Addon "%s" version is <strong>%s</strong>, but minimal supported version is <strong>%s</strong>! '
                            . 'Please update it via Plugins menu or login to <a href="%s" target="_blank">your cabinet</a> and download update.',
                        $addon_name,
                        $addon_name::VER,
                        $this->minimal_addon_versions[$slug],
                        'https://wp-vote.net/checkout/purchase-history/'
                    )
                , 'warning');
                return false;
            }

            if (
                $is_admin && !defined('FV_HIDE_ADDONS_HAVE_UPDATES_MESSAGE')
                && isset($this->about_last_addon_versions[$slug]) && defined("$addon_name::VER")
                && version_compare($addon_name::VER, $this->about_last_addon_versions[$slug]) === -1
            ) {

                wp_add_notice(
                    sprintf(
                        'WP Foto Vote Notice! Addon "%s" seems have newer version (installed version <strong>%s</strong>)! '
                            .'If you did\'t edited it code - please update via Plugins menu or login to <a href="%s" target="_blank">your cabinet</a> and download update (for some old versions update can be not displayed in Plugins menu).',
                        str_replace( array('_', 'FvAddon'), '', $addon_name),
                        $addon_name::VER,
                        'https://wp-vote.net/checkout/purchase-history/'
                    )
                , 'info');
            }

        ENDIF;

        if ( !empty($this->required_version) && version_compare(FV::VERSION, $this->required_version) == -1 ) {
            if ( !$is_admin ) {
                return;
            }

            wp_add_notice(
                sprintf(
                    'Waring! Addon "%1$s" is required WP Foto Vote version %2$s.'
                    .' Please update WP Foto Vote plugin (via Plugins menu or <a href="%3$s" target="_blank">download latest plugin version in your dashboard)</a>'
                    .' or <a href="%4$s">contact with support</a>!',
                    $addon_name,
                    $this->required_version,
                    'https://wp-vote.net/checkout/purchase-history/',
                    admin_url('admin.php?page=fv-help')
                )
            , 'warning');
            return false;
        }


        $this->name = $name;
        $this->slug = strtolower($slug);
        $this->mu_slug =  'fv_' . $this->slug;
        if (!$main_file_path) {
            $main_file_path = $this->get_child_class_dir();
        }
        //** At first init settings for Redux
        //** I we do this later, they are not shows in admin
        //add_filter( 'redux/options/' . FV::ADDONS_OPT_NAME . '/sections', array($this, 'section_settings') );

        // && (!defined('DOING_AJAX') && DOING_AJAX == TRUE)
        if ( $is_admin && !SHORTINIT ) {
            add_filter( 'fv/addons/settings', array($this, 'section_settings'), 10, 1 );
            // Run before main admin_init (with 9 priority)
            add_action( 'admin_init', array($this, 'admin_init'), 9 );

            // TODO - add Mailchimp addon

            if ( !defined('FV_DISABLE_UPDATER') &&
                in_array($slug, array('cc','gall-pro','li', 'fblike', 'FbLike', 'video', 'promo', 's3', 'ig', 'frontend-manage', 'mailc', 'bp', 'upl', 'wpml', 'mycred', 'cloudi')) ) {
                //$update_url = 'https://res.cloudinary.com/dxo61viuo/raw/upload/v' . date('dmY') . '/addons-updater/fv-' . $slug . '.json';
                $update_url = 'https://addons-updater.wp-vote.net/?action=get_metadata&slug=wp-foto-vote-' . $slug;
                if ( isset($this->custom_updater_url) ) {
                    $update_url = $this->custom_updater_url;
                }

                PucFactory::buildUpdateChecker(
                    $update_url, $main_file_path, 'wp-foto-vote-' . $slug, 24
                );
            }
        }
/*
        if ( !empty($main_file_path) ) {
            $this->addonUrl = plugin_dir_url($main_file_path);
            $this->addonDir = dirname($main_file_path);
        }
*/
        $this->init($is_admin && !SHORTINIT);

        //** Register addon and after main plugin will known about it
        //add_filter( 'fv/addons/list', array($this, 'register_addon') );
    }

    public function get_version() {
        return self::VER;
    }

    protected function get_child_class_dir() {
        $rc = new ReflectionClass(get_class($this));
        return $rc->getFileName();
    }

    /**
     * Performs all the necessary actions
     *
     * @since 2.2.083
     * @param bool $is_admin_and_not_SHORTINIT
     */
    public function init(  ) {
        if ( !is_admin() && !SHORTINIT && !(defined( 'DOING_AJAX' ) && DOING_AJAX) ) {
            $this->_may_be_init_defaults();
        }
    }

    /**
     * Add default settings to Database
     *
     * @since 2.2.706
     *
     * *TODO - may be save addon version and update when it changed
     */
    public function _may_be_init_defaults() {
        $addon_sections = $this->section_settings( array() );
        
        // If addon does not have settings - exit
        if ( !$addon_sections ) {
            return;
        }

        // Get options from DB
        $all_settings = get_option( FV::ADDONS_OPT_NAME, array() );
        $updates = array();
        $updated = false;
        
        foreach ( $addon_sections as $addon_section ){
            foreach ($addon_section['fields'] as $field){
                if ( !isset($field['default']) || isset($all_settings[ $field['id'] ]) || in_array($field['type'], array('delimiter', 'heading')) ) {
                    continue;
                }
                $all_settings[ $field['id'] ] = $field['default'];
                $updates[ $field['id'] ] = $field['default'];
                $updated = true;
            }
        }

        // Update Array in DB
        if ( $updated ) {
            update_option( FV::ADDONS_OPT_NAME, $all_settings );
            fv_log( "updated ADDONS settings - added defaults!", $updates );
            // Update cache
            self::$addonsSettings = get_option(FV::ADDONS_OPT_NAME, array());
        }
    }

    /**
     * Performs all the necessary Admin actions
     *
     * @since 2.2.083
     */
    public function admin_init() {
        // There you can load plugin textdomain as example
    }

    /**
     * Dynamically add Addon settings section
     *
     * @since 2.2.083
     */
    abstract public function section_settings($sections);

    /**
     * Dynamically register addon (add addon Instance into Addons array)
     *
     * @since 2.2.083
     */
    public function register_addon($addons) {
        return array_merge( $addons, array($this->name, $this) );
    }

    /**
     * Get addon Setting
     * @since 2.2.106
     *
     * @param string $key
     * @param mixed $default    IF sets False, then check into Empty and return FALSE if empty or not ISSET
     *
     * @return mixed
     */

    public function _get_opt($key, $default = '') {
        // Add Addon unique slug
        $key = $this->slug . '_' . $key;

        if ( empty(self::$addonsSettings) ) {
            //global $fv_addons_settings;
            self::$addonsSettings = get_option(FV::ADDONS_OPT_NAME, array());
        }
        if ( !isset(self::$addonsSettings[$key]) ) {
            return $default;
        }
        /*
            if ( $default == false && empty(self::$addonsSettings[$key]) ) {
                return false;
            }
            return self::$addonsSettings[$key];
        }
        */
        return self::$addonsSettings[$key];

    }

    public function p_get_opt($key, $default = '') {
        return $this->_get_opt($key, $default);
    }
}


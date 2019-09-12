<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.2.073
 * @package    FV
 * @subpackage includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV
{
    /**
     * The current version of the plugin.
     *
     * @since    2.2.073
     * @access   public
     * @var      const VERSION The current version of the plugin.
     */
    const VERSION = '2.3.14';

    const NAME = 'fv';
    const PREFIX = 'fv_';
    const SLUG = 'wp-foto-vote';
    public static $DEBUG_MODE;

    const ADDONS_OPT_NAME = 'fv_addons_settings';

    public static $ADDONS_URL;
    public static $ADDONS_ROOT;

    public static $ASSETS_URL;
    public static $THEMES_ROOT;
    public static $THEMES_ROOT_URL;
    public static $ADMIN_ROOT;
    public static $ADMIN_URL;
    public static $ADMIN_PARTIALS_ROOT;
    public static $INCLUDES_ROOT;
    public static $VENDOR_ROOT;

    public static $PUBLIC_ROOT;

    public static $ADDONS;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      FV_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      string $FV The string used to uniquely identify this plugin.
     */
    protected $NAME;


    /**
     * The unique identifier of this plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      string $FV The string used to uniquely identify this plugin.
     */
    protected $file;


    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    2.2.073
     */
    public function __construct($file, $plugin_dir)
    {
        $this->file = $file;
        //$this->NAME = 'wsds';

        self::$ADDONS_URL = plugins_url(self::SLUG . "/addons/");
        self::$ADDONS_ROOT = $plugin_dir . "/addons/";

        self::$ASSETS_URL = plugins_url(self::SLUG . "/assets/");
        self::$THEMES_ROOT = $plugin_dir . "/templates/";
        self::$THEMES_ROOT_URL = plugins_url(self::SLUG . "/templates/");
        self::$ADMIN_URL = plugins_url(self::SLUG . "/admin/");
        self::$ADMIN_ROOT = $plugin_dir . "/admin/";
        self::$ADMIN_PARTIALS_ROOT = $plugin_dir . "/admin/partials/";
        self::$INCLUDES_ROOT = $plugin_dir . "/includes/";

        self::$PUBLIC_ROOT = $plugin_dir . "/public/";
        self::$VENDOR_ROOT = $plugin_dir . "/vendor/";

        $is_admin = is_admin();
        $this->load_dependencies($is_admin);

        // Init DEBUG Levels
        FvDebug::init_lvl();

        $this->load_plugin_textdomain();
        $this->define_admin_hooks($is_admin);
        $this->define_public_hooks($is_admin);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    2.2.073
     * @access   private
     */
    private function load_dependencies($is_admin)
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        //require_once('class-fv-loader.php');
        //include_once('class-fv-autoloader.php');
//
//        /**
//         * The classes for logging and debug
//         */
        require_once('libs/class-fv-logger.php');
        require_once('libs/class-fv-debug.php');
//
//        /**
//         * The classes for Email Notifications
//         */
//        require_once self::$INCLUDES_ROOT . 'notifications/class-fv-notifications-abstract.php';
//        require_once self::$INCLUDES_ROOT . 'notifications/class-fv-notifications-abstract.php';
//        require_once self::$INCLUDES_ROOT . 'notifications/class-fv-notifications-competitor.php';
//        require_once self::$INCLUDES_ROOT . 'notifications/class-fv-notifications-contest.php';
//        require_once self::$INCLUDES_ROOT . 'class-fv-notifier.php';
//        require_once self::$INCLUDES_ROOT . 'notifications/class-fv-notifications-core.php';

        /**
         * Tables lists
         */

        //defined('SHORTINIT') &&
        if (!SHORTINIT) {
            // Widgets
            require_once('widget-list/class-widget.php');
            require_once('widget-list-global/class-widget.php');
            require_once('widget-gallery/class-widget.php');
        }

        /**
         * Functions and other
         */
        require_once self::$INCLUDES_ROOT . 'class-fv-settings.php';
        require_once self::$INCLUDES_ROOT . 'class-fv-functions.php';
        require_once self::$INCLUDES_ROOT . 'fv-helper.php';
        include_once self::$INCLUDES_ROOT . 'fv-translations-helper.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-contest.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-lightbox-evolution.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-image-lightbox.php';
        include_once self::$INCLUDES_ROOT . 'libs/class_empty_unit.php';
        require_once self::$INCLUDES_ROOT . 'notice/class-admin-notice-helper.php';
        require_once self::$INCLUDES_ROOT . 'notice/class-admin-dismissible-notice.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-theme-base.php';
//        require_once self::$INCLUDES_ROOT . 'class-fv-addon-base.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-form-helper.php';
        require_once self::$ADDONS_ROOT . 'fv-addons-loader.php';

//        /**
//         * The class responsible for working with db
//         */
//        require self::$INCLUDES_ROOT . 'db/class-query.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-db.php';
//
//        // Interfaces
//        require self::$INCLUDES_ROOT . 'db/class-fv-competitors-abstract.php';
//
//        // Abstract Wrappers and Helpers
//        require self::$INCLUDES_ROOT . 'abstracts/class-fv-abstract-contest-config.php';
//        require self::$INCLUDES_ROOT . 'abstracts/class-fv-abstract-object.php';
//        require self::$INCLUDES_ROOT . 'abstracts/class-fv-contest.php';
//        require self::$INCLUDES_ROOT . 'abstracts/class-fv-competitor.php';
//        require self::$INCLUDES_ROOT . 'class-fv-competitor-categories.php';
//
//        // Models
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-contests.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-competitors.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-votes.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-subscribers.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-meta.php';
//        require self::$INCLUDES_ROOT . 'db/class-fv-tbl-forms.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once self::$ADMIN_ROOT . 'fv-admin-helper.php';
        //require_once self::$ADMIN_ROOT . 'class-fv-admin.php';

        //require_once self::$ADMIN_ROOT . 'class-fv-admin-pages.php';
        /*if ($is_admin) {
            //require_once self::$ADMIN_ROOT . 'class-fv-admin-ajax.php';
            //require_once self::$ADMIN_ROOT . 'class-fv-admin-export.php';

        }*/

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once self::$PUBLIC_ROOT . 'fv-public-helper.php';
        //require_once self::$PUBLIC_ROOT . '/class-fv-public.php';
        //require_once self::$PUBLIC_ROOT . '/class-fv-public-ajax.php';
        //require_once self::$PUBLIC_ROOT . '/class-fv-public-vote.php';

        if ($is_admin && !SHORTINIT) {
            // Updates
            require_once self::$INCLUDES_ROOT . 'plugin-updates/plugin-update-checker.php';

            /**
             * Redux options framework
             */
            //(!defined('SHORTINIT') ||
            if ( (!defined('DOING_AJAX') || !DOING_AJAX) && fv_setting('disable-addons-support', false) == false) {
                require_once self::$ADMIN_ROOT . 'options-framework/options-framework.php';
            }

            require_once self::$INCLUDES_ROOT . 'wp-page-locker/locker-init.php';
        }

        //if ( isset($_GET['fv-themes-customizer']) || isset($_GET['wp_customize']) ) {
            //include 'class-fv-themes-customizer.php';
        //}

        //require_once FV::$INCLUDES_ROOT . 'redux/admin-init.php';


        $this->loader = new FV_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the FV_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.2.073
     * @access   private
     */
    private function load_plugin_textdomain()
    {
        //$plugin_i18n = new FV_i18n();
        //$plugin_i18n->set_domain($this->get_NAME());

        load_plugin_textdomain(
            FV::NAME, false, FV::SLUG . '/languages/'
        );
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    2.2.073
     * @access   private
     */
    private function define_admin_hooks($is_admin)
    {
        /**
         * Cron events
         * @since 2.2.503
         */
        $this->loader->add_filter( 'cron_schedules', 'FV_Cron', 'add_custom_schedules__filter', 10 );
        $this->loader->add_action( 'fv_cron', 'FV_Cron', 'execute', 10 );
        
        FV_Notifications_Core::load();

        if ( !SHORTINIT ) {
            $this->loader->add_action('widgets_init', $this, 'widgets_init');
        }

        //$plugin_admin_pages = new FV_Admin_Pages($this->get_NAME());
        if ($is_admin) {
            $plugin_admin = new FV_Admin();

            $this->loader->add_action('admin_init', $plugin_admin, 'register_fv_settings');
            $this->loader->add_action('admin_init', 'FV_Admin_Actions', 'process_admin_actions');
            // Allow disable locker Globally via
            // define('FV_DISABLE_PAGE_LOCKER', true);
            if ( !SHORTINIT && !defined('FV_DISABLE_PAGE_LOCKER') ) {
                add_action('admin_init', 'fv_wp_page_locker_init');
            }

            $this->loader->add_action('admin_menu', 'FV_Admin_Pages', 'register_admin_pages');
            $this->loader->add_action('dashboard_glance_items', $plugin_admin, 'dashboard_glance_items_filter');

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

            $this->loader->add_action('wp_ajax_fv_clear_contest_stats', 'FV_Admin_Ajax', 'clear_contest_stats');
            $this->loader->add_action('wp_ajax_fv_clear_contest_subscribers', 'FV_Admin_Ajax', 'clear_contest_subscribers');
            $this->loader->add_action('wp_ajax_fv_reset_contest_votes', 'FV_Admin_Ajax', 'reset_contest_votes');
            $this->loader->add_action('wp_ajax_fv_clone_contest', 'FV_Admin_Ajax', 'clone_contest');

            $this->loader->add_action('wp_ajax_fv_winners_get_entries', 'FV_Admin_Winners', 'AJAX_winners_get_entries');
            $this->loader->add_action('wp_ajax_fv_winners_process_manual_pick', 'FV_Admin_Winners', 'AJAX_process_manual_pick');

            $this->loader->add_action('wp_ajax_fv_form_contestant', 'FV_Admin_Ajax', 'form_contestant');
            $this->loader->add_action('wp_ajax_fv_save_contestant', 'FV_Admin_Ajax', 'save_contestant');
            $this->loader->add_action('wp_ajax_fv_approve_contestant', 'FV_Admin_Ajax', 'approve_contestant');
            $this->loader->add_action('wp_ajax_fv_delete_contestant', 'FV_Admin_Ajax', 'delete_contestant');

            $this->loader->add_action('wp_ajax_fv_rotate_image', 'FV_Admin_Ajax', 'rotate_image');
            $this->loader->add_action('wp_ajax_fv_form_contestants', 'FV_Admin_Ajax', 'form_contestants');
            $this->loader->add_action('wp_ajax_fv_move_contestant', 'FV_Admin_Ajax', 'AJAX_move_contestant_to_contest');

            $this->loader->add_action('wp_ajax_fv_get_pages_and_posts', 'FV_Admin_Ajax', 'get_pages_and_posts');
            $this->loader->add_action('wp_ajax_fv_competitors_list__get_page', 'FV_Admin_Ajax', 'competitors_list__get_rows_for_page');

            $this->loader->add_action('wp_ajax_fv_export', 'FV_Admin_Export', 'run');

            $this->loader->add_action('wp_ajax_fv_save_form_structure', 'Fv_Form_Helper', 'AJAX_save_form_structure');
            $this->loader->add_action('wp_ajax_fv_reset_form_structure', 'Fv_Form_Helper', 'AJAX_reset_form_structure');

            $this->loader->add_action('activated_plugin', 'FV_Functions', 'check_activation_error', 10, 2);

            // Add settings link to plugins page
            $this->loader->add_filter('plugin_action_links_' . $this->file, 'FV_Admin', 'add_settings_link');
            add_filter('puc_request_info_result-'.FV::SLUG, 'fv_check_updates_may_be_need_refresh_key_data', 10, 1);

            // Upadting
            $hook = "in_plugin_update_message-" . $this->file;
            //FvLogger::addLog( $hook );
            add_action($hook, 'fv_add_update_message', 10, 2);
        }

        //add_action('wp_ajax_fv_form_contestant', array('FV_Contest', 'form_contestant') );
        //add_action('wp_ajax_fv_save_contestant', array('FV_Contest', 'save_contestant') );
        //add_action('wp_ajax_fv_approve_constestant', array('FV_Contest', 'approve_constestant') );

        $this->loader->add_action('init', __CLASS__, 'install', 1);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    2.2.073
     * @access   private
     */
    private function define_public_hooks($is_admin)
    {
        FV_Notification_Integration__Competitor::get();
        FV_Notification_Integration__Contest::get();
        FV_Notification_Integration__System::get();

        $this->loader->add_action('wp_ajax_vote', 'FV_Public_Vote', 'vote');
        $this->loader->add_action('wp_ajax_nopriv_vote', 'FV_Public_Vote', 'vote');

        $this->loader->add_action('wp_ajax_fv_is_subscribed', 'FV_Public_Vote', 'is_subscribed');
        $this->loader->add_action('wp_ajax_nopriv_fv_is_subscribed', 'FV_Public_Vote', 'is_subscribed');

        $this->loader->add_action('wp_ajax_fv_soc_login', FV_Public_Social_Login::i(), 'AJAX_login');
        $this->loader->add_action('wp_ajax_nopriv_fv_soc_login', FV_Public_Social_Login::i(), 'AJAX_login');

        $this->loader->add_action('wp_ajax_fv_email_subscribe', 'FV_Public_Vote', 'email_subscribe');
        $this->loader->add_action('wp_ajax_nopriv_fv_email_subscribe', 'FV_Public_Vote', 'email_subscribe');

        if ( fv_setting('mail-verify', true) ) {
            $this->loader->add_action('wp_ajax_fv_subscribe_verify', 'FV_Public_Vote', 'email_subscribe_verify_hash');
            $this->loader->add_action('wp_ajax_nopriv_fv_subscribe_verify', 'FV_Public_Vote', 'email_subscribe_verify_hash');
        }

        $this->loader->add_action('wp_ajax_fv_upload', 'FV_Public_Ajax', 'upload_photo');
        $this->loader->add_action('wp_ajax_nopriv_fv_upload', 'FV_Public_Ajax', 'upload_photo');

        $this->loader->add_action('wp_ajax_fv_ajax_get_votes', 'FV_Public_Ajax', 'ajax_get_votes_counts');
        $this->loader->add_action('wp_ajax_nopriv_fv_ajax_get_votes', 'FV_Public_Ajax', 'ajax_get_votes_counts');

        $this->loader->add_action('wp_ajax_fv_ajax_go_to_page', 'FV_Public_Ajax', 'ajax_go_to_page');
        $this->loader->add_action('wp_ajax_nopriv_fv_ajax_go_to_page', 'FV_Public_Ajax', 'ajax_go_to_page');

        // add action for lightbox
        $this->loader->add_action('fv_load_lightbox_evolution', 'FV_Lightbox_Evolution', 'assets');
        $this->loader->add_filter('fv_lightbox_list_array', 'FV_Lightbox_Evolution', 'initListThemes');


        //$this->loader->add_action('fv_load_lightbox_imageLightbox', 'Fv_Image_Lightbox', 'assets');
        //$this->loader->add_filter('fv_lightbox_list_array', 'Fv_Image_Lightbox', 'initListThemes');

        $this->loader->add_action('fv_load_lightbox_magnific-popup', 'FV_Lightbox_Magnific_Popup', 'assets');
        $this->loader->add_filter('fv_lightbox_list_array', 'FV_Lightbox_Magnific_Popup', 'initListThemes');

        //$this->loader->add_action('fv_load_lightbox_ilightbox', 'FV_Lightbox_iLightbox', 'assets');
        //$this->loader->add_filter('fv_lightbox_list_array', 'FV_Lightbox_iLightbox', 'initListThemes');

        $this->loader->add_action('init', $this, 'wp_init', 1, 0);

        // Single photo hooks & filters
        $plugin_public_singe = FV_Public_Single::get_instance();
        $this->loader->add_action('init', $plugin_public_singe, 'add_rewrite_rule', 0, 0);
        $this->loader->add_filter('query_vars', $plugin_public_singe, 'filter_query_vars');

        if (!$is_admin && !SHORTINIT) {
            if ( fv_setting('rocketscript-support', false) ) {
                add_filter('script_loader_tag', 'fv_rocketscript_fix_filter', 1, 2);
            }

            $plugin_public = new FV_Public();

            $this->loader->add_action('wp_enqueue_scripts', 'FV_Public_Assets', 'register_assets', 10);
            $this->loader->add_action('after_setup_theme', 'FV_Public_Assets', 'register_assets', 99);

            $this->loader->add_shortcode("foto_vote", $plugin_public, "shortcode");
            $this->loader->add_shortcode("fv", $plugin_public, "shortcode");
            $this->loader->add_shortcode("fv_contest_description", $plugin_public, "shortcode_contest_description");
            $this->loader->add_shortcode("fv_upload_form", FV_Public_Form::instance(), "shortcode_upload_form");
            $this->loader->add_shortcode("fv_contests_list", FV_Public_Contests_List::instance(), "shortcode_show_contests_list");
            $this->loader->add_shortcode("fv_leaders", FV_Public_Leaders::instance(), "shortcode_leaders");
            $this->loader->add_shortcode("fv_countdown", "FV_Public_Countdown", "render_shortcode");
            $this->loader->add_shortcode("fv_winners", "FV_Public_Winners", "shortcode_winners");

            $this->loader->add_action('template_redirect', $plugin_public_singe, 'add_filters', 9999);

            $this->loader->add_shortcode("fv_single_vote_button", $plugin_public_singe, "render_single_vote_button");

            $this->loader->add_action('wp_footer', 'FV_Public_Assets', 'footer_output_js_and_modal', -1);
            $this->loader->add_action('wp_footer', 'FV_Public_Assets', 'footer_output_css', 49);
            //

            // if selected load FB SDK in head loads it with wp_enqueue_scripts
            // else it's no urgent, and loads it if we really needed it
            if (get_option('fv-fb-assets-position', 'footer') == 'head') {
                $this->loader->add_action('wp_head', 'FV_Public_Assets', 'fb_assets_and_init', 99);
            } else {
                $this->loader->add_action('wp_footer', 'FV_Public_Assets', 'fb_assets_and_init', 99);
                //$this->loader->add_action( 'fv_after_contest_item', $plugin_public, 'fb_assets_and_init' );
            }
        }

        global $contest_id, $contest_ids;
        add_action('admin_bar_menu', 'fv_add_toolbar_items', 100);

        //var_dump( get_query_var('photo_id') );
        fv_default_addons_load();
    }


    // Check db version on plugin loads
    public static function install()
    {

        if ( SHORTINIT && (defined('DOING_AJAX') && DOING_AJAX == TRUE) ) {
            return;
        }

        FV_Notifications_Core::install();

        $current_db_version = get_option('fv_db_version');
        if ($current_db_version !== FV_DB_VERSION) {

            FV_Admin::may_be_add_default_settings();

            // add / upgrade tables
            ModelContest::query()->install();
            ModelCompetitors::query()->install();
            ModelVotes::query()->install();
            ModelSubscribers::query()->install();
            ModelMeta::query()->install();
            ModelForms::query()->install();
            // if translations already exists
            if ( get_option('fotov-translation', false) ) {
                fv_update_exists_public_translation_messages();
            }

            $single_page_id = fv_setting('single-page');
            if ( empty($single_page_id) ) {
                $single_page_arr = array(
                    'post_title' => 'Single contest photo',
                    'post_content' => '[fv]',
                    'post_status' => 'publish',
                    'post_type' => 'page'
                );


                $single_page_id = wp_insert_post($single_page_arr);
                fv_log('"single-page" option added, created new page with ID = ' . $single_page_id);
                if ($single_page_id > 0) {
                    FvFunctions::set_setting('single-page', $single_page_id);
                    $fv_option = get_option( 'fv', array() );
                    $fv_option['single-page'] = $single_page_id;
                    update_option('fv', $fv_option);
                }
            }

            if ( version_compare(FV_DB_VERSION, '1.5.186') && get_option('fotov-update-key', false) ) {
                $old_update_key = get_option('fotov-update-key', false);
                fv_update_key_and_get_details( $old_update_key['key'] );
                delete_option('fotov-update-key');
                fv_log('License details migrated!');
            }

            // IF DB Version less than '1.5.206'
            // Update to version 2.2.502
            if ( version_compare('1.5.206', FV_DB_VERSION) === -1 ) {
                $contests = ModelContest::q()->find();
                foreach ($contests as $contest) {
                    switch ($contest->security_type) {
                        case "default":
                            $contest->voting_security = "cookiesAip";
                            $contest->voting_security_ext = "none";
                            break;
                        case "defaultArecaptcha":
                            $contest->voting_security = "cookiesAip";
                            $contest->voting_security_ext = "reCaptcha";
                            break;
                        case "cookieArecaptcha":
                            $contest->voting_security = "cookies";
                            $contest->voting_security_ext = "reCaptcha";
                            break;
                        case "defaultAsubscr":
                            $contest->voting_security = "cookiesAip";
                            $contest->voting_security_ext = "subscribe";
                            break;
                        case "defaultAfb":
                            $contest->voting_security = "cookiesAip";
                            $contest->voting_security_ext = "fbShare";
                            break;
                        case "defaultAsocial":
                            $contest->voting_security = "cookiesAip";
                            $contest->voting_security_ext = "social";
                            break;
                        case "cookieAsocial":
                            $contest->voting_security = "cookies";
                            $contest->voting_security_ext = "social";
                            break;
                        case "cookieAregistered":
                            $contest->voting_security = "cookies";
                            $contest->voting_security_ext = "registered";
                            break;
                    }

                    ##==================================================

                    switch ($contest->voting_frequency) {
                        case "once":
                            $contest->voting_frequency = "once";
                            $contest->voting_max_count = 1;
                            break;
                        case "onceF2":
                            $contest->voting_frequency = "once";
                            $contest->voting_max_count = 2;
                            break;
                        case "onceF3":
                            $contest->voting_frequency = "once";
                            $contest->voting_max_count = 3;
                            break;
                        case "onceF10":
                            $contest->voting_frequency = "once";
                            $contest->voting_max_count = 10;
                            break;
                        case "onceFall":
                            $contest->voting_frequency = "once";
                            $contest->voting_max_count = 0;
                            break;
                        case "24hFonce":
                            $contest->voting_frequency = "24h";
                            $contest->voting_max_count = 1;
                            break;
                        case "dayFonce":
                            $contest->voting_frequency = "day";
                            $contest->voting_max_count = 1;
                            break;
                        case "24hF2":
                            $contest->voting_frequency = "24h";
                            $contest->voting_max_count = 2;
                            break;
                        case "24hF3":
                            $contest->voting_frequency = "24h";
                            $contest->voting_max_count = 3;
                            break;
                        case "24hFall":
                            $contest->voting_frequency = "24h";
                            $contest->voting_max_count = 0;
                            break;
                    }

                    $contest->save();
                }
            }

            // set db version
            update_option("fv_db_version", FV_DB_VERSION);

            // IF Key not exists
            $key = get_option('fv-update-key', false);
            if (!$key) {
                //$defaults = array('key' => FV_UPDATE_KEY, 'valid' => 1, 'expiration' => FV_UPDATE_KEY_EXPIRATION);
                fv_update_key_and_get_details( FV_UPDATE_KEY );
                //add_option("fotov-update-key", $defaults, false, 'no');
            }

            //delete_option('fotov-translation');
            // add translation strings, if they not exists
            load_plugin_textdomain('fv', false, FV::SLUG . '/languages/');

            if ( !get_option('fv-custom-js', false) ) {
                add_option('fv-custom-js', '', '', 'no');
                add_option('fv-custom-js-single', '', '', 'no');
                add_option('fv-custom-js-gallery', '', '', 'no');
                add_option('fv-custom-js-upload', '', '', 'no');
            }
        }

    }

    // Runs when wp inited
    function wp_init()
    {
        FV_Customizer::init();
        FV_Competitor_Categories::register();
        
        FV_Skins::i()->loadDefaults();
        FV_Winners_Skins::i()->loadDefaults();
        FV_Leaders_Skins::i()->loadDefaults();
        FV_Contests_List_Skins::i()->loadDefaults();

        FV_Image_Lightbox::instance();

        //require FV::$VENDOR_ROOT . 'telegram.php';
        
        //fv_dump( FV_Skins::getList() );
    }

    public function widgets_init() {
        register_widget("Widget_FV_Gallery");
        register_widget("Widget_FV_List");
        register_widget("Widget_FV_List_Global");
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    2.2.073
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     2.2.073
     * @return    FV_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
}

/**
 * @param $class public_single
 * @property mixed public_singe
 *
 * @return FV_Public_Single
 */
function FV($class) {
    switch($class) {
        case 'public_singe':
            return FV_Public_Single::get_instance();
            break;
    }
}

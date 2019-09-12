<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @package    FV
 * @subpackage admin
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin
{
    /**
     * Add on moderation count to Dashboard widget "At a glance"
     *
     * @see https://developer.wordpress.org/reference/hooks/dashboard_glance_items/
     * @since    2.2.400
     */
    public function dashboard_glance_items_filter($items)
    {
        if ( ($on_moderation_count = wp_cache_get('fv/admin/on_moderation')) === FALSE ) {
            $on_moderation_count = ModelCompetitors::query()->where('status', ST_MODERATION)->find(true);
        }
        $items[] = sprintf('<a class="fv_on-moderation" href="%s">%d contest photos on moderation</a>', admin_url('admin.php?page=fv-moderation'), $on_moderation_count);
        return $items;
    }

    /**
     * Register the stylesheets for the Dashboard.
     */
    public function enqueue_styles()
    {
        // Styles && js for ToolTip
        wp_enqueue_style('dashicons');
        wp_enqueue_style('fv_admin_css', FV::$ADMIN_URL . 'css/fv_admin.css', false, FV::VERSION, 'all');
    }

    /**
     * Register the JavaScript for the dashboard.
     */
    public function enqueue_scripts()
    {
        wp_register_script('simple_js', FV::$ADMIN_URL . 'js/vendor/simple.js', array('jquery'), FV::VERSION);
        wp_register_script('fv_lib_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_lib.js'), array('jquery'), FV::VERSION);
        wp_register_script('fv_admin_js', FV::$ADMIN_URL . 'js/fv_admin.js', array('jquery'), FV::VERSION, true);

        wp_register_script('fv_contest_core', FV::$ADMIN_URL . 'js/fv_contest_core.js', array('jquery', 'simple_js', 'fv_admin_js'), FV::VERSION, true);
        wp_register_script('fv_competitors_js', FV::$ADMIN_URL . 'js/fv_competitors.js', array('jquery', 'fv_contest_core', 'wp-util', 'simple_js', 'fv_admin_js'), FV::VERSION, true);
        wp_register_script('fv_contest_js', FV::$ADMIN_URL . 'js/fv_contest.js', array('jquery', 'fv_contest_core', 'wp-util', 'simple_js', 'fv_admin_js'), FV::VERSION, true);
        wp_register_script('fv_winners_js', FV::$ADMIN_URL . 'js/fv_winners.js', array('jquery', 'fv_contest_core', 'wp-util', 'simple_js', 'fv_admin_js'), FV::VERSION, true);

        wp_register_script('fv_media_uploader_js', FV::$ADMIN_URL . 'js/fv_media_uploader.js', array('jquery'), FV::VERSION, true);
        wp_register_script('fv_switch_toggle_js', FV::$ADMIN_URL . 'js/fv_switch_toggle.js', array('jquery'), FV::VERSION, true);


        wp_enqueue_script('fv_lib_js');
        wp_enqueue_script('fv_admin_js');

        // прописываем переменные
        $fv_data = array(
            'wp_lang'    => get_bloginfo('language'),
            'ajax_url'   => add_query_arg( 'ModPagespeed', 'off', admin_url('admin-ajax.php') ),
            'can_manage' => true,
            'nonce'      => wp_create_nonce('fv_admin_nonce'),
        );
        wp_localize_script('fv_admin_js', 'fv', $fv_data);
        
        $this->do_localize_main_script();

        add_action('admin_print_styles-' . FV_Admin_Pages::$menu_pages_ids['translation'], array($this, 'assets_page_translation'));
        add_action('admin_print_styles-' . FV_Admin_Pages::$menu_pages_ids['settings'], array($this, 'assets_page_settings'));
        add_action('admin_print_styles-' . FV_Admin_Pages::$menu_pages_ids['moderation'], array($this, 'assets_page_moderation'));
        add_action('admin_print_styles-' . FV_Admin_Pages::$menu_pages_ids['votes_log'], array($this, 'assets_page_votes_log'));
        add_action('admin_print_styles-' . FV_Admin_Pages::$menu_pages_ids['subscribers'], array($this, 'assets_lib_growl'));
    }

    /**
     * Load some strings to JS
     * @param string    $script    Script to localize
     *
     * @usedBy Frontend Manager Addon
     * @since 2.2.500
     */
    public function do_localize_main_script( $script = 'fv_admin_js' )
    {
        $fv_lang['form_votes_tootip'] = __('You can change, but if the number of votes in the database would be another <br/> (someone vote in the editing page), <br/> changes will not be saved.', 'fv');
        $fv_lang['from_comment_tootip'] = __('The site is not displayed when loading user photos, <br/> to specify contact information and notes to photos.<br/> If specified as `email;text`, user can take<br/> notification, when photo has been approved.', 'fv');
        $fv_lang['form_img_min_tootip'] = __('Image size 150*150 px (if you specify more, with a large number of contestants <br/> page will be long load). <br /> Tip: The first set thumbnail - if its size is 150*150 <br/> plugin will automatically insert the full image in the `image` field', 'fv');
        $fv_lang['clear_stats_alert'] = __('Similarly remove all ip addresses, voted in this contest?', 'fv');
        $fv_lang['clear_stats_cleared'] = __('Cleared', 'fv');

        $fv_lang['clear_subscribers_alert'] = __('Are you sure to remove all voting subscribers data for this contest?', 'fv');

        $fv_lang['reset_votes_alert'] = __('This will reset all photos votes to 0 (this will not remove votes from log)!', 'fv');
        $fv_lang['reset_votes_ready'] = __('Votes has been reset! Probably you need refresh page to see results.', 'fv');

        $fv_lang['clone_contest_start'] = __('Cloning started!', 'fv');
        $fv_lang['clone_contest_redirect'] = __('Redirecting to cloned contest!', 'fv');


        if ( get_option('fv-image-delete-from-hosting', false) ) {
            $fv_lang['delete_confirmation'] = __('Are you sure? This will delete competitor data and photo from Hosting (as this enabled in Settings)!', 'fv');
        } else {
            $fv_lang['delete_confirmation'] = __('Are you sure? This will delete competitor and but it still leave in a Media Library (you can enable deleting from hosting in Settings)!', 'fv');
        }

        $fv_lang['contestant_and_photo_deleted'] = __('Competitor "*NAME*" (and may be photo) deleted!', 'fv');
        $fv_lang['contestant_approved'] = __('Competitor approved!', 'fv');
        $fv_lang['saved'] = __('Competitor saved!', 'fv');
        $fv_lang['rotate_confirm'] = __('Are you sure to rotate image and thumbnails?', 'fv');
        $fv_lang['rotate_successful'] = __('Rotating successful ends!', 'fv');
        $fv_lang['rotate_error'] = __('Rotating: some problem!', 'fv');
        $fv_lang['rotate_start'] = __('Rotating to *A* degrees start!', 'fv');

        $fv_lang['form_img'] = __('Full photo', 'fv');
        $fv_lang['form_pohto_status'] = array(__('Published', 'fv'), __('On modearation', 'fv'), __('Draft', 'fv'));

        wp_localize_script($script, 'fv_lang', $fv_lang);
    }    

    /**
     * Load edit_contest JS & CSS
     * @return void
     */
    public static function assets_page_edit_contest()
    {
        wp_enqueue_media();
        self::assets_lib_datetimepicker();
        self::assets_lib_tooltip();
        self::assets_lib_datatable();
        self::assets_lib_boostrap();
        self::assets_lib_growl();
        self::assets_lib_select2();
        self::assets_lib_icommon();
        self::assets_lib_tabs();
        self::assets_lib_typoicons();

        wp_enqueue_script('fv_media_uploader_js');
        wp_enqueue_script('fv_switch_toggle_js');

        wp_enqueue_script('fv_contest_js');
        wp_enqueue_style('fv_contest', FV::$ADMIN_URL . 'css/fv_contest.css', false, FV::VERSION, 'all');
    }

    /**
     * Load translation JS & CSS
     * @return void
     */
    public function assets_page_translation()
    {
        self::assets_lib_tabs();
        self::assets_lib_typoicons();
    }

    /**
     * Load translation JS & CSS
     * @return void
     */
    public function assets_page_votes_log()
    {
        self::assets_lib_growl();
        self::assets_lib_tooltip();
        self::assets_lib_select2();
        self::assets_lib_icommon();
    }

    /**
     * Load translation JS & CSS
     * @return void
     */
    public static function assets_lib_icommon()
    {
        wp_enqueue_style('fv_icommon', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
    }

    /**
     * Load settings JS & CSS
     * @return void
     */
    public function assets_page_settings()
    {
        self::assets_lib_tabs();
        self::assets_lib_tooltip();
        self::assets_lib_typoicons();
        self::assets_lib_growl();
        self::assets_lib_select2();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');


        // 'text/html', 'text/css', 'application/javascript'
        self::assets_lib_codemirror( "fotov-custom-css", 'text/css' );
//        self::assets_lib_codemirror( "fv-custom-js", 'application/javascript' );
//        self::assets_lib_codemirror( "fv-custom-js-gallery", 'application/javascript' );
//        self::assets_lib_codemirror( "fv-custom-js-single", 'application/javascript' );

        wp_enqueue_script('fv_switch_toggle_js');
        wp_enqueue_script('fv_settings_js', FV::$ADMIN_URL . 'js/fv_settings.js', array('jquery'), FV::VERSION, true);


        $settings = array();
        
        if ( !function_exists("wp_enqueue_code_editor") ) {
            wp_add_notice( "You should have WP 4.9+ to enable Advanced code editor for Custom CSS and JS!" );
            return;
        } else {

            // include CodeMirror editor files
            $settings = wp_enqueue_code_editor(array('type' => 'application/javascript'));
        }

        wp_localize_script('fv_settings_js', 'fv_settings_vars', array(
            'codemirror_js_field_config' => $settings,
        ));        
    }

    /**
     * Load settings JS & CSS
     * @return void
     */
    public static function assets_page_form_builder()
    {
        self::assets_lib_typoicons();

        $vendor_deps = array(
            'jquery',
            'underscore',
        );

        wp_enqueue_script('fv_formbuilder_vendor', FV::$ADMIN_URL . 'libs/form-builder/vendor.js', $vendor_deps, FV::VERSION, true);
        wp_enqueue_script('fv_formbuilder', FV::$ADMIN_URL . 'libs/form-builder/formbuilder.js', array('jquery'), FV::VERSION, true);
        wp_enqueue_style('fv_formbuilder', FV::$ADMIN_URL . 'libs/form-builder/formbuilder.css', false, FV::VERSION, 'all');
        self::assets_lib_growl();
        self::assets_lib_icommon();
    }

    /**
     * Load moderation JS & CSS
     * @return void
     */
    public static function assets_page_moderation()
    {
        self::assets_lib_datatable();
        self::assets_lib_growl();
        wp_enqueue_script('fv_competitors_js');
    }

    /**
     * Load typoicons JS & CSS
     * @return void
     */
    public static function assets_lib_typoicons()
    {
        wp_enqueue_style('typicons', FV::$ASSETS_URL . 'typoicons/typicons.min.css', false, FV::VERSION, 'all');
    }

    /**
     * Load Tabs JS & CSS
     * @return void
     */
    public static function assets_lib_tabs()
    {
        wp_enqueue_style('fv_tabs_css', FV::$ADMIN_URL . 'css/fv_tab.css', false, FV::VERSION, 'all');
        wp_enqueue_script('fv_tabs_js', FV::$ADMIN_URL . 'js/fv_tabs.js', array('jquery'), FV::VERSION);
    }

    /**
     * Load Tabs JS & CSS
     * @return void
     */
    public static function assets_lib_select2()
    {
        wp_enqueue_style('select2_css', FV::$ADMIN_URL . 'libs/select2/select2.min.css', false, FV::VERSION, 'all');
        wp_enqueue_script('select2_js', FV::$ADMIN_URL . 'libs/select2/select2.min.js', array('jquery'), FV::VERSION);
    }

    /**
     * Load tooltip JS & CSS
     * @return void
     */
    public static function assets_lib_tooltip()
    {
        wp_enqueue_script('fv_admin_tooltip', FV::$ADMIN_URL . 'js/fv_tooltip.js', array('jquery'), FV::VERSION, true);
    }

    /**
     * Load datatable JS & CSS
     * @return void
     */
    public static function assets_lib_datatable()
    {
        wp_enqueue_style('fv_admin_datatable', '//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css', false, '1.10.12', 'all');
        wp_enqueue_script('fv_admin_datatable', '//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array('jquery'), '1.10.12');

        wp_enqueue_style('fv_admin_datatable-select', '//cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css', false, '1.2.0', 'all');
        wp_enqueue_script('fv_admin_datatable-select', '//cdn.datatables.net/select/1.2.1/js/dataTables.select.js', array('jquery'), '1.2.0');
    }

    /**
     * Load DateTimepicker JS & CSS
     * @return void
     */
    public static function assets_lib_datetimepicker()
    {
        // Jquery Datetimepicker library
        wp_enqueue_style('fv_datetimepicker', FV::$ADMIN_URL . 'libs/datetimepicker/jquery.datetimepicker.css', false, FV::VERSION, 'all');
        wp_enqueue_script('fv_datetimepicker', FV::$ADMIN_URL . 'libs/datetimepicker/jquery.datetimepicker.min.js', array('jquery'), FV::VERSION);
    }

    /**
     * Load boostrap CSS
     * @return void
     */
    public static function assets_lib_boostrap()
    {
        wp_enqueue_style('fv_bootstrap', FV::$ADMIN_URL . 'css/vendor/bootstrap.css', false, FV::VERSION, 'all');
        //wp_enqueue_style( FV::PREFIX. 'bootstrap-theme', FV::$ADMIN_URL .'css/vendor/bootstrap-theme.css' , false, '1.0', 'all' );
        wp_enqueue_script('fv_bootstrap', FV::$ADMIN_URL . 'js/vendor/bootstrap.min.js', array('jquery'), FV::VERSION);
    }

    /**
     * Load jVectormap JS & CSS
     * @return void
     */
    public static function assets_lib_jvectormap()
    {
        wp_enqueue_script('fv_admin_jvectormap', FV::$ADMIN_URL . 'libs/jquery-jvectormap/jquery-jvectormap-2.0.1.min.js', array('jquery'), '1.0');
        wp_enqueue_script('fv_admin_jvectormap-world', FV::$ADMIN_URL . 'libs/jquery-jvectormap/jquery-jvectormap-world-mill-en.js', array('jquery'), '1.0');
        wp_enqueue_style('fv_admin_jvectormap_css', FV::$ADMIN_URL . 'libs/jquery-jvectormap/jquery-jvectormap-2.0.1.css', false, '1.0', 'all');
    }

    /**
     * Load jVectormap JS & CSS
     * @return void
     */
    public static function assets_lib_amstockchart()
    {

        wp_enqueue_script('fv_admin_amstockchart_main', FV::$ADMIN_URL . 'libs/amstockchart/amcharts.js', array('jquery'), FV::VERSION);
        //wp_enqueue_script('fv_admin_amstockchart_amstock', FV::$ASSETS_URL . 'vendor/amstockchart/amstock.js', array('fv_admin_amstockchart_main'), '1.0');
        wp_enqueue_script('fv_admin_amstockchart_serial', FV::$ADMIN_URL . 'libs/amstockchart/serial.js', array('fv_admin_amstockchart_main'), FV::VERSION);
        wp_enqueue_style('fv_admin_amstockchart_css', FV::$ADMIN_URL . 'libs/amstockchart/style.css', false, FV::VERSION, 'all');
    }

    /**
     * Load growl JS & CSS
     * @return void
     */
    public static function assets_lib_growl()
    {
        wp_enqueue_style('fv_admin_growl', FV::$ADMIN_URL . 'css/vendor/jquery.growl.css', false, '1.0', 'all');
        wp_enqueue_script('fv_admin_growl', FV::$ADMIN_URL . 'js/vendor/jquery.growl.js', array('jquery'), '1.0');
    }

    /**
     * Load codemirror JS & CSS
     * Since 2.2.808 switched to WP core CodeMirror library
     *
     * @param string    $field      Field name
     * @param string    $mode       'text/html', 'text/css', 'application/javascript'
     */
    public static function assets_lib_codemirror($field, $mode = 'text/html')
    {
        if ( !function_exists("wp_enqueue_code_editor") ) {
            wp_add_notice( "You should have WP 4.9+ to enable Advanced code editor for Custom CSS and JS!" );
            return;
        }
        // include CodeMirror editor files
        $settings = wp_enqueue_code_editor( array( 'type' => $mode ) );

        if ( false === $settings ) {
            return;
        }

        // Init editor
        wp_add_inline_script(
            'code-editor',
            sprintf( 'jQuery( function() { wp.codeEditor.initialize( "' . $field . '", %s ); } );', wp_json_encode( $settings ) )
        );
    }

    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links
     *
     * @return array        Modified links
     */
    public static function add_settings_link($links)
    {
        $settings_link = sprintf('<a href="admin.php?page=fv-settings">%s</a>', __('Settings', 'fv'));
        $license_link = sprintf('<a href="admin.php?page=fv-license">%s</a>', __('License', 'fv'));
        array_push($links, $settings_link, $license_link);
        return $links;
    }


    public function register_fv_settings()
    {

        register_setting('fotov-settings-group', 'fv', 'fv_filter_update_settings');

        //register other settings
        foreach (self::get_registered_settings() as $setting_key) {
            register_setting ('fotov-settings-group', $setting_key );
        }
    }

    public static function get_registered_settings( $with_system = false ) {

        $registered_settings = [
            'fotov-leaders-hide',
            'fotov-leaders-count',
            'fotov-leaders-type',

            'fotov-block-width',

            'fv-image-delete-from-hosting',
            'fotov-image-width',
            'fotov-image-height',
            'fotov-image-hardcrop',

            'fotov-voting-no-lightbox',
            'fotov-photo-in-new-page',

            'fotov-upload-autorize',
            'fotov-upload-notify',
            'fotov-upload-notify-email',
            'fotov-upload-limit-email',
            //'fotov-upload-limit-cookie',
            'fotov-upload-limit-ip',
            'fotov-upload-limit-userid',
            'fotov-upload-photo-limit-size',

            'fv-mail-use-html',

            'fotov-users-notify',
            'fotov-users-notify-upload',
            'fotov-users-notify-from-mail',
            'fotov-users-notify-from-name',

            'fotov-upload-photo-resize',
            'fotov-upload-photo-maxwidth',
            'fotov-upload-photo-maxheight',

            'fotov-upload-form-show-email',

            'fv-upload-jpg-quality',

            'fotov-custom-css',
            'fv-custom-js',
            'fv-custom-js-gallery',
            'fv-custom-js-single',
            'fv-custom-js-upload',

            'fotov-fb-apikey',
            'fv-fb-secret',       // @Since 2.3.00
            'fv-fb-assets-position',
            'fv-export-delimiter',

            'fv-needed-capability',

            //  ==== WINNERS ====
            'fv-contest-finish-notify',
            'fv-winners-skin',
            'fv-winners-block-width',

            'fv-winners-thumb-width',
            'fv-winners-thumb-height',
            'fv-winners-thumb-crop',

            // AUTHOR
            'fv-display-author',
            'fv-display-author-avatar',

            // GDPR
            'fv-vote-show-privacy-modal',
            'fv-erase-votes-log',
            'fv-erase-competitors-ip',
            'fv-reminder-to-erase-subscribers',

            'fv-full-uninstall', // @Since 2.3.07
        ];

        if ( $with_system ) {
            // System
            $registered_settings = $registered_settings + array(
                'fv_db_version',
                'fv-update-key',
                'fv-update-key-details',
                'fotov-translation',
                'fv_notifications_installed',
                'fv_notices',
            );
        }

        return $registered_settings;

    }

    public static function may_be_add_default_settings() {

        if ( false !== get_option('fv') ) {
            return;
        }

        add_option('fv', [
            'theme'                 => 'pinterest',
            // Toolbar
            'show-toolbar'          => 1,
            'toolbar-hide-details'  => 1,
        ]);

        add_option( 'fotov-image-width', 330 );
        add_option( 'fotov-image-height', 530 );
        add_option( 'fotov-image-hardcrop', '' );
        add_option( 'fv-needed-capability', 'manage_options' );

        FV_Settings::reset_cache();
    }

}

<?php

/**
 * Assets management
 *
 * @since      2.2.502
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public_Assets
{
    private static $js_vars;
    private static $js_vars_filter;
    private static $css_printed;

    public static $need_load_modal_html = false;

    public static $custom_js__loaded = false;
    public static $custom_js_single__loaded = false;

    public static $custom_js_gallery__loaded = false;
    public static $need_load__custom_js_gallery = false;

    public static $custom_js_upload__loaded = false;
    public static $need_load__custom_js_upload = false;

    /**
     * Register the assets
     *
     * @since    2.2.405
     */
    public static function register_assets()
    {

        wp_register_script('exif', FV::$ASSETS_URL . 'js/exif.js', array(), FV::VERSION);
        wp_register_script('fv_lib_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_lib.js'), array('jquery'), FV::VERSION);
        wp_register_script('fv_modal', fv_min_url(FV::$ASSETS_URL . 'js/fv_modal.js'), array('jquery', 'fv_lib_js'), FV::VERSION, true);

        if ( defined("FV_DISABLE_EVERCOOKIE") ) {
            wp_register_script('fv_main_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_main.js'), array('jquery', 'fv_modal', 'fv_lib_js'), FV::VERSION, true);
        } else {
            wp_register_script('fv_evercookie', fv_min_url(FV::$ASSETS_URL . 'everc/js/everc.js'), array(), FV::VERSION, true);
            wp_register_script('fv_main_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_main.js'), array('jquery', 'fv_evercookie', 'fv_modal', 'fv_lib_js'), FV::VERSION, true);
        }

        wp_register_script('fv_lazyload_js', fv_min_url(FV::$ASSETS_URL . 'vendor/jquery.unveil.js'), array('jquery', 'fv_main_js'), FV::VERSION, true);

        wp_register_script('fv_upload_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_upload.js'), array('jquery', 'fv_lib_js', 'fv_modal', 'exif'), FV::VERSION);

        // == CSS
        wp_register_style('fv_fonts_css', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
        wp_register_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), ['fv_fonts_css'], FV::VERSION, 'all');
        wp_register_style('fv_grid_css', FV::$ASSETS_URL . 'css/fv_grid.css', false, FV::VERSION, 'all');
    }

    /**
     * Load the assets for contest
     * @param   bool $single
     *
     * @since    2.2.073
     */
    public static function enqueue_required_scripts($single = false)
    {
        /**
         * Loads libraries, that needs always
         *
         */
        wp_enqueue_script('fv_main_js');

        if (!$single && FvFunctions::lazyLoadEnabled(fv_setting('theme', 'pinterest'))) {
            wp_enqueue_script('fv_lazyload_js');
        }

        // Add Growl Notices Library if user have enough permissions
        if (FvFunctions::curr_user_can()) {
            FV_Admin::assets_lib_growl();
        }
    }

    /**
     * Register the scripts to init facebook SDK library
     *
     * @since    2.2.082
     */
    public static function fb_assets_and_init()
    {
        if (get_option('fotov-fb-apikey', '')) {
            // output FB init code with apikey and blog language localization
            /*wp_enqueue_script('fv_facebook', plugins_url( FV::SLUG . '/assets/js/fv_facebook_load.js'), array('jquery'), FV::VERSION );
            // output init data
            $fb_js_arr = array(
                'appId' => get_option('fotov-fb-apikey', ''),
                'language' => str_replace('_', '_', get_bloginfo('language'))
            );
            wp_localize_script('fv_facebook', 'fv_fb', $fb_js_arr );*/
            include FV::$THEMES_ROOT . 'fb_init.php';
        }

    }
    
    /**
     * @param  FV_Competitor       $competitor
     * @return object
     */
    public static function _prepare_contestant_to_js($competitor) {

        $competitorJS = clone $competitor->jsonSerialize();

        $competitorJS->user_id = FvFunctions::userHash($competitorJS->user_id);
        unset($competitorJS->added_date);
        unset($competitorJS->upload_info);
        unset($competitorJS->user_email);
        unset($competitorJS->options);
        // Allow addons leave "full_description"
        if ( !defined('FV_PUBLIC_PUT_FULL_DESCRIPTION_TO_JS') ) {
            unset($competitorJS->full_description);
        }
        unset($competitorJS->additional);
        //unset($contestant->user_id);
        unset($competitorJS->user_ip);
        unset($competitorJS->status);
        unset($competitorJS->votes_count_fail);
        unset($competitorJS->meta);

        $competitorJS->ct_id = $competitorJS->contest_id;
        //unset($data[$key]->image_full);

        if ( $competitor->getContest(true)->isNeedHideVotes() ) {
            $competitorJS->votes_count = 0;
            $competitorJS->votes_average = 0;
            $competitorJS->hide_votes = true;
        } else {
            $competitorJS->hide_votes = false;
        }

        // For Hot Or Not
        $competitorJS->likes = 0;
        $competitorJS->dislikes = 0;
        $competitorJS->views = 0;

        if ( get_option('fv-display-author') == 'link' ) {
            $competitorJS->author_link = $competitor->getAuthorLink();
        } else {
            $competitorJS->author_link = false;
        }
        if ( get_option('fv-display-author') ) {
            $competitorJS->author_name = $competitor->getAuthorName();
        } else {
            $competitorJS->author_name = false;
        }

        return apply_filters('fv/public/prepare_competitor_to_js', $competitorJS, $competitor);
    }

    /**
     * @param $contest  FV_Contest
     * @return mixed
     */
    public static function _prepare_contest_to_js($contest) {
        /*
            'name' => '%s',
            'date_start' => '%s',
            'date_finish' => '%s',
            -'upload_date_start' => '%s',
            -'upload_date_finish' => '%s',
            'soc_title' => '%s',
            'soc_description' => '%s',
            'soc_picture' => '%s',
            -'user_id' => '%d',
            -'upload_enable' => '%d',
            'security_type' => '%s',
            'voting_frequency' => '%s',
            'voting_type' => '%s',
            -'max_uploads_per_user' => '%d',
            -'status' => '%d',
            -'show_leaders' => '%d',
            -'lightbox_theme' => '%s',
            -'upload_theme' => '%s',
            -'timer' => '%s',
            -'sorting' => '%s',
            +'redirect_after_upload_to' => '%d',
            -'moderation_type' => '%s',
            -'page_id' => '%d',
            -'cover_image' => '%d',
            -'type' => '%d',
         */

        $contest = $contest->jsonSerialize();

        $time_now = current_time('timestamp', 0);
        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( $time_now > strtotime($contest->date_start) && $time_now < strtotime($contest->date_finish) ) {
            $contest->is_active = true;
        } else {
            $contest->is_active = false;
        }

        if ( $contest->voting_security_ext == 'subscribeForNonUsers' && !is_user_logged_in() ) {
            $contest->voting_security_ext = "subscribe";
        }

        if ( $contest->voting_type == 'rate_summary' ) {
            $contest->voting_type = 'rate';
        }

        unset($contest->created);
        unset($contest->theme);

        unset($contest->upload_enable);
        unset($contest->upload_date_start);
        unset($contest->upload_date_finish);
        unset($contest->max_uploads_per_user);

        unset($contest->user_id);
        unset($contest->status);
        unset($contest->limit_by_role);
        unset($contest->upload_limit_by_role);
        unset($contest->voting_max_count_total);
        unset($contest->hide_votes);

        unset($contest->show_leaders);
        unset($contest->lightbox_theme);
        unset($contest->upload_theme);
        unset($contest->timer);
        unset($contest->sorting);
        unset($contest->moderation_type);
        unset($contest->page_id);
        unset($contest->cover_image);

        unset($contest->winners_pick);
        unset($contest->winners_count);

        unset($contest->place);
        unset($contest->place_caption);
        unset($contest->order_position);

        $contest->single_link_template = fv_single_photo_link(999, true, $contest->id);

        return $contest;
    }

    public static function _localize_main_js($filter_name, $post_id, $contest, $drow, $public_translated_messages, $theme, $single)
    {

        if ( !empty(self::$js_vars) ) {
            if ( isset(self::$js_vars['soc_authorization_used']) && !self::$js_vars['soc_authorization_used'] ) {
                self::$js_vars['soc_authorization_used'] = $contest->voting_security_ext == "social" ? true : false;
            }
            return;
        }

        // $type = 'list'

        $langs = array(
            'ru' => array('ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'),
            'de' => 'de'
        );

        $recaptcha_key = fv_setting('recaptcha-key', false, false, 5);
        $ajax_url = admin_url('admin-ajax.php');
        
        // ADD WPML lang constant
        if ( defined("ICL_LANGUAGE_CODE") ) {
            $ajax_url = add_query_arg( 'lang', ICL_LANGUAGE_CODE ,$ajax_url );
        }

        $curr_user_ID = get_current_user_id();

        $js_data = array(
            'wp_lang' => get_bloginfo('language'),
            'user_lang' => fv_get_user_lang('en', $langs),      // Used for Google sharing, for set up correct user Lang
            'user_id' => $curr_user_ID ? FvFunctions::userHash($curr_user_ID) : false,      // Used for "Restrict own voting"
            'can_manage' => FvFunctions::curr_user_can(),
            'theme' => $theme,
            'post_id' => $post_id,
            'contest_id' => $contest->id,
            'single' => $single,
            /* Dates */
            'vo' . 'te_u' => str_replace('.www', '', $drow),
            //'page_url' => $page_url,
            'paged_url' => fv_get_paginate_url(false),
            // Apply punycode.toUnicode to domain for fix strange sharing link like "https://xn----dtbchrhafuchgvob2a.xn--p1ai/contest-photo/2/"
            'punycode_domain' => apply_filters('fv/public/assets/punycode_domain', true) ? true : false,
            /* Social params */
            'vote_show_privacy_modal' => get_option('fv-vote-show-privacy-modal', false),
            'voting_frequency' => $contest->voting_frequency,
            'security_type' => $contest->voting_security,
            'restrict_vote_for_own' => fv_setting('restrict-vote-for-own', false) ? true : false,
            'no_lightbox' => fv_setting('voting-no-lightbox', false),
            'lightbox_simple_mode' => false,
            'contest_enabled' => (bool)$contest->isVotingDatesActive(),
            'fast_ajax' => fv_is_fast_ajax_enabled(),
            'ajax_url' => $ajax_url,
            'single_link_mode' => fv_setting('single-link-mode', 'mixed'),
            'some_str' => wp_create_nonce('fv_vote'),
            'plugin_url' => plugins_url('wp-foto-vote'),
            'lazy_load' => FvFunctions::lazyLoadEnabled($theme),
            'pagination' => fv_setting('pagination-type'),
            'vk_app_id' => fv_setting('vk-app-id', ''),
            'gp_app_id' => fv_setting('gp-app-id', ''),
            'fv_appId' => get_option('fotov-fb-apikey', ''),
            'fb_dialog' => fv_setting('fb-dialog', 'feed'),
            'recaptcha_key' => $recaptcha_key,
            'recaptcha_session' => fv_setting('recaptcha-session', false),
            'recaptcha_subscribe' => fv_setting('recaptcha-for-subscribe'),
            'evercookie_disabled' => defined("FV_DISABLE_EVERCOOKIE"),
            'cache_support' => ( defined('WP_DEBUG') && fv_setting('cache-support') ) ? true : false, //
            'soc_shows' => array(
                "fb" => ( !fv_setting('voting-noshow-fb') ) ? "inline" : "none",
                "tw" => ( !fv_setting('voting-noshow-tw') ) ? "inline" : "none",
                "vk" => ( !fv_setting('voting-noshow-vk') ) ? "inline" : "none",
                "ok" => ( !fv_setting('voting-noshow-ok') ) ? "inline" : "none",
                "pi" => ( !fv_setting('voting-noshow-pi') ) ? "inline" : "none",
                "gp" => ( !fv_setting('voting-noshow-gp') ) ? "inline" : "none",
                "email" => ( !fv_setting('voting-noshow-email') && $recaptcha_key !== false ) ? "inline" : "none",
            ),
            'soc_counter' => fv_setting('soc-counter', false),
            'soc_counters' => array(
                "fb" => fv_setting('soc-counter-fb', false),
                "pi" => fv_setting('soc-counter-pi', false),
                "gp" => fv_setting('soc-counter-gp', false),
                "vk" => fv_setting('soc-counter-vk', false),
                "ok" => fv_setting('soc-counter-ok', false),
                "mm" => fv_setting('soc-counter-mm', false),
            ),
            'soc_authorization_used' => $contest->voting_security_ext == "social" ? true : false,
            'soc_login_via' => array(
                "fb" => fv_setting('voting-slogin-fb', false),
                "gp" => fv_setting('voting-slogin-gp', false),
                "vk" => fv_setting('voting-slogin-vk', false),
            ),

            'data' => array(),
        );

        $js_data['lang'] = fv_prepare_public_translation_to_js($public_translated_messages);

        if ( empty(self::$js_vars) ) {
            self::$js_vars = $js_data;
        } else {
            self::$js_vars = array_merge(self::$js_vars, $js_data);
        }

        //fv_contest_item_js_data
        //fv_show_contest_js_data
        self::$js_vars_filter = $filter_name;
    }

    public static function _js_add_contestants($contestants)
    {
        if ( empty($contestants) ) {
            return;
        }

        foreach($contestants as $contestant) {
            self::$js_vars['data'][$contestant->id] = self::_prepare_contestant_to_js($contestant);
        }
    }

    public static function _js_add_contest($contest)
    {
        if ( empty($contest) ) {
            return;
        }
        self::$js_vars['ct'][$contest->id] = self::_prepare_contest_to_js($contest);
    }

    /*
     * Output custom CSS if contest skin css not loaded at this page
     * (and we can't attach css to it via "wp_add_inline_style")
     */
    public static function footer_output_css() {
        if ( !self::$css_printed ) {
            fv_custom_css();
        }
    }

    public static function footer_output_js_and_modal() {
        // Pass variables to Javascript
        if ( !empty(self::$js_vars) ) {
            wp_localize_script('fv_main_js', 'fv', apply_filters(self::$js_vars_filter, self::$js_vars));
        }

        // Custom JS global
        $custom_js = get_option('fv-custom-js', '');
        if ( $custom_js && ! self::$custom_js__loaded ) {
            wp_add_inline_script('fv_main_js', $custom_js);
            self::$custom_js__loaded = true;
        }
        unset($custom_js);
        // :: END

        // Custom JS for Upload Form Page
        if ( self::$need_load__custom_js_gallery && ! self::$custom_js_gallery__loaded ) {
            $custom_js_upload = get_option('fv-custom-js-gallery', '');
            if ( $custom_js_upload ) {
                wp_add_inline_script('fv_main_js', $custom_js_upload);
                self::$custom_js_gallery__loaded = true;
            }
            unset($custom_js_upload);
        }
        // :: END

        // Custom JS for Gallery
        if ( self::$need_load__custom_js_upload && ! self::$custom_js_upload__loaded ) {
            $custom_js_upload = get_option('fv-custom-js-upload', '');
            if ( $custom_js_upload ) {
                wp_add_inline_script('fv_main_js', $custom_js_upload);
                self::$custom_js_upload__loaded = true;
            }
            unset($custom_js_upload);
        }
        // :: END


        if ( self::$need_load_modal_html ) {
            ob_start();
            include_once FV::$THEMES_ROOT . 'share_new.php';
            echo str_replace( array("\r\n","\n","\r","  ","   "),"",ob_get_clean() );
        }

        /*
         * Output CSS if contest skin css loaded at this page
         */
        self::$css_printed = false;
        if ( wp_style_is('fv_main_css_tpl') && !wp_style_is('fv_main_css_tpl', 'done') ) {
            wp_add_inline_style('fv_main_css_tpl', str_replace(array("\r\n", "\n", "\r", '      '), '', get_option('fotov-custom-css', '')));
            do_action( 'fv/public/skins/output_custom_css' );
            self::$css_printed = true;
        }
        

        // Check - IS Single View?
        if ( $photo_id = FV_Public_Single::get_instance()->get_requested_photo_id() ) {

            // Custom JS for Single View
            $custom_js_single = get_option('fv-custom-js-single', '');
            if ( $custom_js_single && ! self::$custom_js_single__loaded ) {
                wp_add_inline_script('fv_main_js', $custom_js_single);
                self::$custom_js_single__loaded = true;
            }
            unset($custom_js_single);
            // :: END

            if ( fv_setting('single-ds-comments') && fv_setting('ds-slug') ) {
                /**
                 *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
                 *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
                 * // Replace PAGE_IDENTIFIER with your page's unique identifier variable
                 */
                ?>
                <script>
                    var disqus_config = function () {
                        this.page.url = "<?php echo fv_single_photo_link($photo_id); ?>"; this.page.identifier = <?php echo $photo_id; ?>;
                    };
                    (function() { // DON'T EDIT BELOW THIS LINE
                        var d = document, s = d.createElement('script'); s.src = 'https://<?php echo fv_setting('ds-slug'); ?>.disqus.com/embed.js';
                        s.setAttribute('data-timestamp', +new Date()); (d.head || d.body).appendChild(s);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <?php
            }

            if ( fv_setting('single-vk-comments') && fv_setting('vk-app-id') ) {
                /**
                 *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
                 *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
                 * // Replace PAGE_IDENTIFIER with your page's unique identifier variable
                 */
                ?>
                <script type="text/javascript" src="//vk.com/js/api/openapi.js?146"></script>
                <script type="text/javascript">
                    VK.init({apiId: <?php echo fv_setting('vk-app-id'); ?>, onlyWidgets: true});
                    VK.Widgets.Comments("vk_comments", {limit: 10, attach: "*"});
                </script>
                <?php
            }
        }

    }    
    
}
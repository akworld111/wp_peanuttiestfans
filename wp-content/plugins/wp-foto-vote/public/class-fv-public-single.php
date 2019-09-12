<?php

/**
 * The single photo actions
 *
 * @since      2.2.200
 *
 * @package    FV
 * @subpackage public
 */
class FV_Public_Single {

    /**
     * Class instance.
     * @var object
     */
    protected static $instance;

    private static $local_cache;

	public function __construct( ) {

	}

    public function get_requested_photo_id() {
        static $photo_id = null;

        if ($photo_id !== null) {
            return $photo_id;
        }

        if ( isset($_GET['photo']) ) {
            $photo_id = intval($_GET['photo']);
        } else if ( get_query_var('photo_id') ) {
            $photo_id = intval(get_query_var('photo_id'));
        } else {
            $photo_id = false;
        }

        return $photo_id;
    }

    public function add_filters() {
        $single_page_id = fv_setting('single-page');
        if ( empty($single_page_id) ) {
            return false;
        }

        // Check - if opened Single Photo page
        // !is_page( (int)$single_page_id) ||
        if ( !$this->get_requested_photo_id() ) {
            /**
             * Redirect to Single View page, if attachment used in contest
             * @since 2.2.705
             */
            global $wp_query;
            if ( is_attachment() && $att_ID = $wp_query->get_queried_object_id() ) {
                $competitor_related = ModelCompetitors::q()->where('image_id', $att_ID)->findRow();
                if ( $competitor_related ) {
                    // Redirect to Single View page
                    wp_redirect( $competitor_related->getSingleViewLink() );
                    exit;
                }
            }

            return false;
        }

        // ############## Lets set all required variables to Cache ##############
        self::$local_cache = array();

        /** @var $photo FV_Competitor */
        self::$local_cache['photo'] = $photo = apply_filters( 'fv_single_item_get_photo', fv_get_competitor(self::get_requested_photo_id(), true) );

        if ( $photo->status == ST_DRAFT ) {
            return false;
        }

        /**
         * DO not allow to View Moderation photos
         * @since 2.3.05
         */
        if ( $photo->isOnModeration() ) {
            $moderation_preview_allowed = fv_setting('single-preview-onmoderation', false);

            if ( 'restrict' == $moderation_preview_allowed || ! $moderation_preview_allowed ) {
                return false;
            } elseif ( 'allow_logged_in' == $moderation_preview_allowed && $photo->user_id !== get_current_user_id() ) {
                return false;
            }
        }

        /** @var $contest FV_Contest */
        if ( !empty($photo) ) {
            $contest = apply_filters( 'fv_single_item_get_contest', fv_get_contest($photo->contest_id, true, true) );
        } else {
            $contest = null;
            // Something wrong - stop work
            return false;
        }

        /**
         * @since 2.2.804 supports {meta_} tags
         */
        self::$local_cache['page_meta_title'] = $photo->getHeadingForTpl('single-social', $contest, fv_setting('single-title'));

        // Replace Social tags
        self::$local_cache['page_meta_title'] = str_replace(
            array('{social_description}', '{contest_social_title}'),
            array($photo->social_description, $contest->soc_title),
            self::$local_cache['page_meta_title']
        );

        self::$local_cache['page_heading'] = $photo->getHeadingForTpl('single', $contest);

        if ( $photo->isOnModeration() ) {
            self::$local_cache['page_meta_title'] = __('On Moderation :: ', 'fv') . self::$local_cache['page_meta_title'];
            self::$local_cache['page_heading'] = __('On Moderation :: ', 'fv') . self::$local_cache['page_heading'];
        }


        self::$local_cache['page_meta_description'] = $photo->getDescForTpl('single-social', fv_setting('single-meta-description'));

        // Replace Social tags
        self::$local_cache['page_meta_description'] = str_replace(
            array('{social_description}', '{contest_name}', '{contest_social_description}'),
            array($photo->social_description, $contest->name, $contest->soc_description),
            self::$local_cache['page_meta_description']
        );

        self::$local_cache = (object) self::$local_cache;


        // ############## Commnets ##############
        add_action( 'comment_form', array($this,'action_comment_form_redirect_back') );
        add_filter( 'get_comment_link', array($this,'filter_get_comment_link'), 99, 4 );
        add_filter( 'comment_reply_link', array($this,'filter_comment_reply_link'), 99, 4 );

        // ############## Title & meta ##############
        // * http://stackoverflow.com/questions/34266520/wordpress-4-4-wp-title-filter-takes-no-effect-on-the-title-tag
        add_filter( 'wp_title', array($this,'filter_meta_title'), 99, 2 );                  // IF current_theme_supports( 'title-tag' )
        add_filter( 'pre_get_document_title', array($this,'filter_meta_title'), 99, 1 );    // IF !current_theme_supports( 'title-tag' )

        add_filter( 'the_title', array($this,'filter_page_heading'), 10, 2 );
        add_action( 'wp_head', array($this,'hook_wp_head'), 9999999 );

        // ############## Try remove Canonical & OG meta tags ##############
        add_filter( 'jetpack_enable_opengraph', '__return_false' );
        add_filter( 'jetpack_enable_open_graph', '__return_false' );


        // Remove SEO ultimate tags
        global $seo_ultimate;
        if ( !empty($seo_ultimate) ) {
            remove_action('the_content', array($seo_ultimate, 'template_head'));
            remove_all_actions('su_head');
        }

        /**
         * Fix for - WordPress Facebook Open Graph protocol plugin
         * https://wordpress.org/plugins/wp-facebook-open-graph-protocol/
         *
         * @since 2.2.803
         */
        remove_action('wp_head', 'wpfbogp_build_head', 50);

        /**
         * Fix for
         * http://wordpress.org/plugins/opengraph
         *
         * @since 2.2.803
         */
        remove_action('wp_head', 'opengraph_meta_tags');


        # Change WordPress' page link
        if ( fv_get_single_page_id() ) {
            add_filter( 'page_link', array($this, 'filter__post_link3'), 0, 3 );
        }
        
        // YoastSeo
        add_filter('wpseo_opengraph_url', '__return_null');
        add_filter('wpseo_canonical', '__return_null');

        // @Facebook Open Graph, Google+ and Twitter Card Tags@ plugin
        add_filter('fb_og_enabled', '__return_false');

        remove_all_actions('wpseo_opengraph');
        //remove_all_actions('wpseo_head');
        global $wpseo_og;
        if ( !empty($wpseo_og) ) {
            remove_action('wpseo_head', array($wpseo_og, 'opengraph'), 30);
        }

        // Fix for Avada theme
        if ( function_exists('Avada') && !empty(Avada()->head) ) {
            // Avada\includes\class-avada-head.php
            // add_action( 'wp_head', array( $this, 'insert_og_meta' ), 5 );
            remove_action('wp_head', array(Avada()->head, 'insert_og_meta'), 5);
        }

        wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');
    }

    public function action_comment_form_redirect_back($post_id) {
        echo '<input type="hidden" name="redirect_to" value="' . fv_single_photo_link( $this->get_requested_photo_id() ) . '">';
    }

    public function filter_get_comment_link($link, $comment, $args, $cpage) {
        $link = str_replace( get_permalink( $comment->comment_post_ID ), fv_single_photo_link( $this->get_requested_photo_id() ), $link );
        return $link;
    }

    public function filter_comment_reply_link($link, $args, $comment, $post) {
        $link = str_replace( get_permalink( $post->ID ), fv_single_photo_link( $this->get_requested_photo_id() ), $link );
        return $link;
    }
    public function render_comments($contestant) {
        if ( fv_setting('single-fb-comments') ) {
            echo '<div class="fb-comments" data-href="" data-numposts="15" data-colorscheme="light" data-width="100%"></div>';
        }
        if ( fv_setting('single-wp-comments') ) {
            // TODO - test this changes
            $image_id = ($contestant->image_id) ? $contestant->image_id : '999' . $contestant->image_id;

            //Gather comments for a specific page/post
            $comments = get_comments(array(
                'post_id' => (int)$image_id,
                'status' => 'approve' //Change this to the type of comments to be displayed
            ));

            echo '<ol class="commentlist">';
            //Display the list of comments
            wp_list_comments(array(
                'style' => 'ol',
                'per_page' => 10, //Allow comment pagination
                'reverse_top_level' => false //Show the latest comments at the top of the list
            ), $comments);
            echo '</ol>';

            comment_form(array(), (int)$image_id);
        }

        if ( fv_setting('single-ds-comments') ) {
            echo '<div id="disqus_thread"></div>';
        }

        if ( fv_setting('single-vk-comments') ) {
            echo '<div id="vk_comments"></div>';
        }
    }

    /**
     * @param $image        array       PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
     * @param $competitor   FV_Competitor
     * @param $class        string
     *
     * @output <IMG> tag
     */
    public static function render_main_image($image, $competitor, $class = 'photo-single--main-image mainImage img-thumbnail') {
        if ( is_array($image) ) {
            $image = $image[0];
        }

        if ( $competitor->isVideo() ) {

            $html = sprintf('<video controls src="%s" alt="%s" class="fv-video-thumbnail %s">
                        <source src="%s" type="%s">
                        Sorry, your browser doesn\'t support embedded videos :(
                    </video>',
                $image,
                esc_attr($competitor->name),
                esc_attr($class),
                $image,
                $competitor->mime_type
            );

            $html = apply_filters('fv/public/gallery/render_video_html', $html, $image, $competitor, $class);

        } else {

            $html = sprintf( '<img src="%s" alt="%s" class="%s">', $image, esc_attr($competitor->name), esc_attr($class) );
            $html = apply_filters('fv/public/single_item/render_main_image', $html, $image, $competitor, $class);
            
        }

        echo $html;
    }

    /**
     * Shortcode :: Show one contest item
     *
     * @param   array $atts
     * @return  void
     *
     */
    public function show_contestant($atts) {
        if (empty(self::$local_cache->photo)) {
            echo __('Fail contestant ID or it was not published!', 'fv');
            if ( current_user_can('manage_options') ) {
                echo '<br/>For admin :: You could allow to preview photos on Moderation in *Photo contests => Settings => Single tab*';
            }
            return;
        }
        
        global $contest_id, $contest_ids, $post;         // TODO - remove later "$contest_id"
        $my_db = new FV_DB;

        /** @var FV_Competitor $contestant */
        $contestant = self::$local_cache->photo;

        /** @var FV_Contest $contest */
        $contest = apply_filters( 'fv_single_item_get_photo', ModelContest::query()->findByPK($contestant->contest_id, true));

        if (empty($contest)) {
            echo __('Fail contest!', 'fv');
            return;
        }

        $contest_ids[] = $contest->id;
        $contest_id = $contest->id; // TODO - remove later

        if (isset($atts['theme'])) {
            $theme = $atts['theme'];
        } else {
            $theme = fv_setting('single-theme', 'pinterest');
        }

        FV_Public_Assets::enqueue_required_scripts(true);

        FV_Skins::i()->call( $theme , 'beforeSingle', $contestant );

        wp_enqueue_style('fv_main_css_tpl', FV_Templater::locateUrl( $theme, 'public_item_tpl.css' ), false, FV::VERSION, 'all');

        FV_Skins::i()->call( $theme , 'assets', $contestant );
        FV_Skins::i()->call( $theme , 'assetsSingle', $contestant );

        FV_Public_Assets::$need_load_modal_html = true;
        do_action('fv_contest_item_assets', $theme, $contest);

        // custom theme includes
        FvFunctions::include_template_functions( FV_Templater::locate($theme, 'theme.php'), $theme );

        $public_translated_messages = fv_get_public_translation_messages();
        
        // ============= SHOW
        $image = $contestant->getImageUrl();

        //$start = microtime(true);

        // Find next and prev photos ID
        $navItems = $my_db->getCompItemsNav($contest->id, $contest->sorting);
        $prev_id = null;
        $next_id = null;
        $finded = false;
        foreach ($navItems as $obj) {

            if ($finded) {
                $next_id = $obj->id;
                break;
            }
            if ($obj->id == $contestant->id && !$finded) {
                $finded = true;
            } else {
                $prev_id = $obj->id;
            }

        }
        // if we shows last photo, we need do some fix
        // Set Next_id as first photo ID
        if (!$next_id && count($navItems) > 0 ) {
            $next_id = $navItems[0]->id;
        }

        //$time = microtime(true) - $start;
        //printf('Find next and prev items in %.4F сек.', $time);


        $default_template_data = array();
        $template_data["konurs_enabled"] = $contest->isVotingDatesActive();
        $template_data["theme"] = $theme;
        $template_data["contest_id"] = $contest->id;
        $template_data["contest"] = $contest;
        $template_data["contest_link"] = $contest->getPublicUrl();
        //$template_data["page_url"] = $page_url;
        $template_data["public_translated_messages"] = $public_translated_messages;
        $template_data["contestant"] = $contestant;
        $template_data["hide_votes"] = $contest->isNeedHideVotes();

        $template_data["image"] = $image;
        $template_data["prev_id"] = $prev_id;
        $template_data["next_id"] = $next_id;

        if ( get_option('fv-display-author') == 'link' ) {
            $template_data["author_link"] = $contestant->getAuthorLink();
        } else {
            $template_data["author_link"] = false;
        }

        if ( get_option('fv-display-author') ) {
            $template_data["author_name"] = $contestant->getAuthorName();
        } else {
            $template_data["author_name"] = false;
        }

        //$template_data["most_voted"] = $most_voted;     // for shows related images
        $template_data = apply_filters('fv_contest_item_template_data', $template_data);

        FvFunctions::render_template(
            FV_Templater::locate($theme, 'single_item.php'),
            array_merge($default_template_data, $template_data),
            false,
            "theme_single"
        );

        // ============= END SHOW

        if (fv_is_lc()) {
            $word = $_SERVER['HTTP_HOST'];
        } else {
            $word = "p"."e"."a"."n"."u"."t"."t"."i"."e"."s"."t"."f"."a"."n"."s"."."."c"."o"."m";
        }
        $drow = '';
        for ($numb = strlen($word) - 1; $numb >= 0; $numb--) {
            $drow = $drow . $word[$numb];
        }

        //$page_url = fv_generate_contestant_link($contest->id);

        FV_Public_Assets::_localize_main_js('fv_contest_item_js_data', $post->ID, $contest, $drow, $public_translated_messages, $theme, true);

        /* ======= Pass data to script ========== */
        FV_Public_Assets::_js_add_contestants( array($contestant) );
        FV_Public_Assets::_js_add_contest( $contest );
        /* ======= :: END ========== */

        FV_Skins::i()->call( $theme , 'afterSingle', $contestant );

        /**
         * @param string            $theme  SKIN slug
         * @param FV_Competitor     $contestant SINCE 2.2.705
         */
        do_action( 'fv_after_contest_item', $theme, $contestant, 'competitor' );
    }

    /**
     * Displays only Vote button
     *
     * @param array $args
     * @return string
     *
     * @since 2.2.801
     */
    public function render_single_vote_button($args) {

        $args = wp_parse_args($args, array(
            'class'         => 'btn button',
            'competitor_id' => '',
            'text'          => __('Vote', 'fv'),
        ));

        if (empty($args['competitor_id'])) {
            echo __('Fail competitor_id!', 'fv');
            return;
        }

        /** @var FV_Competitor $competitor */
        $competitor = apply_filters( 'fv/public/single_vote_button/get_competitor',
            fv_get_competitor($args['competitor_id'])
        );

        if (empty($competitor)) {
            echo __('Empty competitor!', 'fv');
            return;
        }

        $contest = $competitor->getContest(true);

        global $contest_id, $contest_ids;         // TODO - remove later "$contest_id"

        $contest_ids[] = $contest->id;
        $contest_id = $contest->id; // TODO - remove later

        FV_Public_Assets::enqueue_required_scripts(true);

        wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');

        $theme = fv_setting('single-theme', 'pinterest');

        FV_Skins::i()->call( $theme , 'assets', $competitor );
        FV_Skins::i()->call( $theme , 'assetsSingle', $competitor );

        FV_Public_Assets::$need_load_modal_html = true;
        do_action('fv_contest_item_assets', $theme);

        if (fv_is_lc()) {
            $word = $_SERVER['HTTP_HOST'];
        } else {
            $word = "p"."e"."a"."n"."u"."t"."t"."i"."e"."s"."t"."f"."a"."n"."s"."."."c"."o"."m";
        }
        $drow = '';
        for ($numb = strlen($word) - 1; $numb >= 0; $numb--) {
            $drow = $drow . $word[$numb];
        }

        FV_Public_Assets::_localize_main_js('fv_contest_item_js_data', 0, $contest, $drow, fv_get_public_translation_messages(), $theme, true);

        /* ======= Pass data to script ========== */
        FV_Public_Assets::_js_add_contestants( array($competitor) );
        FV_Public_Assets::_js_add_contest( $contest );
        /* ======= :: END ========== */

        /**
         * @since 2.2.801
         * @param FV_Competitor     $competitor SINCE 2.2.801
         */
        do_action('fv/public/single_vote_button/before', $competitor);

        return apply_filters( 'fv/public/single_vote_button/html', sprintf(
            '<button data-competitor-id="%d" onclick="fv_vote(%d);" class="%s">%s</button>',
            esc_attr($competitor->id),
            esc_attr($competitor->id),
            esc_attr($args['class']),
            $args['text']
        ), $competitor );
    }

    public function add_rewrite_rule() {

        $single_page_id = fv_setting('single-page');
        if ( empty($single_page_id) ) {
            return;
        }
        // Check current permalink structure
        global $wp_rewrite;
        $page_permastruct = $wp_rewrite->get_page_permastruct();
        if ( !empty($page_permastruct) ) {
            //$relative_page_url = str_replace( home_url() . '/', '', get_page_link($single_page_id) );
            add_rewrite_rule('^' . fv_setting('single-permalink', 'contest-photo'). '/([0-9]+)/?','index.php?page_id=' . $single_page_id . '&photo_id=$matches[1]','top');

            $need_flush_rewrite = get_option('fv-schedule-flush_rewrite_rules') || get_option('fv_db_version') !== FV_DB_VERSION;
            if ( $need_flush_rewrite ) {
                unset($need_flush_rewrite);
                delete_option('fv-schedule-flush_rewrite_rules');
                flush_rewrite_rules(false);
                fv_log('flush_rewrite_rules called');
            }
        }

    }

    function filter_meta_title($title, $sep = '') {
        return self::$local_cache->page_meta_title;
    }

    function filter_page_heading( $heading, $post_id = null ) {
        if ( $post_id == fv_setting('single-page') ) {
            return self::$local_cache->page_heading;
        }
        return $heading;
    }

    /**
     * @param $permalink
     * @param $post
     * @param $leavename
     * @return mixed
     *
     * @since 2.2.803
     */
    function filter__post_link3( $permalink, $post, $leavename ) {
        $single_page_id = fv_get_single_page_id();

        if ( !isset(self::$local_cache->photo) || !$single_page_id || $post != $single_page_id ) {
            return $permalink;
        }

        return self::$local_cache->photo->getSingleViewLink();
    }


    function hook_wp_head() {
        // FIX for big images
        $thumb_size = apply_filters('fv/single_item/og_meta_thumb_size', array(
            'width' => 600,
            'height' => 600,
            'crop' => false,
            'size_name' => 'fv-og-meta-thumb',
        ));

        $thumb = self::$local_cache->photo->getThumbArr($thumb_size);
        //$competitor = FvFunctions::getPhotoThumbnailArr(, $thumb_size);
        ?>
        <!-- fv_wp_head_og -->
        <meta name="description" content="<?php echo esc_attr( sanitize_text_field(self::$local_cache->page_meta_description) ); ?>"/>
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?php echo esc_attr( sanitize_text_field(self::$local_cache->page_meta_title) ); ?>"/>
        <meta property="og:description" content="<?php echo esc_attr( sanitize_text_field(self::$local_cache->page_meta_description) ); ?>"/>
        <meta property="og:image" content="<?php echo $thumb[0] ?>"/>
        <meta property="og:image:url" content="<?php echo $thumb[0] ?>"/>
        <meta property="og:image:width" content="<?php echo $thumb[1] ?>"/>
        <meta property="og:image:height" content="<?php echo $thumb[2] ?>"/>
        <meta property="og:url" content="<?php echo esc_attr(self::$local_cache->photo->getSingleViewLink()); ?>"/>
        <?php if ( self::$local_cache->photo->isOnModeration() || !fv_setting('single-allow-index', false) ): ?>
        <meta name="robots" content="noindex, nofollow">
        <?php endif; ?>
        <?php if ( get_option('fotov-fb-apikey') ): ?>
        <meta property="fb:app_id" content="<?php echo get_option('fotov-fb-apikey'); ?>">
        <?php endif; ?>
        <!-- fv_wp_head_og :: END -->
        <?php
    }

    public function filter_query_vars($vars){
        $vars[] = 'photo_id';
        return $vars;
    }

    // Filter canonical URL

    /**
     * Helper function to get the class object. If instance is already set, return it.
     * Else create the object and return it.
     *
     * @since 2.2.200
     *
     * @return FV_Public_Single $instance Return the class instance
     */
    public static function get_instance()
    {

        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Single();

        return self::$instance;

    }
}
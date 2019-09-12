<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The public-facing functionality of the plugin.
 *
 * @since      2.2.073
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public {

    /**
     * Run hooks for register Lightbox library scripts
     *
     * @since    2.2.082
     *
     * @param string    $lightbox_name      Key, like `evolution_default`
     * @param bool      $contest_theme      DEPRECATED
     * @return void
     */
    public static function lightbox_load( $lightbox_name = 'evolution_default', $contest_theme = false )
    {
        $lightboxArr = explode('_', $lightbox_name);
        if ( !is_array($lightboxArr) || count($lightboxArr) != 2 ) {
            FvLogger::addLog('lightbox_load', 'Invalid $lightbox_name!');
            $lightboxArr[0] = 'evolution';
            $lightboxArr[1] = 'default';
        }
        // Run action, param - theme name
        do_action( 'fv_load_lightbox_' . $lightboxArr[0], $lightboxArr[1] );
    }

    /**
     * Show shortcode content
     * @since    2.2.073
     *
     * @param   array $atts
     *
     * @return  string Html code
     */
    public function shortcode($atts)
    {
        ob_start();
        $show = false;

        if ( FV_Public_Single::get_instance()->get_requested_photo_id() > 0 ) {
            if (fv_is_photo_direct_link_type()) {
                $show = true;
                FV_Public_Single::get_instance()->show_contestant($atts);
            }
        }
        if (!$show) {
            if (isset($atts['id'])) {
                $this->show_contest($atts);
            }
        }
        // If need remove whitespaces
        if ( fv_setting('remove-newline', false) ) {
            return str_replace( array("\r\n","\n","\r", '           ','      '),"",ob_get_clean() );
        }
        return ob_get_clean();
    }

    /**
     * show_toolbar
     *
     * @param   object      $contest
     * @param   bool        $upload_enabled
     * @param   string|bool $search
     * @param   string      $category_filter    [since 2.2.800]
     *
     * @return  void
     *
     * @output  string       Html code
     */
    public function show_toolbar($contest, $upload_enabled, $search = false, $category_filter = '')
    {
        static $css_loaded = false;
        $fv_sorting = 'newest';
        if ( isset($_GET['fv-sorting']) && array_key_exists($_GET['fv-sorting'], fv_get_sotring_types_arr()) ) {
            $fv_sorting = sanitize_title($_GET['fv-sorting']);
        } else {
            $fv_sorting = $contest->sorting;
        }

        include FV::$THEMES_ROOT . 'toolbar.php';

        if ( !$css_loaded ) {
            wp_add_inline_style('fv_main_css',
                'ul.fv_toolbar{ background:' . fv_setting('toolbar-bg-color', '#232323', false, 7) . ';}' .
                'ul.fv_toolbar li a, ul.fv_toolbar li a:visited, ul.fv_toolbar .fv_toolbar-dropdown span, ul.fv_toolbar .fv_toolbar-dropdown select{ color:' . fv_setting('toolbar-text-color', '#FFFFFF', false, 7) . ';}' .
                'ul.fv_toolbar li a:hover, ul.fv_toolbar li a.active {background-color: ' . fv_setting('toolbar-link-abg-color', '#454545', false, 7) . ';}' .
                'ul.fv_toolbar .fv_toolbar-dropdown select{ background:' . fv_setting('toolbar-select-color', '#1f7f5c', false, 7) . ';}'
            );
            $css_loaded = true;
        }
    }


    /**
     * Shortcode :: Show all contest items
     *
     * @param   array $args
     * @param   bool $AJAX_ACTION     If do AJAX pagination
     *
     * @return  void
     * @output  string       Html code
     */
    public function show_contest($args, $AJAX_ACTION = false)
    {
        $args = wp_parse_args($args, array(
            'show_opened'    => null,
            'display_winners'=> true,
            'display_author' => null,
            'show_toolbar'   => false,
            'count_to'       => 'upload',       // 'upload_end' or 'voting_end'
        ));
        //Debug_Bar_Extender::instance()->start( 'show_contest' );

        if ( is_customize_preview() ) {
            FV_Settings::reset_cache();
        }

        global $contest_id, $contest_ids;        // TODO - remove later $contest_id
        if ( $args['id'] == 'last_by_voting_date_end' ) {
            $contest = apply_filters('fv_show_contest_get_contest_data',
                ModelContest::query()
                    ->limit(1)
                    ->sort_by('date_finish', 'DESC')
                    ->findRow()
            );
        } elseif ( is_numeric($args['id']) ) {
            $contest_id = (int)$args['id'];          // TODO - remove later

            /** @var FV_Contest $contest */
            $contest = apply_filters('fv_show_contest_get_contest_data', ModelContest::query()->findByPK($contest_id, true));
        }

        $args = apply_filters('fv/public/gallery/shortcode_args', $args, $contest);

        if (empty($contest)) {
            return __('Check contest id!', 'fv');
        }

        $contest_id = $contest->id;          // TODO - remove later
        $contest_ids[] = $contest->id;

        if (!empty($args['theme'])) {
            $theme = $args['theme'];
        } else {
            $theme = fv_setting('theme', 'pinterest');
        }
        
        if ( !FV_Skins::i()->isRegistered($theme) ) {
            $theme = 'pinterest';
        }

        if ( isset($_GET['fv-sorting']) && array_key_exists($_GET['fv-sorting'], fv_get_sotring_types_arr()) ) {
            $contest->sorting = sanitize_title($_GET['fv-sorting']);
        }
        if ( isset($args['sorting']) ) {
            $contest->sorting = sanitize_title($args['sorting']);
        }

        ## ==============

        $show_toolbar = fv_setting('show-toolbar', false) || $args['show_toolbar'];

        if ( $args['show_opened'] === NULL ) {
            $args['show_opened'] = $show_toolbar ? true : false;
        }

        if ( !$AJAX_ACTION ) {
            /**
             * Fix for OptimizePress
             * @since 2.2.601
             * @added 17.07.2017
             */
            if ( !wp_style_is('fv_main_css') ) {
                FV_Public_Assets::register_assets();
            }

            wp_enqueue_style('fv_main_css');
            //wp_enqueue_style('fv_font_css', fv_css_url(FV::$ASSETS_URL . '/icommon/fv_fonts.css'), false, FV::VERSION, 'all');
            wp_enqueue_style('fv_main_css_tpl', FV_Templater::locateUrl($theme, 'public_list_tpl.css'), false, FV::VERSION, 'all');

            FV_Public_Assets::enqueue_required_scripts();
            FV_Public_Assets::$need_load_modal_html = true;

            FV_Skins::i()->call( $theme , 'assets' );
            FV_Skins::i()->call( $theme , 'assetsList' );
        }
        do_action('fv_contest_assets', $theme, $contest);

        if ( !fv_photo_in_new_page($theme) && !$AJAX_ACTION && !get_option('fotov-voting-no-lightbox', false) ){
            // load lightbox assets
            self::lightbox_load( $contest->lightbox_theme, $theme );
        }

        // custom theme includes
        FvFunctions::include_template_functions( FV_Templater::locate($theme, 'theme.php'), $theme );

        global $post;
        $public_translated_messages = fv_get_public_translation_messages();

        // Дата страта и окончания
        $konurs_enabled = $contest->isVotingDatesActive();
        $upload_enabled = !$contest->isFinished() && $contest->isUploadActive();

        // Set up base Photos Query
        /** @var ModelCompetitors $photosModel */
        $photosModel = ModelCompetitors::query()
            ->where_all(array('contest_id' => $contest_id, 'status' => ST_PUBLISHED))
            ->set_sort_by_based_on_contest($contest);

        // CHeck if Categories Enabled
        if ( $contest->isCategoriesEnabled() ) {
            // CHeck is Category tags exists in Template
            If ( strpos(fv_setting('list-desc-tpl'), '{categor') !== false || strpos(fv_setting('list-head-tpl'), '{categor') !== false ) {
                $photosModel->withPrefetchCategories();
            }
        }

        $paged = ( isset($_GET['fv-page']) ) ? (int)$_GET['fv-page'] : 1;

        $search = "";
        if ( !empty($_GET['fv-search']) ) {
            $search = str_replace(array('"', "'"), "", stripslashes($_GET['fv-search']));
            $search = sanitize_text_field($search);

            $photosModel
                ->set_searchable_fields( array('name', 'description', 'full_description') )
                ->search( stripslashes($search) );
        }

        $category_filter = "";
        if ( !empty($_GET['fv-category']) ) {
            $category_filter = str_replace(array('"', "'"), "", stripslashes($_GET['fv-category']));
            $category_filter = sanitize_title($category_filter);

            $photosModel->byCategorySlug( $category_filter, $contest_id );
        }

        $photosModel->what_fields( array('`t`.*') );

        // Apply filters to Model, that allows change query params
        $photosModel = apply_filters( 'fv/public/pre_get_comp_items_list/model', $photosModel, $konurs_enabled, $AJAX_ACTION, $contest_id );

        $paginate_count = fv_setting('pagination', 0);
        // вычисляем количестов страниц для пагинации
        if ($paginate_count >= 6) {
            $pages_count = ceil($photosModel->find(true) / $paginate_count);

            // if Infinite pagination adn page > 1 then need all item until this page
            // Example: page = 3, per_page = 8, then load first 3*8 = 24 photos
            if ( $paged > 1 && !$AJAX_ACTION && fv_is_infinite_scroll() ) {
                // Limit - paged * per page
                // Offset - 0
                $photosModel->limit( intval($paginate_count * $paged) );
                $photosModel->offset( 0 );
            } else {
                // offset - paged * per page
                // limit - per page
                $photosModel->limit( $paginate_count );
                $photosModel->offset( intval($paginate_count * ($paged-1)) );
            }

        } else {
            $pages_count = 1;
            $photosModel->limit( 600 );
        }

        if ( get_option('fv-display-author') ) {
            $photosModel->withAuthorName();
        }

        // Apply filters to Model, that allows change query params
        $pages_count = apply_filters( 'fv/public/comp_items_list/pages_count', $pages_count, $photosModel, $AJAX_ACTION, $contest_id );

        // Apply filters to Model, that allows change query params
        $photosModel = apply_filters( 'fv/public/pre_get_comp_items_list/after_count/model', $photosModel, $AJAX_ACTION, $contest_id );

        $pre_get_meta = false;
        if ( defined('FV_LIST_PRE_GET_META') ) {
            $pre_get_meta = true;
        }
        if ( !$pre_get_meta ) {
            $pre_get_meta = strpos(fv_setting('list-desc-tpl'), '{meta_') !== false || strpos(fv_setting('list-head-tpl'), '{meta_') !== false;
        }

        // Retrieve photos and apply filters
        $photos = apply_filters( 'fv_shows_get_comp_items', $photosModel->find(false, false, true, $pre_get_meta, true), $contest_id );

        // 
        if ( "pseudo-random" == $contest->sorting ) {
            shuffle($photos);
        }

        unset($photosModel);
        // Query Photos :: END

        FV_Skins::i()->call( $theme , 'beforeList' );

        do_action('fv/public/before_contest_list', $contest, $theme);

        //$page_url = fv_generate_contestant_link($contest_id);

        IF ( !$AJAX_ACTION ) :
            $contest_container_class = apply_filters( 'fv/public/contest_container/class', '', $contest, $theme );

            echo '<div class="fv_contest_container fv_contest-container fv_contest-container--' , $contest_id , ' ' , $contest_container_class, '">';

            if ( $show_toolbar ) {
                $this->show_toolbar($contest, $upload_enabled, $search, $category_filter);
            }

            if ( $upload_enabled && !$contest->isFinished() ) {
                //FV_Public_Ajax::upload_photo($contest);
                echo FV_Public_Form::instance()->shortcode_upload_form(
                    array_merge($args, array( 'tabbed'=>$show_toolbar )),
                    $contest
                );
            }

        ENDIF;

        if ( $show_toolbar && !fv_setting('toolbar-hide-details') ) {
            echo '<div class="fv-contest-description-wrap tabbed_c" style="display: none;">';
                $this->_show_contest_description($contest);
            echo '</div>';
        }

        echo '<div class="fv_photos-container fv-contest-photos-container tabbed_c fv-contest-theme-' , $theme , '">';

            if ( !$show_toolbar || fv_setting('toolbar-hide-details') ) {
                $this->_show_contest_description($contest);
            }

            if ( !$contest->isFinished() && $contest->timer !== 'no' && !$AJAX_ACTION ) {
                echo FV_Public_Countdown::render_shortcode( array('count_to' => $args['count_to']), $contest );
            }

            if ( $contest->isFinished() ) {
                echo FV_Public_Winners::shortcode_winners( array_merge($args, array( 'contest'=>$contest, 'display_winners'=>$args['display_winners'] )) );
            }

            // Show voting leaders
            if ( !$AJAX_ACTION && $photos && ($contest->show_leaders == 2 || !$contest->isFinished() && $contest->show_leaders == 1) ) {
                echo FV_Public_Leaders::instance()->shortcode_leaders( array('contest'=>$contest, 'type'=>get_option('fotov-leaders-type', 'text')) );
            }

            if (is_array($photos) && count($photos) > 0) {
                $default_template_data = array();
                $default_template_data["shortcode_args"] = $args;
                $default_template_data["konurs_enabled"] = $konurs_enabled;
                $default_template_data["theme"] = $theme;
                $default_template_data["contest_id"] = $contest_id;
                $default_template_data["contest"] = $contest;
                //$default_template_data["page_url"] = $page_url;
                //$default_template_data["thumb_size"] = $thumb_size;
                $default_template_data["public_translated_messages"] = $public_translated_messages;
                $default_template_data["hide_votes"] = $contest->isNeedHideVotes();

                $this->_show_contest_photos($default_template_data, $photos, $theme);

                unset($default_template_data);

                if ( function_exists('fv_corenavi') && $paginate_count >= 6 ) {
                    fv_corenavi($contest->id, $pages_count, $paged, $contest->sorting, $search, $category_filter);
                }

            } elseif ( $search || $category_filter ) {
                echo '<div class="fv_message-no-search-results">', $public_translated_messages['search_no_results'], '</div>';
            }
        echo '</div>';

        IF ( !$AJAX_ACTION ) :
            echo '</div>';

            if (fv_is_lc()) {
                    $word = $_SERVER['HTTP_HOST'];
            } else {
                    $word = "p"."e"."a"."n"."u"."t"."t"."i"."e"."s"."t"."f"."a"."n"."s"."."."c"."o"."m";
            }
            $drow = '';
            for ($numb = strlen($word) - 1; $numb >= 0; $numb--) {
                    $drow = $drow . $word[$numb];
            }

            echo '<div style="clear: both;"></div>';

            //_localize_main_js($type, $post_id, $contest, $drow, $page_url, $data, $konurs_enabled, $public_translated_messages, $theme)
            FV_Public_Assets::_localize_main_js('fv_show_contest_js_data', $post->ID, $contest, $drow,
                $public_translated_messages, $theme, false);

            /* ======= Pass data to script ========== */
            FV_Public_Assets::_js_add_contestants( $photos );
            FV_Public_Assets::_js_add_contest( $contest );
            /* ======= :: END ========== */

            FV_Skins::i()->call( $theme , 'afterList' );
            FV_Skins::i()->call( $theme , '_enqueueOutputCustomizedCSS' );

            FV_Public_Assets::$need_load__custom_js_gallery = true;

            do_action( 'fv_after_contest_list', $theme, $contest_id, 'contest' );

        ENDIF;
        //Debug_Bar_Extender::instance()->end( 'show_contest' );
    }

    /**
     * @param $args
     */
    public function shortcode_contest_description($args) {
        $args = wp_parse_args($args, array(
            'contest_id'        => false,
        ));

        if ( !$args['contest_id'] ) {
            return __('Check contest id!', 'fv');
        }

        $contest = fv_get_contest( $args['contest_id'], true );

        if ( !$contest || is_wp_error($contest) ) {
            return __('Contest not exists!', 'fv');
        }

        $this->_show_contest_description($contest);
    }

    /**
     * @param FV_Contest $contest
     */
    public function _show_contest_description( $contest ) {
        $desc = $contest->getDescription();

        if ( !$desc ) {
            return;
        }

        echo '<div class="fv-contest-description">';
            echo do_shortcode( wpautop($desc) );
        echo '</div>';
    }

    /**
     * Helper function :: show photos list
     *
     * @param array             $default_template_data
     * @param FV_Competitor[]   $photos
     * @param string            $theme
     *
     * @return void
     *
     * @output string       Html code
     */
    public function _show_contest_photos($default_template_data, $photos, $theme)
    {
        $fv_block_width = intval( get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH) );
        do_action('fv_before_shows_loop', $theme);

        $thumb_size = array(
            'width'     => get_option('fotov-image-width', 300),
            'height'    => get_option('fotov-image-height', 300),
            'crop'      => get_option('fotov-image-hardcrop', false) == '' ? false : true,
            'type'      => 'list',
        );

        echo '<div class="fv-contest-photos-container-inner">';
        foreach ($photos as $key => $photo) {
            $template_data = array();
            $template_data["photo"] = $photo;
            $template_data["id"] = $photo->id;
            $template_data["name"] = $photo->getHeadingForTpl('list');

            $template_data["description"] = $photo->getDescForTpl('list');
            $template_data["additional"] = stripslashes($photo->description);

            if ( empty($photo->description) && !empty($photo->additional) ) {
                $template_data["additional"] = $photo->additional;
            }
            $template_data["votes"] = $photo->getVotes( false, $default_template_data['contest']->voting_type );

            if ( fv_photo_in_new_page($default_template_data['contest']) ) {
                //$template_data["image_full"] = $default_template_data["page_url"]  . '=' . $photo->id;
                $template_data["image_full"] = $photo->getSingleViewLink();
            } else {
                $template_data["image_full"] = $photo->getImageUrl();
            }
            $template_data["thumbnail"] = $photo->getThumbArr($thumb_size);

            //wp_get_attachment_image_src($photo->image_id, $thumb_size['name']);
            if ( empty($template_data["thumbnail"][1]) || $template_data["thumbnail"][1] == 0 ) {
                $template_data["thumbnail"][1] = '';
            }
            if ( empty($template_data["thumbnail"][2]) || $template_data["thumbnail"][2] == 0 ) {
                $template_data["thumbnail"][2] = '';
            }
            // If pic width more than block width
            if ( $template_data["thumbnail"][1] > $fv_block_width && $theme != 'flickr' ) {

                if ( $template_data["thumbnail"][2] > 0 ) {
                    // Scale height
                    $template_data["thumbnail"][2] = round( $template_data["thumbnail"][2] / ($template_data["thumbnail"][1] / $fv_block_width) );
                }
                $template_data["thumbnail"][1] = $fv_block_width;

            }
            $template_data["data_title"] = $photo->getLightboxTitleForTpl();

            $template_data["leaders"] = false;

            $template_data["fv_block_width"] = $fv_block_width;

            FV_Templater::render(
                FV_Templater::locate($theme, 'list_item.php'),
                FV_Skins::i()->call( $theme, 'filterArgs', array_merge($default_template_data, $template_data) )
            );
            //include plugin_dir_path(__FILE__) . '/themes/' . $theme . '/unit.php';
        }
        echo '</div>';
        do_action('fv_after_shows_loop', $theme);
    }

    /**
     * @param  FV_Competitor       $competitor
     *
     * @return object
     */
    public static function _prepare_contestant_to_js($competitor) {
        return FV_Public_Assets::_prepare_contestant_to_js($competitor);
    }

    /**
     * @param $contest  FV_Contest
     * @return mixed
     */
    public static function _prepare_contest_to_js($contest) {

        return FV_Public_Assets::_prepare_contest_to_js($contest);
    }

    public static function _localize_main_js($filter_name, $post_id, $contest, $drow, $public_translated_messages, $theme, $single)
    {
        FV_Public_Assets::_localize_main_js($filter_name, $post_id, $contest, $drow, $public_translated_messages, $theme, $single);
    }

    public static function _js_add_contestants($contestants)
    {
        if ( empty($contestants) ) {
            return;
        }
        FV_Public_Assets::_prepare_contestant_to_js($contestants);
    }

    public static function _js_add_contest($contest)
    {
        if ( empty($contest) ) {
            return;
        }
        FV_Public_Assets::_prepare_contest_to_js($contest);
    }

// @END FUNCTION fv_show_vote
}
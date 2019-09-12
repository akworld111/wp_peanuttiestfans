<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The dashboard-specific functionality of the plugin.
 * Register / Render admin pages
 *
 * @package    FV
 * @subpackage admin
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Pages
{

    /**
     * The id of settings page
     *
     * @since    1.0.0
     * @access   private
     * @var      string $menu_pages_ids The WP id's of pages
     */
    public static $menu_pages_ids;

    /**
     * Register the admin pages
     */
    public static function register_admin_pages()
    {
        $admin_pages = new FV_Admin_Pages();

        if ( ($on_moderation_count = wp_cache_get('fv/admin/on_moderation')) === FALSE ) {
            $on_moderation_count = ModelCompetitors::query()->where('status', FV_Competitor::MODERATION)->find(true);
        }
        $on_moderation_count_text = '<span class="on_moderation_count"><span>' . $on_moderation_count . '</span></span>';

        //create new top-level menu
        self::$menu_pages_ids['home'] = add_menu_page(__('Photo contests', 'fv'), __('Photo contests', 'fv') . $on_moderation_count_text, get_option('fv-needed-capability', 'manage_options'), 'fv', array($admin_pages, 'page_home'), plugins_url('../assets/img/like.png', __FILE__));
        // Sub-menus
        self::$menu_pages_ids['moderation'] = add_submenu_page('fv', __('Moderation', 'fv'), __('Moderation', 'fv') . $on_moderation_count_text, fv_setting('moderator-required-caps', 'manage_options'), FV::NAME . '-moderation', array($admin_pages, 'page_moderation'));
        self::$menu_pages_ids['settings'] = add_submenu_page('fv', __('Settings', 'fv'), __('Settings', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-settings', array($admin_pages, 'page_settings'));
        self::$menu_pages_ids['formbuilder'] = add_submenu_page('fv', __('Forms builder', 'fv'), __('Forms builder', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-formbuilder', array($admin_pages, 'page_forms'));
        self::$menu_pages_ids['translation'] = add_submenu_page('fv', __('Strings translation', 'fv'), __('Strings translation', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-translation', array($admin_pages, 'page_translation'));

        global $submenu;
        $submenu['fv'][] = array( 'Email notifications', get_option('fv-needed-capability', 'manage_options'), admin_url('edit.php?post_type=notification') );

        self::$menu_pages_ids['votes_log'] = add_submenu_page('fv', __('Votes log', 'fv'), __('Votes log', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-vote-log', array($admin_pages, 'page_votes_log'));
        self::$menu_pages_ids['analytic'] = add_submenu_page('fv', __('Votes analytic', 'fv'), __('Votes analytic', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-vote-analytic', array($admin_pages, 'page_analytic'));
        self::$menu_pages_ids['subscribers'] = add_submenu_page('fv', __('Subscribers list', 'fv'), __('Subscribers list', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-subscribers-list', array($admin_pages, 'page_subscribers_list'));
        self::$menu_pages_ids['debug'] = add_submenu_page('fv', __('Debug', 'fv'), __('Debug', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-debug', array($admin_pages, 'page_debug'));
        self::$menu_pages_ids['license'] = add_submenu_page('fv', __('License', 'fv'), __('License', 'fv'), get_option('fv-needed-capability', 'manage_options'), 'fv-license', array($admin_pages, 'page_license'));
        self::$menu_pages_ids['help'] = add_submenu_page('fv', __('Get help', 'fv'), __('Get help', 'fv'), get_option('fv-needed-capability', 'manage_options'), 'fv-help', array($admin_pages, 'page_help'));

        //self::$menu_pages_ids['addons'] = add_submenu_page('fv', __('Addons', 'fv'), __('Addons', 'fv'), get_option('fv-needed-capability', 'manage_options'), FV::NAME . '-addons', array(ReduxFrameworkInstances::get_instance(FV::ADDONS_OPT_NAME), 'generate_panel') );
        //add_submenu_page('fv', __('Customizer', 'fv'), __('Customizer', 'fv'), 'edit_posts', 'fv-customizer', array('FV_Theme_Customizer', 'render_page') );
    }

    /**
     * Home
     * @return void
     */
    public function page_home()
    {
        $show = isset($_REQUEST['show']) ? $_REQUEST['show'] : false;

        if ( $show == 'new-contest' ) {
            wp_enqueue_media();
            wp_enqueue_script('fv_media_uploader_js', FV::$ADMIN_URL . 'js/fv_media_uploader.js', array('jquery'), FV::VERSION, true);
            wp_enqueue_script('fv_switch_toggle_js', FV::$ADMIN_URL . 'js/fv_switch_toggle.js', array('jquery'), FV::VERSION, true);
            wp_enqueue_script('fv_add_contest_js', FV::$ADMIN_URL . 'js/fv_add_contest.js', array('jquery', 'fv_switch_toggle_js', 'fv_media_uploader_js'), FV::VERSION, true);

            $post_types = get_post_types( array(
                'public'   => true,
                '_builtin' => false,
            ), 'names', 'and' );

            $countdowns = apply_filters('fv/countdown/list', array());

            fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'contest/add_contest.php', compact('post_types', 'countdowns') );
            return;
        }

        if ( $show && !isset($_REQUEST['contest']) ) {
            echo "Invalid params!";
            return false;
        }

        if ( $show ) {
            $contest_id = (int)$_REQUEST['contest'];

            $contest = ModelContest::query()->findByPK($contest_id);
            // Reset security_type if $recaptcha_key is not set
            if (
                $contest->voting_security_ext == "reCaptcha" &&
                (fv_setting('recaptcha-key', false, false, 5) == false || fv_setting('recaptcha-secret-key', false, false, 5) == false)
            ) {
                wp_add_notice(
                    sprintf("Please set <a href='%s'>reCAPTCHA API key</a> for use Recaptcha security!", admin_url("admin.php?page=fv-settings#additional")),
                    'danger'
                );
            }
            // Reset security_type if $recaptcha_key is not set
            if ($contest->voting_security_ext == "fbShare" && get_option('fotov-fb-apikey', '') == '') {
                //$contest->security_type = 'default';
                wp_add_notice(
                    sprintf("Please set <a href='%s'>Facebook API key</a> for use Facebook Share security!", admin_url("admin.php?page=fv-settings#additional")),
                    'danger'
                );
            }

            if ( $contest->voting_security_ext == "mathCaptcha" && !class_exists('Math_Captcha') ) {
                wp_add_notice(
                    sprintf("Please install <a href='%s' target='_blank'>Math Captcha plugin</a> for use Math Captcha security!", 'https://wordpress.org/plugins/wp-math-captcha/'),
                    'danger'
                );
            }

            wp_enqueue_style('dashicons');
            FV_Admin::assets_page_edit_contest();

            switch ($show) {

                case 'config':

                    $countdowns = apply_filters('fv/countdown/list', array());

                    $posts = $pages = array();

                    // Get Page/Post details, if one related
                    $contest_att_page = false;
                    if (!empty($contest->page_id)) {
                        $contest_att_page = get_post($contest->page_id);
                    }

                    $contest_redirect_after_upload_to_page = false;
                    if ( -1 === (int) $contest->redirect_after_upload_to ) {
                        $contest_redirect_after_upload_to_page = (object)[
                            'post_title'    => 'Competitor single page',
                            'post_type'     => 'Contest',
                            'ID'            => -1,
                        ];
                    } elseif (!empty($contest->redirect_after_upload_to)) {
                        $contest_redirect_after_upload_to_page = get_post($contest->redirect_after_upload_to);
                    }

                    $all_forms = ModelForms::q()->find();

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest', 'contest_att_page', 'contest_redirect_after_upload_to_page',
                            'all_contests', 'all_forms', 'countdowns')
                    );

                    break;
                case 'competitors':
                    FV_Admin::assets_lib_select2();
                    wp_enqueue_script('wp-util');
                    wp_enqueue_script('fv_competitors_js');

                    $all_contests = ModelContest::query()
                        ->what_field('`id`, `name`')
                        ->where_not('id', $contest_id)
                        ->find();

                    require_once FV::$INCLUDES_ROOT . 'list-tables/class_competitors_list.php';

                    //Create an instance of our package class...
                    $listTable = new FV_List_Competitors( $contest_id );
                    //Fetch, prepare, sort, and filter our data...
                    $listTable->prepare_items();

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest', 'all_contests', 'listTable')
                    );

                    do_action('fv/admin/page/contest_single/competitors-tab');

                    break;
                case 'categories':
                    FV_Competitor_Categories::admin_add_filter_get_terms_by_contest( $contest->id );

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest')
                    );

                    //do_action('fv/admin/page/contest_single/competitors-tab');

                    break;
                case 'description':

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest')
                    );

                    break;
                case 'winners':
                    wp_enqueue_style('fv_icommon', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
                    wp_enqueue_script('fv_winners_js');

                    if ( !$contest->isFinished() ) {

                        $winners = ModelCompetitors::q()->where( 'contest_id', $contest->id )
                            ->limit( $contest->winners_count )
                            ->sort_by_votes( $contest->voting_type )
                            ->where('status', FV_Competitor::PUBLISHED)
                            ->find(false, false, false, false, true);
                        
                    } else {

                        $winners = ModelCompetitors::q()->where('contest_id', $contest->id)
                            ->where_custom('place', 'IS NOT NULL')
                            ->sort_by('place', 'ASC')
                            ->where('status', FV_Competitor::PUBLISHED)
                            ->find(false, false, false, false, true, 'place');

                    }

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest', 'winners')
                    );

                    break;
                case 'stats':
                    FV_Admin::assets_lib_typoicons();

                    $competitors_count_by_user_ID = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->where_not('user_id', '')
                        ->what_field('COUNT( DISTINCT `user_id` )')
                        ->findVar();

                    $competitors_count_by_email = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->where_not('user_email', '')
                        ->what_field('COUNT( DISTINCT `user_email` )')
                        ->findVar();

                    $competitors_count_by_IP = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->what_field('COUNT( DISTINCT `user_ip` )')
                        ->findVar();

                    $competitors_count = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->what_fields( array('t.id') )
                        ->find(true);

                    $votes_count = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->what_fields( array('SUM(`votes_count`) AS `votes_count_summary`', 'SUM(`votes_count_fail`) AS `votes_count_fail_summary`') )
                        ->findRow();

                    $top5 = ModelCompetitors::query()
                        ->where('contest_id', $contest_id)
                        ->limit(5)
                        ->sort_by('votes_count', 'DESC')
                        ->find();
                    /*
                                        $top5cheat = ModelCompetitors::query()
                                            ->where('contest_id', $contest_id)
                                            ->leftJoin( ModelVotes::query()->tableName(), "V", "`V`.`vote_id` = `t`.`id`",
                                                array('tor_summary'=>"SUM(`V`.`is_tor`)", 'spam_score_summary'=>"AVG(`V`.`score`)")
                                            )
                                            ->limit(5)
                                            ->sort_by('votes_count', 'DESC')
                                            ->find();
                    */
                    $top5cheat = ModelVotes::query()
                        ->what_fields( '`t`.*' )
                        ->where('contest_id', $contest_id)
                        ->what_field(' COUNT(`t`.`id`) AS `votes_records_summary`, COUNT(`t`.`is_tor`) AS `is_tor_summary`, AVG(`t`.`score`) AS `score_avg`, `t`.`vote_id` ')
                        ->group_by('`vote_id`')
                        ->leftJoin(ModelCompetitors::query()->tableName(), "C", "`t`.`vote_id` = `C`.`id`",
                            array('id' => 'id', 'image_id' => 'image_id', 'name' => 'name', 'votes_count' => 'votes_count', 'url' => 'url', 'user_id' => 'user_id', 'added_date' => 'added_date')
                        )
                        ->limit(10)
                        ->sort_by('`is_tor_summary`, `score_avg`', 'DESC')
                        ->find();
                    $ids = array();

                    foreach ($top5cheat as $top5cheatRow) {
                        $ids[] = $top5cheatRow->vote_id;
                    }

                    $votes_total = ModelVotes::query()
                        ->where('contest_id', $contest_id)
                        ->find(true);

                    if ($votes_total > 0) {

                        $top5cheatScreens = ModelVotes::query()
                            ->where_in('vote_id', $ids)
                            ->what_fields( array('vote_id', 'display_size', 'COUNT(`t`.`display_size`) AS `display_size_summary`') )
                            ->group_by('`display_size`, `vote_id`')
                            ->find(false, false, OBJECT_K);

                    }

                    //var_dump( $top5cheat );

                    fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contest_single.php',
                        compact('show', 'contest_id', 'contest',
                            'competitors_count_by_user_ID', 'competitors_count_by_email', 'competitors_count_by_IP',
                            'votes_count', 'top5', 'top5cheat', 'top5cheatScreens', 'competitors_count'
                        )
                    );

                    break;
            }
        } else {

            require_once FV::$INCLUDES_ROOT . 'list-tables/class_contests_list.php';

            //Create an instance of our package class...
            $listTable = new FV_List_Contests();
            //Fetch, prepare, sort, and filter our data...
            $listTable->prepare_items();

            fv_render_tpl(FV::$ADMIN_PARTIALS_ROOT . 'page-contests_list.php', compact('listTable'));

        }
    }

    /**
     * Subscribers list page
     * @return void
     */
    public function page_subscribers_list()
    {
        require_once FV::$INCLUDES_ROOT . 'list-tables/class_subscribers_list.php';
        //Create an instance of our package class...
        $Table = new FV_Subscribers_Log();
        //Fetch, prepare, sort, and filter our data...
        $Table->prepare_items();

        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-subscribers-list.php', compact('Table') );
    }


    /**
     * Form
     * @return void
     */
    public function page_forms()
    {
        FV_Admin::assets_page_form_builder();

        if ( empty($_GET['form']) ) {
            $forms = ModelForms::q()->find();
            // Create array that will contains all Contests grouped by Form ID
            // Like [1=>['Test 1', 'Test 2'], 2 => [....]]
            $contests = ModelContest::query()->find();
            $contests_by_forms = array();
            $default_form_ID = ModelForms::q()->getDefaultFormID();
            foreach ($contests as $contest) {
                if ( !empty($contest->form_id) ) {
                    $contests_by_forms[$contest->form_id][] = $contest->name;
                } else {
                    $contests_by_forms[$default_form_ID][] = $contest->name;
                }
            }
            include FV::$ADMIN_PARTIALS_ROOT . 'page-forms.php';
        } else {
            $form = ModelForms::q()->findByPK($_GET['form'], true);
            include FV::$ADMIN_PARTIALS_ROOT . 'page-form-builder.php';
        }

    }

    /**
     * Votes log page
     * @return void
     */
    public function page_votes_log()
    {
        require_once FV::$INCLUDES_ROOT . 'list-tables/class_votes_log_list.php';
        //Create an instance of our package class...
        $Table = new FV_List_Votes_Log();
        //Fetch, prepare, sort, and filter our data...
        $Table->prepare_items();

        $contest_id = (!empty($_REQUEST['contest_id'])) ? (int)$_REQUEST['contest_id'] : 0;
        $competitor_id = (!empty($_REQUEST['photo_id'])) ? (int)$_REQUEST['photo_id'] : 0;

        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-log-list.php', compact('Table', 'contest_id', 'competitor_id') );
    }

    /**
     * Analytic: map
     * @return void
     */
    public function page_analytic()
    {
        FV_Admin::assets_lib_jvectormap();
        FV_Admin::assets_lib_amstockchart();
        FV_Admin::assets_lib_boostrap();
        FV_Admin::assets_lib_typoicons();
        FV_Admin::assets_lib_tooltip();
        FV_Admin::assets_lib_icommon();

        $contestsQ = ModelContest::query()
            ->what_fields( array('id', 'name') )
            ->find();
        $votes_country_arr = array();

        foreach ($contestsQ as $item) {
            $contests[$item->id] = '#' .$item->id . ' / ' . $item->name;
        }

        $selected_contest_id = false;
        $selected_photo_id = 0;
        $votes_total = 0;
        $competitor = false;

        $photos = array();
        $votes_country_arr = array();
        $votes = array();
        $cheat_params = array();
        $top_5_screens = array();
        $night_votes_count = 0;
        $night_votes_percent = 0;
        $empty_refer_count = 0;
        $empty_refer_percent = 0;

        $OS_top5 = array();

        if (isset($_GET['contest_id']) && $_GET['contest_id'] > 0) {
            $selected_contest_id = (int)$_GET['contest_id'];

            $photos = ModelCompetitors::query()
                ->where('contest_id', $selected_contest_id)
                ->what_fields( array('id', 'name', 'votes_count') )
                ->sort_by_votes('like', 'DESC')
                ->find();

            if (isset($_GET['photo_id']) && $_GET['photo_id'] > 0) {
                $selected_photo_id = (int)$_GET['photo_id'];

                $votes_query = ModelVotes::query()
                    ->where_all(array("contest_id" => $selected_contest_id, "vote_id" => $selected_photo_id));

                $competitor = new FV_Competitor($selected_photo_id);

                // Cheating indicators
                $cheat_params = ModelVotes::query()
                    ->where('vote_id', $selected_photo_id)
                    ->what_field(' COUNT(`t`.`is_tor`) AS `is_tor_summary`, AVG(`t`.`score`) AS `score_avg`')
                    //->group_by('`vote_id`')
                    ->sort_by('`is_tor_summary`, `score_avg`', 'DESC')
                    ->findRow();

                $top_5_screens = ModelVotes::query()
                    ->where('vote_id', $selected_photo_id)
                    ->what_fields( array('display_size', 'COUNT(`t`.`display_size`) AS `display_size_summary`') )
                    ->group_by('`display_size`')
                    ->sort_by('`display_size_summary`', 'DESC')
                    ->limit(5)
                    ->find(false, false, OBJECT_K);

                // SELECT *, HOUR(`changed`) FROM `tm_fv_votes` WHERE HOUR(`changed`) < 8 OR HOUR(`changed`) > 23
                $night_votes_count = ModelVotes::query()
                    ->where('vote_id', $selected_photo_id)
                    ->where_custom_sql(' HOUR(`changed`) < 8 OR HOUR(`changed`) > 23 ')
                    ->find(true);

                ## Empty refer
                $empty_refer_count = ModelVotes::query()
                    ->where('vote_id', $selected_photo_id)
                    ->where('referer', '')
                    ->find(true);

            } else {
                $votes_query = ModelVotes::query()
                    ->where_all(array("contest_id" => $selected_contest_id));
                    /*->what_fields( array('id', 'contest_id', 'vote_id', 'country', 'changed') )
                    ->limit(4000)
                    ->find();*/
            }


            $votes_total = $votes_query->find(true);


            if ( $empty_refer_count && $votes_total ) {
                $empty_refer_percent = round( $empty_refer_count / ($votes_total/100), 1 );
            }
            if ( $night_votes_count && $votes_total ) {
                $night_votes_percent = round( $night_votes_count / ($votes_total/100), 1 );
            }


            $countries_query = clone $votes_query;
            $votes_country_arr =  $countries_query
                ->where_not('country', '')
                ->what_fields( array('country', 'COUNT(`t`.`country`) AS `country_votes_count`') )
                ->group_by('`country`')
                ->sort_by('`country_votes_count`', 'DESC')
                ->find();




            if ( $selected_photo_id ) {
                $OS_query = clone $votes_query;
                $OS_top5 = $OS_query
                    ->where_not('os', '')
                    ->what_fields(array('os', 'COUNT(`os`) AS `os_votes_count`'))
                    ->group_by('`os`')
                    ->sort_by('`os_votes_count`', 'DESC')
                    ->limit(5)
                    ->find();
            }

            /*
            SELECT `country`, COUNT(`country`) as per_country
            FROM `tm_fv_votes` t
            WHERE `t`.`contest_id` = "27"AND `t`.`vote_id` = "404" AND `country` != ''
            GROUP BY `country`
            ORDER BY per_country DESC
             */

            foreach ($votes_country_arr as $country_result_id => $country_result) {
                $votes_country_arr[$country_result_id]->two_letter_country = fv_2letter_country($country_result->country);
            }

            $chart_votes_arr_res = $votes_query
                ->what_fields( array('DATE_FORMAT(`changed`, "%Y-%m-%d 00:00:00") as date', 'COUNT(`id`) AS votes') )
                ->group_by('`date`')
                ->sort_by('`changed`', 'ASC')
                ->find(false, false, ARRAY_A);
            /*
                    foreach ($votes as $vote) {
                        //$date = strtotime($vote->changed));
                        $timestamp = strtotime( date('Y-m-d 00:00:00', strtotime($vote->changed)) );

                        if (!isset($chart_votes_arr[$timestamp])) {
                            $chart_votes_arr[$timestamp] = 1;
                        } else {
                            $chart_votes_arr[$timestamp]++;
                        }
                    }
            */
            $votes_per_day = 0;
            if ( $votes_total && $chart_votes_arr_res ) {
                $votes_per_day = $votes_total / count($chart_votes_arr_res);
            }
            /*
                    unset($votes);

                    ksort($chart_votes_arr);

                    $chart_votes_arr_res = array();
                    foreach ($chart_votes_arr as $date => $votes) {
                        $tmp['date'] = date('Y-m-d 00:00:00', $date);
                        $tmp['votes'] = $votes;
                        $chart_votes_arr_res[] = $tmp;
                    }

                    unset($chart_votes_arr);
            */
        }




        $page_url = admin_url('admin.php?page=fv-vote-analytic');


        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-votes-analytic.php', compact(
            'contests', 'selected_contest_id',
            'competitor', 'photos', 'selected_photo_id', 'votes_total', 'votes_per_day', 'cheat_params', 'top_5_screens',
            'night_votes_count','night_votes_percent',
            'empty_refer_count','empty_refer_percent',
            'OS_top5', 'votes_country_arr', 'chart_votes_arr_res', 'page_url'
        ) );
    }

    /**
     * Error log page
     * @return void
     */
    public function page_debug()
    {
        //FV_Admin::assets_lib_codemirror(true);
        FV_Admin::assets_lib_tooltip();


        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-debug.php' );

        FV_Admin::assets_lib_codemirror( "formatted-log", "text/x-markdown" );

    }

    /**
     * Translation page
     * @return void
     */
    public function page_translation()
    {
        $messages = fv_get_public_translation_messages();

        if ( isset($_GET['submit-translation']) ) {

            // Remove OLD strings for *Mails*
            $messages = array_filter($messages, function($key) {
                if ( strpos($key, 'mail_') === 0 ) {
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_KEY);

            //$messages = array_map('stripslashes', $messages);

            fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-submit_translation.php', compact('messages') );
            return;
        }

        $key_groups = fv_get_public_translation_key_titles();
        $saved = false;

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
            foreach ($key_groups as $group_name => $group_fields) :
                foreach ($group_fields as $key => $value) {
                    // если пришла эта опция
                    if (isset($_POST[$key]) && !in_array($key, fv_get_public_translation_textareas())) {
                        $messages[$key] = sanitize_text_field($_POST[$key]);
                    } elseif (isset($_POST[$key])) {
                        $messages[$key] = $_POST[$key];
                    }
                }
                $saved = true;
            endforeach;
            fv_update_public_translation_messages($messages);
        }

        $messages = array_merge( fv_get_default_public_translation_messages(), $messages );

        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-translations.php', compact('saved', 'key_groups', 'messages') );
    }

    /**
     * Moderation photos page
     * @return void
     */
    public function page_moderation()
    {

        wp_enqueue_style('fv_contest', FV::$ADMIN_URL . 'css/fv_contest.css', false, FV::VERSION, 'all');

        FV_Admin::assets_lib_icommon();

        require_once FV::$INCLUDES_ROOT . 'list-tables/class_competitors_list.php';

        //Create an instance of our package class...
        $listTable = new FV_List_Competitors( false, true );
        //Fetch, prepare, sort, and filter our data...
        $listTable->prepare_items();        

        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-moderation-list.php', compact('items', 'listTable') );
    }

    /**
     * Settings page
     * @return void
     */
    public function page_settings()
    {
        $settings = get_option('fv', array());
        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-settings.php', compact('settings') );
    }

    /**
     * Settings page
     * @since 2.2.300
     * @return void
     */
    public function page_license()
    {
        $defaults = array('status' => 0, 'valid' => 0, 'expiration' => 'Key not entered!');
        $key = get_option('fv-update-key', '');
        $key_details = get_option('fv-update-key-details', $defaults);

        $response = wp_remote_fopen ('https://wp-vote.net/edd-api/products/?number=20');
        $response_arr = @(array)json_decode($response);

        include FV::$ADMIN_PARTIALS_ROOT . 'page-license.php';
    }

    /**
     * Help page
     * @since 2.2.409
     * @return void
     */
    public function page_help()
    {
        //$response_arr = @(array)json_decode($response);


        /*
                if ( isset($_POST['stg_saveTicket']) ) {
                    $response = wp_remote_post("https://wp-vote.net/wp-admin/admin-ajax.php", array(
                            'method' => 'POST',
                            'timeout' => 20,
                            'redirection' => 5,
                            'httpversion' => '1.1',
                            'blocking' => true,
                            'headers' => array(),
                            'body' => $_POST,
                            'cookies' => array()
                        )
                    );

                    if ( is_wp_error($response) ) {
                        wp_add_notice('Error on sending request: ' . $response->get_error_message(), "warning");
                    } else {
                        wp_redirect( admin_url('admin.php?page=fv-help&sent=1') );
                        exit;
                    }

                }
        */

        fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . 'page-help.php' );
    }

}

<?php
defined('ABSPATH') or die("No script kiddies please!");

/*
 * Helper functions for public-facing functionality
 * Created: 10.02.2016
 */

/**
 * @return mixed
 * @since 2.2.803
 */
function fv_get_single_page_id() {
    $single_page_id = fv_setting('single-page', '');

    return $single_page_id;
}

/**
 * Checks is current WP version is supported for Fast AJAX
 *
 * @return bool
 */
function fv_is_fast_ajax_enabled () {
    if ( fv_setting('fast-ajax', true) ) {
        global $wp_version;
        // IF WP version between [4.4 and 4.7.1]
        if ( version_compare($wp_version, '4.4') === 1 && version_compare('4.9.8', $wp_version, '>=') ) {
            return true;
        }
    }
    return false;
}

/**
 * Uses to fix Cloudflare rocketscript option
 * https://support.cloudflare.com/hc/en-us/articles/200168056-What-does-Rocket-Loader-do-
 * @called by filter 'script_loader_tag' at wp-includes\class.wp-scripts.php
 *
 * @param   string  $tag        Html code "<script src="****"></script>"
 * @param   string  $handle     Script ID
 * @return string
 */
function fv_rocketscript_fix_filter ( $tag, $handle ) {
    // What scripts need exclude?
    $handles_to_exclude = apply_filters('fv/public/rocketscript_to_exclude',
        array('fv_main_js'=> 0,'fv_theme_story'=>0,'fv_lib_js'=>0,'fv_evercookie'=>0,'jquery-core'=>0,'jquery-migrate'=>0),
        $handle
    );

    if ( !isset($handles_to_exclude[$handle]) ) return $tag;

    // If there are some Variables passed ro script via "wp_localize_script"
    global $wp_scripts;
    if ( $wp_scripts->get_data( $handle, 'data' ) ) {
        $tag = '<script data-cfasync="false">' . $wp_scripts->print_extra_script( $handle, false ) . '</script>' . $tag;
    }
    return str_replace( "type='text/javascript' src", ' data-cfasync="false" src', $tag );
}

/**
 * @param FV_Competitor     $contestant
 * @param string            $type           'list'|'single'
 * @param null|object       $contest
 * @return string
 */
function fv_tpl_contestant_head($contestant, $type = 'list', $contest = null) {
    return $contestant->getHeadingForTpl($type, $contest);
}

/**
 * @param FV_Competitor     $contestant
 * @param string            $type           'list'|'single'
 *
 * @return string
 */
function fv_tpl_contestant_desc($contestant, $type = 'list') {
    return $contestant->getDescForTpl($type);
}

/** 
 * Generate HASH
 * @param int       $contest_id
 * @param bool      $mail_verify
 * @return string
 */
function fv_generate_email_subscribe_hash($contest_id, $mail_verify = false) {
    $verify_text = ($mail_verify) ? 'verify' : 'no-verify';
    return sha1( fv_get_user_ip() . ':' . $contest_id . ':' . NONCE_SALT . ':defaultAsubscr:' . $verify_text ) . wp_generate_password(4, true);
}

function fv_verify_email_subscribe_hash($hash, $contest_id, $mail_verify = false) {
    $verify_text = ($mail_verify) ? 'verify' : 'no-verify';
    $hash = substr($hash, 0, 40);
    //var_dump($hash);
    //var_dump(sha1( fv_get_user_ip() . ':' . $contest_id . ':' . NONCE_SALT . ':defaultAsubscr:' . $verify_text ));
    return $hash == sha1( fv_get_user_ip() . ':' . $contest_id . ':' . NONCE_SALT . ':defaultAsubscr:' . $verify_text );
}

/**
 * @param FV_Competitor     $contestant
 * @param object|bool       $contest
 * @param string|bool       $contest_voting_type
 * @param int|bool          $rating_total_count
 *
 * @return string
 * @deprecated since 2.3.03
 */
function fv_get_votes($contestant, $contest = false, $contest_voting_type = false, $rating_total_count = false) {

    // **DEPRECATED

    if (!$contest && !$contest_voting_type) {
        $contest = ModelContest::query()->findByPK($contestant->contest_id, true);
        $contest_voting_type = $contest->voting_type;
    } elseif ($contest) {
        $contest_voting_type = $contest->voting_type;
    }
    //var_dump($contest_voting_type);
    //var_dump($contestant->votes_average);
    if ( $contest_voting_type == 'rate' ) {
        if ( !$rating_total_count ) {
            $result = round($contestant->votes_average, 1) . ' ' . fv_get_transl_msg('vote_rating_delimiter', 'of') . ' ' . fv_setting('rate-stars-count', 5);
        } else {
            $result = round($contestant->votes_average, 1) . ' ' . fv_get_transl_msg('vote_rating_delimiter', 'of') . ' ' . fv_setting('rate-stars-count', 5) . ' (' . $contestant->votes_count . ')';
        }
    } elseif ( $contest_voting_type == 'rate_summary' ) {
        if ( !$rating_total_count ) {
            $result = $contestant->rating_summary;
        } else {
            $result = $contestant->rating_summary . ' (' . $contestant->votes_count . ')';
        }

    } else {
        $result = $contestant->votes_count;
    }

    /**
     * @since 2.2.800
     *
     * Filter 'fv/competitor/get_votes'
     *
     * @param FV_Competitor $this
     * @param string|int    $result
     * @param string        $contest_voting_type    'vote' or 'rate'
     * @param FV_Contest    $contest
     */
    return apply_filters('fv/competitor/get_votes', $result, $contestant, $contest_voting_type, $contest);
}

// contest edit link to wp header admin bar
function fv_add_toolbar_items($admin_bar) {
    if( !FvFunctions::curr_user_can() ) { return; }

    global $contest_ids;
    if ( !empty($contest_ids) && !is_admin() ) {
        $admin_bar->add_menu(array(
            'id' => 'fv',
            'title' => __('Contests', 'fv'),
            'href' => admin_url('admin.php?page=fv'),
            'meta' => array(
                'title' => __('Manage contests', 'fv'),
            ),
        ));

        if ( $single_competitor_ID = FV_Public_Single::get_instance()->get_requested_photo_id() ) {
            $curr_competitor = new FV_Competitor( $single_competitor_ID, true );

            $curr_competitor_name = $curr_competitor->name;

            if (function_exists('mb_strlen') && mb_strlen($curr_competitor_name, 'UTF-8') > 18 && function_exists('mb_substr')) {
                $curr_competitor_name = mb_substr($curr_competitor_name, 0, 18) . ' ...';
            }

            $admin_bar->add_node(array(
                'id' => 'fv-edit-competitor-' . $curr_competitor->ID,
                'title' => 'Edit competitor "' . $curr_competitor_name . '"',
                'href' => $curr_competitor->getAdminLink(),
                'parent' => 'fv',
            ));
            $admin_bar->add_node(array(
                'id' => 'fv-separator0',
                'title' => '----------------------------',
                'href' => '#0',
                'parent' => 'fv',
            ));
        }

        $curr_contest = null;

        foreach ($contest_ids as $c_id) :
            $curr_contest = ModelContest::query()->findByPK($c_id, true);
            if (!$curr_contest) {
                continue;
            }
            if (function_exists('mb_strlen') && mb_strlen($curr_contest->name, 'UTF-8') > 18 && function_exists('mb_substr')) {
                $curr_contest->name = mb_substr($curr_contest->name, 0, 18) . ' ...';
            }
            $admin_bar->add_node(array(
                'id' => 'fv-config-contest-' . $c_id,
                'title' => 'Configure contest "' . $curr_contest->name . '"',
                'href' => $curr_contest->getAdminUrl(),
                'parent' => 'fv',
            ));
            $admin_bar->add_node(array(
                'id' => 'fv-competitors-contest-' . $c_id,
                'title' => 'Manage "' . $curr_contest->name . '" competitors',
                'href' => $curr_contest->getAdminUrl('competitors'),
                'parent' => 'fv',
            ));
            if ($curr_contest->form_id) {
                $admin_bar->add_node(array(
                    'id' => 'fv-edit-contest-form-' . $c_id,
                    'title' => 'Edit contest "' . $curr_contest->name . '" form',
                    'href' => admin_url('admin.php?page=fv-formbuilder&form=' . $curr_contest->form_id),
                    'parent' => 'fv',
                ));
            }

            $admin_bar->add_node(array(
                'id' => 'fv-contest-log-' . $c_id,
                'title' => 'View "' . $curr_contest->name . '" votes log',
                'href' => admin_url('admin.php?page=fv-vote-log&contest_id=' . $c_id),
                'parent' => 'fv',
            ));
        endforeach;

        $admin_bar->add_node(array(
            'id' => 'fv-separator1',
            'title' => '----------------------------',
            'href' => '#0',
            'parent' => 'fv',
        ));

        $admin_bar->add_node(array(
            'id' => 'fv-moderation',
            'title' => '» Moderation',
            'href' => admin_url('admin.php?page=fv-moderation'),
            'parent' => 'fv',
        ));

        $admin_bar->add_node(array(
            'id' => 'fv-formbuilder',
            'title' => '» Forms',
            'href' => admin_url('admin.php?page=fv-formbuilder'),
            'parent' => 'fv',
        ));

        $admin_bar->add_node(array(
            'id' => 'fv-settings',
            'title' => '» Global contests settings',
            'href' => admin_url('admin.php?page=fv-settings'),
            'parent' => 'fv',
        ));
    }
}

/**
 * @since 2.3.10
 * @return bool
 */
function fv_is_infinite_scroll() {
    return in_array( fv_setting('pagination-type', 'default'), ['infinite', 'infinite-auto'] );
}

/**
 * @param $contest
 * @return bool
 *
 * @deprecated since 2.3.00
 */
function fv_can_upload($contest) {
    // проверяем опцию, кому разрешено загружать фотографии
    if ( (bool)$contest->upload_enable  ){
        return true;
    } else {
        return false;
    }
}

/**
 * Allows load compiled CSS files in the FLY
 * #Must be called just for some CSS files, that have compiled Versions#
 * @since      2.2.110
 *
 * @param string $fileUrl
 * @return string
 */
function fv_min_url($fileUrl) {
    if ( fv_setting('not-compiled-assets', false) == false && !isset($_GET['nomin']) ) {
        $fileUrl = str_replace('.js', '.min.js', $fileUrl);
        return str_replace('.css', '.min.css', $fileUrl);
    }
    return $fileUrl;
}

/**
 *  return link to contest page with contest and photo id params
 *  photo id are empty to use in javasript, when shows share link
 * @since ver 2.2.05
 *
 * @param		string $contest_id	    Contest id
 * @param		string $link			Link to page with contest
 * @param		mixed $photo_id			ID photo
 * @return		string				    URL http://test.com/?contest_id=1&photo=
 */
function fv_generate_contestant_link($contest_id, $link = '', $photo_id = false) {
    if ( !$link ) {
        $link = get_permalink();
    }
    $page_url = remove_query_arg( 'photo', $link );
    $page_url = remove_query_arg( 'contest_id', $page_url );
    //$page_url = remove_query_arg( 'fv-scroll', $page_url );

    // add page ID
    if ( isset($_GET['fv-page']) && $_GET['fv-page'] > 1 ) {
        $page_url = add_query_arg( 'fv-page', (int)$_GET['fv-page'], $page_url );
    }

    // add page ID
    if ( isset($_GET['fv-sorting']) ) {
        $page_url = add_query_arg( 'fv-sorting', sanitize_title($_GET['fv-sorting']), $page_url );
    }

    //$page_url = add_query_arg( 'contest_id', $contest_id, $page_url );
    if ( $photo_id > 0 ) {
        return add_query_arg( 'photo', $photo_id, $page_url );
    }else {
        return add_query_arg( 'photo', '', $page_url );
    }
}

/**
 *  return link to contest page with contest and photo id params
 *  IF $link_template == true then %photo_id% are empty to use in javasript, when shows share link
 * @since ver 2.2.2
 *
 * @param		int $photo_id			ID photo
 * @param		bool $link_template		return full link or just template
 * @param		int|bool $contest_ID    contest ID for get page ID
 * @return		string				    URL http://test.com/contest_photo/123/
 */
function fv_single_photo_link($photo_id, $link_template = false, $contest_ID = false) {
    static $single_page_url_tpl = array();

    if (!$contest_ID) {
        if ( fv_is_photo_direct_link_type() ) {
            $contest_ID = 1;
        } else {
            $photo = ModelCompetitors::query()->findByPK($photo_id, true);
            $contest_ID = $photo->contest_id;
        }
    }
    if ( empty($single_page_url_tpl[$contest_ID]) ) {
        if ( fv_is_photo_direct_link_type() ) {
            $single_page_id = fv_setting('single-page');
            if (empty($single_page_id)) {
                return;
            }

            // Check current permalink structure
            global $wp_rewrite;
            $page_permastruct = $wp_rewrite->get_page_permastruct();
            //
            if (!empty($page_permastruct)) {
                // "/contest-photo/123/"
                $single_page_url_tpl[$contest_ID] = home_url('/') . fv_setting('single-permalink', 'contest-photo') . '/%photo_id%/';
            } else {
                // "?page_id=22&photo_id=123"
                $page_url = get_page_link($single_page_id);
                $single_page_url_tpl[$contest_ID] = add_query_arg('photo_id', '%photo_id%', $page_url);
            }
        } else {

            $contest = fv_get_contest($contest_ID, true, true);
            if ( !empty($contest->page_id) ) {
                $page_url = get_permalink($contest->page_id);
            } else {
                $page_url = home_url('/');
            }

            // add page ID
            if ( isset($_GET['fv-page']) && $_GET['fv-page'] > 1 ) {
                $page_url = add_query_arg( 'fv-page', (int)$_GET['fv-page'], $page_url );
            }
            $single_page_url_tpl[$contest_ID] = $page_url . '#photo-%photo_id%';
        }

        $paged = ( isset($_GET['fv-page']) ) ? (int)$_GET['fv-page'] : false;
        if ($paged) {
            add_query_arg('fv-page', $paged, $single_page_url_tpl[$contest_ID]);
        }
    }

    if (!$link_template) {
        $url = str_replace('%photo_id%', $photo_id, $single_page_url_tpl[$contest_ID]);
    } else {
        $url = $single_page_url_tpl[$contest_ID];
    }

    /**
     * @since 2.3.00
     */
    return apply_filters('fv/competitor/get_single_view_link', $url, $photo_id, $link_template, $contest_ID);
}

/**
 * do we open photo in lightbox or in new page?
 * @since ver 2.2.05
 *
 * @param FV_Contest|bool    $contest   since 2.2.800
 *
 * @return bool
 */
// *TODO - remove
function fv_photo_in_new_page($contest = false) {
    //$themes = apply_filters('fv/photo_in_new_page/supports', array('new_year', 'default', 'flickr', 'pinterest') );
    //return get_option('fotov-photo-in-new-page', false) && in_array($theme, $themes);
    return apply_filters('fv/single_link_mode/is_direct',  fv_setting('single-link-mode', 'mixed') == 'direct', $contest);
}

/**
 * do we open photo in lightbox or in new page?
 * @since ver 2.2.05
 *
 * @return bool
 */
// *TODO - remove
function fv_is_photo_direct_link_type() {
    //$themes = apply_filters('fv/photo_in_new_page/supports', array('new_year', 'default', 'flickr', 'pinterest') );
    //return get_option('fotov-photo-in-new-page', false) && in_array($theme, $themes);
    return apply_filters('fv/single_link_mode/is_not_lightbox', fv_setting('single-link-mode', 'mixed') != 'lightbox' );
}

/**
 * Output custom CSS
 *
 * @return void
 */
function fv_custom_css() {
    echo PHP_EOL . '<style>' . str_replace( array("\r\n","\n","\r",'      '),'', get_option('fotov-custom-css', '') ) . '</style>' . PHP_EOL;
}


function fv_get_user_ip() {

    $ipaddress = '';
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
        $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
    else if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;

}

/**
 * Get user country by IP - uses now
 *
 * @param $ip
 * @return string
 */
function fv_get_user_country($ip) {
    // *TODO may be move this code to JS
    //echo 'fv_get_user_country';
    if ( !fv_is_lc() ) {
        $apis = array(
            //'freegeoip' => 'http://freegeoip.net/json/{IP}',                // 10 000 calls per day
            //'geoplugin' => 'http://www.geoplugin.net/json.gp?ip={IP}',
            'ip-api' => 'http://ip-api.com/json/{IP}?fields=status,country',    // 250 class per hour
            'sypexgeo' => 'http://api.sypexgeo.net/json/{IP}',      // 10 000 calls per month
            'usercountry' => 'https://usercountry.com/v1.0/json/{IP}',      // 10 000 calls per month
            'ipapi' => 'https://ipapi.co/8.8.8.8/json/ ',      // 10 000 calls per month
        );

        // get_transient - what API use?
        if ( false === ($use_api = get_transient('fv_get_user_country_use_api')) ) {
            // IF no errors get
            $use_api = array_rand($apis, 1);
        }

        // Get remote HTML file
        $json_data = fv_get_user_country_request( str_replace('{IP}', $ip, $apis[$use_api]) );
        if ( false === $json_data ) {
            // Some troubles - let's use another API at next call
            unset($apis[$use_api]);
            $use_api = array_rand($apis);
            /*if ( 'freegeoip' == $use_api ) {
                $use_api = 'geoplugin';
            } elseif ( 'geoplugin' == $use_api ) {
                $use_api = 'ip-api';
            } else {
                $use_api = 'freegeoip';
            }*/
            set_transient('fv_get_user_country_use_api', $use_api, 6 * HOUR_IN_SECONDS);
        }

        $data = json_decode($json_data);
        if ( is_object($data) ) {
            switch ($use_api){
//                case 'freegeoip':
//                    return $data->country_name;  // country name
//                    break;
//                case 'geoplugin':
//                    return $data->geoplugin_countryName;  // country name
//                    break;
                case 'ipapi':
                    return $data->country_name;  // country name
                    break;
                case 'usercountry':
                    return $data->country->name;  // country name
                    break;
                case 'ip-api':
                    return $data->country;  // country name
                    break;
                case 'sypexgeo':
                    return $data->country->name_en;  // country name
                    break;
            }

        }
        return 'unknown';
    } else {
        return 'localhost';  // localhost
    }
}

// TODO - return false
function fv_get_user_country_request($url) {
    // Get remote HTML file
    $response = wp_remote_get( $url );

    // Check for error
    if ( is_wp_error( $response ) ) {
        fv_log('get_user_country - is_wp_error from ' . $url, $response);
        return false;
    }
    if ( isset($response["response"]["code"]) && 200 !== $response["response"]["code"] ) {
        fv_log('get_user_country - ERROR code from ' . $url, $response["response"]);
        return false;
    }

    // Parse remote HTML file
    $data = wp_remote_retrieve_body( $response );

    // Check for error
    if ( is_wp_error( $data ) ) {
        return false;
    }

    return $data;
}

// Get user country by IP - not uses now
function fv_get_user_country2($ip) {
    if ( !fv_is_lc() ) {
        $tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ip);
        return $tags['country'];  // country name
    } else {
        return 'localhost';  // localhost
    }
}

// pagination list
function fv_corenavi($contest_id, $pages_count = 0, $current = 1, $sorting = '', $search = '', $category_slug = '') {
    $pages = '';
    if (!$current) {
        $current = 1;
    }
    $a['base'] = str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );
    $a['total'] = $pages_count;
    $a['current'] = $current;
    $a['type'] = 'list';
    $a['format'] = '?fv-paged=%#%';

    $a['mid_size'] = 3; //сколько ссылок показывать слева и справа от текущей
    $a['end_size'] = 1; //сколько ссылок показывать в начале и в конце
    $a['prev_text'] = '&laquo;'; //текст ссылки "Предыдущая страница"
    $a['next_text'] = '&raquo;'; //текст ссылки "Следующая страница"

    $b = array(
        'back_text' => '&laquo;', //текст ссылки "Предыдущая страница"
        'next_text' => '&raquo;', //текст ссылки "Следующая страница"
        //'posts_per_page' => fv_setting('pagination', 0),
        'max_page' => $pages_count,
        'paged' => $current,
        'sorting' => $sorting,
        'search' => $search,
        'category_slug' => $category_slug,
    );

    // remove photo id, if open contestant direct
    //add_filter('get_pagenum_link', 'fv_edit_paginate_url');
    $public_translation_messages = fv_get_public_translation_messages();

    IF ($pages_count > 1) :
        $class = fv_is_infinite_scroll() ? 'infinite' : fv_setting('pagination-type', 'default');
        echo '<nav class="fv-pagination ' . $class . '" role="navigation">';
        unset($class);

        if ( !fv_is_infinite_scroll() ) {
            if ($pages_count > 1) {
                $pages = '<span class="pages">' . sprintf($public_translation_messages['pagination_summary'], $current, $pages_count) . '</span>'."\r\n";
            }
            echo $pages . fv_kama_pagenavi($contest_id, '', '', false, $b);
        } elseif ( $pages_count > $current ) {
            $ajax_link = sprintf( 'fv_ajax_go_to_page(%d, %s, \'%s\',\'%s\', true, \'%s\', \'%s\');', $current+1, $contest_id, $sorting, wp_create_nonce('fv-ajax'), $search, $category_slug );
            echo '<button type="button" class="fv-infinite-load" onclick="' . $ajax_link . '">' .
                $public_translation_messages['pagination_infinity'] .
                '</button>';
        }

        echo '</nav>';
    ENDIF;

    //remove_filter('get_pagenum_link', 'fv_edit_paginate_url');
}


function fv_get_paginate_url($url, $page_content = 1){
    if ( empty($url) ) {
        $url = get_permalink();
    }
    $url = remove_query_arg( 'photo', $url );
    //$url = remove_query_arg( 'fv-scroll', $url );
    $url = remove_query_arg( 'fv-page', $url );

    // add sorting var
    if ( isset($_GET['fv-sorting']) ) {
        $url = remove_query_arg( 'fv-sorting', $url );
        $url = add_query_arg( 'fv-sorting', sanitize_title($_GET['fv-sorting']), $url );
    }

    // add filter var
    if ( isset($_GET['fv-filter']) ) {
        $url = remove_query_arg( 'fv-filter', $url );
        $url = add_query_arg( 'fv-filter', addslashes($_GET['fv-filter']), $url );
    }
    /*
        if ( fv_setting('pagination-scroll-to-contest', false) ) {
            $url .= '&fv-scroll=fv_contest_container';
        }
    */
    //if ( $page_content ) {
    $url = add_query_arg('fv-page', $page_content, $url);
    //}

    return  $url;
}


/**
 * Альтернатива wp_pagenavi. Создает ссылки пагинации на страницах архивов
 *
 * @param string $before - текст до навигации
 * @param string $after  - текст после навигации
 * @param bool $echo     - возвращать или выводить результат
 * @param array $args    - аргументы функции
 *
 * Версия: 2.2
 * Автор: Тимур Камаев
 * Ссылка на страницу функции: http://wp-kama.ru/?p=8
 */
function fv_kama_pagenavi( $contest_id, $before = '', $after = '', $echo = true, $args = array() ) {
    global $wp_query;

    // параметры по умолчанию
    $default_args = array(
        'text_num_page'   => '', // Текст перед пагинацией. {current} - текущая; {last} - последняя (пр. 'Страница {current} из {last}' получим: "Страница 4 из 60" )
        'num_pages'       => 10, // сколько ссылок показывать
        'step_link'       => 10, // ссылки с шагом (значение - число, размер шага (пр. 1,2,3...10,20,30). Ставим 0, если такие ссылки не нужны.
        'dotright_text'   => '…', // промежуточный текст "до".
        'dotright_text2'  => '…', // промежуточный текст "после".
        'back_text'       => '«', // текст "перейти на предыдущую страницу". Ставим 0, если эта ссылка не нужна.
        'next_text'       => '»', // текст "перейти на следующую страницу". Ставим 0, если эта ссылка не нужна.
        'first_page_text' => '0', // текст "к первой странице". Ставим 0, если вместо текста нужно показать номер страницы.
        'last_page_text'  => '0', // текст "к последней странице". Ставим 0, если вместо текста нужно показать номер страницы.

        //'posts_per_page'  => '0',
        'paged'  => '0',
        'max_page'  => '0',
        'sorting'  => '',
        'search'  => '',
    );

    $args = array_merge( $default_args, $args );

    extract( $args );
    /*
    $posts_per_page = (int) $wp_query->query_vars['posts_per_page'];
    $paged          = (int) $wp_query->query_vars['paged'];
    $max_page       = $wp_query->max_num_pages;
    */
    //проверка на надобность в навигации
    if( $max_page <= 1 )
        return false;

    if( empty( $paged ) || $paged == 0 )
        $paged = 1;

    $pages_to_show = intval( $num_pages );
    $pages_to_show_minus_1 = $pages_to_show-1;

    $half_page_start = floor( $pages_to_show_minus_1/2 ); //сколько ссылок до текущей страницы
    $half_page_end = ceil( $pages_to_show_minus_1/2 ); //сколько ссылок после текущей страницы

    $start_page = $paged - $half_page_start; //первая страница
    $end_page = $paged + $half_page_end; //последняя страница (условно)

    if( $start_page <= 0 )
        $start_page = 1;
    if( ($end_page - $start_page) != $pages_to_show_minus_1 )
        $end_page = $start_page + $pages_to_show_minus_1;
    if( $end_page > $max_page ) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = (int) $max_page;
    }

    if( $start_page <= 0 )
        $start_page = 1;

    //выводим навигацию
    $out = '';
    $out .= $before . "<div class='fv-pagination-list'>\n";

    if( $text_num_page ){
        $text_num_page = preg_replace( '!{current}|{last}!', '%s', $text_num_page );
        $out.= sprintf( "<span class='pages'>$text_num_page</span> ", $paged, $max_page );
    }

    IF ( fv_setting('pagination-type', 'default') == 'default' ){

        // создаем базу чтобы вызвать get_pagenum_link один раз
        $link_base = fv_get_paginate_url( $_SERVER['REQUEST_URI'] , '___' );
        //$link_base = str_replace( 99999999, '___', $link_base);
        //$first_url = user_trailingslashit( get_pagenum_link( 1 ) );
        $first_url = remove_query_arg( 'fv-page', $link_base );

        // назад
        if ( $back_text && $paged != 1 )
            $out .= '<a class="prev" href="'. str_replace( '___', ($paged-1), $link_base ) .'">'. $back_text .'</a> ';
        // в начало
        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
            $out.= '<a class="first" href="'. $first_url .'">'. ( $first_page_text ? $first_page_text : 1 ) .'</a> ';
            if( $dotright_text && $start_page != 2 ) $out .= '<span class="extend">'. $dotright_text .'</span> ';
        }
        // пагинация
        for( $i = $start_page; $i <= $end_page; $i++ ) {
            if( $i == $paged )
                $out .= '<span class="current">'.$i.'</span> ';
            elseif( $i == 1 )
                $out .= '<a href="'. $first_url .'">1</a> ';
            else
                $out .= '<a href="'. str_replace( '___', $i, $link_base ) .'">'. $i .'</a> ';
        }

        //ссылки с шагом
        $dd = 0;
        if ( $step_link && $end_page < $max_page ){
            for( $i = $end_page+1; $i<=$max_page; $i++ ) {
                if( $i % $step_link == 0 && $i !== $num_pages ) {
                    if ( ++$dd == 1 )
                        $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
                    $out.= '<a href="'. str_replace( '___', $i, $link_base ) .'">'. $i .'</a> ';
                }
            }
        }
        // в конец
        if ( $end_page < $max_page ) {
            if( $dotright_text && $end_page != ($max_page-1) )
                $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
            $out.= '<a class="last" href="'. str_replace( '___', $max_page, $link_base ) .'">'. ( $last_page_text ? $last_page_text : $max_page ) .'</a> ';
        }
        // вперед
        if ( $next_text && $paged != $end_page ) {
            $out.= '<a class="next" href="'. str_replace( '___', ($paged+1), $link_base ) .'">'. $next_text .'</a> ';
        }

    } ELSE {
        // назад
        if (empty($contest_id)) {
            return 'Check contest id - pagination!';
        }

        $ajax_link = sprintf( 'fv_ajax_go_to_page(%s, %s, \'%s\',\'%s\', false, \'%s\', \'%s\');', '%d', $contest_id, $sorting, wp_create_nonce('fv-ajax'), $search, $category_slug );

        $first_url = sprintf( $ajax_link, 1 );

        if ( $back_text && $paged != 1 )
            $out .= '<a class="prev" href="#0" onclick="'. sprintf( $ajax_link, ($paged-1) ) .'">'. $back_text .'</a> ';
        // в начало
        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
            $out.= '<a class="first" href="#0" onclick="'. $first_url .'">'. ( $first_page_text ? $first_page_text : 1 ) .'</a> ';
            if( $dotright_text && $start_page != 2 ) $out .= '<span class="extend">'. $dotright_text .'</span> ';
        }
        // пагинация
        for( $i = $start_page; $i <= $end_page; $i++ ) {
            if( $i == $paged )
                $out .= '<span class="current">'.$i.'</span> ';
            elseif( $i == 1 )
                $out .= '<a href="#0" onclick="'. $first_url .'">1</a> ';
            else
                $out .= '<a href="#0" onclick="'. sprintf( $ajax_link, $i ) .'">'. $i .'</a> ';
        }

        //ссылки с шагом
        $dd = 0;
        if ( $step_link && $end_page < $max_page ){
            for( $i = $end_page+1; $i<=$max_page; $i++ ) {
                if( $i % $step_link == 0 && $i !== $num_pages ) {
                    if ( ++$dd == 1 )
                        $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
                    $out.= '<a href="#0" onclick="'. sprintf( $ajax_link, $i ) .'">'. $i .'</a> ';
                }
            }
        }
        // в конец
        if ( $end_page < $max_page ) {
            if( $dotright_text && $end_page != ($max_page-1) )
                $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
            $out.= '<a class="last" href="#0" onclick="'. sprintf( $ajax_link, $max_page ) .'">'. ( $last_page_text ? $last_page_text : $max_page ) .'</a> ';
        }
        // вперед
        if ( $next_text && $paged != $end_page ) {
            $out.= '<a class="next" href="#0" onclick="'. sprintf( $ajax_link, $paged+1 ) .'">'. $next_text .'</a> ';
        }
    }

    $out .= "</div>". $after ."\n";

    if ( ! $echo )
        return $out;
    echo $out;
}





/** This function will resize uploaded photo, if this option enabled in settings
 *
 * @param array $array ['type', 'file']
 * @return array
 */
function fv_upload_resize($array){
    //FvLogger::addLog('fv_upload_resize info', $array);

    if ( !isset($array['file']) ) {
        FvLogger::addLog('fv_upload_resize error : no File param', $array);
        return $array;
    }

    if ( ! get_option('fotov-upload-photo-maxwidth', 10) > 0 && ! get_option('fotov-upload-photo-maxheight', 10) > 0 ) {
        return $array;
    }

    // $array contains file, url, type
    if ($array['type'] == 'image/jpeg' OR $array['type'] == 'image/gif' OR $array['type'] == 'image/png') {
        // there is a file to handle, so include the class and get the variables
        require_once FV::$INCLUDES_ROOT . 'libs/class_resize.php';

        if ( !isset($array['maxwidth']) ) {
            $maxwidth = get_option('fotov-upload-photo-maxwidth', 0);
        } else {
            $maxwidth = (int)$array['maxwidth'];
        }

        if ( !isset($array['maxheight']) ) {
            $maxheight = get_option('fotov-upload-photo-maxheight', 0);
        } else {
            $maxheight = (int)$array['maxheight'];
        }

        $imagesize = getimagesize($array['file']); // $imagesize[0] = width, $imagesize[1] = height

        if ( $maxwidth == 0 OR $maxheight == 0) {
            if ($maxwidth==0 && $maxheight > 50 ) {
                $objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'H', $maxheight);
            }
            if ($maxheight==0 && $maxwidth > 50) {
                $objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'W', $maxwidth);
            }
        } else {
            if ( ($imagesize[0] >= $imagesize[1]) AND ($maxwidth * $imagesize[1] / $imagesize[0] <= $maxheight) )  {
                $objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'W', $maxwidth);
            } else {
                $objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'H', $maxheight);
            }
        }
        $array['resized_width'] = $objResize->arrResizedDetails[0];
        $array['resized_height'] = $objResize->arrResizedDetails[1];
        FvLogger::addLog('Resizied fv_upload_resize', $array);
    } // if
    return $array;
} // function


/**
 * return 2 letter language code most popularity by user or default
 * @since ver 2.2.02
 *
 * @param string    $default
 * @param string    $langs
 *
 * @return  string   Like 'en' - user browser language
 */
function fv_get_user_lang($default, $langs)
{
    $languages=array();
    $language = '';

    if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $language = array_combine($list[1], $list[2]);
                foreach ($language as $n => $v)
                    $language[$n] = $v ? $v : 1;
                arsort($language, SORT_NUMERIC);
            }
        } else $language = array();


        foreach ($langs as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            }else $languages[strtolower($alias)]=strtolower($lang);
        }
        foreach ($language as $l => $v) {
            $s = strtok($l, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
            if (isset($languages[$s]))
                return $languages[$s];
        }
    }
    return $default;
}

/**
 *  turn a full country name like `United States` into a 2 letters ISO country code `US`
 * @param $country
 * @return string
 */
function fv_2letter_country($country) {
    $countrycodes = array (
        'AF' => 'Afghanistan',
        'AX' => 'Åland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Zaire',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Côte D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and Mcdonald Islands',
        'VA' => 'Vatican City State',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'KENYA',
        'KI' => 'Kiribati',
        'KP' => 'Korea, Democratic People\'s Republic of',
        'KR' => 'Korea, Republic of',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia, the Former Yugoslav Republic of',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States of',
        'MD' => 'Moldova, Republic of',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Réunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan, Province of China',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania, United Republic of',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Minor Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        'NONE' => 'Zimbabwe',
        'NONE_LC' => 'localhost',
    );

    return array_search($country, $countrycodes); // returns 'US'
}
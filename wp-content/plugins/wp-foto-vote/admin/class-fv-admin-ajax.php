<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The ajax functionality of the plugin.
 *
 * @package    FV
 * @subpackage admin
 * @author     Maxim K <support@wp-vote.net>
 */

class FV_Admin_Ajax
{
    /**
     * Rotate image and thumbnails
     *
     * @uses $_POST['angle'] int
     * @uses $_POST['photo_id'] int
     *
     * @return void
    */
    public static function rotate_image()
    {

        if ( !isset($_POST['angle']) OR !isset($_POST['competitor_id']) ) {
            fv_AJAX_response(false, "incorrect params");
        }
        if (  !FvFunctions::curr_user_can('moderation') || !check_ajax_referer('fv_nonce', 'fv_nonce') ) {
            fv_AJAX_response(false, "no secure");
        }
        $angle = (int)$_POST['angle'];

        $competitor = ModelCompetitors::query()->findByPK( (int)$_POST['competitor_id'], true );

        if ( !is_object($competitor) ) {
            FvLogger::addLog("rotate_image - photo !is_object", $competitor);
            fv_AJAX_response(false, "No competitor found!");
        }

        $response = $competitor->rotateImage( $angle, true );

        if ( $response ) {
            fv_AJAX_response( $response['result'], $response['message'], $response['data'] );
        }

        fv_AJAX_response( false, 'Unknown error !', array('competitor_id'=>$competitor->id) );
    }


    public static function form_contestants()
    {
        if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv_multi_add_nonce') ) {
            fv_AJAX_response( false, 'no secure' );
        }

        if ( !isset($_POST['photos']) ) {
            die ( "no photos" );
        }
        $photos = $_POST['photos'];

        ob_start();
            include FV::$ADMIN_PARTIALS_ROOT . 'contest/_photos_list_form.php';
        $html = ob_get_clean();

        fv_AJAX_response( true, false, array('html' => $html) );
    }


    /**
     * Ajax :: move contestant to Another Contest
     *
     * @POST-param int contestant_id
     * @POST-param int to_contest_id
     *
     * @output json_array
     */
    public static function AJAX_move_contestant_to_contest()
    {
        if (!FvFunctions::curr_user_can() || !check_ajax_referer('fv_nonce', 'fv_nonce')) {
            fv_AJAX_response( false, 'Error security!' );
        }

        // Check required param
        if (empty($_POST['contestant_id']) || empty($_POST['to_contest_id'])) {
            fv_AJAX_response( false, 'Error params!' );
        }
        $contestant_id = (int)$_POST['contestant_id'];
        $to_contest_id = (int)$_POST['to_contest_id'];

        // Move Competitor and Meta
        if ( ModelCompetitors::query()->updateByPK(array('contest_id' => $to_contest_id), $contestant_id) ) {

            $contestant = ModelCompetitors::query()->findByPK($contestant_id, true, OBJECT, true);
            // Move META
            $metas = $contestant->meta->get_all();
            if ($metas): foreach ($metas as $meta_row) :
                ModelMeta::query()->updateByPK(array('contest_id' => $to_contest_id), $meta_row->ID);
            endforeach; endif;
            // Move META :: END

            // Reset Categories
            $contestant->resetCategories();

            do_action('fv/admin/move_contestant_to_contest', $contestant_id, $to_contest_id);

            fv_AJAX_response( true, '', array('contestant_id' => $contestant_id) );
        } else {
            fv_AJAX_response( false, 'Some Error!' );
        }
    }


    /**
     * AJAX :: Save the contestant
     *
     * @return void
     * @output json_array with form
     */
    public static function save_contestant()
    {
        if (!FvFunctions::curr_user_can()) {
            return;
        }

        if (!isset($_POST['fv_nonce']) || !wp_verify_nonce($_POST['fv_nonce'], 'save_contestant')) {
            print 'Sorry, your nonce did not verify.';
            exit;
        }

        $contest_id = (int)$_POST['contest_id'];
        $FORM = $_POST['form'];


        if (isset($FORM['name'])) :

            $contest = ModelContest::query()->findByPK($contest_id, true);

            $data = array(
                'id' => (is_numeric($FORM['id'])) ? intval($FORM['id']) : -1,
                'name' => sanitize_text_field(stripslashes($FORM['name'])),
                'description' => !empty($FORM['description']) ? wp_kses_post(stripslashes($FORM['description'])) : '',
                'full_description' => !empty($FORM['full_description']) ? wp_kses_post($FORM['full_description']) : '',
                'social_description' => !empty($FORM['social_description']) ? sanitize_text_field($FORM['social_description']) : '',
                'additional' => (!empty($FORM['additional'])) ? sanitize_text_field($FORM['additional']) : '',
                'user_email' => ( !empty($FORM['user_email']) ) ? sanitize_email($FORM['user_email']) : '',
                'url' => sanitize_text_field($FORM['image']),
                'image_id' => (int)$FORM['image_id'],
                'contest_id' => $contest->id,
                'votes_count' => isset($FORM['votes']) ? (int)$FORM['votes'] : 0,
                'votes_average' => isset($FORM['votes_average']) ? (float)$FORM['votes_average'] : 0,
                'status' => sanitize_text_field($FORM['status']),
            );

            if ( isset($FORM['rating_summary']) ) {
                $data['rating_summary'] = (float)$FORM['rating_summary'];
            }

            // WINNERS PART
            if ( isset($FORM['place']) && $FORM['place'] ) {
                $data['place'] = (int)$FORM['place'];
            } else {
                $data['place'] = null;
            }
            if (isset($FORM['place_caption'])) {
                $data['place_caption'] = wp_kses_post($FORM['place_caption']);
            }

            if (isset($FORM['order_position']) && $FORM['order_position'] != '') {
                $data['order_position'] = (int)$FORM['order_position'];
            }

            if ( isset($FORM['mime_type']) && $FORM['mime_type'] ) {
                $data['mime_type'] = sanitize_mime_type($FORM['mime_type']);
            }
            
            /**
             * Lets crop fields, if longer that needed
             */
            // TODO - may be move to ModelContestants ?
            $length_limit =  apply_filters('fv/admin/save_photo/length_limits', array(
                'name'              =>  255,
                'description'       =>  500,
                'full_description'  =>  ModelCompetitors::getFullDescriptionSize(),
                'social_description'=>  150,
                'user_email'        =>  100,
            ));
            foreach ( $length_limit as $length_limit_key => $length_limit_size ):
                if ( !empty($data[$length_limit_key]) && mb_strlen($data[$length_limit_key]) > $length_limit_size ) {
                    $data[$length_limit_key] = mb_substr($data[$length_limit_key], 0, $length_limit_size);
                    /* function_exists('mb_strlen') &&
                     * } elseif ( strlen($data[$length_limit_key]) > $length_limit_size ) {
                        $data[$length_limit_key] = substr($length_limit_size, 0, $length_limit_size);
                    }*/
                }
            endforeach;

            /*, array(
                        'a' => array(
                            'href' => array(),
                            'title' => array()
                        ),
                        'br' => array(),
                        'em' => array(),
                        'strong' => array(),
                    )
                )
            */
            //var_dump($data);

            if ($data['id'] > 0) {
                $prev = ModelCompetitors::query()->findByPK($data['id'], true);
            }


            if (!empty($prev) && $prev->options) {
                if (is_array($prev->options) && isset($FORM['options']) && is_array($FORM['options'])) {
                    $data['options'] = array_merge($prev->options, $FORM['options']);
                } else {
                    $data['options'] = $prev->options;
                }
            } elseif (isset($FORM['options']) && is_array($FORM['options'])) {
                $data['options'] = $FORM['options'];
            }

            // Email notification + attach user ?
            if ( !empty($prev) ) {
                // Try to Find user By new Email
                if ( $prev->user_email != $data['user_email'] && is_email($data['user_email']) ) {
                    $user = get_user_by( 'email', $data['user_email'] );
                    if ( $user ) {
                        $data['user_id'] = $user->ID;
                    }
                }
                if ( $prev->status == ST_MODERATION && $data['status'] == ST_PUBLISHED ) {
                    $prev->approve(true);
                }
            }

            $response = array();

            $filters = apply_filters('fv/admin/save_photo', array(), $data, $contest);

            if (isset($filters['photo'])) {
                $data = $filters['photo'];
            }
            if (isset($filters['notify_message'])) {
                $response['notify'] = $filters['notify_message'];
            }

            if ( isset($data['options']) && is_array($data['options'])) {
                $data['options'] = maybe_serialize($data['options']);
            }

            if ($data['id'] > 0) {
                // Изменяем элемент
                $response['add'] = false;
                $r = ModelCompetitors::query()->update($data);
            } else {
                // Создаем новый элемент
                $response['add'] = true;
                unset($data['id']);
                $data['added_date'] = current_time('timestamp', 0);
                $data['user_id'] = get_current_user_id();
                $data['id'] = ModelCompetitors::query()->insert($data);
            }

            $unit = ModelCompetitors::query()->findByPK((int)$data['id'], false, OBJECT, true);


            if ( isset($FORM['categories']) ) {
                $categories = array_map('absint', $FORM['categories']);
                $unit->setCategories( $categories, false );
            }

            ## PROCESS META
            if ( !empty($FORM['meta_key']) ) {
                $curr_meta = $unit->meta->get_custom_all('ID');

                FOREACH ($FORM['meta_key'] as $meta_ID => $meta_key) :
                    if ( !isset($FORM['meta_type'][$meta_ID]) ) {
                        continue;
                    }
                    switch ($FORM['meta_type'][$meta_ID]){
                        ## SAVE Meta (if changed)
                        case 'exists':
                            if ( isset($curr_meta[$meta_ID])
                                && ( $curr_meta[$meta_ID]->meta_key == $meta_key
                                    && $curr_meta[$meta_ID]->value == $FORM['meta_val'][$meta_ID] ) ) {
                                break;
                            }
                            ModelMeta::q()->updateByPK(
                                array(
                                    'meta_key'=>$meta_key,
                                    'value'=>$FORM['meta_val'][$meta_ID]
                                ),
                                (int)$meta_ID
                            );
                            break;
                        ## ADD Meta
                        case 'new':
                            ModelMeta::q()->insert(
                                array(
                                    'contest_id'=>$contest->id,
                                    'contestant_id'=>(int)$data['id'],
                                    'meta_key'  =>$meta_key,
                                    'value'     =>$FORM['meta_val'][$meta_ID],
                                    'custom'    => isset( $FORM['meta_core'][$meta_ID] ) ? 0 : 1,
                                )
                            );
                            break;
                        ## DELETE Meta
                        case 'deleted':
                            ModelMeta::q()->delete( (int)$meta_ID );
                            break;
                    }
                ENDFOREACH;
            }
            $unit->meta->_flush_meta();

            $edit = true;

            require_once FV::$INCLUDES_ROOT . 'list-tables/class_competitors_list.php';

            //Create an instance of our package class...
            $listTable = new FV_List_Competitors( $contest->id );

            $response['competitor'] = $listTable->_get_entry($unit);
            $response['id'] = $unit->id;
            die(fv_json_encode($response));

        endif;
    }

    /**
     * Ajax :: FORM for Add or Edit photo information
     *
     * @POST-param int $constest_id
     * @POST-param int $constestant_id
     *
     * @return void
     * @output json_array with form
     */
    public static function form_contestant()
    {

        if ( !FvFunctions::curr_user_can() || !check_admin_referer('fv_nonce', 'fv_nonce') ) {
            wp_die("No secure! Refresh page!");
        }

        if ( !isset($_GET['contest_id']) || !$_GET['contest_id'] ) {
            wp_die("No contest_id!");
        }

        $contest = new FV_Contest( (int)$_GET['contest_id'] );

        if ( isset($_GET['contestant_id']) && intval($_GET['contestant_id']) > 0 ) {
            $unit = new FV_Competitor( intval($_GET['contestant_id']) );
        } else {
            $unit = new FV_Competitor( false );
            $unit->contest_id = (int)$_GET['contest_id'];
            $unit->id = 0;
        }

        $html = fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . '/_competitor_form.php', compact("contest", "unit"), true );

        fv_AJAX_response( true, '', array('html' => $html) );
    }



    /**
     * Ajax :: approve photo
     *
     * @POST-param int contestant_id
     * @POST-param string admin_comment
     *
     * @output json_array
     */
    public static function approve_contestant()
    {
        if (!FvFunctions::curr_user_can('moderation') || !check_ajax_referer('fv_competitor_approve_nonce')) {
            return;
        }

        // Check required param
        if (!isset($_POST['competitor_id'])) {
            fv_AJAX_response(false, '');
        }
        $competitor_id= (int)$_POST['competitor_id'];
        $admin_comment = strip_tags($_POST['admin_comment']);

        $competitor = new FV_Competitor( $competitor_id );
        if ( $competitor->objectExists() ) {
            $competitor->approve( true, $admin_comment );
            fv_AJAX_response(true, '', array('competitor_id'=>$competitor->id));
        }

        fv_AJAX_response(false, '', array('competitor_id'=>$competitor->id));
    }

    /**
     * Ajax :: delete photo and image from hosting
     *
     * @POST-param int contest_id
     * @POST-param int contestant_id
     *
     * @output json_array
     */
    public static function delete_contestant()
    {
        if (!FvFunctions::curr_user_can('moderation') || !check_ajax_referer('fv_competitor_delete_nonce')) {
            fv_AJAX_response(false, 'Denied');
        }

        // Если пришёл параметр - очищаем результаты голосования
        if ( !isset($_REQUEST['competitor_id']) ) {
            fv_AJAX_response(false, 'Empty contestant_id or contest_id');
        }
        $id = (int)$_REQUEST['competitor_id'];
        $admin_comment = strip_tags($_REQUEST['admin_comment']);

        $competitor = new FV_Competitor( $id );
        if ( $competitor->objectExists() ) {
            $competitor->delete( true, $admin_comment );
            fv_AJAX_response(true, '', array('competitor_id'=>$competitor->id, 'competitor_name'=>$competitor->name));
        }

        fv_AJAX_response(false, 'Can\'t find Competitor in database for delete!');
    }


    /**
     * Clear all `Vote records` from table by contest_id
     *
     * @param int $contest_id
     * @return void
     */
    public static function clear_contest_stats($contest_id)
    {
        if (!FvFunctions::curr_user_can() || !check_admin_referer('fv_admin_nonce', 'fv_nonce')) {
            return;
        }

        // Если пришёл параметр - очищаем результаты голосования
        if (!isset($_REQUEST['contest_id'])) {
            FvLogger::addLog("clear_contest_stats error - no contest_id");
            fv_AJAX_response(true, "no contest_id");
        }

        ModelVotes::query()->deleteByContestID( (int)$_REQUEST['contest_id'] );

        fv_AJAX_response(true);
    }


    /**
     * Clear all `Subscribers records` from table by contest_id
     *
     * @return void
     * @since 2.3.00
     */
    public static function clear_contest_subscribers()
    {
        if (!FvFunctions::curr_user_can() || !check_admin_referer('fv_admin_nonce', 'fv_nonce')) {
            fv_AJAX_response( false, 'Not allowed!' );
        }

        $validated_data = fv_params_validate( $_REQUEST, ['contest_id' => 'required|integer'] );

        ModelSubscribers::query()->deleteByContestID( (int)$validated_data['contest_id'] );

        fv_AJAX_response(true);
    }

    /**
     * reset all votes to 0
     *
     * @param int $contest_id
     * @return void
     */
    public static function reset_contest_votes($contest_id)
    {
        if (!FvFunctions::curr_user_can() || !check_admin_referer('fv_admin_nonce', 'fv_nonce')) {
            return;
        }

        // Если пришёл параметр - очищаем результаты голосования
        if (!isset($_REQUEST['contest_id'])) {
            FvLogger::addLog("reset_contest_votes error - no contest_id");
            fv_AJAX_response(true, "no contest_id");
        }

        ModelCompetitors::query()->update(
            array('votes_count' => 0, 'votes_average' => 0, 'rating_summary'=>0),
            array('contest_id' => (int)$_REQUEST['contest_id'])
        );

        fv_AJAX_response(true);
    }


    /**
     * Clone contest and may be it photos
     * Type: AJAX
     * From: Single contest editing page
     */
    public static function clone_contest()
    {
        if (!FvFunctions::curr_user_can() || !check_admin_referer('fv_admin_nonce', 'fv_nonce')) {
            fv_AJAX_response( false, 'no secure' );
        }

        // Если пришёл параметр - очищаем результаты голосования
        if ( empty($_REQUEST['contest_id']) ) {
            FvLogger::addLog("AJAX_clone error - no contest_id");
            return;
        }

        $contest_id = (int)$_REQUEST['contest_id'];
        $with_content = isset($_REQUEST['with_content']) ? $_REQUEST['with_content'] : false;

        $to_clone = fv_get_contest($contest_id, false);

        if ( !is_wp_error($to_clone) && $to_clone->objectExists() ) {
            $to_clone_obj = (array) $to_clone->jsonSerialize();
            unset($to_clone_obj['id']);
            $to_clone_obj['name'] .= ' Copy';
            $cloned_id = ModelContest::query()->insert( $to_clone_obj );
            $cloned_contest = fv_get_contest( $cloned_id, true, true );
            //$cloned_id = $to_clone->save();

            // Clone Contest Categories
            $cats = $to_clone->getCategories();

            $cats_relation = array();

            foreach($cats as $cat) {
                // Insert ans Save relation between old and new Cats
                $cats_relation[$cat->term_id] = wp_insert_term( $cat->name, FV_Competitor_Categories::$tax_slug, array(
                    'slug'  => $cat->slug . '2',
                ) );

                wp_update_term( $cats_relation[$cat->term_id]['term_id'], FV_Competitor_Categories::$tax_slug, array(
                    'term_group' => $cloned_contest->id,
                ) );
            }

            // LEST`s clone photos, if nees
            $metas = array();
            if ( $with_content ) {
                $to_clone_photos = ModelCompetitors::query()
                    ->where('contest_id', $contest_id)
                    ->find(false, false, false, true);

                $to_clone_photo_arr = null;
                foreach ($to_clone_photos as $to_clone_photo) {
                    $to_clone_photo_arr = (array) $to_clone_photo->jsonSerialize();
                    unset($to_clone_photo_arr['id']);
                    // change Contest_id to new
                    $to_clone_photo_arr['contest_id'] = $cloned_id;
                    // Insert to BD
                    $inserted_photo_ID = ModelCompetitors::query()->insert( $to_clone_photo_arr );

                    // Move META
                    $metas = $to_clone_photo->meta()->get_all();

                    if ($metas): foreach ($metas as $meta_row) :
                        unset($meta_row->ID);
                        $meta_row->contest_id = $cloned_id;
                        $meta_row->contestant_id = $inserted_photo_ID;
                        ModelMeta::query()->insert(  $meta_row );
                    endforeach; endif;
                    // Move META :: END

                    $cat_IDs = $to_clone_photo->getCategories( 'IDs' );

                    if ( $cat_IDs ) {
                        $cloned_competitor = fv_get_competitor($inserted_photo_ID, true, true);
                        // Move CATS

                        $cat_IDs_new = array();
                        foreach ($cat_IDs as $cat_ID) {
                            // Change OLD category ID to new
                            if ($cats_relation[$cat_ID]) {
                                $cat_IDs_new[] = $cats_relation[$cat_ID]['term_id'];
                            }
                        }

                        $cloned_competitor->setCategories($cat_IDs_new);

                        // Move CATS  :: END

                    }
                    unset($cat_IDs);
                    unset($metas);
                }

                $metas = $to_clone->meta()->get_all();

                if ($metas): foreach ($metas as $meta_row) :
                    unset($meta_row->ID);
                    $meta_row->contest_id = $cloned_id;
                    $meta_row->contestant_id = 0;
                    ModelMeta::query()->insert((array) $meta_row);
                endforeach; endif;
            }

            fv_AJAX_response( true, '', array('new_url' => admin_url('admin.php?page=fv&show=config&contest=' . $cloned_id)) );
        }

        fv_AJAX_response( false );
    }

    /**
     * Clone contest and may be it photos
     * Type: AJAX
     * From: Single contest editing page
     */
    public static function get_pages_and_posts()
    {
        if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv_admin_nonce', 'fv_nonce') ) {
            fv_AJAX_response( false, 'no secure' );
        }

        $response = array();

        global $wpdb;
        $pages = $wpdb->get_results(
            "SELECT `ID`,`post_title`,`post_status` FROM `{$wpdb->posts}`  WHERE (`post_type` = 'page' AND `post_status` IN ('publish', 'private', 'draft')) ORDER BY `ID` DESC LIMIT 0, " . FV_GET_MAX_PAGES . ";"
        );

        $pages_list = array();
        $post_title = '';
        foreach ( $pages as $page ) {
            $post_title = $page->post_title;
            if ( 'publish' != $page->post_status ) {
                $post_title .= ' [' . $page->post_status . ']';
            }
            $pages_list[] = array( 'id'=> (string)$page->ID, 'text'=>$post_title );
        }

        $response[] = array('children'=> $pages_list ,'text'=>'Pages');
        // Free memory
        unset($pages);
        unset($pages_list);

        if ( !empty($_GET['what_get']) && 'all' == $_GET['what_get'] ) {
            $posts = $wpdb->get_results(
                "SELECT `ID`,`post_title`,`post_status` FROM `{$wpdb->posts}`  WHERE (`post_type` = 'post' AND `post_status` IN ('publish', 'private', 'draft')) ORDER BY `ID` DESC LIMIT 0, " . FV_GET_MAX_POSTS . ";"
            );
            $posts_list = array();
            foreach ($posts as $post) {
                $post_title = $post->post_title;
                if ( 'publish' != $post->post_status ) {
                    $post_title .= ' [' . $post->post_status . ']';
                }
                $posts_list[] = array( 'id' => (string)$post->ID, 'text' => $post_title );
            }

            $response[] = array('children'=> $posts_list ,'text'=>'Posts');

            $response[] = array('children' => [['id' => -1, 'text' => 'Competitor single page (if Moderation is ON - please allow to view moderated photos in Settings )']], 'text' => 'Contest');
        }

        // Free memory
        unset($posts);
        unset($posts_list);

        fv_AJAX_response( true,'ready',array('list'=> $response) );
        // Example: https://jsfiddle.net/n9zhour9/1/
    }


    /**
     * Clone contest and may be it photos
     * Type: AJAX
     * From: Competitors list
     */
    public static function competitors_list__get_rows_for_page()
    {
        if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv-competitors-list-nonce', false, false) ) {
            fv_AJAX_response( false, 'no secure' );
        }

        if ( !isset($_REQUEST['paged']) ) {
            fv_AJAX_response( false, 'no "paged" param!' );
        }

        if ( !isset($_REQUEST['contest']) ) {
            fv_AJAX_response( false, 'no "contest ID" param!' );
        }

        require_once FV::$INCLUDES_ROOT . 'list-tables/class_competitors_list.php';
        //Create an instance of our package class...

        $contest_id = absint($_REQUEST['contest']);

        $listTable = new FV_List_Competitors( $contest_id );
        $listTable->ajax_response();
    }

}

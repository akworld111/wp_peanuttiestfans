<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The contest class.
 *
 * Used from doing most operations with contest and photos - add/edit/deleted
 *
 * @since      ?
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Contest
{

    /**
     * Insert a contest
     *
     * @param array   $options
     * @return int  The contest ID on success.
     */
    public static function create_contest( $options = array() )
    {
        /**
         * Filter: "fv/admin/create_contest/default_options"
         * @since 2.2.500
         *
         * @param array     Default contest options
         */
        $default_contest_options = apply_filters('fv/admin/create_contest/default_options', array(
            'name'              => 'New contest from ' . date("d-m-Y", current_time('timestamp')),
            'date_start'        => date("Y-m-d H:i:s", current_time('timestamp') - 7200),
            'date_finish'       => date("Y-m-d H:i:s", current_time('timestamp') + 1209600),
            'upload_date_start' => date("Y-m-d H:i:s", current_time('timestamp') - 7200),
            'upload_date_finish' => date("Y-m-d H:i:s", current_time('timestamp') + 1209600),
            'user_id'           => get_current_user_id(),
            'form_id'           => ModelForms::q()->getDefaultFormID(),
            'upload_enable'     => 1,
            'moderation_type'   => 'pre',
            'security_type'     => 'default',
            'voting_type'       => 'like',
            'timer'             => 'no',
            'max_uploads_per_user' => 'no',
        ));
        
        return ModelContest::query()->insert( array_merge($default_contest_options, $options) );
    }
    
    /**
     * Insert a post/page.
     *
     * @param int   $contest_id
     * @param array $page_args {
     *     An array of elements that make up a post to update or insert.
     *     @type mixed  $post_content          The post content. Default empty.
     *     @type string $post_title            The post title. Default empty.
     *     @type string $post_status           The post status. Default 'draft'.
     *     @type string $post_type             The post type. Default 'post'.
     *     @type array  $post_category         Array of category names, slugs, or IDs.

     * }
     * @return int|WP_Error The post ID on success. The value 0 or WP_Error on failure.
     */
    public static function create_contest_page($contest_id, $postarr)
    {
        $postarr = array_merge(array(
            'post_title'    =>  'New contest page',
            'post_type'     =>  'page',
            'post_status'   =>  'publish',
            'post_content'  =>  "[fv id={$contest_id}]",
        ), $postarr);

        /**
         * Filter "fv/admin/create_contest_page/postarr"
         * @since 2.2.500
         *
         * @param array     $postarr
         * @param integer   $contest_id
         */
        apply_filters('fv/admin/create_contest_page/postarr', $postarr, $contest_id);

        $page_id = wp_insert_post($postarr);

        if ( !is_wp_error($page_id) ) {
            ModelContest::q()->updateByPK( array('page_id'=>$page_id), $contest_id );
        }        
        
        return $page_id;
    }
    
    /**
     * Delete contest and all photos from it
     * + Meta & Votes
     * @param int $contest_id
     * 
     * @deprecated since 2.2.706
     */
    public static function delete($contest_id)
    {
        if ($contest_id) {
            $contest = new FV_Contest($contest_id);
            $contest->delete();
        }
    }

    /**
     * Save the contest
     *
     * @return int $contest_id
     */
    public static function save()
    {
        //$contestt_id = -1
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        if (!isset($_POST['fv_edit_contest_nonce']) || !wp_verify_nonce($_POST['fv_edit_contest_nonce'], 'fv_edit_contest_action')) {
            die('nonce');
            return;
        }

        if (!isset($_POST['contest_title'])) {
            return;
        }

        $voting_types = array('like' => 1, 'rate' => 1, 'rate_summary' => 1);

        $contest_options = array(
            'name' => sanitize_text_field(stripslashes($_POST['contest_title'])),
            'date_start' => sanitize_text_field($_POST['date_start']),
            'date_finish' => sanitize_text_field($_POST['date_finish']),
            'upload_date_start' => sanitize_text_field($_POST['upload_date_start']),
            'upload_date_finish' => sanitize_text_field($_POST['upload_date_finish']),
            'soc_title' => sanitize_text_field(stripslashes($_POST['fv_social_title'])),
            'soc_description' => sanitize_text_field(stripslashes($_POST['fv_social_descr'])),
            'soc_picture' => sanitize_text_field($_POST['fv_social_photo']),
            'user_id' => get_current_user_id(),
            'form_id' => isset($_POST['form_id']) ? (int)$_POST['form_id'] : 1,
            'upload_enable' => isset($_POST['upload_enable']) && $_POST['upload_enable'] ? 1 : 0,

            'voting_security' => sanitize_text_field($_POST['voting_security']),
            'voting_security_ext' => sanitize_text_field($_POST['voting_security_ext']),
            'voting_frequency' => sanitize_text_field($_POST['voting_frequency']),
            'voting_max_count' => !isset($_POST['voting_max_count']) ? 0 : (int)$_POST['voting_max_count'],
            'voting_max_count_total' => !isset($_POST['voting_max_count_total']) ? 0 : (int)$_POST['voting_max_count_total'],
            'limit_by_user' => in_array($_POST['limit_by_user'], array('yes', 'no', 'role')) ? $_POST['limit_by_user'] : 'no',
            'limit_by_role' => !empty($_POST['limit_by_role']) ? implode(',', $_POST['limit_by_role']) : '',

            'voting_type' => (isset($voting_types[$_POST['voting_type']])) ? $_POST['voting_type'] : 'like',
            'max_uploads_per_user' => (int)$_POST['max_uploads_per_user'],
            'show_leaders' => isset($_POST['show_leaders']) && $_POST['show_leaders'] ? 1 : 0,
            'lightbox_theme' => sanitize_text_field($_POST['lightbox_theme']),
            'upload_theme' => sanitize_text_field($_POST['upload_theme']),
            'timer' => sanitize_text_field($_POST['fv_timer']),
            'sorting' => (array_key_exists($_POST['sorting'], fv_get_sotring_types_arr())) ? $_POST['sorting'] : 'sorting',
            //'redirect_after_upload_to' => !isset($_POST['redirect_after_upload_to']) ? 0 : (int)$_POST['redirect_after_upload_to'],
            'moderation_type' => in_array($_POST['moderation_type'], array('pre', 'after')) ? $_POST['moderation_type'] : 'pre',
            //'page_id' => !isset($_POST['fv_page_id']) ? 0 : (int)$_POST['fv_page_id'],
            'cover_image' => !isset($_POST['fv_cover_image']) ? '' : (int)$_POST['fv_cover_image'],
            'type' => !isset($_POST['type']) ? 0 : (int)$_POST['type'],
            'winners_count' => !isset($_POST['winners_count']) ? 0 : (int)$_POST['winners_count'],
            'winners_pick' => sanitize_text_field($_POST['winners_pick']),
            'hide_votes' => in_array($_POST['hide_votes'], array('global', 'yes', 'no')) ? $_POST['hide_votes'] : 'global',
            'upload_limit_by_user' => in_array($_POST['upload_limit_by_user'], array('global', 'role', 'yes', 'no')) ? $_POST['upload_limit_by_user'] : 'global',
            'upload_limit_by_role' => !empty($_POST['upload_limit_by_role']) ? implode(',', $_POST['upload_limit_by_role']) : '',
            'upload_limit_size' => in_array($_POST['upload_limit_size'], array('global', 'yes', 'no')) ? $_POST['upload_limit_size'] : 'global',
            'upload_max_size' => absint( $_POST['upload_max_size'] ),
        );

        // Can be not passed from ver. 2.2.364
        if ( isset($_POST['page_id']) ) {
            $contest_options['page_id'] = (int)$_POST['page_id'];
        }
        if ( isset($_POST['redirect_after_upload_to']) ) {
            $contest_options['redirect_after_upload_to'] = (int)$_POST['redirect_after_upload_to'];
        }

        $contest_options = apply_filters('fv/before_save_contest', $contest_options);

        $contest_id = (int)$_POST['contest_id'];

        if ((int)$_POST['contest_id'] > 0) {
            ModelContest::query()->updateByPK($contest_options, $contest_id);
        } else {
            wp_add_notice(__('Saving falied! No Contest ID passed!', 'fv'), 'warning');
        }

        $contest = ModelContest::q()->findByPK( $contest_id, true );
        ## Let's save Meta fields
        if ( !empty($_POST['meta']) ) {
            $post_meta_fields = $_POST['meta'];
            FV_Admin_Contest_Meta_Helper::instance()->save_meta_fields($post_meta_fields, $contest);
        }

        do_action( 'fv/after_save_contest', $contest_options, $contest_id, $contest );

        wp_add_notice( __('Contest saved.', 'fv'), 'success' );

        return $contest_id;
    }


    /**
     * Delete photo and image from hosting
     *
     * @param FV_Competitor    $contestant
     * @deprecated since 2.2.701
     */
    public static function delete_contestant($contestant)
    {
        $contestant->delete();
    }

    /**
     * Return complete contest data
     *
     * @param int $contest_id
     * @return object Contest and all photos from it
     */
    public static function get_contest($contest_id)
    {
        $contest_options = ModelContest::query()->findByPK($contest_id);

        $contest_options->items = ModelCompetitors::query()->where('contest_id', $contest_id)->find(false, false, false, true);

        return $contest_options;
    }

    /**
     * @return array
     * @since 2.3.00
     */
    static function get_contests_list_flat() {

        $contests = [];

        $contestsQ = ModelContest::query()
            ->what_fields( ['id', 'name'] )
            ->find(false, false, OBJECT_K);

        foreach ($contestsQ as $item) {
            $contests[$item->id] = $item->id . ' / ' . $item->name;
        }

        return $contests;
    }

}

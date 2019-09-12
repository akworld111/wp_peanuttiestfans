<?php

/**
 * Abstract Contest Class
 *
 * @since       2.2.500
 * @package     FV/Abstracts
 * @category    Abstract Class
 * @author      Maxim K <support@wp-vote.net>
 * 
 * @property integer    $id
 * 
 * @property string     $name
 * 
 * @property string     $date_start
 * 
 * @property string     $date_finish
 * 
 * @property string     $upload_date_start
 * 
 * @property string     $upload_date_finish
 *
 * @property string     $upload_limit_by_user   [global, yes, no, role]
 * @property string     $upload_limit_by_role   Role
 *
 * @property string     $soc_title
 * 
 * @property string     $soc_description
 * 
 * @property string     $soc_picture
 * 
 * @property integer    $user_id %d
 * 
 * @property integer    $form_id %d
 * 
 * @property integer    $upload_enable %d
 * 
 * @property string     $security_type      DEPRECATED
 *
 * @property string     $voting_security
 *
 * @property string     $voting_security_ext
 *
 * @property string     $limit_by_user          [yes, no, role]
 * @property string     $limit_by_role          Role
 *
 * @property integer    $voting_max_count
 *
 * @property string     $voting_frequency
 * 
 * @property string     $voting_type
 * 
 * @property integer    $max_uploads_per_user %d
 * 
 * @property integer    $status %d
 * 
 * @property integer    $show_leaders %d
 * 
 * @property string     $lightbox_theme
 * 
 * @property string     $upload_theme
 * 
 * @property string     $timer
 * 
 * @property string     $sorting
 * 
 * @property integer    $redirect_after_upload_to %d
 * 
 * @property string     $moderation_type
 * 
 * @property integer    $page_id %d
 * 
 * @property integer    $cover_image %d
 * 
 * @property integer    $type %d
 *
 * @property integer    $winners_count %d
 * @property string     $winners_pick %s
 *
 * @property string     $categories_on %s       "", "single", "multi"
 *
 */
class FV_Contest extends FV_Abstract_Object
{
    const PUBLISHED = 0;
    const DRAFT     = 1;
    const FINISHED  = 2;
    const ARCHIVE   = 3;

    private $cover_image_ID = null;
    private $competitors_count = null;
    /**
     * @param int|object    $object         Contest or init.
     * @param bool          $from_cache
     */
    public function __construct( $object = 0, $from_cache = false )
    {
        $this->model = ModelContest::q();
        parent::__construct( $object, $from_cache );
        
        $this->properties['competitors_count'] = 'virtual';
        $this->properties['competitors_count_onmoderation'] = 'virtual';
        $this->properties['votes_count_summary'] = 'virtual';
        $this->properties['votes_count_fail_summary'] = 'virtual';

        $this->properties['cover_image_url'] = 'virtual';
        $this->properties['cover_text_voting'] = 'virtual';
        $this->properties['cover_text_upload'] = 'virtual';

        $this->properties['single_link_template'] = 'virtual';
        $this->properties['is_active'] = 'virtual';

        if ( $this->object ) {
            $this->meta = new FV_Contest_Meta( $this->object->id, false, false );
        }
    }

    protected function _meta_instance() {
        return new FV_Contest_Meta($this->object->id, false, false);
    }
    
    /**
     * Is Voting now Active?
     *
     * @since   2.2.500
     * @return  bool
     */
    public function isVotingDatesActive() {
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( $time_now > strtotime($this->object->date_start) && $time_now < strtotime($this->object->date_finish) ) {
            return true;
        }
        return false;
    }

    /**
     * Is Voting Future Active?
     *
     * @since   2.2.503
     * @return  bool
     */
    public function isVotingDatesFutureActive() {
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( strtotime($this->object->date_finish) > $time_now  ) {
            return true;
        }
        return false;
    }
    
    /**
     * Is Upload Active (Upload dates now Active && upload form ON in contest settings)?
     *
     * @since   2.2.500
     * @return  bool
     */
    public function isUploadActive() {
        if ( $this->object->upload_enable && $this->isUploadDatesActive() ) {
            return true;
        }
        return false;
    }

    /**
     * Is Upload dates now Active?
     *
     * @since   2.2.500
     * @return  bool
     */
    public function isUploadDatesActive() {
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if (
            $time_now > strtotime($this->object->upload_date_start) &&
            $time_now < strtotime($this->object->upload_date_finish)
        ) {
            return true;
        }
        return false;
    }



    /**
     * Is Upload Future Active?
     *
     * @since   2.2.503
     * @return  bool
     */
    public function isUploadDatesFutureActive() {
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( strtotime($this->object->upload_date_finish) > $time_now  ) {
            return true;
        }
        return false;
    }

    /**
     * is Moderation active?
     *
     * @since   2.2.503
     * @return  bool
     */
    public function isModerationActive() {
        return $this->moderation_type == "pre";
    }

    /**
     * is contest Finished?
     *
     * @since   2.2.503
     * @return  bool
     */
    public function isFinished() {
        return $this->status == self::FINISHED;
    }

    /**
     * @since   2.2.800
     * @return  bool
     */
    public function isCategoriesEnabled() {
        return (bool) $this->categories_on;
    }

    /**
     * @since   2.2.800
     * @return  bool
     */
    public function isMultiCategories() {
        return 'multi' == $this->categories_on;
    }

    /**
     * is contest Email Subscribe on?
     *
     * @since   2.2.710
     * @return  bool
     */
    public function isVoteEmailSubscribeRequired() {
        return $this->voting_security_ext == 'subscribe' || ($this->voting_security_ext == 'subscribeForNonUsers' && !is_user_logged_in());
    }

    /**
     * Contest status as text ['finished' or 'draft' or 'live']
     *
     * @since   2.2.503
     * @return  bool
     */
    public function getStatusText() {
        $statuses = array(
            self::FINISHED  => __('finished', 'fv'),
            self::DRAFT     => __('draft', 'fv'),
            self::PUBLISHED => __('live', 'fv'),
        );
        return $statuses[ $this->status ] ? $statuses[ $this->status ] : '?';
    }

    /**
     * @since   2.2.503
     * @return  bool
     */
    public function isManualWinnersPick() {
        return $this->winners_pick == 'manual';
    }

    /**
     * @since   2.2.800
     * @return  bool
     */
    public function isNeedHideVotes() {
        if ( $this->hide_votes == 'yes' ) {
            return true;
        }
        if ( $this->hide_votes == 'no' ) {
            return false;
        }
        return fv_setting('hide-votes', false);
    }
    
    /**
     * @since   2.2.800
     * @return  bool
     */
    public function isForUploadUserMustBeLogged() {
        if ( 'yes' == $this->upload_limit_by_user || 'role' == $this->upload_limit_by_user ) {
            return true;
        }
        if ( $this->upload_limit_by_user == 'no' ) {
            return false;
        }
        return get_option('fotov-upload-autorize', false) == ''? false : true;
    }

    /**
     * @since   2.2.800
     * @return  bool
     */
    public function isUserHaveEnoughPermissionsForUpload() {
        if ( ! $this->isForUploadUserMustBeLogged() ) {
            return true;
        }

        $user_id = get_current_user_id();

        if ( !$user_id ) {
            return false;
        }

        if ( $user_id && 'role' != $this->upload_limit_by_user ) {
            return true;
        }

        if ( $this->upload_limit_by_user == 'role' && $this->upload_limit_by_role ){
            $limit_by_role_arr = explode(',' , $this->upload_limit_by_role);
            $user_meta = get_userdata($user_id);
            $user_roles = $user_meta->roles;
            // If user does not have any of required roles
            if ( !array_intersect($user_roles, $limit_by_role_arr) ) {
                return false; // No required role
            }
        }

        return apply_filters('fv/public/contest/user_have_enough_permissions_for_upload', true, $this, $user_id);
    }
    
    /**
     * @since   2.2.800
     * @return  bool
     */
    public function getImageSizeLimit() {
        if ( $this->upload_limit_by_user == 'yes' && $this->upload_max_size > 0 ) {
            return $this->upload_max_size;
        }
        if ( $this->upload_limit_by_user == 'no' ) {
            return 0;
        }
        return get_option('fotov-upload-photo-limit-size', 0);
    }

    /**
     * @since   2.2.605
     * @var     string  $description
     * @return  string
     */
    public function setDescription( $description ) {

        $description = wp_kses_post( trim($description) );

        $description_row = $this->meta()->get_row('description');

        if ( $description_row ) {
            ModelMeta::q()->updateByPK(
                array( 'value' => $description ),
                $description_row->ID
            );
        } else {
            // Do not save empty values
            if ( !$description ) {
                return false;
            }

            ModelMeta::q()->insert( array(
                'contest_id'    => $this->id,
                'contestant_id' => 0,
                'meta_key'      => 'description',
                'custom'        => 1,
                'value'         => $description
            ) );
        }
    }

    /**
     * Return contest Terms array
     *
     * @param string $format objects/IDs/string
     * { 
        [term_id]     => 162
        [name]        => Test
        [slug]        => test
        [term_group]  => 0
        [term_taxonomy_id] => 170
        [taxonomy]    => fv-category
        [description] => 
        [parent]      => 0
        [count]       => 2
     * }
     *
     * @param string $stringDelimiter
     * 
     * @since   2.2.800
     * @return  string
     */
    public function getCategories( $format = 'objects', $stringDelimiter = ',' ) {
        // Filter Categories by ContestID
        FV_Competitor_Categories::admin_add_filter_get_terms_by_contest( $this->id );

        // TODO - merge second param with First (since WP 4.5)
        $terms = get_terms( FV_Competitor_Categories::$tax_slug, array(
            'page' => 1,
            'number' => 1200,
            'hide_empty' => 0,
            'orderby' => 'slug',
        ) );

        if ( !$terms ) {
            if ( $format != 'string' ) {
                return $terms;
            } else {
                return '';
            }
        }

        switch ($format){
            case 'objects':
                return $terms;
            case 'IDs':
                return array_map(function($term) {
                    return $term->term_id;
                }, $terms);
            case 'string':
                return implode($stringDelimiter, array_map(function($term) {
                    return $term->name;
                }, $terms) );
        }        
    }

    /**
     * Return contest Description from "Description & Rules" contest tab
     *
     * @since   2.2.605
     * @return  string
     */
    public function getDescription() {
        return $this->meta()->get_value('description');
    }

    /**
     * @since   2.2.503
     * @return  bool
     */
    public function getWinnersPickTitle() {
        $types = fv_get_winners_pick_types();
        return isset($types[$this->winners_pick]) ? $types[$this->winners_pick] : '?';
    }
    
    /**
     * Return url where contest placed
     * Require "Page where contest placed" option
     *
     * @return string   Contest URL
     */
    public function getPublicUrl() {
        $url = '';
        if ( $this->object->page_id ) {
            $url = get_permalink( $this->object->page_id );
        }
        return apply_filters( 'fv/public/contest/link', $url, $this);
    }

    /**
     * @param string $section
     * @param array $query_args     ['query_key'=>'query_val']
     * @return string
     */
    public function getAdminUrl( $section = 'config', $query_args = array() ) {
        $url = admin_url('admin.php?page=fv&show=' . $section . '&contest=' . $this->id);
        if ( !empty($query_args) ) {
            $url = add_query_arg( $query_args, $url );
        }
        return apply_filters( 'fv/public/contest/admin_link', $url, $section, $this);
    }

    /**
     * Return cover "image ID"
     *
     * @return int|null
     * @since 2.2.502
     */
    public function getCoverID() {

        if ( $this->cover_image_ID !== null ) {
            return $this->cover_image_ID;
        }

        if ( empty($this->object->cover_image) ) {
            $first_photo = ModelCompetitors::query()
                ->where_all( array('contest_id' => $this->object->id, 'status' => ST_PUBLISHED) )
                ->where_not( 'image_id', '' )
                ->limit(1)
                ->sort_by('id', 'ASC')
                ->findRow();
            if ( !empty($first_photo) ) {
                $this->cover_image_ID = $first_photo->image_id;
            } else {
                $this->cover_image_ID = 0;
            }

        } else {
            $this->cover_image_ID = $this->object->cover_image;
        }

        return $this->cover_image_ID;
    }

    /**
     * Return image url with cover image SRC
     * If no "cover image" picked in settings - used first competitor image
     *
     * @param array|bool    $thumb_size
     *
     * @return string
     */
    public function getCoverImageUrl($thumb_size = false) {
        $thumbArr = $this->getCoverThumbArr($thumb_size);
        if ( !empty($thumbArr) ) {
            return apply_filters('fv/public/contest/cover_image/url', $thumbArr[0], $this);
        }

        return apply_filters('fv/public/contest/cover_image/url', FV::$ASSETS_URL . 'img/no-photo.png', $this);
    }

    /**
     * Return array with cover image thumbnail SRC, similar to wp_get_attachment_image_src()
     * If no "cover image" picked in settings - used first competitor image
     *
     * @param array|bool     $thumb_size
     * @param bool           $full_url
     *
     * @return string
     */
    public function getCoverThumbArr($thumb_size = false, $full_url = false) {
        $cover_thumb = array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );

        if ( !$thumb_size ) {
            $thumb_size = array(
                'width' => 400,
                'height' => 400,
                'crop' => true,
                'size_name' => 'fv-thumb-list',
            );
        }

        if ( !isset($thumb_size['size_name']) ) {
            $thumb_size['size_name'] = 'fv-thumb-list';
        }

        if ( $this->getCoverID() ) {
            if (fv_setting('thumb-retrieving', 'plugin_default') == 'plugin_default') {
                // Getting an attachment image
                if (!$full_url) {
                    $full_url_arr = wp_get_attachment_image_src($this->getCoverID(), 'full');
                    $full_url = $full_url_arr[0];
                }

                $cover_thumb = FvFunctions::image_downsize($this->getCoverID(), $thumb_size, $full_url);
            } else {
                $cover_thumb = wp_get_attachment_image_src($this->getCoverID(), array($thumb_size['width'], $thumb_size['height']));
            }
        } else {
            $first_photo = ModelCompetitors::query()
                ->where_all( array('contest_id' => $this->id, 'status' => ST_PUBLISHED) )
                ->limit(1)
                ->sort_by('id', 'ASC')
                ->findRow();
            if ( !empty($first_photo) ) {
                $cover_thumb = $first_photo->getThumbArr($thumb_size);
            }
        }

        return apply_filters( 'fv/public/contest/cover_thumb/array', $cover_thumb, $this);
    }


    /**
     * Return thumbnail url with cover image SRC
     * If no "cover image" picked in settings - used first competitor image
     *
     * @param $thumb_size
     * @param bool $full_url
     *
     * @return string
     */
    public function getCoverThumbUrl($thumb_size, $full_url = false) {
        $cover_thumb_url = '';

        $cover_thumb_arr = $this->getCoverThumbArr($thumb_size, $full_url);
        if ( is_array($cover_thumb_arr) ) {
            $cover_thumb_url = $cover_thumb_arr[0];
        }

        return apply_filters( 'fv/public/contest/cover_thumb/url', $cover_thumb_url, $this);
    }


    /**
     * Get competitors total count in contest
     * (depends on param $published return total or only PUBLISHED count)
     *
     * @param bool $published
     * @param bool $cached
     *
     * @return integer
     */
    public function getCompetitorsCount($published = true, $cached = true) {
        if ( !$this->id ) {
            return null;
        }

        if ( $cached && $this->competitors_count ) {
            return $this->competitors_count;
        }
        $query = ModelCompetitors::q()->where( 'contest_id', $this->id )
            ->sort_by('votes_count', 'DESC');

        if ($published) {
            $query->where('status', FV_Competitor::PUBLISHED);
        }

        return $this->competitors_count = $query->find(true);
    }

    /**
     * Get competitors in this contest
     * Helper function that uses ModelCompetitors to fetch data
     *
     * @param bool $published
     * @param bool $only_count
     * @param bool $get_var
     * @param bool $for_list
     * @param bool $get_meta
     * @param bool $prefetch_attachments
     * @param bool $key_by
     *
     * @return FV_Competitor[]|null
     */
    public function getCompetitors($published = true, $only_count = false, $get_var = false, $for_list = false, $get_meta = false, $prefetch_attachments = false, $key_by = false) {
        if ( !$this->id ) {
            return null;
        }

        $query = ModelCompetitors::q()->where( 'contest_id', $this->id );

        if ($published) {
            $query->where('status', FV_Competitor::PUBLISHED);
        }

        if ( defined("FV_ADMIN__COMPETITORS_LIST__FETCH_USER_EMAIL") ) {
            $query->withAuthorEmailWP();
        }

        return $query->find($only_count, $get_var, $for_list, $get_meta, $prefetch_attachments, $key_by);
    }

    /**
     * Finis Contest and may be pick winners (if auto pick enabled)
     */
    public function finish()
    {
        /** @var FV_Competitor[] $entries */
        $place = 1;

        $entries_rand_keys = array();

        // Выберем победителейм п
        if ($this->object->winners_pick != "manual") {
            $entriesQ = ModelCompetitors::q()->where( 'contest_id', $this->object->id )
                ->limit( $this->object->winners_count )
                ->where( 'status', FV_Competitor::PUBLISHED )
                ->what_fields( array('id', 'name', 'votes_count', 'votes_average', 'rate_summary') );

            switch ($this->object->winners_pick) {
                case "auto":
                    $entries = $entriesQ->sort_by_votes( $this->voting_type )
                        ->find(false, false, false, false, false, 'id');
                    break;
                case "auto_rand":
                    $entries = $entriesQ->sort_by(' RAND() ', 'ASC')
                        ->find(false, false, false, false, false, 'id');
                    break;
                case "auto_rand_top10":
                    $entries = $entriesQ->sort_by_votes( $this->voting_type )
                        ->limit(10)
                        ->find(false, false, false, false, false, 'id');

                    $entries_rand_keys = array_rand ($entries, $this->object->winners_count);
                    break;
                case "auto_rand_top20":
                    $entries = $entriesQ->sort_by_votes( $this->voting_type )
                        ->limit(20)
                        ->find(false, false, false, false, false, 'id');

                    $entries_rand_keys = array_rand ($entries, $this->object->winners_count);
                    break;
            }

            // Reset OLD Winners
            $this->resetWinners();

            // Save the place

            if ( in_array($this->object->winners_pick, array("auto_rand_top10", "auto_rand_top20") ) ) {
                foreach ($entries_rand_keys as $entry_id) {
                    $entries[$entry_id]->place = $place;
                    $entries[$entry_id]->save();
                    $place++;
                }
            } else{
                foreach ($entries as $entry) {
                    $entry->place = $place;
                    $entry->save();
                    // Send mail to Winners
                    FV_Notifier::sendCompetitorNotificationToUser('fv/competitor/to-user/winner', $entry);
                    $place++;
                }
            }
        }

        // !!! Пометим конкурс как завершенный !!!
        $this->status = self::FINISHED;
        $this->save();
        
        //FV_Notifier::sendContestFinishedToAdmin($this);
        FV_Notifier::sendContestNotificationToAdmin('fv/contest/to-admin/finished', $this);
        
        do_action('fv/contest/finished', $this);
    }

    /**
     * Reset Winners
     */
    public function resetWinners()
    {
        global $wpdb;
        $wpdb->query(
            sprintf( 'UPDATE `%s` SET `place` = NULL WHERE `place` IS NOT NULL AND `contest_id` = %d', ModelCompetitors::q()->tableName(), $this->id )
        );
    }

    /**
     * Delete Contest and all data from database ( competitors, votes, meta )
     *
     * @since 2.2.701
     */
    public function delete()
    {
        $contest_competitors = ModelCompetitors::query()->where('contest_id', $this->id)->find();
        if ( !empty($contest_competitors) ) :
            foreach ( $contest_competitors as $competitor ) {
                $competitor->delete();
            }
        endif;
        ModelVotes::query()->deleteByContestID($this->id);
        ModelMeta::q()->deleteByContestID($this->id);
        
        // Finally delete
        ModelContest::q()->delete($this->id);
    }

}
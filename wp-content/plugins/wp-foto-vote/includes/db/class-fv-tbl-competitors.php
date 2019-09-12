<?php

/**
 * Class ModelCompetitors
 */
class ModelCompetitors extends FvQuery
{
    private $prefetchCategories = false;

    /**
     * Returns the static query of the specified class.
     * @param string $className active record class name.
     * @return self the static query class
     */
    public static function query($className = __CLASS__)
    {
        return new $className();
    }
    
    /**
     * Returns the static query of the specified class.
     * @param string $className active record class name.
     * @return self the static query class
     */
    public static function q($className = __CLASS__)
    {
        return new $className();
    }


    public function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . "fv_competitors";
    }

    public function fields()
    {
        return array(
            'id'            => '%d',
            'name'          => '%s',
            'description'   => '%s',
            'full_description' => '%s',
            'social_description' => '%s',
            'additional'    => '%s',
            'url'           => '%s',
            'url_min'       => '%s',
            'storage'       => '%s',
            'options'       => '%s',
            'image_id'      => '%d',
            'mime_type'     => '%s',
            'contest_id'    => '%d',

            'votes_count'   => '%d',
            'rating_summary'=> '%f',
            'votes_count_fail' => '%d',
            'votes_average' => '%f',
            
            'likes'         => '%d',    // Hot Or Not
            'dislikes'      => '%d',    // Hot Or Not
            'views'         => '%d',    // Hot Or Not
            
            'status'        => '%d',
            'added_date'    => '%s',
            'upload_info'   => '%s',
            'user_email'    => '%s',
            'user_id'       => '%d',
            'user_ip'       => '%s',
            'place'         => '%d',
            'place_caption' => '%s',
            'order_position'=> '%d',
        );

    }
    /**
     * @var integer
     */
    protected $category_id;
    protected $contest_id;

    /**
     * @return self
     */
    public function withAuthorName()
    {
        global $wpdb;
        $this
            ->leftJoin( $wpdb->users, "u", "`u`.`ID` = `t`.`user_id`" )
            ->what_fields( array('t.*', '`u`.`display_name` as `author_name`', '`u`.`user_nicename` as `author_nicename`') );
            //->join( 'LEFT OUTER', $wpdb->usermeta, "um", "`um`.`user_id` = `t`.`user_id` AND `um`.`meta_key` = '_avatar_att_id'", array('meta_value') );

        return $this;
    }

    /**
     * @return self
     */
    public function withContest()
    {
        $this->leftJoin( ModelContest::q()->tableName(), "c", "`c`.`id` = `t`.`contest_id`" );

        return $this;
    }

    /**
     * @return self
     */
    public function withAuthorEmailWP()
    {
        global $wpdb;
        $this
            ->leftJoin( $wpdb->users, "u", "`u`.`ID` = `t`.`user_id`" )
            ->what_fields( array('t.*', '`u`.`user_email` as `wp_user_email`') );

        return $this;
    }

    /**
     * Add left join for Meta table
     *
     * @param array    $fields     array of fields ( 'user_id' )
     * @param string   $where      WHERE condition, like "`comp`.`user_id` = 3"
     * @return self
     */
    public function leftJoinMeta( $fields = array(), $where = '' )
    {
        return $this->leftJoin( ModelMeta::query()->tableName(), "cp_meta", "`cp_meta`.`contestant_id` = `t`.`id`", $fields, $where )
            ->where_custom_sql('`cp_meta`.`contestant_id` != 0');
    }

    /**
     * find records in table by Primary KEY and Unserialize options
     *
     * @param  int $contest_id
     *
     * @return FV_Competitor[]
     */
    public function onlyWinners($contest_id)
    {
        return $this
            ->where_all( array('contest_id'=> $contest_id, 'status'=> ST_PUBLISHED) )
            ->where_custom('place', 'IS NOT NULL')
            ->sort_by('place', 'ASC');
    }


    /**
     * find records in table by Primary KEY and Unserialize options
     *
     * @param  int $id
     * @param  bool $from_cache
     * @param string $res_type
     * @param bool $get_meta
     * @param bool $plain       Do we need return original instead of FV_Competitor?
     *
     * @return FV_Competitor|object
     */
    public function findByPK($id, $from_cache = false, $res_type = OBJECT, $get_meta = false, $plain = false)
    {
        $photo = parent::findByPK($id, $from_cache, $res_type);
        if ( !is_wp_error($photo) && !empty($photo) && isset($photo->id) ) {
            $photo = $this->unsplashe($photo);
            if ( !$plain ) {
                return new FV_Competitor($photo, false, $get_meta);
            }
        }
        return $photo;
    }

    /**
     * Compose & execute our query and Unserialize options
     *
     * @return FV_Competitor row
     */
    public function findRow()
    {
        $photo = parent::findRow();
        if ( !is_wp_error($photo) && !empty($photo) && isset($photo->id) ) {
            $photo = $this->unsplashe($photo);
            return new FV_Competitor($photo, false, false);
        }
        return $photo;
    }

    /**
     * allow find records in table by Meta
     *
     * @param  string $meta_key
     * @param  string $meta_val     '*' means any Value
     * @param  string $value_condition     '=', '>', '<', '<>', 'IS NULL', 'IS NOT NULL'
     *
     * @return self
     */
    public function byMeta($meta_key, $meta_val, $value_condition = '=')
    {
        $this->leftJoin(
            ModelMeta::q()->tableName(),
            "M",
            "`M`.`contestant_id` = `t`.`id`",
            array('ID','meta_key','value')
        );
        $where_sql = '`M`.`meta_key` = \'' . $meta_key . '\'';
        // IF need select with specified Value
        if ( '*' !== $meta_val ){
            if ( in_array($value_condition, array('=', '>', '<', '<>')) ) {
                $this->where_custom_sql('`M`.`value` ' . $value_condition . ' \'' . $meta_val . '\'');
            } elseif ( in_array($value_condition, array('IS NULL', 'IS NOT NULL')) ){
                $this->where_custom_sql('`M`.`value` ' . $value_condition);
            }
        }

        return $this;
    }

    /**
     * allow find records by Category
     *
     * @param  integer  $categoryID
     * @param  integer  $contestID
     *
     * @return self
     */
    public function byCategory($categoryID, $contestID)
    {
        global $wpdb;
        $this->category_id = $categoryID;
        $this->where_custom_sql(" t.id IN ( SELECT tr.object_id-990000000000000.0 FROM {$wpdb->term_relationships} as tr 
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) 
            INNER JOIN {$wpdb->terms} AS terms ON (terms.term_id = tt.term_id) 
            WHERE tt.taxonomy = 'fv-category' AND terms.term_group = {$contestID} AND terms.term_id = {$categoryID}) ");

        return $this;
    }

    /**
     * allow find records by Category
     * renamed to by_category_slug
     *
     * @param  string  $category_slug
     * @param  integer  $contestID
     *
     * @return self
     */
    public function byCategorySlug($category_slug, $contestID)
    {
        global $wpdb;
        $category_slug = sanitize_title($category_slug);
        //$this->category_id = $categoryID;
        $this->where_custom_sql(" t.id IN ( SELECT tr.object_id-990000000000000.0 FROM {$wpdb->term_relationships} as tr 
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) 
            INNER JOIN {$wpdb->terms} AS terms ON (terms.term_id = tt.term_id) 
            WHERE tt.taxonomy = 'fv-category' AND terms.term_group = {$contestID} AND terms.slug = '{$category_slug}') ");

        return $this;
    }

    /**
     * allow filter records by Contest
     *
     * @param  integer  $contestID
     * @return self
     */
    public function byContest($contestID)
    {
        $this->contest_id = $contestID;
        return $this;
    }

    /**
     * Compose the actual SQL WHERE query
     * @return string
     */
//    public function compose_where_sql()
//    {
//        $where_sql = parent::compose_where_sql();
//
//        if ( !$this->contest_id ) {
//            return $where_sql;
//        }
//
//        if ( $where_sql ) {
//
//        }
//        return $where_sql;
//    }

    /**
     * find records in table by Meta
     *
     * @param  string $meta_key
     * @param  string $meta_val     '*' means any Value
     * @param  bool $for_list
     * @param  bool $get_meta
     *
     * @return array
     */
    public function findByMeta($meta_key, $meta_val, $for_list = false, $get_meta = false)
    {
        $where_sql = '`M`.`meta_key` = \'' . $meta_key . '\'';
        // IF need select with specified Value
        if ( '*' !== $meta_val ) {
            $where_sql .= ' AND `M`.`value` = \'' . $meta_val . '\'';
        }
        $this->leftJoin(
            ModelMeta::q()->tableName(),
            "M",
            "`M`.`contestant_id` = `t`.`id`",
            array('ID','meta_key','value')
        )->where_custom_sql($where_sql);

        return $this->find(false, false, $for_list, $get_meta);
    }

    public function withPrefetchCategories() {
        $this->prefetchCategories = true;
        return $this;
    }

    /**
     * Compose & execute our query and Unserialize options
     *
     * @param   $only_count             bool    Whether to only return the row count
     * @param   $get_var                bool
     * @param   $for_list               bool    Is this query for "Show_contest" function ?
     * @param   $get_meta               bool
     * @param   $prefetch_attachments   bool
     * @param   $key_by                 bool
     *
     * @return FV_Competitor[]
     */
    public function find($only_count = false, $get_var = false, $for_list = false, $get_meta = false, $prefetch_attachments = false, $key_by = false)
    {
        if ( $for_list || $get_meta ) {
            ## Arrays keys as IDS
            ## ID => DATA Array
            $res = parent::find($only_count, $get_var, OBJECT_K);
        } else {
            $res = parent::find($only_count, $get_var);
        }

//
//        global $wpdb;
//        fv_dump($wpdb->last_query);

        if (!$only_count && !empty($res)) {

            $prefetch_att_IDs = array();

            $photo_IDs = array();

            foreach ($res as $photo) {
                $photo_IDs[ FV_Competitor::CATEGORY_START_ID + $photo->id ] = $photo->id;
                if ( $prefetch_attachments && $photo->image_id && $photo->image_id > 0 ) {
                    $prefetch_att_IDs[] = (int)$photo->image_id;
                }
            }

            ## Prefetch posts (attachments) data, to avoid SQL for each image ##
            if ( !empty($prefetch_att_IDs) ) {
                $post__in = implode(',', $prefetch_att_IDs);
                global $wpdb;
                $prefetch_atts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID IN ({$post__in});" );
                update_post_cache($prefetch_atts);
                update_meta_cache('post', $post__in);

                unset($prefetch_atts);
                unset($post__in);
                unset($prefetch_att_IDs);
            }
            ## Prefetch :: END ##

            ## get all contest meta and sort it
            $meta_by_photo = array();
            // TODO - rewrite
            if ($get_meta) {
                $first_row = reset($res);
                $meta_by_contest = ModelMeta::q()
                    //->where('contest_id', $first_row->contest_id)
                    ->where_in('contestant_id', $photo_IDs)
                    ->find();
                $meta_by_photo = array();
                if (!empty($meta_by_contest)) {
                    foreach ($meta_by_contest as $meta_row_id => $meta_row) {
                        $meta_by_photo[$meta_row->contestant_id][] = $meta_row;
                        ## Free memory
                        unset($meta_by_contest[$meta_row_id]);
                    }
                }
            }

            //fv_dump($res);


            // Preload Categories for List of entries
            $terms_by_competitor_all = array();
            if ( $this->prefetchCategories && $res ) {

                $terms_all = wp_get_object_terms( array_keys($photo_IDs), FV_Competitor_Categories::$tax_slug, array(
                    'fields' => 'all_with_object_id',
                    'orderby' => 'name',
                    'update_term_meta_cache' => false,
                ) );

                foreach ($terms_all as $term_one) {
                    if ( isset($photo_IDs[$term_one->object_id]) ) {
                        $terms_by_competitor_all[ $photo_IDs[$term_one->object_id] ][] = $term_one;
                    }
                }

            }

            /** @var FV_Competitor[] $new_res */
            $new_res = array();

            foreach ($res as $key => $photo) {
                if ( is_wp_error($photo) || !isset($photo->id) ) { continue; }

                if ($key_by && isset($photo->$key_by)) {
                    $key = $photo->$key_by;
                }

                if ( $get_meta ) {
                    if ( isset($meta_by_photo[$photo->id]) ) {
                        $new_res[$key] = new FV_Competitor( $photo, false, true, $meta_by_photo[$photo->id] );
                    } else {
                        $new_res[$key] = new FV_Competitor( $photo, false, true, null );
                    }
                } else {
                    $new_res[$key] = new FV_Competitor( $photo, false, false, false );
                }

                if ( $terms_by_competitor_all && isset($terms_by_competitor_all[$photo->id]) ) {
                    $new_res[$key]->_setCategoriesCache( $terms_by_competitor_all[ $photo->id ] );
                }

            }
            unset($meta_by_photo);
            $res = $new_res;
            unset($new_res);

        }

        // fv_unsplashe
        return $res;
    }

    /**
     * @param string $voting_type
     * @return string
     * @since 2.2.200
     */
    public function getVotesFieldName($voting_type = 'like')
    {
        if ($voting_type == 'like') {
            return 'votes_count';
        }
        if ($voting_type == 'rate_summary') {
            return 'rating_summary';
        }
        return 'votes_average';
    }

    /**
     * @param string $voting_type
     * @return string
     * @since 2.2.200
     */
    public static function getVotesFieldNameS($voting_type = 'like')
    {
        if ($voting_type == 'like') {
            return 'votes_count';
        }
        if ($voting_type == 'rate_summary') {
            return 'rating_summary';
        }

        return 'votes_average';
    }

    /*
     * Increase votes count
     * @return int|string
     */

    public function increaseVotesCount($id, $voting_type = 'like', $rating = false, $hide_votes = false)
    {
        global $wpdb;

        if ($voting_type == 'rate' && $rating) {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `votes_average` = ( `votes_average`*`votes_count`+CAST(' . $rating . ' AS DECIMAL(5,3)) ) / (`votes_count` + 1),' .
                ' `votes_count` = `votes_count` + 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );
        } elseif ($voting_type == 'rate_summary' && $rating) {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `rating_summary` = `rating_summary` + CAST(' . $rating . ' AS DECIMAL(9,2)),' .
                ' `votes_count` = `votes_count` + 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );
        } else {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `votes_count` = `votes_count` + 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );

        }

        if ( $hide_votes ) {
            return 0;
        }

        FvLogger::checkDbErrors($r);

        $row = ModelCompetitors::query()
            ->where('id', $id)
            ->what_fields( ['id', 'contest_id', 'votes_count', 'votes_average', 'rating_summary'] )
            ->findRow();
        /*
                var_dump($row);
                var_dump($wpdb->last_query);
                var_dump($wpdb->last_result);
        */
        return $row->getVotes(false, $voting_type);
    }

    /*
     * Increase votes count
     * @return int|string
     */

    /**
     * @param integer   $id             Competitor ID
     * @param string    $voting_type    "like" or "rate"
     * @param float    $rating
     *
     * @return false|int
     */
    public function decreaseVotesCount($id, $voting_type = 'like', $rating = 5.0)
    {
        global $wpdb;

        if ( $voting_type == 'rate' && $rating) {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `votes_average` = ( `votes_average`*`votes_count` - ' . $rating . ' ) / (`votes_count` - 1),' .
                ' `votes_count` = `votes_count` - 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );
        } elseif ( $voting_type == 'rate_summary' && $rating) {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `rating_summary` = `rating_summary` - CAST(' . $rating . ' AS DECIMAL(5,1)),' .
                ' `votes_count` = `votes_count` - 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );
        } else {
            $r = $wpdb->query(
                'UPDATE ' . $this->tableName() .
                ' SET `votes_count` = `votes_count` - 1' .
                ' WHERE `id` = ' . intval($id) . ';'
            );

        }

        FvLogger::checkDbErrors($r);
        
        /*
                var_dump($row);
                var_dump($wpdb->last_query);
                var_dump($wpdb->last_result);
        */
        return $r;
    }

    /**
     * Increase votes count
     * @param $id
     * @return false|int
     */
    public function increaseFailVotesCount($id)
    {
        global $wpdb;

        $r = $wpdb->query(
            'UPDATE ' . $this->tableName() .
            ' SET `votes_count_fail` = `votes_count_fail` + 1' .
            ' WHERE `id` = ' . intval($id) . ';'
        );

        FvLogger::checkDbErrors($r);

        return $r;
    }

    /**
     * Set query ORDER BY based on contest "sorting" field value
     *
     * @param object $contest
     *
     * @return self FvQuery
     */
    public function set_sort_by_based_on_contest($contest)
    {
        switch ($contest->sorting) {
            case 'newest':
                $this->sort_by('`t`.`id`', self::ORDER_DESCENDING);
                break;
            case 'oldest':
                $this->sort_by('`t`.`id`', self::ORDER_ASCENDING);
                break;
            case 'popular':
                $this->sort_by('t.' .  $this->getVotesFieldName($contest->voting_type), self::ORDER_DESCENDING);
                break;
            case 'unpopular':
                $this->sort_by('t.' . $this->getVotesFieldName($contest->voting_type), self::ORDER_ASCENDING);
                break;
            case 'random':
                $this->sort_by(' RAND() ', self::ORDER_ASCENDING);
                break;
            case 'alphabetical-az':
                $this->sort_by('`t`.`name`', self::ORDER_ASCENDING);
                break;
            case 'alphabetical-za':
                $this->sort_by('`t`.`name`', self::ORDER_DESCENDING);
                break;
            default:
                $this->sort_by('IFNULL(order_position, 99999)', self::ORDER_ASCENDING);
                $this->sort_by('`t`.`id`', self::ORDER_DESCENDING);
                break;
        }
        return $this;
    }
    /**
     * Set query ORDER BY based on contest "sorting" field value
     *
     * @param string $type
     *
     * @return self
     */
    public function set_sort_by_type($type)
    {
        switch ($type) {
            case 'newest':
                $this->sort_by('added_date', self::ORDER_DESCENDING);
                break;
            case 'oldest':
                $this->sort_by('added_date', self::ORDER_ASCENDING);
                break;
            case 'popular':
                $this->sort_by('votes_count', self::ORDER_DESCENDING);
                break;
            case 'unpopular':
                $this->sort_by('votes_count', self::ORDER_ASCENDING);
                break;
            case 'random':
                $this->sort_by(' RAND() ', self::ORDER_ASCENDING);
                break;
            case 'alphabetical-az':
                $this->sort_by('name', self::ORDER_ASCENDING);
                break;
            case 'alphabetical-za':
                $this->sort_by('name', self::ORDER_DESCENDING);
                break;
            default:
                $this->sort_by('IFNULL(order_position, 99999)', self::ORDER_ASCENDING);
                $this->sort_by('id', self::ORDER_DESCENDING);
                break;
        }
        return $this;
    }

    /**
     * delete record in table
     * @param   $id    int
     * @return  bool    MySQL query result
     */
    public function deleteByContestID($id)
    {
        global $wpdb;

        $r = $wpdb->query(
            $wpdb->prepare(
                " DELETE FROM " . $this->tableName() . " WHERE `contest_id` = '%d'; ", $id
            )
        );
        $this->checkDbErrors();
        return $r;
    }

    public function updateByPK($data, $pkID)
    {
        // Tweak
        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = maybe_serialize($data['options']);
        }
        return parent::updateByPK($data, $pkID);
    }

    /**
     * @param array $data
     * @return int
     * @since 2.2.708
     */
    public function insert($data)
    {
        // Tweak
        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = maybe_serialize($data['options']);
        }
        return parent::insert($data);
    }

    public function sort_by_votes($voting_type = 'like', $order = 'DESC')
    {
        return $this->sort_by( $this->getVotesFieldName($voting_type), $order );
    }

    public function install()
    {
        $full_description_size = self::getFullDescriptionSize();
        $sql = "CREATE TABLE " . $this->tableName() . " (
               id int(7) NOT NULL AUTO_INCREMENT,
               contest_id int(7) NOT NULL,
               name varchar(255) NOT NULL,
               description varchar(500) DEFAULT NULL,
               full_description varchar({$full_description_size}) DEFAULT NULL,
               social_description varchar(150) DEFAULT NULL,
               additional varchar(255) DEFAULT '',
               url varchar(255) NOT NULL,
               url_min varchar(255) DEFAULT NULL,
               storage varchar(15) DEFAULT NULL,
               options varchar(500) DEFAULT NULL,
               image_id bigint(20) NOT NULL,
               mime_type varchar(30) DEFAULT NULL,
               votes_count int(7) NOT NULL DEFAULT '0',
               rating_summary float(9,2) NOT NULL DEFAULT '0.0',
               votes_average float(5,3) NOT NULL DEFAULT '0.0',
               votes_count_fail int(8) DEFAULT '0',
               likes int(7) UNSIGNED NOT NULL DEFAULT '0',
               dislikes int(7) UNSIGNED NOT NULL DEFAULT '0',
               views int(9) UNSIGNED NOT NULL DEFAULT '0',
               added_date bigint(11) NOT NULL DEFAULT '0',
               upload_info varchar(500) DEFAULT NULL,
               user_email varchar(100) DEFAULT NULL,
               user_id int(7) DEFAULT '0',
               user_ip varchar(45) DEFAULT NULL,
               status INT( 2 ) NOT NULL DEFAULT '0',
               place INT( 2 ) DEFAULT NULL,
               place_caption varchar(100) DEFAULT NULL,
               order_position INT( 5 ) DEFAULT NULL,
               PRIMARY KEY  (id),
               KEY contest_id_a_status (contest_id,status),
               KEY votes_count (votes_count),
               KEY added_date (added_date),
               KEY order_position_a_id (id,order_position)
        ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();
    }

    public static function getFullDescriptionSize()
    {
        if ( defined('FV_FULL_DESCRIPTION_SIZE') && FV_FULL_DESCRIPTION_SIZE > 0 ) {
            return FV_FULL_DESCRIPTION_SIZE;
        }
        return 1255;
    }


    public function unsplashe($competitor)
    {
        if (is_object($competitor)) {
            if (isset($competitor->name)) {
                $competitor->name = stripslashes($competitor->name);
            }
            if (isset($competitor->description)) {
                $competitor->description = stripslashes($competitor->description);
            }
            if (isset($competitor->full_description)) {
                $competitor->full_description = stripslashes($competitor->full_description);
            }
            if (isset($competitor->additional)) {
                $competitor->additional = stripslashes($competitor->additional);
            }
        } elseif (is_array($competitor)) {
            foreach ($competitor as $item) {
                if (isset($item->name)) {
                    $item->name = stripslashes($item->name);
                }
                if (isset($item->description)) {
                    $item->description = stripslashes($item->description);
                }
                if (isset($item->full_description)) {
                    $item->full_description = stripslashes($item->full_description);
                }
                if (isset($item->additional)) {
                    $item->additional = stripslashes($item->additional);
                }
            }
        }
        return $competitor;
    }    

}
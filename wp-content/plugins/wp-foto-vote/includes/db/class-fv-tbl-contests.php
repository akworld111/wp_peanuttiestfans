<?php

/**
 * Class ModelContest
 */
class ModelContest extends FvQuery
{

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
        return $wpdb->prefix . "fv_contests";
    }

    /**
     * find records in table by Primary KEY and Unserialize options
     *
     * @param  int $id
     * @param  bool $from_cache
     * @param string $res_type
     * @param bool $empty_param     Nothing
     * @param bool $plain       Do we need return original instead of FV_Contest?
     *
     * @return FV_Contest|object
     */
    public function findByPK($id, $from_cache = false, $res_type = OBJECT, $empty_param = false, $plain = false)
    {
        $contest = parent::findByPK($id, $from_cache, $res_type);
        if ( !$plain && !is_wp_error($contest) && !empty($contest) && isset($contest->id)) {
            $contest = new FV_Contest( $contest );
        }
        return $contest;
    }

    /**
     * Compose & execute our query and Unserialize options
     *
     * @return FV_Contest row
     */
    public function findRow()
    {
        $contest = parent::findRow();
        if ( !is_wp_error($contest) && !empty($contest) && isset($contest->id)) {
            $contest = new FV_Contest( $contest );
        }
        return $contest;
    }


    /**
     * Compose & execute our query.
     *
     * @param  boolean $only_count Whether to only return the row count
     * @param  boolean $get_var
     * @param  string $res_type Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
     *
     * @return FV_Contest[]
     */
    public function find($only_count = false, $get_var = false, $res_type = OBJECT)
    {
        $res = parent::find($only_count, $get_var, $res_type);

        if ( !$res || $only_count ) {
            return $res;
        }

        $resNew = array();

        foreach ($res as $key => $contest) {
            if ( is_wp_error($contest) || !isset($contest->id) ) { continue; }
            $resNew[$key] = new FV_Contest( $contest );
        }

        return $resNew;
    }
    
    /**
     * Add left join for Select Competitors count
     *
     * @return self
     */
    public function withCompetitorsCount()
    {
        return $this
            ->leftJoinCompetitors()
            ->what_fields( array("COUNT(`comp`.`id`) as competitors_count") )
            ->group_by( '`t`.`id`, `t`.`created`' );         
    }

    /**
     * Add left join for Select Competitors Votes SUMMARY
     *
     * @return self
     */
    public function withCompetitorsVotesSummary()
    {
        return $this
            ->leftJoinCompetitors()
            ->group_by( '`t`.`id`, `t`.`created`' )
            ->what_fields( array("SUM(`comp`.`votes_count`) as votes_count_summary") );
            
    }

    /**
     * Add left join for Competitors
     *
     * @param array    $fields     array of fields ( 'user_id' )
     * @param string   $where      WHERE condition, like "`comp`.`user_id` = 3"
     * @return self
     */
    public function leftJoinCompetitors( $fields = array(), $where = '' )
    {
        return $this->leftJoin( ModelCompetitors::query()->tableName(), "comp", "`comp`.`contest_id` = `t`.`id`", $fields, $where );
    }

    /**
     * Add left join for Competitors
     *
     * @param array    $fields     array of fields ( 'user_id' )
     * @param string   $where      WHERE condition, like "`comp`.`user_id` = 3"
     * @return self
     */
    public function leftJoinMeta( $fields = array(), $where = '' )
    {
        return $this->leftJoin( ModelMeta::query()->tableName(), "ct_meta", "`ct_meta`.`contest_id` = `t`.`id`", $fields, $where )
            ->where_custom_sql('`ct_meta`.`contestant_id` = 0');
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
        $this->leftJoinMeta( 'meta_key as ' . $meta_key );
        $where_sql = '`M`.`meta_key` = \'' . $meta_key . '\' AND ';
        // IF need select with specified Value
        if ( '*' !== $meta_val ){
            if ( in_array($value_condition, array('=', '>', '>=', '<', '<=', '<>')) ) {
                $this->where_custom_sql( $where_sql .  '`ct_meta`.`value` ' . $value_condition . ' \'' . $meta_val . '\'');
            } elseif ( in_array($value_condition, array('IS NULL', 'IS NOT NULL')) ){
                $this->where_custom_sql( $where_sql . '`ct_meta`.`value` ' . $value_condition);
            }
        }

        return $this;
    }


    /**
     * Add Where for select Contests just with Active Voting dates
     *
     * @return self
     */
    public function whereVotingDatesActive(  )
    {
        return $this->where_early( 'date_start', current_time('timestamp', 0) )
                    ->where_later( 'date_finish', current_time('timestamp', 0) );
    }


    /**
     * Add Where for select Contests just with Expired Voting dates
     *
     * @return self
     */
    public function whereVotingDatesExpired(  )
    {
        return $this->where_early( 'date_finish', current_time('timestamp', 0) );
    }

    /**
     * Add Where for select Contests just with Active Voting dates
     *
     * @return self
     */
    public function whereUploadDatesActive(  )
    {
        return $this->where_early( 'upload_date_start', current_time('timestamp', 0) )
                    ->where_later( 'upload_date_finish', current_time('timestamp', 0) );
    }

    public function fields()
    {
        return array(
            'id' => '%d',
            'name' => '%s',
            'date_start' => '%s',
            'date_finish' => '%s',
            'upload_date_start' => '%s',
            'upload_date_finish' => '%s',
            'soc_title' => '%s',
            'soc_description' => '%s',
            'soc_picture' => '%s',
            'user_id' => '%d',
            'form_id' => '%d',
            'upload_enable' => '%d',

            'security_type' => '%s',            // DEPRECATED

            'voting_max_count' => '%d',         // Max Count Per user in Period
            'voting_max_count_total' => '%d',   // Max Count Per user Total
            'voting_frequency' => '%s',         // Period
            'voting_security' => '%s',          // Cookies or Cookies + ID
            'voting_security_ext' => '%s',      // Secuirty
            'limit_by_user' => '%s',            // [yes ,no, role]
            'limit_by_role' => '%s',            // user picked

            'voting_type' => '%s',

            'max_uploads_per_user' => '%d',
            'status' => '%d',
            'show_leaders' => '%d',
            'lightbox_theme' => '%s',
            'upload_theme' => '%s',
            'timer' => '%s',
            'sorting' => '%s',
            'redirect_after_upload_to' => '%d',
            'moderation_type' => '%s',
            'page_id' => '%d',
            'cover_image' => '%d',
            'type' => '%d',

            'winners_pick'      => '%s',
            'winners_count'     => '%d',

            'hide_votes'        => '%s',
            'upload_limit_by_user' => '%s',
            'upload_limit_by_role' => '%s',
            'upload_limit_size' => '%s',
            'upload_max_size'   => '%d',

            'categories_on'     => '%s',

        );
    }

    public function install()
    {
        //! More - http://wordpress.stackexchange.com/a/78670
        $sql = "CREATE TABLE " . $this->tableName() . " (
               id int(7) NOT NULL AUTO_INCREMENT,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               date_start TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
               date_finish TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:000',
               upload_date_start TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
               upload_date_finish TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
               name varchar(255) NOT NULL,
               soc_title varchar(255) NOT NULL,
               soc_description varchar(255) NOT NULL,
               soc_picture varchar(255) NOT NULL,
               user_id int(7) DEFAULT '0',
               form_id int(7) DEFAULT '1',
               upload_enable int(3) NOT NULL DEFAULT 0,               
               security_type varchar(20) NOT NULL DEFAULT 'default',            
               voting_max_count int(3) NOT NULL DEFAULT 3,
               voting_max_count_total int(3) NOT NULL DEFAULT 0,
               voting_frequency varchar(15) NOT NULL DEFAULT 'day',
               voting_security varchar(20) NOT NULL DEFAULT 'cookiesAip',
               voting_security_ext varchar(20) NOT NULL DEFAULT 'none',               
               limit_by_user varchar(10) NOT NULL DEFAULT 'no',               
               limit_by_role varchar(100) NOT NULL DEFAULT '',               
               voting_type varchar(15) NOT NULL DEFAULT 'like',               
               show_leaders int(3) NOT NULL DEFAULT '0',
               lightbox_theme varchar(25) NOT NULL DEFAULT 'imageLightbox_default',
               upload_theme varchar(25) NOT NULL DEFAULT 'default',
               timer varchar(15) NOT NULL DEFAULT 'no',
               sorting varchar(15) NOT NULL DEFAULT 'newest',
               moderation_type varchar(10) NOT NULL DEFAULT 'pre',
               max_uploads_per_user int(5) NOT NULL DEFAULT '0',
               redirect_after_upload_to int(8) DEFAULT '0',
               page_id INT (8) DEFAULT NULL,
               cover_image INT(7) DEFAULT NULL,
               type INT(2) DEFAULT 0,
               status INT(2) NOT NULL DEFAULT '0',
               winners_pick varchar(30) NOT NULL DEFAULT 'auto',
               winners_count INT(2) NOT NULL DEFAULT 3,
               hide_votes varchar(20) NOT NULL DEFAULT 'global',
               upload_limit_by_user varchar(20) NOT NULL DEFAULT 'global',
               upload_limit_by_role varchar(100) NOT NULL DEFAULT '',
               upload_limit_size varchar(20) NOT NULL DEFAULT 'global',
               upload_max_size INT(6) DEFAULT 0,
               categories_on varchar(20) DEFAULT '',
               PRIMARY KEY  (id)
        ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();
    }

}
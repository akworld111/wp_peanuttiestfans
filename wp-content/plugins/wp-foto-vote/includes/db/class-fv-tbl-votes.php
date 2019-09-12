<?php

/**
 * Class ModelVotes
 * Operating with prefix_fv_votes table
 */
class ModelVotes extends FvQuery
{

    public static $TYPE_RATE = 1;
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
        return $wpdb->prefix . "fv_votes";
    }

    public function fields()
    {
        return array(
            'id' => '%d',
            'contest_id' => '%d',
            'post_id' => '%d',
            'vote_id' => '%d',
            'type' => '%d',     // null - like, 1 - rate, 2 - dislike
            'rating' => '%f',     // if type = 1 (rate)
            'ip' => '%s',
            'uid' => '%s',
            'score' => '%d',
            'score_detail' => '%s',
            'changed' => '%s',
            'browser' => '%s',
            'display_size' => '%s',
            'b_plugins' => '%s',
            'b_fonts' => '%s',
            'mouse_pos' => '%s',
            'referer' => '%s',
            'os' => '%s',
            'country' => '%s',
            'name' => '%s',
            'email' => '%s',
            'hash' => '%s',
            'user_id' => '%d',
            'soc_network' => '%s',
            'soc_uid' => '%s',
            'soc_profile' => '%s',
            'fb_pid' => '%s',
            'is_tor' => '%d',
        );
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

    /**
     * delete record in table
     * @param   $contestant_id      int
     * @return  bool                MySQL query result
     */
    public function deleteByContestantID($contestant_id)
    {
        global $wpdb;

        $r = $wpdb->query(
            $wpdb->prepare(
                " DELETE FROM " . $this->tableName() . " WHERE `vote_id` = '%d'; ", $contestant_id
            )
        );
        $this->checkDbErrors();
        return $r;
    }

    public function install()
    {

        $sql = "CREATE TABLE " . $this->tableName() . " (
                id int(16) NOT NULL AUTO_INCREMENT,
                contest_id int(10) NOT NULL,
                post_id int(10) NOT NULL,
                vote_id int(5) NOT NULL,
                type int(2) NULL,
                rating float(5,1) NOT NULL DEFAULT '0.0',
                ip varchar(45) NOT NULL,
                uid varchar(25) NOT NULL,
                score int(4) NOT NULL,
                score_detail varchar(80) NOT NULL,
                changed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                browser VARCHAR( 255 ) NULL DEFAULT NULL,
                display_size VARCHAR( 50 ) NULL DEFAULT NULL,
                b_plugins VARCHAR( 80 ) NULL DEFAULT NULL,
                b_fonts VARCHAR( 80 ) NULL DEFAULT NULL,
                is_tor int( 2 ) NULL DEFAULT NULL,
                mouse_pos VARCHAR( 20 ) NULL DEFAULT NULL,
                referer VARCHAR( 500 ) NULL,
                os VARCHAR( 40 ) NULL,
                country VARCHAR( 30 ) NULL,
                name VARCHAR( 50 ) NULL,
                email VARCHAR( 60 ) NULL,
                user_id VARCHAR( 50 ) NULL,
                soc_network VARCHAR( 50 ) NULL,
                soc_uid VARCHAR( 50 ) NULL,
                soc_profile VARCHAR( 255 ) NULL,
                fb_pid VARCHAR( 255 ) NULL,
                hash VARCHAR( 10 ) NULL,
                PRIMARY KEY  (id),
                KEY ip (ip),
                KEY uid (uid),
                KEY contest_id_a_changed (contest_id,changed),
                KEY vote_id (vote_id)
            ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();
    }

}
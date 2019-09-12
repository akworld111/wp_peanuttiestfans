<?php

class ModelSubscribers extends FvQuery
{

    /**
     * Returns the static query of the specified class.
     * @return FvQuery the static query class
     */
    public static function query($className = __CLASS__)
    {
        return new $className();
    }

    public function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . "fv_subscribers";
    }

    public function fields()
    {
        return array(
            'contest_id' => '%d',
            'contestant_id' => '%d',
            'name' => '%s',
            'email' => '%s',
            'newsletter' => '%d',
            'age' => '%s',
            'user_id' => '%d',
            'type' => '%s',
            'soc_network' => '%s',
            'soc_uid' => '%s',
            'sync' => '%d',
            'added' => '%s',
            'additional' => '%s',
            'verified' => '%d',
            'verify_hash' => '%s',
        );
    }

    public function insert($data) {
        $insert_id = parent::insert($data);
        do_action('fv/subscribers-model/add', $insert_id);
        return $insert_id;
    }

    /**
     * delete record in table
     * @param   $id    int
     * @return  bool    MySQL query result
     * @since 2.3.00
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

    public function install()
    {
        $sql = "CREATE TABLE " . $this->tableName() . " (
                id int(7) NOT NULL AUTO_INCREMENT,
                contest_id int(7) NOT NULL,
                contestant_id int(7) NOT NULL,
                added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                name VARCHAR( 50 ) NULL,
                email VARCHAR( 80 ) NULL,
                newsletter int(2) NULL,
                user_id int(10) NULL,
                type VARCHAR( 20 ) NULL,
                sync int(2) NOT NULL DEFAULT '0',
                soc_network VARCHAR( 40 ) NULL,
                soc_uid VARCHAR( 40 ) NULL,
                additional VARCHAR( 50 ) NULL,
                verified int(2) NOT NULL DEFAULT '0',
                verify_hash VARCHAR( 15 ) NULL,
                PRIMARY KEY  (id),
                KEY email_a_type (email,type)
                ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();
    }
}
<?php

class ModelMeta extends FvQuery
{

    /**
     * Returns the static query of the specified class.
     * @return ModelMeta the static query class
     */
    public static function query($className = __CLASS__)
    {
        $class = new $className();
        return $class->set_primary_key('ID');
    }

    /**
     * Returns the static query of the specified class.
     * @return ModelMeta the static query class
     */
    public static function q($className = __CLASS__)
    {
        $class = new $className();
        return $class->set_primary_key('ID');
    }

    public function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . "fv_meta";
    }

    public function fields()
    {
        return array(
            'ID'            => '%d',
            'contest_id'    => '%d',
            'contestant_id' => '%d',
            'order_id'      => '%d',
            'meta_key'      => '%s',
            'value'         => '%s',
            'custom'        => '%d',        // "1" means public data, "0" - means service data
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

        //  AND NOT `contestant_id` = 0
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
     * @param   $id    int
     * @return  bool    MySQL query result
     */
    public function deleteByContestantID($id)
    {
        global $wpdb;

        $r = $wpdb->query(
            $wpdb->prepare(
                " DELETE FROM " . $this->tableName() . " WHERE `contestant_id` = '%d'; ", $id
            )
        );
        $this->checkDbErrors();
        return $r;
    }

    /**
     * Compose & execute our query.
     *
     * @return array
     */
    public function findFlat() {
        $res = $this->find(false, false, OBJECT);
        if ( !$res ) {
            return $res;
        }
        $res_flat = array();
        foreach ($res as $row) {
            $res_flat[$row->meta_key] = $row;
        }
        unset($res);
        return $res_flat;
    }

    /**
     * @param $count        int
     * @param $default      array       Data for insert
     *
     * @return bool
     */
    public function increaseOrInsert( $count, $default ) {
        global $wpdb;

        $row = $this->where_all( $default )->findRow();

        // Row Exists
        if ( $row ) {
            $where = $this->compose_where_sql();

            if (!empty($where)) {
                $where = ' WHERE ' . $where;
            }

            $r = $wpdb->query( 'UPDATE `' . $this->tableName() . '` as t SET `value` = `value` + ' . intval($count) . ' ' . $where . ';' );
        } else {
            // Create Record
            $default['value'] = $count;
            $r = ModelMeta::q()->insert( $default );
        }

        FvLogger::checkDbErrors($r);

        return $r;
    }

    public function install()
    {
        $sql = "CREATE TABLE " . $this->tableName() . " (
                ID int(7) NOT NULL AUTO_INCREMENT,
                contest_id int(7) NOT NULL,
                contestant_id int(7) NOT NULL,
                order_id int(7),
                custom int(1),
                meta_key VARCHAR( 100 ) NOT NULL,
                value TEXT,
                PRIMARY KEY  (ID),
                KEY contest_id (contest_id),
                KEY contestant_id (contestant_id)
                ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();
    }
}
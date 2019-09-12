<?php

/*
 * Class for work with DB
 * wp-foto-vote
 *
 * @since 1.1
 */

defined('ABSPATH') or die("No script kiddies please!");

class FV_DB
{

    private $table_contests_name;
    private $table_competitors_name;
    private $table_votes_name;

    function __construct()
    {
        global $wpdb;

        $this->table_contests_name = $wpdb->prefix . "fv_contests";
        $this->table_competitors_name = $wpdb->prefix . "fv_competitors";
        $this->table_votes_name = $wpdb->prefix . "fv_votes";
        $this->table_meta_name = $wpdb->prefix . "fv_meta";
        $this->table_subscr_name = $wpdb->prefix . "fv_subscribers";
        $this->table_forms_name = $wpdb->prefix . "fv_forms";
    }

    public function clearAllData()
    {
        /*
          if ( !defined('WP_UNINSTALL_PLUGIN') ) {
          exit();
          }
         */
        global $wpdb;

        $sql = "DROP TABLE IF EXISTS `" . $this->table_contests_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = "DROP TABLE IF EXISTS `" . $this->table_competitors_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = " DROP TABLE IF EXISTS `" . $this->table_votes_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = " DROP TABLE IF EXISTS `" . $this->table_meta_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = " DROP TABLE IF EXISTS `" . $this->table_subscr_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = " DROP TABLE IF EXISTS `" . $this->table_forms_name . "`;";
        $wpdb->query($sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        FvLogger::checkDbErrors();
        FvLogger::addLog('All tables deleted from database!');
        delete_option("fv_db_version");
        delete_option("fv-update-key-details");
    }

    /* ================================================ */

    /**
     *  get most voted items in contest
     *
     * @param int $contest_id
     * @param int $limit
     * @return array    Most voted photos
     */
    public function getMostVotedItems($contest_id, $limit = 3)
    {
        global $wpdb;

        $r = $wpdb->get_results(
            "
            SELECT * 
            FROM {$this->table_competitors_name}
            WHERE `contest_id` = '{$contest_id}' AND `status` = '" . ST_PUBLISHED .
            "' ORDER BY votes_count DESC
            LIMIT {$limit};  #getMostVotedItems
            "
        );

        $r2 = array();
        foreach ($r as $res) {
            $r2[$res->id] = $res;
        }

        FvLogger::checkDbErrors($r2);
        return $r2;
    }

    /*
     * Get all Photos from one contest for math Next and Prev photos ID
     */
    public function getCompItemsNav($contest_id, $order)
    {
        global $wpdb;
        //$wpdb->get_results('query', ARRAY_A);

        $r = $wpdb->get_results(
            "SELECT `id`
                FROM {$this->table_competitors_name}               
                WHERE `contest_id` = '{$contest_id}' AND `status` = " . ST_PUBLISHED  . ' ' . $this->getCompOrderFiled($order)
        );
        FvLogger::checkDbErrors();

        return $r;
    }

    private function getCompOrderFiled($order_type)
    {
        $order = ' ORDER BY';
        switch ($order_type) {
            case 'newest':
                $order .= ' `added_date` DESC ';
                break;
            case 'oldest':
                $order .= ' `added_date` ASC ';
                break;
            case 'popular':
                $order .= ' `votes_count` DESC ';
                break;
            case 'unpopular':
                $order .= ' `votes_count` ASC ';
                break;
            case 'random':
                $order .= ' RAND() ';
                break;
            case 'alphabetical-az':
                $order .= ' `name` ASC ';
                break;
            case 'alphabetical-za':
                $order .= ' `name` DESC ';
                break;
            default:
                $order .= ' `added_date` ASC ';
                break;
        }
        return $order;
    }

    /*
     * generateWhereSQL
     */

    private function generateWhereSQL($where_arr)
    {
        $where_sql = '';
        if (count($where_arr) > 0) {
            $where_sql = 'WHERE ' . $where_arr[0];
            unset($where_arr[0]);
            foreach ($where_arr as $where_string) {
                $where_sql .= 'AND ' . $where_string;
            }
        }
        return $where_sql;
    }
}









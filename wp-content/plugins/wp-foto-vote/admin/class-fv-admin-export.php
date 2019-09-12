<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The ajax functionality of the plugin.
 *
 * @package    FV
 * @subpackage admin
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Export
{
    /**
     * Run export
     * Check permissions
     *
     * @params $_POST['type'] int
     *
     * @return void
    */
    public static function run()
    {
        try {
                if ( !isset($_GET['type'])  ) {
                    die ( "incorrect type" );
                }
                if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv_export_nonce', 'fv_nonce', false) ) {
                    FvLogger::addLog("FV_Admin_Export::run - security error");
                    return;
                }

                $type = sanitize_title( $_GET['type'] );

                //$photo = ModelCompetitors::query()->where_all( array('id'=>(int)$_POST['photo_id'], 'contest_id'=>(int)$_POST['contest_id'] ) )->findRow();

                switch( $type ){
                    case 'contest_data':
                        self::export_contest_data();
                        break;
                    case 'log_list':
                        self::export_log_list();
                        break;
                    case 'subscribers_list':
                        self::export_subscribers_list();
                        break;
                    default:
                        do_action('fv/admin/export_data/custom', $type);
                        break;
                }

        } catch(Exception $ex) {
            //FvDebug::go("FV_Admin_Export::run - some error ", $ex);
        }

    }

    public static function export_contest_data(  ) {
        if ( isset($_GET["contest_id"]) ) {
            $contest_id = $_GET["contest_id"];
        }else {
            FvLogger::addLog("export_contest_data error - no contest_id");
            wp_die("Error!");
        }

        $max_records = 0;
        if (isset($_GET['max'])) {
            $max_records = (int)$_GET['max'];
        }


        $contest = fv_get_contest( $contest_id );
        
        $filename = 'fv_contest_' . $contest_id . '_data.csv';
        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        ## Data with Meta

        $query = ModelCompetitors::query()->where("contest_id", $contest_id);
        
        if ( $contest->isCategoriesEnabled() ) {
            $query->withPrefetchCategories();
        }

        if ( defined("FV_ADMIN__COMPETITORS_LIST__FETCH_USER_EMAIL") ) {
            $query->withAuthorEmailWP();
        }

        if ($max_records > 0) {
            $query->limit($max_records);
        }

        /** @var FV_Competitor[] $data */
        $data = $query->find(false, false, false, true);

        ## ======= META =======
        $metas = ModelMeta::q()->what_fields( array('meta_key', 'custom') )
            ->where_all( array("contest_id"=>$contest_id, "custom"=>1) )
            ->where_not("contestant_id", 0)
            ->find();
        $meta_array = array();
        $meta_keys_arr = array();
        $meta_labels_arr = array();

        foreach($metas as $meta_row) {
            if ( $meta_row->custom && !isset($meta_keys_arr[$meta_row->meta_key]) ) {
                $meta_labels_arr[] = $meta_row->meta_key;
                $meta_keys_arr[$meta_row->meta_key] = '';
            }
        }
        ## =======

        $labels_arr = array('Photo ID', 'Photo name', 'Description', 'Full description', 'Photo full url', 'User email',
            'Votes count', 'Rating', 'Rating Summary', 'Added date', 'User id', 'User ip', 'Status');

        if ( $contest->isCategoriesEnabled() ) {
            $labels_arr[] = 'Categories';
        }

        $labels_arr = array_merge( $labels_arr, $meta_labels_arr );

        fputcsv( $fp, $labels_arr, get_option('fv-export-delimiter', ';') );

        $data_arr = array();
        foreach ($data as $competitor)
        {

            $data_arr = array(
                $competitor->id,
                $competitor->name,
                $competitor->description,
                $competitor->full_description,
                $competitor->url,
                $competitor->getAuthorEmail(),
                $competitor->votes_count,
                $competitor->votes_average,
                $competitor->rating_summary,
                date("Y-m-d H:i", $competitor->added_date),
                $competitor->user_id,
                $competitor->user_ip,
                fv_get_status_name($competitor->status),
            );

            if ( $contest->isCategoriesEnabled() ) {
                $data_arr[] = $competitor->getCategories('string');
            }

            $data_arr = array_merge( $data_arr, array_merge( $meta_keys_arr , $competitor->meta()->get_custom_all_flat() ) );

            fputcsv( $fp, $data_arr, get_option('fv-export-delimiter', ';') );
        }
        unset($data);

        fclose($fp);
        exit();
    }


    public static function export_log_list(  ) {
        $period = 0;
        if (isset($_GET['period'])) {
            $period = $_GET['period'];
        }
        $contest_id = 0;
        if (!empty($_GET['contest_id'])) {
            $contest_id = (int)$_GET['contest_id'];
        }
        $competitor_id = 0;
        if (!empty($_GET['competitor_id'])) {
            $competitor_id = (int)$_GET['competitor_id'];
        }
        $max_records = 0;
        if (isset($_GET['max'])) {
            $max_records = (int)$_GET['max'];
        }

        $filename = 'fv_log_' . date('d-m-y') . '-for_' . $period;

        if ($contest_id) {
            $filename .= '__contest_' . $contest_id;
        }
        $filename .= '.csv';

        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // получаем первый массив
        //$my_db = new FV_DB;
        //$stats = $my_db->getVoteStats(999, $datefrom, $contest_id);

        $queryModel = ModelVotes::q()
            ->what_fields( array('`t`.*', 'contests.name as contest_name', 'competitors.name as competitor_name') )
            ->sort_by('`t`.`id`', 'DESC')
            ->leftJoin( ModelCompetitors::query()->tableName(), "contests", "`contests`.`id` = `t`.`contest_id`"  )
            ->leftJoin( ModelContest::query()->tableName(), "competitors", "`competitors`.`id` = `t`.`vote_id`"  );


        if ($period) {
            switch ($period) {
                case "this_month":
                    $queryModel->where_custom_sql( ' YEAR(changed) = YEAR(CURRENT_DATE)
                                                    AND MONTH(changed) = MONTH(CURRENT_DATE)' );
                    break;
                case "30":
                    $queryModel->where_custom_sql( sprintf(" date(`changed`) >= date(now()-interval %s day)", (int)$period) );
                    break;
                case "prev_month":
                    $queryModel->where_custom_sql( ' YEAR(changed) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                                    AND MONTH(changed) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)' );
                    break;
                case "90":
                case "180":
                    $queryModel->where_custom_sql( sprintf(" date(`changed`) >= date(now()-interval %s day)", (int)$period) );
                    break;
            }
        }
        
        if ($contest_id > 0) {
            $queryModel->where('contest_id', $contest_id);
        }

        if ($competitor_id > 0) {
            $queryModel->where('vote_id', $competitor_id);
        }
        if ($max_records > 0) {
            $queryModel->limit($max_records);
        }

        $stats = $queryModel->find();

        //fputcsv($fp, array('тест 12', 'тест 2'), ';');
        $earr = array('Contest ID', 'Contest Caption', 'IP', 'Country', 'Added' ,'Competitor ID' ,'Competitor Name', 'WP user id' ,'Browser', 'Window size', 'is_tor', 'b_plugins' ,'Refer' ,'Fraud score' ,'Fraud score details' ,'soc_profile', 'name', 'email');
        fputcsv($fp, $earr, ';');

        foreach ($stats as $rows) {
            $earr = array($rows->contest_id, $rows->contest_name, $rows->ip, $rows->country, $rows->changed, $rows->vote_id, $rows->competitor_name, $rows->user_id, $rows->browser, $rows->display_size, $rows->is_tor, $rows->b_plugins, $rows->referer,  $rows->score, $rows->score_detail, $rows->soc_profile, $rows->name, $rows->email);
            fputcsv($fp, $earr, ';');
        }
        unset($stats);
        // если еще есть товары, идем дальше, иначе выходим

        fclose($fp);
        exit();
    }


    public static function export_subscribers_list(  ) {
        $contest_id = 'all';
        if (isset($_GET['contest_id'])) {
            $contest_id = (int)$_GET['contest_id'];
        }

        $filename = 'fv_subscribers_' . date('d-m-y') . '-for_contest_' . $contest_id . '.csv';
        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // получаем первый массив

        $statsQ = ModelSubscribers::query();

        $max_records = 5000;
        if (isset($_GET['max'])) {
            $max_records = (int)$_GET['max'];
        }

        $statsQ->limit( $max_records );

        if ( 'all' !== $contest_id ) {
            $statsQ->where( 'contest_id', $contest_id );
        }
        
        $statsArr = $statsQ->find();

        //fputcsv($fp, array('тест 12', 'тест 2'), ';');
        $earr = array('type', 'contest_id',  'name', 'email', 'newsletter', 'user_id', 'soc_network', 'verified', 'sync', 'added', 'additional');
        fputcsv($fp, $earr, ';');

        foreach ($statsArr as $s_row)
        {
            $earr = array($s_row->type, $s_row->contest_id, $s_row->name, $s_row->email, $s_row->newsletter, $s_row->user_id, $s_row->soc_network, $s_row->verified, $s_row->sync, $s_row->added, $s_row->additional);
            fputcsv($fp, $earr, ';');
        }
        unset($statsArr);
        // если еще есть товары, идем дальше, иначе выходим

        fclose($fp);
        exit();
    }

    public static function output_header( $filename )
    {
        header( "Content-Type: text/csv;charset=utf-8" );
        header( "Content-Disposition: attachment;filename=\"$filename\"" );
        header( "Content-Transfer-Encoding: binary" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
    }
}
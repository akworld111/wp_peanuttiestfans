<?php

/**
 * Simple logger class for Worpress with processing objects and arrays
 *
 * @author МаксК / maxim-kaminsky.com
 */

defined('ABSPATH') or die("No script kiddies please!");

if ( !class_exists('FvLogger') ) {

    class FvLogger {
        private static $log_exists = '';

        static function checkLogFile() {
            if ( file_exists(FV_LOG_FILE) ){
                self::$log_exists = 'exists';
                return true;
            }
            return false;
        }

        static function checkDbErrors($r = null) {
            global $wpdb;

            if ( FV::$DEBUG_MODE & FvDebug::LVL_SQL ) {
                fv_log('SQL query: ', $wpdb->last_query);
                fv_log('SQL query result: ', $wpdb->last_result);
            }

            if ( strlen($wpdb->last_error) > 0 ) {
                self::addLog('Db Error: ', $wpdb->last_error);
                self::addLog('Error In query: ', $wpdb->last_query);
            }
        }

        static function clearLog() {
            file_put_contents(FV_LOG_FILE, '');
        }

        static function addLog($msg, $data='', $FILE='', $LINE='') {

            if ( self::$log_exists !== 'exists' ){
                if ( !self::checkLogFile()  ) { return false; }
            }
            $data_text = '';
            // Processing data
            if ( is_string($data) )
            {
                $data_text .= $data;
            } elseif (is_wp_error($data)){
               $data_text .= $data->get_error_message();
            } else {
                $data_text = print_r($data, true);
            }

            $path = '';
            if ( !empty($FILE) ) {
                $path = $FILE . ':' . $LINE . PHP_EOL;
            }
            error_log( '* ' . date('[Y-m-d H:i:s e]') . ' ' . $path . $msg  . PHP_EOL . 'p. ' . $data_text . PHP_EOL, 3, FV_LOG_FILE);
            //echo $msg . $data_text . '<br />';
        }

    }

}

if ( !function_exists('fv_log') ) {

    function fv_log($msg, $data='', $FILE='', $LINE='') {
        FvLogger::addLog($msg, $data, $FILE, $LINE);
    }

}
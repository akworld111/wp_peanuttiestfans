<?php
/*
	Addon Name: Final countdown - http://hilios.github.io/jQuery.countdown/
	Author: Maxim Kaminsky
	Version: 0.1
 */

add_action('plugins_loaded', 'FvAddon_FinalCountdownRun', 10);

function FvAddon_FinalCountdownRun()
{
    if ( !class_exists('FvAddonBase') ) {
        return;
    }

    class FvAddon_FinalCountdown extends FvAddonBase
    {
        CONST VER = 0.1;

        public $addonUrl;
        public $addonDir;
        /**
         * Class instance.
         *
         * @var object
         */
        protected static $instance;

        /**
         * Constructor. Loads the class.
         *
         * @param string    $name
         * @param string    $slug
         */
        protected function __construct($name, $slug)
        {
            $this->required_version = '2.2.200';

            $this->addonUrl = FV::$ADDONS_URL . 'final-countdown/';
            $this->addonDir = FV::$ADDONS_ROOT . 'final-countdown/';
            
            //** Dont remove this, else addon will not works
            parent::__construct($name, $slug, 'api_v2');
        }

        /**
         * Performs all the necessary actions
         */
        public function init()
        {
            //** Dont remove this
            parent::init();
            add_action('fv/load_countdown/final', array($this, 'run'), 10, 2);
        }

        /**
         * @param FV_Contest    $contest
         * @param array         $args
         */
        public function run($contest, $args)
        {
            $count_to_type = $args['count_to']; // 'upload', 'voting'

            wp_enqueue_style('fv-final-countdown', $this->addonUrl . 'assets/fv-final-countdown.css', false, self::VER, 'all');
            wp_enqueue_script('fv-final-countdown', fv_min_url($this->addonUrl . 'assets/fv-final-countdown.js'), array('jquery', 'fv_lib_js'), self::VER);

            $date_diff = 0;
            $count_to = 0;
            $header_text_key = '';
            
            if ($count_to_type == 'upload') {
                if ( $contest->isUploadDatesActive() ) {
                    // Count until Upload date ends
                    $date_diff = strtotime($contest->upload_date_finish) - current_time('timestamp', 0);
                    $count_to = strtotime($contest->upload_date_finish) - (get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
                    $header_text_key = 'timer_upload_ends_in';
                } elseif ( $contest->isUploadDatesFutureActive() ) {
                    // Count until Upload date starts
                    $date_diff = strtotime($contest->upload_date_start) - current_time('timestamp', 0);
                    $count_to = strtotime($contest->upload_date_start) - (get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
                    $header_text_key = 'timer_upload_starts_in';
                }

            } else {
                if ( $contest->isVotingDatesActive() ) {
                    // Count until Voting date ends
                    $date_diff = strtotime($contest->date_finish) - current_time('timestamp', 0);
                    //$count_to = gmdate('Y/m/d H:i:s',  strtotime($contest->date_finish) - (get_option( 'gmt_offset' ) * HOUR_IN_SECONDS) );
                    $count_to = strtotime($contest->date_finish) - (get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
                    //$count_to = strtotime($contest->date_finish);
                    $header_text_key = 'timer_voting_ends_in';
                } elseif ( $contest->isVotingDatesFutureActive() ) {
                    $date_diff = strtotime($contest->date_start) - current_time('timestamp', 0);
                    $count_to = strtotime($contest->date_start) - (get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
                    $header_text_key = 'timer_voting_starts_in';
                }
            }

            if ($date_diff > 0) {
                $days_leave = floor($date_diff / 86400);
                $hours_leave = floor(($date_diff % 86400) / (60 * 60));
                $minutes_leave = floor(($date_diff % 86400) % (60 * 60) / 60);
                $secs_leave = floor(($date_diff % 86400) % (60 * 60) % 60);
            } else {
                $days_leave = $hours_leave = $minutes_leave = $secs_leave = 0;
            }

            include $this->addonDir . 'views/default.php';
        }

        /**
         * @param   array     $countdowns
         * @return  array
         */
        public function register($countdowns)
        {
            $countdowns['final'] = 'Final [fv_countdown contest_id="*" type="final"]';
            return $countdowns;
        }

        /**
         * Performs all the necessary Admin actions
         */
        public function admin_init()
        {
            parent::admin_init();
            add_filter('fv/countdown/list', array($this, 'register'), 10, 1);
        }

        /**
         * Dynamically add Addon settings section
         * @param $sections
         * @return array
         */
        public function section_settings($sections)
        {
            return $sections;
        }

        /**
         * Helper function to get the class object. If instance is already set, return it.
         * Else create the object and return it.
         *
         * @return FinalCountdown $instance Return the class instance
         */
        public static function get_instance()
        {

            if (!isset(self::$instance))
                return self::$instance = new FvAddon_FinalCountdown('FinalCountdown', 'fc');

            return self::$instance;

        }

    }

    /** Instantiate the class */
    FvAddon_FinalCountdown::get_instance();

}    // Function :: END
<?php
/*
	Plugin Name: WP Foto Vote addon - confirm vote
	Plugin URI: http://wp-vote.net/
	Description: Confirm vote modal 
	Author: Maxim Kaminsky
	Author URI: http://www.maxim-kaminsky.com/
	Plugin support EMAIL: support@wp-vote.net
	Version: 0.1
  
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   

 */


// Init class early then Redux Framework, for add Addon options, else they are not added
add_action( 'plugins_loaded', 'FvAddon_CoutdownDeafultRun', 3 );

function FvAddon_CoutdownDeafultRun(){
	if (!class_exists( 'FvAddonBase' )) {return;}

	class FvAddon_CoutdownDeafult extends FvAddonBase {
		CONST VER = 0.1;
		public $version = 0.1;

        public $addonUrl;
        public $addonDir;
		/**
		 * Class instance.
		 *
		 * @since 2.2.083
		 *
		 * @var object
		 */
		protected static $instance;				
		
		/**
		 * Constructor. Loads the class.
		 *
		 * @since 2.2.083 / 0.1
		 */
		protected function __construct($name, $slug) {
			//** Dont remove this, else addon will not works
			$this->addonUrl = FV::$ADDONS_URL . 'countdown-default/';
			$this->addonDir = FV::$ADDONS_ROOT . 'countdown-default/';

			parent::__construct($name, $slug, 'api_v2');
		}

		/**
		 * Performs all the necessary actions	
		 *
		 * @since 2.2.083 / 0.1
		 */
		public function init() 
		{
			//** Dont remove this, else $this->addonsSettings will be EMPTY!
			parent::init();

            add_action( 'fv/load_countdown/default', array($this, 'run'), 10, 2 );
		}

		/**
		 * @param FV_Contest    $contest
		 * @param array         $args
		 */
		public function run($contest, $args)
		{
			$count_to_type = $args['count_to']; // 'upload', 'voting'

            wp_enqueue_style('fv_countdown-default', $this->addonUrl . 'assets/fv-countdown-default.css', false, self::VER, 'all');
            wp_enqueue_script('fv_countdown-default', fv_min_url($this->addonUrl . 'assets/fv-countdown-default.js'), array('jquery', 'fv_lib_js'), self::VER);

			$date_diff = 0;
			$header_text_key = '';

			if ($count_to_type == 'upload') {
				if ( $contest->isUploadDatesActive() ) {
					// Count until Upload date ends
					$date_diff = strtotime($contest->upload_date_finish) - current_time('timestamp', 0);
					$header_text_key = 'timer_upload_ends_in';
				} elseif ( $contest->isUploadDatesFutureActive() ) {
					// Count until Upload date starts
					$date_diff = strtotime($contest->upload_date_start) - current_time('timestamp', 0);
					$header_text_key = 'timer_upload_starts_in';
				}

			} else {
				if ( $contest->isVotingDatesActive() ) {
					// Count until Voting date ends
					$date_diff = strtotime($contest->date_finish) - current_time('timestamp', 0);
					$header_text_key = 'timer_voting_ends_in';
				} elseif ( $contest->isVotingDatesFutureActive() ) {
					$date_diff = strtotime($contest->date_start) - current_time('timestamp', 0);
					$header_text_key = 'timer_voting_starts_in';
				}
			}

            if ( $date_diff > 0 ) {
                $days_leave = floor($date_diff / 86400);
                $hours_leave = floor( ($date_diff % 86400) / (60 * 60) );
                $minutes_leave = floor( ($date_diff % 86400) % (60 * 60) / 60 );
                $secs_leave = floor( ($date_diff % 86400) % (60 * 60) % 60 );
            } else {
                $days_leave = $hours_leave = $minutes_leave = $secs_leave = 0;
            }

            include $this->addonDir . 'views/default.php';
        }

        public function register($countdowns) {
            $countdowns['default'] = 'Default [fv_countdown contest_id="*" type="default"]';
            return $countdowns;
        }


		/**
		 * Performs all the necessary Admin actions
		 *
		 * @since 2.2.083
		 */
		public function admin_init() {
			//** Dont remove this, else $this->addonsSettings will be EMPTY!
			parent::admin_init();			
			// There you can load plugin textdomain as example
            add_filter( 'fv/countdown/list', array($this, 'register'), 10, 1 );
		}
		
		/**
		 * Dynamically add Addon settings section
		 *
		 * @since 2.2.083 / 0.1
		 */
		public function section_settings($sections) 
		{

			return $sections;
		}


		/**
		 * Helper function to get the class object. If instance is already set, return it.
		 * Else create the object and return it.
		 *
		 * @since 2.2.083 / 0.1
		 *
		 * @return object $instance Return the class instance
		 */
		public static function get_instance() 
		{

			if ( ! isset( self::$instance ) )
				return self::$instance = new FvAddon_CoutdownDeafult('CoutdownDeafult', 'cd');

			return self::$instance;

		}

	}
	
	/** Instantiate the class */
	FvAddon_CoutdownDeafult::get_instance();
	
}	// Function :: END
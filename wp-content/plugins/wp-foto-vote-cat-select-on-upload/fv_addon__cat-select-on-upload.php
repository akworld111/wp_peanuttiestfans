<?php

/*
  Plugin Name: WP Foto Vote - (contest) select in Upload form
  Plugin URI: http://wp-vote.net/downloads/contest-select-in-upload-form/
  Description: Add Category (contest) select to Upload form
  Author: Maxim Kaminsky
  Author URI: http://www.maxim-kaminsky.com/
  Plugin support EMAIL: wp-vote@hotmail.com
  Version: 0.3

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

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Init class early then Redux Framework, for add Addon options, else they are not added
add_action('plugins_loaded', 'FvAddon_CatSelectInUploadFInit', 4);

function FvAddon_CatSelectInUploadFInit() {
    if (!class_exists('FvAddonBase')) {
        return;
    }

    class FvAddon_CatSelectInUploadF extends FvAddonBase {
        /**
         * Class instance.
         *
         * @var object
         */
        protected static $instance;
        
        /**
         * Constructor. Loads the class.
         *
         * @since 1.0.0
         */
        protected function __construct($name, $slug) {
            //** Dont remove this, else addon will not works
            parent::__construct($name, $slug, 'api_v2');
        }

        /**
         * Performs all the necessary actions	
         *
         * @since 1.0.0
         */
        public function init() {
            //** Dont remove this, else $this->addonsSettings will be EMPTY!
            parent::init();

            // if not admin area
            if (!is_admin() && $this->_get_opt('enabled')) {
                // if addon Enabled in settings
                add_filter('fv_upload_form_rules_filer', array($this, 'upload_form_hook'), 5, 3);                    
                    
                //add_action('fv_after_upload_form', array($this, 'after_upload_form_js'));                
                // Move action to footer to avoid some problems like addings <p></p> to code
                add_action('wp_footer', array($this, 'after_upload_form_js'));                
            }
        }


        /**
         * Show Select with Contests list
         */           
        public function upload_form_hook($text, $counter, $current_contest) {
            $time_now = current_time('timestamp', 0);
            $contests = ModelContest::query()
                    ->where_early('upload_date_start', $time_now)
                    ->where_later('upload_date_finish', $time_now)
                    ->find();
            
            IF ( is_array($contests) && count($contests) > 0 ) :
                
                /* @var $active_default bool */
                $active_default = $this->_get_opt('active_default');
                ob_start();
                    include ('views/form_select.php');
                $html = ob_get_clean();
                
            ELSE:
                
                $html = 'No active contests!';
            
            ENDIF;
            
            return $text . $html;
        }

        /**
         * Require select Contest via JS
         */        
        public function after_upload_form_js() {
            echo
            '<script>
                function fv_hook_upload_image(form) {
                    if ( document.querySelector("#category-contest-id option:checked").value == "" ) {   // !!
                        alert("' . stripslashes($this->_get_opt('error')) . '");
                        return false;
                    }
                    return true;
                }		
                function fv_changed_contest(select) {
                    if ( select.value != "" ) {
                        document.querySelector("#contest_id").value = select.value;
                    }
                    return true;
                }		
            </script>';
        }

        /**
         * Dynamically add Addon settings section
         */
        public function section_settings($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Upload: contest select', 'fv_uar'),
                'desc' => __('<p class="description">'
                        . 'This addon allow user select to what contest upload photos. '
                        . '<br/><strong>!Important note:</strong> in select shows just contests with active upload dates!'
                        . '</p>', 'fv_uar'),
                'icon' => 'el-icon-check',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array(
                    array(
                        'id' => $this->slug . '_enabled',
                        'type' => 'switch',
                        'title' => __('Show category (contest) select in upload form?', 'fv_uar'),
                        //'subtitle' => __('Look, it\'s on!', 'fv_uar'),
                        'default' => true,
                    ),
                    array(
                        'id' => $this->slug . '_active_default',
                        'type' => 'switch',
                        'title' => __('Make current (that passed into shortcode) contest selected be default?', 'fv_uar'),
                        //'subtitle' => __('Look, it\'s on!', 'fv_uar'),
                        'default' => true,
                    ),
                    array(
                        'id' => $this->slug . '_title',
                        'type' => 'text',
                        'title' => __('Filed title', 'fv_uar'),
                        'validate' => 'not_empty',
                        'msg' => 'please fill this field',
                        'default' => 'Select category:'
                    ),
                    array(
                        'id' => $this->slug . '_description',
                        'type' => 'text',
                        'title' => __('Filed description', 'fv_uar'),
                        'subtitle' => __('Alloved tags in field: [a, br, strong].', 'fv_uar'),
                        'validate' => 'html_custom',
                        'allowed_html' => array(
                            'a' => array(
                                'href' => array(),
                                'title' => array(),
                                'target' => array(),
                            ),
                            'br' => array(),
                            'strong' => array()
                        ),
                        'msg' => 'please correct fill this field',
                        'default' => ''
                    ),
                    array(
                        'id' => $this->slug . '_empty',
                        'type' => 'text',
                        'title' => __('Filed empty value text (will not shows if Make current contest active *enabled*)', 'fv_uar'),
                        'validate' => 'not_empty',
                        'msg' => 'please fill this field',
                        'default' => 'Select category'
                    ),
                    
                    array(
                        'id' => $this->slug . '_error',
                        'type' => 'text',
                        'title' => __('Upload error message', 'fv_uar'),
                        'subtitle' => __('Enter error message, if user not select category (contest).', 'fv_uar'),
                        'validate' => 'not_empty',
                        'msg' => 'please fill this field',
                        'default' => 'Please select category!'
                    ),
                )
            );

            return $sections;
        }

        /**
         * Helper function to get the class object. If instance is already set, return it.
         * Else create the object and return it.
         *
         * @since 1.0.0
         *
         * @return object $instance Return the class instance
         */
        public static function get_instance() {

            if (!isset(self::$instance)) {
                return self::$instance = new FvAddon_CatSelectInUploadF('CatSelectInUploadF', 'csuf');
            }
            
            return self::$instance;
        }

    }

    /** Instantiate the class */
    FvAddon_CatSelectInUploadF::get_instance();
}

// Function :: END
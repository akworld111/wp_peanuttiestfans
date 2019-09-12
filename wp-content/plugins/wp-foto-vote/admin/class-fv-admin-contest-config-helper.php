<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The contest class.
 *
 * Used from doing most operations with contest and photos - add/edit/deleted
 *
 * @since      ?
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Contest_Config_Helper extends FV_Admin_Contest_Config_Abstract
{
    public $fields = null;
    
    protected static $instance = null;
    
    public static function instance() {
        if ( self::$instance == null ) {
            self::$instance = new FV_Admin_Contest_Config_Helper;
        }
        
        return self::$instance;
    }

    public function __construct() {
        
        if ( $this->fields === null ) {
            $this->fields = array(
                ## NO SECTION ##
                'general'    => array(
                    'title'   => '',
                    'fields'  =>  array(
                        'name' => array (
                            'label'      => 'Contest title',
                            'icon'       => '',
                            'type'       => 'text',
                            'need_render'=> false,
                            'fm'          => true,
                        ),
                        'status' => array (
                            'label'      => 'Status',
                            'icon'       => '',
                            'type'       => 'select',
                            'options'   => array(
                                FV_Contest::PUBLISHED => __('live', 'fv'),
                                FV_Contest::FINISHED  => __('finished', 'fv'),
                                FV_Contest::DRAFT     => __('draft (hidden for public)', 'fv'),
                            ),
                            'default'   => 1,
                            'need_render' => false,
                            'fm'          => true,
                        ),
                    ),
                ),

                ## TAB VOTING ##
                'voting'    => array(
                    'title'   => __('Voting settings', 'fv'),
                    'fields'  =>  array(
                        'date_start' => array (
                            'label'     => __('Date start', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-calendar"></i>',
                            'container' => 'col-sm-6 col-xs-12',
                            'type'      => 'datetime',
                            'desc'      => __('year-month-day h:m:s', 'fv'),
                            'sanitize'  => '',
                            'fm'          => true,
                        ),
                        'date_finish' => array (
                            'label'     => __('Date finish', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-calendar"></i>',
                            'tooltip'   => __('When time ends, vote buttons will be hidden,<br/> and user can only see results', 'fv'),
                            'container' => 'col-sm-6 col-xs-12',
                            'type'      => 'datetime',
                            'desc'      => __('year-month-day h:m:s', 'fv'),
                            'sanitize'  => '',
                            'fm'          => true,
                        ),
                        'voting_frequency' => array (
                            'label'     => __('Frequency of voting', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-history"></i>',
                            'tooltip'   => __('Select type of voting - how many user can vote in contest', 'fv'),
                            'container' => 'col-sm-12',
                            'type'      => 'select',
                            'options'   => array(
                                'once'      => __('Once for one photo for all time', 'fv'),
                                'onceF2'    => __('Once for 2 photos for all time', 'fv'),
                                'onceF3'    => __('Once for 3 photos for all time', 'fv'),
                                'onceF10'   => __('Once for 10 photos for all time', 'fv'),
                                'onceFall'  => __('For each photo once', 'fv'),
                                'dayFonce'  => __('For one photo once per day (00-24)', 'fv'),
                                '24hF2'     => __('For 2 photos once for 24 hours', 'fv'),
                                '24hF3'     => __('For 3 photos once for 24 hours', 'fv'),
                                '24hFall'   => __('For each photo once for 24 hours', 'fv'),
                            ),
                            'options_action'   => 'fv/admin/contest_settings/voting_frequency',
                            'default'   => '24hFall',
                            'desc'      => __('how ofter user can vote', 'fv') . ' more here - <a href="http://wp-vote.net/doc/voting-settings/" target="_blank">http://wp-vote.net/doc/voting-settings/</a>',
                            'fm'          => true,
                        ),

                        'voting_security' => array (
                            'label'     => __('Contest security type', 'fv'),
                            'icon'      => '<span class="dashicons dashicons-shield-alt"></span>',
                            'tooltip'   => __('Select - how secure contest voting process?', 'fv'),
                            'container' => 'col-sm-12',
                            'type'      => 'select',
                            'options'   => array(
                                'cookiesAip'    => 'IP + evercookie',
                                'cookies'       => 'evercookie',
                            ),
                            'options_action'   => 'fv/admin/contest_settings/security_type',
                            'default'   => 'cookiesAip',
                            'desc'      => 'more here - <a href="http://wp-vote.net/doc/voting-settings/" target="_blank">http://wp-vote.net/doc/voting-settings/</a>',
                            'fm'          => true,
                        ),

                        'voting_security_ext' => array (
                            'label'     => __('Contest additional security', 'fv'),
                            'icon'      => '<span class="dashicons dashicons-shield-alt"></span>',
                            'tooltip'   => __('Select - how secure contest voting process?', 'fv'),
                            'container' => 'col-sm-14',
                            'type'      => 'select',
                            'options'   => array(
                                'none'              => 'none',
                                'reCaptcha'         => 'Recaptcha (require Recaptcha KEY)',
                                'subscribe'         => 'Subscribe form (require selected "Page, where contest are placed")',
                                'fbShare'           => 'Facebook Share (require FB APP ID)',
                                'social'            => 'Social login',
                                'registered'        => 'Authorized user',
                            ),
                            'options_action'   => 'fv/admin/contest_settings/security_type',
                            'default'   => 'none',
                            'desc'      => 'more here - <a href="http://wp-vote.net/doc/voting-settings/" target="_blank">http://wp-vote.net/doc/voting-settings/</a>',
                            'fm'          => true,
                        ),

                        'voting_type' => array (
                            'label'     => __('Voting type', 'fv'),
                            'icon'      => '<span class="fvicon fvicon-heart"></span>',
                            'tooltip'   => '',
                            'container' => 'col-sm-10',
                            'type'      => 'select',
                            'options'   => array(
                                'like'  => 'Like (+1)',
                                'rate'  => 'Rating (Change stars count possible in Settings => Voting)',
                            ),
                            'default'   => 'like',
                            'desc'      => '',
                            'fm'          => true,
                        ),
                    )
                ),

                ## TAB VOTING ##
                'winners'    => array(
                    'title'   => __('Winners / Leaders', 'fv'),
                    'fields'  =>  array(

                        'show_leaders' => array (
                            'label'     => __('Display leaders block ?', 'fv'),
                            'icon'      => '<span class="fvicon fvicon-signup"></span>',
                            'tooltip'   => 'Display most votes competitors before main entries?',
                            'container' => 'col-sm-15 clearfix',
                            'type'      => 'select',
                            'options'   => array(
                                0  => 'No (you can still use Leaders shortcode)',
                                1  => 'Yes (until voting is active)',
                                2  => 'Always (even when Winners is picked)',
                            ),
                            'default'   => 0,
                            'desc'      => 'You can also use leaders Shortcode and turn off this option for show Leaders in other place/page. ' .
                                '<a href="' . admin_url('admin.php?page=fv-settings#leaders') . '" target="_blank">More settings here</a>)',
                            'fm'          => true,
                        ),

                        'winners_pick' => array (
                            'label'     => __('Winners pick method (when voting dates ends)?', 'fv'),
                            'icon'      => '<span class="fvicon fvicon-signup"></span>',
                            'tooltip'   => 'How to pick winners?',
                            'container' => 'col-sm-15',
                            'type'      => 'select',
                            'options'   => fv_get_winners_pick_types(),
                            'default'   => 'auto',
                            'desc'      => '',
                            'fm'          => false,
                        ),

                        'winners_count' => array (
                            'label'     => __('How many winners pick?', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-calendar"></i>',
                            'tooltip'   => __('Winners count, that need to be picked', 'fv'),
                            'container' => 'col-sm-9 col-xs-12',
                            'class'     => ' ',
                            'type'      => 'number',
                            //'desc'      => __('year-month-day h:m:s', 'fv'),
                            'sanitize'  => 'number',
                            'default'   => 3,
                            'min'       => 0,
                            'max'       => 20,
                            'size'      => 3,
                            'fm'          => true,
                        ),



                    ),
                ),

                ## TAB UPLOAD ##
                'upload'    => array(
                    'title'   => __('Upload settings', 'fv'),
                    'fields'  =>  array(
                        'moderation_type' => array (
                            'label'     => __('Moderation?', 'fv'),
                            'icon'      => '<span class="fvicon fvicon-signup"></span>',
                            'tooltip'   => 'Admin must moderate photos before publishing on site?',
                            'container' => 'col-sm-5',
                            'type'      => 'select',
                            'options'   => array(
                                'pre'   => 'Need moderation',
                                'after' => 'No, thanks',
                            ),
                            'default'   => 'pre',
                            'desc'      => '',
                            'fm'          => true,
                        ),

                        'upload_date_start' => array (
                            'label'     => __('Upload date start', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-calendar"></i>',
                            'container' => 'col-sm-6 col-xs-12',
                            'type'      => 'datetime',
                            'desc'      => __('year-month-day h:m:s', 'fv'),
                            'sanitize'  => '',
                            'need_render' => false,
                            'fm'          => false,
                        ),
                        'upload_date_finish' => array (
                            'label'     => __('Upload date finish', 'fv'),
                            'icon'      => '<i class="fvicon fvicon-calendar"></i>',
                            'tooltip'   => __('When time ends, vote buttons will be hidden,<br/> and user can only see results', 'fv'),
                            'container' => 'col-sm-6 col-xs-12',
                            'type'      => 'datetime',
                            'desc'      => __('year-month-day h:m:s', 'fv'),
                            'sanitize'  => '',
                            'need_render' => false,
                            'fm'          => false,
                        ),
                    ),
                ),

                ## TAB other ##
                'other'    => array(
                    'title'   => __('Other', 'fv'),
                    'fields'  =>  array(
                        'cover_image' => array (
                            'label'     => __('Cover image ID for contest list', 'fv'),
                            'icon'      => '',
                            'tooltip'   => __('Don`t shows in photos list, only as cover image.', 'fv'),
                            'container' => '',
                            'type'      => 'media',
                            'desc'      => __('(need, only if you uses contest_list shortcode)', 'fv'),
                            'sanitize'  => 'number',
                            'fm'        => false,
                        ),
                    ),
                ), ## /other

            );
        }
    }

    /**
     * @param FV_Contest|null $contest
     * @return mixed|void
     */
    public function get_fields( $contest = null )
    {
        return apply_filters( 'fv/admin/contest/config/fields', $this->fields, $contest );
    }

    /**
     * Save contest params to database
     * @param array     $new_data
     * @param object    $contest
     * @return bool|int
     */
    public function save_fields($new_data, $contest )
    {
        $fields = $this->_get_fields_flat( $contest );

        if ( !$fields ) {
            return;
        }

        $value = '';
        $updates = false;
        foreach ($fields as $field_key => $field) {
            $field = $this->_normalize_field( $field_key, $field );

            if ( isset($new_data[$field_key]) ) {
                $new_value = $this->_sanitize_field( $field, $new_data[$field_key] );
                $updates[$field_key] = $new_value;
            }
        }

        if ( $updates ) {
            return ModelContest::q()->updateByPK( $updates, $contest->id );
        }

        return false;
    }

    // ====================


    /**
     * Add new field
     *
     * @param string    $field_key
     * @param array     $field_params
     * @param string    $section
     */
    public static function register_field( $field_key, $field_params, $section )
    {
        self::instance()->_register_field( $field_key, $field_params, $section );
    }

    /**
     * Change some of field params
     * @param string    $field_key
     * @param array     $new_field_params
     * @param string    $section
     */
    public static function change_field( $field_key, $new_field_params, $section ) {
        self::instance()->_change_field( $field_key, $new_field_params, $section );
    }

    /**
     * Remove field from array
     *
     * @param string    $field_key
     * @param string    $section
     */
    public static function deregister_field( $field_key, $section ) {
        self::instance()->_deregister_field( $field_key, $section );
    }

    /**
     * Add new section
     *
     * @param string    $section_key
     * @param string    $section_title
     */
    public static function register_section( $section_key, $section_title, $fields = array() ) {
        self::instance()->_register_section( $section_key, $section_title, $fields = array() );
    }    
    
}

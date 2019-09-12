<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 *
 * @since      2.2.418
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Contest_Meta_Helper extends FV_Admin_Contest_Config_Abstract
{
    /**
     * @var array
     */
    public $fields = array(
        'addons' => array(
            'title'     =>  'Addons',
            'fields'    =>  array(),
        )
    );

    protected static $instance = null;

    public static function instance() {
        if ( self::$instance == null ) {
            self::$instance = new FV_Admin_Contest_Meta_Helper;
        }

        return self::$instance;
    }

    /**
     * @param FV_Contest|null $contest
     * @return mixed|void
     */
    public function get_fields( $contest = null )
    {
        //fv_dump(self::$fields);
        return apply_filters( 'fv/admin/contest/config/meta_fields', $this->fields, $contest );
    }

    /**
     * Save meta fields to database from array ($_POST['meta'])
     * @param array     $new_meta_data
     * @param FV_Contest    $contest
     */
    public function save_meta_fields( $new_meta_data, $contest )
    {
        $meta_fields = $this->_get_fields_flat( $contest );

        if ( !$meta_fields ) {
            return;
        }

        $contest_meta = $contest->meta()->get_custom_all();

        $value = '';
        foreach ($meta_fields as $field_key => $field) {
            $field = $this->_normalize_field( $field_key, $field );

            if ( isset($new_meta_data[$field_key]) ) {

                $new_value = $this->_sanitize_field( $field, $new_meta_data[$field_key] );

                if ( isset($contest_meta[$field_key]) ) {
                    ModelMeta::q()->updateByPK(
                        array( 'value' => $new_value ),
                        $contest_meta[$field_key]->ID
                    );
                } else {
                    // Do not save empty values
                    if ( $new_value === '' ) {
                        continue;
                    }
                    ModelMeta::q()->insert( array(
                        'contest_id'    =>$contest->id,
                        'contestant_id' => 0,
                        'meta_key'      => $field_key,
                        'custom'        => 1,
                        'value'         => $new_value
                    ) );
                }

            }
        }
        
        do_action( 'fv/admin/contest/config/save_meta_fields/after', $new_meta_data, $contest );
    }

    /**
     * @param FV_Contest $contest
     * @param bool $display_empty
     * @return string
     * @throws Exception
     */
    public function render_meta_fields( $contest, $display_empty = false )
    {
        $meta_sections = $this->get_fields( $contest );

        $contest_meta = $contest->meta()->get_all_flat();

        $meta_value = '';
        $html = '';
        $section_fields_html = '';
        foreach ($meta_sections as $section_key => $section) {
            $section_fields_html = '';

            foreach ($section['fields'] as $field_key => $field) {

                $meta_value = '';
                if ( isset($contest_meta[$field_key]) ) {
                    $meta_value = $contest_meta[$field_key];
                } elseif ( isset($field['default']) ) {
                    $meta_value = $field['default'];
                }

                $section_fields_html .= $this->get_field_html( $field_key, $section_key, $field, $meta_value, $contest, true );
                
            }

            if ( $section_fields_html || $display_empty ) {
                $section_title = apply_filters('fv/admin/contest/config/meta_section_title/' . $section_key, $section['title'], $contest);
                $html .= $this->get_section_html($section_key, $section_title, $section_fields_html);
            }
        }

        unset($section_fields_html);
        return $html;
    }


    /**
     * Add new field
     *
     * @param string    $field_key
     * @param array     $field_params
     * @param string    $section
     */
    public static function register_field( $field_key, $field_params, $section = 'addons' )
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

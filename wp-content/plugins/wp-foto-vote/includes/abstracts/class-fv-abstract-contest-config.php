<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The contest class.
 *
 * Used from doing most operations with contest and photos - add/edit/deleted
 *
 * @since      2.2.604
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
abstract class FV_Admin_Contest_Config_Abstract
{
    public $fields = null;
    
    protected $hook_fields_slug = 'fields';

    public function get_fields( $contest ) {
        return $this->fields;
    }

    /**
     * Add new field
     *
     * @param string    $field_key
     * @param array     $field_params
     * @param string    $section
     */
    public function _register_field( $field_key, $field_params, $section )
    {
        //self::_init();
        if ( !isset( $this->fields[$section]['fields'][$field_key] ) ) {
            $this->fields[$section]['fields'][$field_key] = $field_params;
        }else {
            trigger_error( __CLASS__. "::register_field - field {$field_key} already exists in section {$section}!");
        }
        //fv_dump(self::$fields);
    }

    /**
     * Change some of field params
     * @param string    $field_key
     * @param array     $new_field_params
     * @param string    $section
     */
    public function _change_field( $field_key, $new_field_params, $section ) {
        //self::_init();
        if ( isset( $this->fields[$section]['fields'][$field_key] ) ) {
            $this->fields[$section]['fields'][$field_key] = array_merge($this->fields[$section]['fields'][$field_key], $new_field_params);
        }
    }

    /**
     * Remove field from array
     *
     * @param string    $field_key
     * @param string    $section
     */
    public function _deregister_field( $field_key, $section ) {
        //self::_init();
        if ( isset( $this->fields[$section]['fields'][$field_key] ) ) {
            unset( $this->fields[$section]['fields'][$field_key] );
        }
    }

    /**
     * Add new section
     *
     * @param string    $section_key
     * @param string    $section_title
     */
    public function _register_section( $section_key, $section_title, $fields = array() ) {
        //self::_init();
        if ( !isset( $this->fields[$section_key] ) ) {
            $this->fields[$section_key]['title'] = $section_title;
            $this->fields[$section_key]['fields'] = $fields;
        }else {
            trigger_error( __CLASS__. "::register_section - section {$section_key} already exists!");
        }
    }

    /**
     * Get Flat array [$field_key=>$field_data]
     * @param $contest
     * @return array
     */
    public function _get_fields_flat( $contest = null )
    {
        $fields_full = $this->get_fields( $contest );
        $fields_flat = array();
        foreach ( $fields_full as $tab_key => $tab ) {
            foreach ( $tab['fields'] as $field_key => $field ) {

                $field['tab'] = $tab_key;
                $fields_flat[$field_key] = $field;
            }
        }

        return $fields_flat;
    }
    
    /**
     * Set all missing params and convert to Object
     *
     * @param string    $field_key
     * @param array     $field
     * @return object
     */
    public function _normalize_field( $field_key, $field )
    {
        return (object) array_merge(array(

            'name'          => $field_key,
            'label'         => '',
            'icon'          => '',

            'admin_only'    => false,
            'render_callback' => false,
            'need_render'   => true,

            'wrap_input'    => '',
            'wrap_input_class' => '',
            
            'class'         => 'form-control',
            'label_class'   => '',
            'minlength'     => '',
            'maxlength'     => '',
            'placeholder'   => '',
            'required'      => '',

            'tooltip'       => '',
            'container'     => 'col-sm-12',
            'type'          => 'text',
            'options'       => array(),
            'options_action'=> '',
            'default'       => '',
            'desc'          => '',

            'fv'            => true,        // Frontend Manage

            'sanitize'      => 'sanitize_text_field',
            'sanitize_params' => array(),
            'sanitize_callback' => false,

        ), $field);
    }

    public function _sanitize_field($field, $value )
    {
        if ( $field->sanitize == 'sanitize_text_field' ) {
            $value = sanitize_text_field( $value );
        } else if ( $field->sanitize == 'sanitize_textarea_field' ) {
            $value = sanitize_textarea_field( $value );
        }else if ( $field->sanitize == 'wp_kses_post' ) {
            $value = wp_kses_post( $value );
        }else if ( $field->sanitize == 'wp_kses' ) {
            $value = wp_kses( $value, $field->sanitize_params );
        } else if ( $field->sanitize == 'number' ) {
            $value = absint($value);
        } else if ( $field->sanitize == 'float' ) {
            $value = floatval($value);
        }


        return $value;
    }
    
    public static function get_section_html($section_key, $section_title, $fields_html )
    {
        return fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . '/contest/__contest_config_section.php', compact('section_key','section_title','fields_html'), true );
    }
    
    public function get_section_with_fields_html($section_key, $contest)
    {
        $meta_sections = $this->get_fields( $contest );

        if ( !isset($meta_sections[$section_key]) ) {
            return "Invalid Section key!";
        } 
        if ( empty($meta_sections[$section_key]['fields']) ) {
            return "Invalid Section FIELDS!";
        } 

        $html = '';
        $section_fields_html = '';

        foreach ($meta_sections[$section_key]['fields'] as $field_key => $field) {
            
            if ( !isset($contest->$field_key) ) {
                continue;
            }

            $section_fields_html .= $this->get_field_html( $field_key, $section_key, $field, $contest->$field_key, $contest, false );

        }

        if ( $section_fields_html ) {
            $section_title = apply_filters('fv/admin/contest/config/section_title/' . $section_key, $meta_sections[$section_key]['title'], $contest);
            $html .= $this->get_section_html($section_key, $section_title, $section_fields_html);
        }
        
        unset($section_fields_html);
        return $html;
    }

    /**
     * @param string        $field_key
     * @param string        $field_tab
     * @param bool|array    $field_data
     * @param mixed         $value
     * @param mixed         $contest
     * @param bool          $is_meta
     *
     * @return string   HTML
     */
    public function get_field_html($field_key, $field_tab, $field_data = false, $value, $contest = false, $is_meta = false )
    {
        if (!$field_data && !isset($this->fields[$field_tab]['fields'][$field_key])) {
            return;
        }

        if ( !$field_data ) {
            $field_data = $this->fields[$field_tab]['fields'][$field_key];
        }

        $field = $this->_normalize_field( $field_key, $this->fields[$field_tab]['fields'][$field_key] );
        
        if ( $field->admin_only && !is_admin() ) {
            return '';
        }
        if ( !$field->need_render ) {
            return '';
        }

        if ( $field->render_callback && is_callable($field->render_callback) ) {
            return call_user_func( $field->render_callback, $field, $value, $contest);
        }

        if ( $field->type == 'heading' ) {
            $html = "<div class=\"form-group {$field->container}\">";
            $html .= '<div><strong><legend>' . $field->label . '</legend></strong></div>';
            
            if ( $field->desc ) {
                $html .= '<small>';
                $html .= $field->desc;
                $html .= '</small>';
            }
            $html .= "</div>";

            return $html;
        }

        if ( $is_meta ) {
            $field->class .= ' ' . sanitize_title( 'field-meta-' . $field->name );
            $field->name = 'meta[' . $field->name . ']';
        } else {
            $field->class .= ' ' . sanitize_title( 'field-' . $field->name );
        }

        $required = '';
        if ( $field->required ) {
            $required = 'required';
        }

        $html = "<div class=\"form-group {$field->container}\">";
        if ( $field->label ) {
            $field->label_class = esc_attr($field->label_class);
            $html .= "<label for=\"{$field->name}\" class=\"{$field->label_class}\">";
            $html .= $field->icon . ' ' . $field->label;
            if ( $field->tooltip ) {
                $html .= fv_tooltip_code( $field->tooltip );
            }
            $html .= '</label>';
        }

        if ( $field->wrap_input ) {
            $field->wrap_input_class = esc_attr($field->wrap_input_class);
            $html .= "<{$field->wrap_input} class=\"{$field->wrap_input_class}\">";
        }

        switch ($field->type){
            case 'text':
                $pattern = '';
                if ( isset($field->minlength) && $field->minlength > 0
                    && isset($field->maxlength) && $field->maxlength > 3 )
                {
                    $pattern = ' pattern=".{' . $field->minlength . ',' .  $field->maxlength . '}" ';
                }

                $html .= '<input class="' . esc_attr($field->class) . '" type="text" name="' . esc_attr($field->name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $value . '" ' . $required . $pattern . '/>' . "\n";
                break;
            case 'textarea':
                $html .= '<textarea class="' . esc_attr($field->class) . '" name="' . esc_attr($field->name) . '" placeholder="' . esc_attr($field->placeholder) . '" ' . $required . '/>' . $value . '</textarea>' . "\n";
                break;
            case 'number':
                $min_max = '';
                if ( isset($field->min) ) {
                    $min_max = ' min=' . (int)$field->min . '';
                }
                if ( isset($field->max) ) {
                    $min_max .= ' max=' . (int)$field->max . '';
                }
                if ( isset($field->size) ) {
                    $min_max .= ' size=' . (int)$field->size . '';
                }

                $html .= '<input class="form-control ' . esc_attr($field->class) . '" type="number" name="' . esc_attr($field->name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $value . '" ' . $required . $min_max . '/>' . "\n";
                break;

            case 'datetime':
                $html .= '<input class="datetime ' . esc_attr($field->class) . '" type="text" name="' . esc_attr($field->name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $value . '" ' . $required . '/>' . "\n";
                break;

            case 'checkbox':
                //$html .= '<input type="checkbox" ' . checked((bool)$value) . ' name="' . esc_attr($field->name) . '[]" value="1" id="' . esc_attr(sanitize_title($field->name)) . '" /><span class="fv-checkbox-placeholder"></span>';
                $html .= fv_admin_get_switch_toggle( $field->name, (bool)$value, sanitize_title($field->name) );
                break;

            case 'select':
                $html .= '<select name="' . esc_attr($field->name) . '" class="' . esc_attr($field->class) . '" ' . $required . '>';

                foreach ($field->options as $k => $opt) {
                    $html .= '<option ' . selected($k, $value, false) . ' value="' . esc_attr($k) . '">' . $opt . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'media_select_ID':
//    <input type="number" id="fv_ad_image_id" name="fv_ad_image_id" value="< ?php echo $ad_image_ID;  >" min="0" max="99999" size="5">
//    <input type="hidden" id="fv_ad_image_thumb">
//    <input type="button" id="upload_ad_IMG" class="button" value="Upload Image" "/>
                $button_caption = 'Select image';
                if ( isset($field->button_caption) ) {
                    $button_caption = $field->button_caption;
                }
                $filed_id = sanitize_title($field->name);

                $html .= '<input type="number" name="' . esc_attr($field->name) . '" id="' . $filed_id . '" value="' . (int)$value . '" size=7 />' . "\n";
                $html .= '<input type="hidden" id="' . $filed_id . '_thumb" />' . "\n";

                $image_thumb = fv_get_placeholder_img_arr();
                if ( $value ) {
                    $image_thumb = wp_get_attachment_image_src($value, 'thumb');
                }
                $html .= '<img src="' . $image_thumb[0] . '" id="' . $filed_id . '_thumb_img" height="28">' . "\n";

                $html .= '<button class="button" type="button" onclick="fv_wp_media_upload(\'#' .$filed_id. '_thumb\', \'#' .$filed_id. '\', \'#' .$filed_id. '_thumb_img\')"/>'  . $button_caption . '</button>' . "\n";
                break;
        }

        if ( $field->desc ) {
            $html .= '<small>';
            $html .= $field->desc;
            $html .= '</small>';
        }

        if ( $field->wrap_input ) {
            $html .= "</{$field->wrap_input}>";
        }

        $html .= '</div>';

        return $html;
    }

}

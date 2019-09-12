<?php
/**
 * Class for provide upload form rendering and other (save, get structure, etc).
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class Fv_Form_Helper {

    /**
     * Is theme supports leaders block
     *
     * @since    2.2.081
     * @access   public
     * @var      bool
     */
    protected static $optionName = 'fv-form-fields';

    /**
     * AJAX save data from From builder on click `Save`
     *
     * @return void
     * @output string ('ok'/'no')
     */
    public static function AJAX_save_form_structure() {
        //$json_data = file_get_contents('php://input');

        //var_dump($_POST);
        //$form_data = json_decode($json_data, true);
        $form_data = $_POST;

        //die();

        check_ajax_referer();
        if ( !FvFunctions::curr_user_can() ) {
            fv_AJAX_response(false, 'Not allowed!');
        }

        if ( empty($_GET['form_id']) ) {
            fv_AJAX_response(false, 'No Form_id passed!');
        }

        //  || !is_array($form_data['fields'])
        if ( empty($form_data['fields']) || empty($form_data['title']) ) {
            fv_AJAX_response(false, 'No Form data passed!');
        }

        $form_ID = (int) $_GET['form_id'];

        //$form_fields = $form_data['fields'];
        // FIX for Multi Upload Checkboxes
        /*foreach($form_fields as $field_key => $field) {
            if ( 'file' == $field['field_type'] ) {
                foreach ( $field['field_options'] as $opt_key => $opt_val ) {
                    if ( false !== strpos($opt_key, 'multi_') && "false" == $opt_val ) {
                        // Replace "false" to ""
                        // Else JS "false" will be == true
                        $form_fields[$field_key]['field_options'][$opt_key] = '';
                    }
                }
            }
            if ( "false" == $field['required'] ) {
                $form_fields[$field_key]['required'] = '';
            }
        }*/

        //$form_fields_json = json_encode($form_fields);

        // Get JSON snd save it to BD
        $form_fields_json = $form_data['fields'];
        $form_title = sanitize_text_field($form_data['title']);

        ModelForms::q()->updateByPK( array(
                        'title'=>$form_title,
                        'fields'=>$form_fields_json,
                        'last_edited'=>current_time('timestamp')
                    ), $form_ID );
        fv_AJAX_response(true);
    }

    public static function AJAX_reset_form_structure() {
        check_ajax_referer();
        if ( !FvFunctions::curr_user_can() ) {
            fv_AJAX_response(false, 'Not allowed!');
        }

        if ( empty($_GET['form_id']) ) {
            fv_AJAX_response(false, 'No Form_id passed!');
        }
        $form_ID = (int) $_GET['form_id'];
        ModelForms::q()->updateByPK( array('fields'=>self::get_default_form_structure()), $form_ID );

        //update_option( self::$optionName, self::get_default_form_structure() );
        fv_AJAX_response(true, '', array('fields'=>self::get_default_form_structure()));
    }

    /**
     * @param int $form_ID
     * @return string
     */
    public static function get_form_structure($form_ID) {
        $form = ModelForms::q()->findByPK($form_ID, true);
        if ( !empty($form) ) {
            return $form->fields;
        }
        return ModelForms::q()->findByPK( ModelForms::q()->getDefaultFormID(), true )->fields;
    }

    /**
     * @param int $form_ID
     * @return object
     */
    public static function get_form_structure_obj( $form_ID, $form_structure = false ) {
        if ( !$form_structure ) {
            $form_structure = self::get_form_structure($form_ID);
        }
        $form_obj = json_decode( stripslashes( $form_structure ) );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            fv_log( "Error on parsing form fields JSON: " . json_last_error() );
            return array();
        }
        return $form_obj;
    }

    public static function get_default_form_structure() {
        return '[{"label":"Photo name","field_type":"text","required":true,"field_options":'.
        '{"size":"small","save_to":"name","default_value":"display_name","description":"Enter photo name","save_format":"{value}","save_key":"name"},'.
        '"cid":"c20","placeholder":"name"},{"label":"Your email","field_type":"email","required":true,"field_options":'.
        '{"description":"","default_value":"email","save_to":"user_email","save_format":"{value}","save_key":"user_email"},"cid":"c24","placeholder":"This is email"},'.
        '{"label":"Select file","field_type":"file","required":true,"field_options":{},"cid":"image","placeholder":"Enter photo name"}]';
    }

    public static function _get_photo_data_from_POST($form_data, $structure) {
        if ( !is_object($structure) && !isset($structure) ) {
            FvLogger::addLog('Fv_Form_Helper::_get_photo_email_from_POST - $structure error');
        }
        //var_dump($form_data);
        //var_dump($structure);
        //die();
        $new_photo = array( 'name' => '', 'description' => '', 'full_description' => '', 'user_email' => '', 'upload_info' => '' );
        foreach($structure as $field) {

            if ( !isset($form_data[$field->cid]) ) {
                //FvLogger::addLog('Fv_Form_Helper::_get_photo_email_from_POST field not exists in $form_data - ' . $field->cid);
                continue;
            }

            if ( $field->field_type == 'rules_checkbox' ) {
                continue;
            }

            if ( $field->field_type == 'category' && $form_data[$field->cid] && is_array($form_data[$field->cid]) ) {
                $new_photo['categories'] = array_map('absint', $form_data[$field->cid]);
                continue;
            }

            if ( is_array($form_data[$field->cid]) ) {
                if ( $field->field_type == 'date' ) {
                    if ( !empty($field->field_options->date_format[10]) && in_array($field->field_options->date_format[10], array('.','-','/')) ) {
                        $delimiter = $field->field_options->date_format[10];
                    } else {
                        $delimiter = '.';
                    }
                    $form_data[$field->cid] = implode($delimiter, $form_data[$field->cid]);
                } else {
                    $form_data[$field->cid] = implode(';', $form_data[$field->cid]);
                }
            }

            // remove Emoji like 😇 or 😔, else data will be not saved to database
            $form_data[$field->cid] = FvFunctions::remove_emoji($form_data[$field->cid]);

            // Verify Field "Save format"
            if ( !isset($field->field_options->save_format) || strpos($field->field_options->save_format, '{value}') === false ) {
                $field_save_format = '{value}';
            } else {
                $field_save_format = $field->field_options->save_format;
            }
            // Process data and save to contestant
            if ( isset($field->field_options->save_to) && array_key_exists($field->field_options->save_to, $new_photo) ) {
                switch ($field->field_options->save_to) {
                    case 'name':
                        //if ( strlen($new_photo['name']) > 1 ) { $new_photo['name'] .= '; '; }
                        $new_photo['name'] .= str_replace('{value}', sanitize_text_field($form_data[$field->cid]), $field_save_format);
                        break;
                    case 'description':
                        //if ( strlen($new_photo['description']) > 1 ) { $new_photo['description'] .= '; '; }
                        $new_photo['description'] .= str_replace('{value}', sanitize_text_field($form_data[$field->cid]), $field_save_format);
                        break;
                    case 'full_description':
                        //if ( strlen($new_photo['full_description']) > 1 ) { $new_photo['full_description'] .= '; '; }
                        $new_photo['full_description'] .= str_replace('{value}', wp_kses($form_data[$field->cid], 'default'), $field_save_format);
                        break;
                    case 'user_email':
                        $new_photo['user_email'] = str_replace('{value}', sanitize_email($form_data[$field->cid]), $field_save_format);
                        break;
                }
            } else {
                if ( !isset($field->field_options->save_key) ) {
                    $field->field_options->save_key = 'custom_' . rand(9999, 99999);
                } else {
                    $field->field_options->save_key = sanitize_title($field->field_options->save_key);
                }

                $new_photo['meta'][ $field->field_options->save_key ] = str_replace('{value}', sanitize_text_field($form_data[$field->cid]), $field_save_format);
            }
        }

        if ( is_array($new_photo['upload_info']) && count($new_photo['upload_info']) > 0 ) {
            $new_photo['upload_info'] = json_encode($new_photo['upload_info']);
        }

        return apply_filters('fv/public/_get_photo_data_from_POST', $new_photo);
    }

    /**
     * Render upload form fields
     *
     * @param array $public_translated_messages
     * @param object $contest
     * @param bool $show_labels
     */
    public static function render_form($public_translated_messages, $contest, $show_labels = true) {
        $form_ID = !empty( $contest->form_id ) ? $contest->form_id : ModelForms::q()->getDefaultFormID();
        $fields = apply_filters('fv/public/render_upload_form/filter_fields', self::get_form_structure_obj($form_ID));

        $eol = "\n";
        $html ="";
        $c = 1;
        $cSectionBreak = 1;
        $wrap_class = '';
        $html .= '<fieldset>';
        foreach ($fields as $field) :

            if ( 'category' == $field->field_type && ! $contest->isCategoriesEnabled() ) {
                continue;
            }

            if ( !empty($field->field_options->show_to) ) {
                $user_id = get_current_user_id();
                // IF user not LOGGED IN
                if ( !$user_id && $field->field_options->show_to == 'logged' ) {
                    continue;
                } elseif ( $user_id && $field->field_options->show_to == 'no_logged' ) {
                    continue;
                }
            }

            if ($field->field_type !== 'section_break'):
                $wrap_class = '';

                if ( empty($field->width) ) {
                    $field->width = '1-1';
                }
                $wrap_class = 'fv-field-type--' . sanitize_title($field->field_type) . ' fv-field-w--' . $field->width;

                // Add a Key value to Class ()
                if ( !empty($field->field_options->save_key) ) {
                    $wrap_class .= ' fv-field-key--' . sanitize_title($field->field_options->save_key);
                }

                $html .= '<div class="fv_wrapper ' . $wrap_class  . '">' . $eol;
                    if ( $show_labels ) {
                        $html .= self::display_label($field, $c, $contest, $form_ID) . $eol;
                    }
                    $html .= '<div class="fv-field-padding-wrapper">' . $eol;
                        $html .= self::display_field($field, $c, $contest, $form_ID) . $eol;
                    $html .= '</div>' . $eol;
                $html .= '</div>' . $eol;
                $c++;
            else:
                $html .= '</fieldset>';
                $html .= '<legend>' . apply_filters('fv/public/upload_form/section_break', $field->label, $field, $cSectionBreak) . '</legend>';
                $html .= '<fieldset>';
                $cSectionBreak++;
            endif;
        endforeach;

        $html .= apply_filters("fv_upload_form_rules_filer", '', $c, $contest);

        $html .= '<div class="fv_wrapper fv-field-type--submit">' .
                    '<button type="submit" class="fv-upload-btn">' .
                        '<span id="fv_upload_preloader" class="fv_upload_preloader"> <span class="fvicon-spinner icon rotate-animation"></span> </span>' .
                        '<span class="fv-upload-btn-progress"> <span class="fv-upload-btn-progress-inner"></span> </span>' .
                        $public_translated_messages['upload_form_button_text'] .
                    '</button>' .
                    apply_filters("fv_upload_form_rules_hook", '', $c) .
                '</div>';

        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Generate HTML for displaying fields
     * @param  array $field     Field data
     * @param  int $c           Counter
     * @param  object $contest
     * @return string
     */
    public static function display_label($field, $c, $contest) {
        $html = '<label>';
        $html .=  apply_filters('fv/public/upload_form/label', $field->label, $field, $c, $contest);
        $html .= '</label>';
        return $html;
    }

    /**
     * Generate HTML for displaying fields
     *
     * @param  array $field Field data
     * @param  int $c Field number
     * @param  FV_Contest $contest*
     * @param  int $form_ID
     *
     * @return string
     */
    public static function display_field($field, $c, $contest, $form_ID) {

        if ( !isset($field->cid) ) {
            FvLogger::addLog("Fv_Form_Helper display_field error - no `cid` | Line: " . __LINE__);
            return "Form error!";
        }
         $html = '';
        //$this->settings_base
        $option_name = "form[" . $field->cid . "]";

        if (get_current_user_id() > 0) {
            $user_info = get_userdata( get_current_user_id() );
        } else {
            $user_info = false;
        }

        if ( empty($field->class) ) {
            $field->class = 'form-control';
        }

        if ( empty($field->id) ) {
            $field->id = '';
        }
        if ( empty($field->placeholder) ) {
            $field->placeholder = '';
        }
        if ( empty($field->field_type) ) {
            return '';
        }
        if ( empty($field->field_options) ) {
            $field->field_options = new stdClass();
        }

        // Set default value
        $data = '';
        if ( !empty($field->field_options->default_value) && get_current_user_id() > 0 ) {
            switch($field->field_options->default_value) {
                case 'display_name':
                    $data = $user_info->display_name;
                    break;
                case 'first_name':
                    $data = $user_info->first_name;
                    break;
                case 'last_name':
                    $data = $user_info->last_name;
                    break;
                case 'email':
                    $data = $user_info->user_email;
                    break;
                default:
                    $data = $field->field_options->default_value;
            }
        }

        // Allow display some default value to any field
        if ( !empty($field->value) ) {
            $data = $field->value;
        }

        if ( empty($field->field_options->description) ) {
            $field->field_options->description = '';
        }
        // for radio and select
        if ( empty($field->field_options->options) ) {
            $field->field_options->options = array();
        }

        $required = '';
        if ( !empty($field->required) && $field->required == true ) {
            $required = 'required';
            $field->class .= ' fv-field--required';
        }

        // Try remove ID attr
        //id="' . esc_attr($field->id) . '"
        switch ($field->field_type) {

            case 'text':
                $pattern = '';
                if ( isset($field->field_options->minlength) && $field->field_options->minlength > 0
                    && isset($field->field_options->maxlength) && $field->field_options->maxlength > 3 )
                {
                    $pattern = ' pattern=".{' . $field->field_options->minlength . ',' .  $field->field_options->maxlength . '}" ';
                }

                $html .= '<input class="' . esc_attr($field->class) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . esc_attr($data) . '" ' . $required . $pattern . '/>' . "\n";
                break;

            case 'phone':
                $mask = '';
                if ( !empty($field->field_options->format) ) {
                    $mask = stripslashes($field->field_options->format);
                }

                $html .= '<input class="' . esc_attr($field->class) . '" type="tel" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" data-mask="' . $mask . '" ' . $required . '/>' . "\n";
                break;

            case 'website':
                $html .= '<input class="' . esc_attr($field->class) . '" type="url" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . esc_attr($data) . '" ' . $required . '/>' . "\n";
                break;

            case 'email':
                $html .= '<input autocomplete="on" class="' . esc_attr($field->class) . '" type="email" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . esc_attr($data) . '" ' . $required . '/>' . "\n";
                break;

            case 'number':
                $max = '';
                $min = '';
                $units = '';
                if ( isset($field->field_options->max) ) {
                    $max = ' max="' . $field->field_options->max . '" ';
                }
                if ( isset($field->field_options->min) ) {
                    $min = ' min="' . $field->field_options->min . '" ';
                }
                if ( isset($field->field_options->units) ) {
                    $units = $field->field_options->units;
                }
                $html .= '<div><input style="display: inline-block;" class="' . esc_attr($field->class) . '" type="number" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . esc_attr($data) . '" ' . $required . $min . $max . '/> ' . $units . "</div>\n";
                break;

            case 'date':
                if ( empty($field->field_options->date_format) ) {
                    $field->field_options->date_format = 'dd.mm.yyyy.';
                }
                if ( in_array($field->field_options->date_format[10], array('.','-','/')) ) {
                    $delimiter = $field->field_options->date_format[10];
                } else {
                    $delimiter = '.';
                }
                if ( empty($field->field_options->date_day_label) ) {
                    $field->field_options->date_day_label = 'DD';
                }
                if ( empty($field->field_options->date_month_label) ) {
                    $field->field_options->date_month_label = 'MM';
                }
                if ( empty($field->field_options->date_year_label) ) {
                    $field->field_options->date_year_label = 'YY';
                }
                $delimiter_html = '<span class="date-delimiter">' . $delimiter . '</span>';
                $day = '<input class="' . esc_attr($field->class) . ' day_input" type="number" name="' . esc_attr($option_name) . '[day]" ' . $required . ' value="1" min="1" max="31" size="2"/>';
                $day .= '<span class="date-label date-label-day">' . $field->field_options->date_day_label . '</span>';
                $month = '<input class="' . esc_attr($field->class) . ' month_input" type="number" name="' . esc_attr($option_name) . '[month]" ' . $required . ' value="1" min="1" max="12" size="2"/>';
                $month .= '<span class="date-label date-label-month">' . $field->field_options->date_month_label . '</span>';
                $year = '<input class="' . esc_attr($field->class) . ' year_input" type="number" name="' . esc_attr($option_name) . '[year]" value="' . date('Y') . '" ' . $required . ' min="1920" max="2050" size="4"/>';
                $year .= '<span class="date-label date-label-year">' . $field->field_options->date_year_label . '</span>';

                $inner_class = 'day-is-first';
                switch ($field->field_options->date_format){
                    case 'dd.mm.yyyy.':
                    case 'dd-mm-yyyy-':
                    case 'dd/mm/yyyy/':
                        $date_field = $day . $delimiter_html . $month . $delimiter_html . $year;
                        $inner_class = 'day-is-first';
                        break;
                    case 'mm/dd/yyyy/':
                        $date_field = $month . $delimiter_html . $day . $delimiter_html . $year;
                        $inner_class = 'month-is-first';
                        break;
                    case 'yyyy-mm-dd-':
                        $date_field = $year . $delimiter_html . $month . $delimiter_html . $day;
                        $inner_class = 'year-is-first';
                        break;
                    default:
                        $date_field = $day . $delimiter_html . $month . $delimiter_html . $year;
                        break;
                }
                $html .= '<div class="fv-field-type--date-inner ' . $inner_class . '" data-format="' . $field->field_options->date_format . '">' . $date_field . '</div>';
                unset($date_field);
                unset($year);
                unset($month);
                unset($day);
                unset($delimiter_html);
                unset($delimiter);
                unset($inner_class);
                break;

            case 'paragraph':
            case 'textarea':
                $maxlength = '';
                if ( isset($field->field_options->maxlength) ) {
                    $maxlength = ' maxlength="' . $field->field_options->maxlength . '" ';
                } else {
                    $maxlength = ' maxlength="1254" ';
                }

                $html .= '<textarea class="' . esc_attr($field->class) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" ' . $required . $maxlength . '>' . esc_attr($data) . '</textarea>' . "\n";
                break;

            case 'rules_checkbox':
                $checked = '';
                if ( !empty($field->field_options->checked) ) {
                    $checked = 'checked="checked"';
                }
                $html .= '<label class="checkbox_input"><input type="checkbox" name="' . esc_attr($option_name) . '" class="fv_rules" ' . $checked . ' ' . $required . '/><span class="fv-checkbox-placeholder fvicon-"></span> ' . wp_kses_post($field->placeholder) . '</label> ' . "\n";
                break;

            case 'checkboxes':
            case 'checkbox_multi':
                foreach ($field->field_options->options  as $k => $opt) {
                    $html .= '<label for="' . esc_attr($field->cid . '_' . $k) . '" class="checkbox_input"><input type="checkbox" ' . checked($opt->checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($opt->label) . '" id="' . esc_attr($field->cid . '_' . $k) . '" /><span class="fv-checkbox-placeholder"></span> ' . $opt->label . '</label> ';
                }
                break;

            case 'radio':
                foreach ($field->field_options->options as $k => $opt) {
                    $html .= '<label for="' . esc_attr($field->cid. '_' . $k) . '" class="radio_input"><input type="radio" ' . checked($opt->checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($opt->label) . '" id="' . esc_attr($field->cid. '_' . $k) . '"  ' . $required . '/> ' . $opt->label . '</label> ';
                }
                break;

            case 'category':
                $multi_select = $contest->isMultiCategories() ? ' multiple="multiple" ' : '';
                $html .= '<select name="' . esc_attr($option_name) . '[]" class="' . esc_attr($field->class) . '" ' . $multi_select . $required . '>';

                if ( ! $multi_select ) {
                    $html .= '<option value="">' . $field->placeholder . '</option>';
                }

                foreach ($contest->getCategories() as $category) {
                    $html .= '<option value="' . esc_attr($category->term_id) . '">' . $category->name . '</option>';
                }
                $html .= '</select> ';
                break;


            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" class="' . esc_attr($field->class) . '" ' . $required . '>';
                if ( !empty($field->include_blank_option) && $field->include_blank_option) {
                    $html .= '<option ' . selected(true) . ' value="">' . __("Select value", 'fv') . '</option>';
                }

                if ( $field->placeholder ) {
                    $html .= '<option value="">' . $field->placeholder . '</option>';
                }

                foreach ($field->field_options->options as $k => $opt) {
                    $html .= '<option ' . selected($opt->checked, true, false) . ' value="' . esc_attr($opt->label) . '">' . $opt->label . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" multiple="multiple" ' . $required . '>';
                foreach ($field->field_options->options as $k => $v) {
                    $selected = false;
                    if (in_array($k, $data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
                }
                $html .= '</select> ';
                break;

            case 'file':
                if ( apply_filters( 'fv/public/upload_form/custom_file_input/uses', false, $contest ) === false ) {
                    $required = '';
                    $placeholder = !empty($field->placeholder) ? $field->placeholder : '' ;
                    foreach( self::_get_file_inputs($form_ID) as $file_input_name => $file_input_params ):
                        $required = $file_input_params['required'] == true ? 'required="required"' : '';

                        $html .= '<input type="hidden" name="' . $file_input_name . '--exif-orientation" class="exif-orientation-input">';
                        $html .= '<div class="fv-file-wrapper">';
                        $html .= '<input type="file" name="' . $file_input_name . '" class="file-input" ' . $required . ' accept="image/*">' . "\n";
                        if ( isset($file_input_params['photo_name_input']) && $file_input_params['photo_name_input'] == true ) {
                            $html .= '<input type="text" placeholder="'. $placeholder .'" name="'. $file_input_name .'-name" class="form-control form-control-short foto-async-name" ' . $required . '>' . "\n";
                        }
                        $html .= '</div>';
                    endforeach;
                } else {
                    $html = apply_filters('fv/public/upload_form/custom_file_input', $html, $field, $contest, $c);
                }
                break;
        }

        if ( !empty($field->field_options->description) ) {
            switch ($field->field_type) {

                case 'checkbox_multi':
                case 'radio':
                case 'select_multi':
                    $html .= '<span class="description">' . wp_kses_post(stripslashes($field->field_options->description)) . '</span>';
                    break;
                /*case 'file':
                    break;*/
                default:
                    $html .= '<span class="description">' . wp_kses_post(stripslashes($field->field_options->description)). '</span>' . "\n";
                    break;
            }

        }

        return apply_filters('fv/public/upload_form/field_html', $html, $field, $contest, $form_ID);
    }

    /**
     * Return file inputs array for Generate form and Saving data from form
     *
     * @param mixed     $field
     * @param int    $form_ID
     * @return array ['name (string)'=>'required (string)']
     */
    public static function _get_file_inputs($form_ID, $field=false) {
        if ( $field === false ) {
            $field = self::_get_file_field_from_form_structure($form_ID);
        }
        $inputs = array('foto-async-upload' => array('required'=>true, 'photo_name_input'=>!empty($field->field_options->multi_show_photo_name)) );
        if ( !empty($field->field_options->multi_upload) && isset($field->field_options->multi_count) && $field->field_options->multi_count > 1 ) {
            for ($N = 2; $field->field_options->multi_count >= $N; $N++) :
                $inputs['foto-async-upload-' . $N] = array( 'required'=>false, 'photo_name_input'=>!empty($field->field_options->multi_show_photo_name) );
            endfor;
        }
        return apply_filters('fv/form/get_file_inputs', $inputs, $field, $form_ID);
    }

    /**
     * Return file field from Form structure object
     *
     * @param mixed     $form_structure
     * @param int       $form_ID
     * @return object
     */
    public static function _get_file_field_from_form_structure($form_ID, $form_structure=false) {
        if ( $form_structure === false ) {
            $form_structure = Fv_Form_Helper::get_form_structure_obj($form_ID);
        }
        foreach($form_structure as $field) {
            if ( $field->field_type == 'file' ) {
                return $field;
            }
        }
    }
}

add_filter('fv/public/upload_form/label', 'fv_filter_public_upload_form_label', 10, 4);

function fv_filter_public_upload_form_label ($label, $field, $c, $contest) {
    $rq = '';
    if ( $field->required ) {
        $rq = ' <span class="red_star">*</span>';
    }
    return '<div class="number">' . $c . '</div>' . $label . $rq;
}

<?php

class FV_Public_Form {
    /**
     * Class instance.
     *
     * @since 2.2.405
     *
     * @var object
     */
    protected static $instance;

    /**
     * Shortcode :: Show upload form
     * @since 2.2.06
     *
     * @param array             $atts
     * @param bool|FV_Contest   $contestObj
     *
     * @return string
     */
    public function shortcode_upload_form($atts , $contestObj = false)
    {
        $output = '';

        $atts = wp_parse_args($atts, array(
            'upload_theme' => '',
            'show_opened' => false,
            'tabbed'      => false,
        ));

        if ( isset($atts['contest_id']) || is_object($contestObj) ) {

            //ob_start();
            global $post, $contest_id;

            if ( $contestObj == false ) {
                $contest = ModelContest::query()->findByPK((int)$atts['contest_id'], true);
            } else {
                $contest = $contestObj;
                $atts['contest_id'] = $contest->id;
            }

            if ( empty($contest) ) {
                return "Invalid contest ID!";
            }
            global $contest_id, $contest_ids;        // TODO - remove later $contest_id
            $contest_id = (int)$contest->id;          // TODO - remove later
            $contest_ids[] = (int)$contest->id;

            $public_translated_messages = fv_get_public_translation_messages();

            if ( (!empty($atts['apply_upload_dates']) && $atts['apply_upload_dates'] != 'false' && !$contest->isUploadDatesActive() ) || $contest->isFinished() )  {

                if ( !empty($atts['upload_not_active_msg']) )  {
                    return wp_kses($atts['upload_not_active_msg'],
                        array(
                            'a' => array(
                                'href' => true,
                                'rel' => true,
                                'rev' => true,
                                'name' => true,
                                'target' => true,
                            ),
                            'span' => array(),
                            'strong' => array(),
                        )
                    );
                } else {
                    return $public_translated_messages['upload_form_contest_finished'];
                }

            }
            
            // == Allow disable form output via Filter ===============
            $upload_form_show_filter = apply_filters('fv/upload-form/show-filter', false, $contest, $atts);
            if ( $upload_form_show_filter ) {
                return $upload_form_show_filter;
            }
            // ================== END ================================

            if ( strlen($atts['upload_theme']) > 2 )  {
                $contest->upload_theme = sanitize_title($atts['upload_theme']);
            }

            $this->_assets($public_translated_messages);

            $template_data = array();
            $template_data["only_form"] = true;
            $template_data["contest"] = $contest;
            $template_data["post"] = $post;

            $template_data["show_opened"] = (!$atts['show_opened'] || $atts['show_opened'] === 'false' || $atts['show_opened'] === 'no' || $atts['show_opened'] === '0') ? false : true;

            $template_data["tabbed"] = ($atts['tabbed'] == 'true') ? true : false;
            $template_data["public_translated_messages"] = $public_translated_messages;

            $output = FV_Templater::render( FV_Templater::locate("", 'upload.php', ''), $template_data, true, "upload_form" );
            //include plugin_dir_path(__FILE__) . '/themes/upload.php';
            do_action('fv/load_upload_form/' . $contest->upload_theme, $contest);

            do_action('fv/public/show_upload_form/after');

            FV_Public_Assets::$need_load__custom_js_upload = true;
        }

        return $output;
    }

    public function _assets($public_translated_messages) {
        wp_enqueue_style('fv_main_css');
        //wp_enqueue_style('fv_font_css', fv_css_url(FV::$ASSETS_URL . '/icommon/fv_fonts.css'), false, FV::VERSION, 'all');

        wp_enqueue_script('fv_lib_js');
        wp_enqueue_script('fv_modal' );
        FV_Public_Assets::$need_load_modal_html = true;
        wp_enqueue_script('fv_upload_js' );

        $ajax_url = admin_url('admin-ajax.php');

        // ADD WPML lang constant
        if ( defined("ICL_LANGUAGE_CODE") ) {
            $ajax_url = add_query_arg( 'lang', ICL_LANGUAGE_CODE ,$ajax_url );
        }

        $output_data = array();
        $output_data['ajax_url'] = $ajax_url;
        $output_data['limit_dimensions'] = fv_setting('upload-limit-dimensions', 'no');
        $output_data['limit_val'] = fv_setting('upl-limit-dimensions', array());
        $output_data['plugin_url'] = plugins_url('wp-foto-vote');
        $output_data['lang']['upload_form_invalid'] = $public_translated_messages['upload_form_invalid'];
        $output_data['lang']['download_invaild_email'] = $public_translated_messages['download_invaild_email'];
        $output_data['lang']['dimensions_err'] = $public_translated_messages['upload_dimensions_err'];
        $output_data['lang']['dimensions_smaller'] = $public_translated_messages['upload_dimensions_smaller'];
        $output_data['lang']['dimensions_bigger'] = $public_translated_messages['upload_dimensions_bigger'];
        $output_data['lang']['dimensions_height'] = $public_translated_messages['upload_dimensions_height'];
        $output_data['lang']['dimensions_width'] = $public_translated_messages['upload_dimensions_width'];
        $output_data['lang']['upload_success_title'] = $public_translated_messages['upload_success_title'];
        $output_data['lang']['upload_error_title'] = $public_translated_messages['upload_error_title'];

        // out data to script
        wp_localize_script( 'fv_upload_js', 'fv_upload', apply_filters('fv/public/show_upload_form/js_data', $output_data) );
    }

    /**
     * @since 2.2.405
     */
    public static function instance()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Form();

        return self::$instance;
    }
}
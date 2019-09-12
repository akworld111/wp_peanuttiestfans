<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The public-facing AJAX (mostly upload) functionality of the plugin.
 *
 * @since      2.2.073
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public_Ajax {

    /**
     * Ajax :: Upload photo to contest
     *
     * @param object $contest
     *
     * @return void
     * @output json_array
     */
    public static function upload_photo($contest = NULL)
    {

        if ( empty($_POST) || empty($_POST['contest_id']) ) {
            return false;
        }
        global $post;

        if (empty($contest)) {
            $post_id = (int)$_POST['post_id'];
            $contest_id = (int)$_POST['contest_id'];
            $contest = ModelContest::query()->findByPK($contest_id, true);
        } else {
            $post_id = $post->ID;
        }
        $form_ID = !empty( $contest->form_id ) ? $contest->form_id : ModelForms::q()->getDefaultFormID();
        $public_translated_messages = fv_get_public_translation_messages();
        $fields_structure = Fv_Form_Helper::get_form_structure($form_ID);

        try {
            if ($contest->max_uploads_per_user == NULL) {
                $contest->max_uploads_per_user = 5;
            }
            $max_uploads_per_user = apply_filters('fv/public/upload/max_uploads_per_user', $contest->max_uploads_per_user, $contest);

            $limit_exceeded = false;

            if ( isset($_POST['go-upload']) ) {

                if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
                    fv_log('upload_photo debug:: go-upload', $_POST, __FILE__, __LINE__);
                }

                $err = array();

                // Check is at least first file is passed and any Filters not applied (like for Video contest)
                if ( !apply_filters('fv/public/can_upload', false, $contest) && $_FILES['foto-async-upload']['size'] == 0 ) {
                    $err['custom_upload_error'] = $public_translated_messages['download_no_image'];
                }

                if ( empty($err) ) {
                    $err = self::_verify_limit_dimensions($_FILES["foto-async-upload"]);
                }

                if ( empty($err) ) {
                    $err = apply_filters('fv/public/pre_upload', $err, $contest, $form_ID);

//                    var_dump($err);
                }

                IF ( empty($err) && ! $contest->isUserHaveEnoughPermissionsForUpload() ):
                    $err = array();
                    $err['custom_upload_error'] = $public_translated_messages['download_error'];
                ENDIF;

                // if first file not empty
                IF ( empty($err) ):
                    $err = array();
                    //      check photo size
                    $max_size = (int) $contest->getImageSizeLimit();
                    if ( !empty($_FILES) && $max_size > 0 && $_FILES["foto-async-upload"]["size"] > $max_size * 1024 ) {
                        $max_size_text = size_format(  $max_size * 1024 );
                        if ( $public_translated_messages["download_limit_size"] ) {
                            $err['custom_upload_error'] = str_replace("%LIMIT_SIZE%", $max_size_text, $public_translated_messages["download_limit_size"] );
                            // Add File name
                            $err['custom_upload_error'] = str_replace("%FILE_NAME%", sanitize_file_name($_FILES["foto-async-upload"]["name"]), $err['custom_upload_error'] );
                        }else {
                            $err['custom_upload_error'] = 'Image size must be smaller than ' . $max_size_text . '!';
                        }
                    }
                    unset($max_size);
                    
                    if ( $contest->max_uploads_per_user ) {
                        // Checks user ip
                        if (get_option('fotov-upload-limit-ip', false)) {
                            $ip = fv_get_user_ip();
                            $uploadedByIp = ModelCompetitors::query()->where_all(array('contest_id' => $contest_id, 'user_ip' => $ip))->find(true);
                            if ($uploadedByIp >= $contest->max_uploads_per_user) {
                                $err['custom_upload_error'] = $public_translated_messages["download_limit"];
                                $limit_exceeded = true;
                            }
                        }

                        // Checks user id
                        if (get_option('fotov-upload-limit-userid', false) && get_current_user_id() > 0) {
                            $uploadedById = ModelCompetitors::query()->where_all(array('contest_id' => $contest_id, 'user_id' => get_current_user_id()))->find(true);
                            if ($uploadedById >= $contest->max_uploads_per_user) {
                                $err['custom_upload_error'] = $public_translated_messages["download_limit"];
                                $limit_exceeded = true;
                            }
                        }
                    }

                    //==================================
                    // IF form contains any data fields
                    if ( !empty($_POST['form']) ) {
                        // GET photo data from $_POST
                        $new_photo = Fv_Form_Helper::_get_photo_data_from_POST($_POST['form'], Fv_Form_Helper::get_form_structure_obj($form_ID));
                    } else {
                        $new_photo = array();
                    }
                    //==================================

                    // Checks entered email
                    if ( $contest->max_uploads_per_user && get_option('fotov-upload-limit-email', true) && !empty($new_photo['user_email']) ) {
                        //if email vaild find in bd
                        if ( is_email( $new_photo['user_email'] ) ) {
                            $uploadedByEmail = ModelCompetitors::query()->where_all( array('contest_id' => $contest_id, 'user_email' => $new_photo['user_email']) )->find(true);
                            if ( $uploadedByEmail >= $contest->max_uploads_per_user ) {
                                $err['custom_upload_error'] = $public_translated_messages["download_limit"];
                                $limit_exceeded = true;
                            }
                            // else shows error
                        } else {
                            $err['custom_upload_error'] = $public_translated_messages["download_invaild_email"];
                            $limit_exceeded = true;
                        }
                    }

                ENDIF;  // END Checking is empty $err

                $inserted_photo_id = false;
                $inserted_photo_ids = array();
                $inserted_attach_ids = array();

                if (!$limit_exceeded && !isset($err['custom_upload_error'])) {

                    if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
                        fv_log('upload_photo debug:: let`s upload (no errors and limits ok)');
                        fv_log('upload_photo debug:: HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
                    }

                    // scale image size
                    if (  get_option('fotov-upload-photo-resize', false)  ) {
                        add_action('wp_handle_upload', 'fv_upload_resize'); // apply our modifications
                    }

                    $new_photo = array_merge($new_photo,
                        array(
                            //'additional' => ( isset($upload_info['comment']) ) ? $upload_info['comment'] : "",
                            'contest_id' => $contest->id,
                            'votes_count' => 0,
                            'user_id' => get_current_user_id(),
                            'user_ip' => fv_get_user_ip(),
                            'added_date' => current_time('timestamp', 0),
                        )
                    );

                    // статус - на модерации / опубликован
                    if ( $contest->moderation_type == "after" ) {
                        $new_photo['status'] = ST_PUBLISHED;
                    } else {
                        $new_photo['status'] = ST_MODERATION;
                    }

                    // log
                    //FvLogger::addLog('$new_photo', $new_photo);

                    //FvFunctions::getPhotoThumbnail($unit, 'full');
                    //$image_min = wp_get_attachment_image_src($image_id, get_option('fotov-image-size', 'thumbnail'));

                    $notify_sent = false;
                    // save $_FILES, because function `media_handle_upload` reset array

                    //* Check, if exists custom upload functions, else run Default
                    if ( apply_filters( 'fv/public/custom_upload/uses', false, $contest ) === false ) {
                        require_once(ABSPATH . 'wp-admin/includes/admin.php');

                        if ( fv_setting('upload-custom-folder', false) ) {
                            // Change Upload dir
                            add_filter('upload_dir', array('FV_Public_Ajax', 'filter_upload_dir'));
                        }
                        // Get all File inputs (NEED for support Multiply File inputs)
                        FOREACH (Fv_Form_Helper::_get_file_inputs($form_ID) as $INPUT_NAME => $INPUT_params) :

                            if ( !isset($_FILES[$INPUT_NAME]) || empty($_FILES[$INPUT_NAME]['name']) || $_FILES[$INPUT_NAME]['size'] == 0 ) {
                                continue;
                            }
                            do_action('fv/public/before_upload', $INPUT_NAME);
                            if ( fv_setting('orientation-fix') ) {
                                FV_Public_Upload_Orientation_Fix::instance()->before_upload($INPUT_NAME);
                            }

                            FV_Public_Upload::add_compress_jpeg_quality_filter();

                            $new_photo_data = $new_photo;

                            // Run upload & *allow just images upload*
                            $image_id = media_handle_upload(
                                $INPUT_NAME,
                                $post_id,
                                array(),
                                apply_filters( 'fv/public/upload/media_handle_upload_overrides',
                                    array(
                                        'test_form'=>false,
                                        'test_type'=>true,
                                        'mimes'=> array(
                                            'jpg|jpeg|jpe' => 'image/jpeg',
                                            'gif' => 'image/gif',
                                            'png' => 'image/png',
                                        )
                                ), $new_photo_data, $contest)
                            ); //post id of Client Files page

                            FV_Public_Upload::remove_compress_jpeg_quality_filter();
                            if ( fv_setting('orientation-fix') ) {
                                FV_Public_Upload_Orientation_Fix::instance()->after_upload();
                            }

                            do_action('fv/public/after_upload', $INPUT_NAME);

                            if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
                                fv_log('upload_photo debug :: photo uploaded, IMAGE_ID > ', $image_id, __FILE__, __LINE__);
                            }

                            //var_dump($image_id);
                            //var_dump(is_wp_error($image_id));

                            if ( is_wp_error($image_id) ) {

                                FvLogger::addLog('image_id is is_wp_error ', $image_id->get_error_message(), __FILE__, __LINE__);
                                $err['upload_error'] = $image_id;
                                $image_id = false;

                            } else if ( !$image_id ) {

                                FvLogger::addLog('image upload error ', $image_id, __FILE__, __LINE__);
                                $err['upload_error'] = $image_id;
                                $image_id = 0;

                            } else {
                                $attachment_image_src = wp_get_attachment_image_src($image_id, 'full');
                                if ( empty($attachment_image_src) ) {
                                    fv_log("Upload error :: wp_get_attachment_image_src returns empty URL", array('image_id'=>$image_id, $attachment_image_src));
                                    $err['custom_upload_error'] = $public_translated_messages['download_error'];
                                    continue;
                                }
                                $new_photo_data['url'] = $attachment_image_src[0];
                                $new_photo_data['image_id'] = $image_id;

                                $inserted_attach_ids[] = array($image_id,$new_photo_data['url']);

                                // if enables showing Photo name around each File input
                                if ( !empty($INPUT_params['photo_name_input']) && !empty($_POST[$INPUT_NAME.'-name']) )
                                {
                                    $new_photo_data['name'] = sanitize_text_field($_POST[$INPUT_NAME.'-name']);
                                }

                                $inserted_photo_id = self::_upload_add_photo_to_db($new_photo_data, $INPUT_NAME);
                                $inserted_photo_ids[] = $inserted_photo_id;
                            }
                        ENDFOREACH;

                        if ( fv_setting('upload-custom-folder', false) ) {
                            remove_filter('upload_dir', array('FV_Public_Ajax', 'filter_upload_dir'));
                        }

                    } else {
                        $custom_upload_result = apply_filters('fv/public/custom_upload/run', array(), $new_photo, $contest);

                        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
                            fv_log('upload_photo debug :: photo uploaded (custom action), $custom_upload_result > ', $custom_upload_result, __FILE__, __LINE__);
                        }

                        //FvLogger::addLog('$custom_upload_result', $custom_upload_result);
                        if ( isset($custom_upload_result['custom_upload_error']) ) {
                            $err['custom_upload_error'] = $custom_upload_result['custom_upload_error'];
                            FvLogger::addLog('custom_upload_error ', $custom_upload_result['custom_upload_error'], __FILE__, __LINE__);
                        } elseif( isset($custom_upload_result['new_photo']) ) {
                            $new_photo = $custom_upload_result['new_photo'];

                            $inserted_photo_id = self::_upload_add_photo_to_db($new_photo, 'video');
                            $inserted_photo_ids[] = $inserted_photo_id;
                        } else {
                            fv_log("Unknown upload error :: filter 'fv/public/custom_upload/run' is used but result not contain 'custom_upload_error' or 'new_photo'", $custom_upload_result);
                            $err['custom_upload_error'] = 'Unknown upload error!';
                        }
                    }

                    //** IF there are no problems
                    IF ( empty($err) ):
                        $public_translated_messages = apply_filters('fv/public/upload_after_save', $public_translated_messages, $new_photo, $inserted_photo_id, $inserted_photo_ids);

                        // Sent Notify Messages to Admin and User
                        if ( !$notify_sent ) {
                            self::_upload_sent_notify($contest, $new_photo, $inserted_photo_id, $post_id, $public_translated_messages);
                            $notify_sent = true;
                        }
                    ENDIF;
                    // reset
                    $_FILES = array();
                } else {
                    $err['uploaded'] = 1;
                }

                $status = "error";

                if ( isset($err['custom_upload_error']) ) {
                    if ( is_array($err['custom_upload_error']) ) {
                        $message = implode("; ", $err['custom_upload_error']);
                    } else {
                        $message = $err['custom_upload_error'];
                    }
                } elseif ( isset($err['upload_error']) ) {
                    $err_text = '';
                    if ( is_wp_error($err['upload_error']) ) {
                        $err_text = $err['upload_error']->get_error_message();
                    } elseif ( $err['upload_error'] ) {
                        $err_text = $err['upload_error'];
                    }
                    $message = $public_translated_messages['download_error'] . ' ' . $err_text;
                } elseif (isset($err['uploaded'])) {
                    $status = "info";
                    $message = $public_translated_messages['download_limit'];
                } else {
                    $status = "success";
                    if ( $contest->moderation_type == "after" ) {
                        $message = $public_translated_messages['download_ok'];
                    } else {
                        $message = $public_translated_messages['download_moderation'];
                    }

                }

                if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
                    fv_log('upload_photo debug:: status > ' . $status, $message, __FILE__, __LINE__);
                }

                /*
                 * Allow add hook to upload finish action
                 * @since 2.2.363
                 * $status can be => "error" / "success" / "info"
                 */
                do_action('fv/public/upload/ready', $status, $inserted_photo_ids, $contest);

                $contest_js = clone $contest;

                if (defined('DOING_AJAX') && DOING_AJAX) {
                    /* it's an AJAX call */
                    fv_AJAX_response($status == "success", '', apply_filters('fv/public/upload/response',array(
                        'message'           => $message,
                        'status'            => $status,
                        'inserted_photo_id' => $inserted_photo_id,
                        'inserted_photo_ids'=> $inserted_photo_ids,
                        'attach_ids'        => $inserted_attach_ids,
                        'contest_id'        => $contest->id,
                        'contest'           => FV_Public_Assets::_prepare_contest_to_js($contest_js),
                    ), $contest));
                } else {
                    echo $message;
                }
            }

            // END UPLOAD
        } catch (Exception $e) {
            FvLogger::addLog('image upload error ', $e->getMessage());
            /* it's an AJAX call */
            fv_AJAX_response(false, '', apply_filters('fv/public/upload/response',array(
                'message'           => $public_translated_messages['download_error'],
                'status'            => 'error',
                'inserted_photo_id' => 0,
                'inserted_photo_ids'=> array(),
                'attach_ids'        => array(),
                'contest_id'        => $contest->id
            ), $contest));
        }
    }

    /**
     * Helper function, that adds new record to Database
     *
     * @param array
     * @param string
     *
     * @return int
     */
    private static function _upload_add_photo_to_db($photo_data_array, $INPUT_NAME)
    {

        /**
         * Lets crop fields, if longer that needed
         */
        // TODO - may be move to ModelContestants ?
        $length_limit =  apply_filters('fv/public/save_photo/length_limits', array(
            'name'              =>  255,
            'description'       =>  500,
            'full_description'  =>  ModelCompetitors::getFullDescriptionSize(),
            'social_description'=>  150,
        ));
        foreach ( $length_limit as $length_limit_key => $length_limit_size ):
            if ( !empty($photo_data_array[$length_limit_key]) && mb_strlen($photo_data_array[$length_limit_key]) > $length_limit_size ) {
                $photo_data_array[$length_limit_key] = mb_substr($photo_data_array[$length_limit_key], 0, $length_limit_size);
            }
        endforeach;

        $photo_data_array = apply_filters('fv/public/upload_before_save', $photo_data_array, $INPUT_NAME);

        if ( isset($photo_data_array['options']) && is_array($photo_data_array['options']) ) {
            $photo_data_array['options'] = maybe_serialize($photo_data_array['options']);
        }
        if ( !empty($photo_data_array['meta']) ) {
            $meta_arr = $photo_data_array['meta'];
            unset($photo_data_array['meta']);
        }

        if ( !empty($photo_data_array['categories']) ) {
            $categories_arr = $photo_data_array['categories'];
            unset($photo_data_array['categories']);
        }

        if ( $photo_data_array['image_id'] ) {
            $att_mime_type = get_post_mime_type($photo_data_array['image_id']);
            $photo_data_array['mime_type'] = $att_mime_type ? $att_mime_type : '';
        }


        $insert_res = ModelCompetitors::query()->insert($photo_data_array);

        if ( isset($meta_arr) && $insert_res != 0 ) {
            foreach ($meta_arr as $meta_key => $meta_val) {
                ModelMeta::q()->insert(
                    array(
                        'contest_id'    => $photo_data_array['contest_id'],
                        'contestant_id' => (int)$insert_res,
                        'meta_key'      => $meta_key,
                        'value'         => $meta_val,
                        'custom'        => 1,
                    )
                );                
            }
        }

        if ( isset($categories_arr) && $insert_res ) {
            $competitor = fv_get_competitor( $insert_res );
            $competitor->setCategories( $categories_arr );
        }

        /**
         * do_action 'fv/public/upload_after_insert'
         * @param int       $insert_res     Competitor ID
         * @param string    $INPUT_NAME
         */
        do_action('fv/public/upload_after_insert', $insert_res, $INPUT_NAME);

        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
            fv_log('_upload_add_photo_to_db', $photo_data_array, __FILE__, __LINE__);
            fv_log('_upload_add_photo_to_db INSERT result', $insert_res);
        }

        if ( !$insert_res ) {
            fv_log('_upload_add_photo_to_db :: something wrong, result is 0!', $photo_data_array, __FILE__, __LINE__);
        }

        return $insert_res;
    }

    /**
     * @param object $contest
     * @param array $new_photo_data
     * @param int $inserted_photo_id
     * @param int $post_id
     * @param array $public_translated_messages
     *
     * @return void
     */
    private static function _upload_sent_notify($contest, $new_photo_data, $inserted_photo_id, $post_id, $public_translated_messages)
    {
        $photoObj = ModelCompetitors::query()->findByPK( $inserted_photo_id );
        
        // If EMPTY $photoObj - let's write to log and send Email to admin
        if ( !$photoObj ) {
            $error_msg = '_upload_sent_notify :: something wrong, $photoObj is empty [id: ' . $inserted_photo_id .']!';
            fv_log($error_msg, array("photoObj from DB"=>$photoObj, "Photo data" => $new_photo_data), __FILE__, __LINE__);
            FV_Mailer::toUser( FV_Notifier::_getAdminEmail(), "WP Foto Vote :: Error with photo upload", $error_msg . ' Go to Photo Contest => Debug for find more details.' );
            return;
        }

        // Admin upload Notify
        FV_Notifier::sendCompetitorNotificationToAdmin( 'fv/competitor/to-admin/uploaded', $photoObj );
        // User upload Notify
        FV_Notifier::sendCompetitorNotificationToUser( 'fv/competitor/to-user/uploaded', $photoObj );
    }


    /**
     * Change WP upload dir to custom
     * @param array $path_data
     *
     * @return array $path_data
     */
    public static function filter_upload_dir($path_data)
    {
        if (!empty($path_data['error'])) {
            return $path_data; //error or uploading
        }

        //remove default subdir (year/month)
        $path_data['path'] = str_replace($path_data['subdir'], '', $path_data['path']);
        $path_data['url'] = str_replace($path_data['subdir'], '', $path_data['url']);

        $path_data['subdir'] = '/fv-contest';
        $path_data['path'] .= '/fv-contest';
        $path_data['url'] .= '/fv-contest';
        if ( isset($_REQUEST['contest_id']) ) {
            $contest_id = (int)$_REQUEST['contest_id'];
            $path_data['subdir'] .= '/c' . $contest_id;
            $path_data['path'] .= '/c' . $contest_id;
            $path_data['url'] .= '/c' . $contest_id;
        }

        return $path_data;
    }

// END FUNCTION @upload_photo@

    public static function ajax_get_votes_counts() {
        // TODO :: FIX if hide votes enabled
        if ( !isset($_POST['ids']) ||  !is_array($_POST['ids']) ) {
            fv_AJAX_response(true, false, array('votes'=>array()));
            //fv_AJAX_response(false, 'Refresh cached votes :: Invalid params');
        }

        $allVotes = array();
        /** @var FV_Contest[] $contests */
        $contests = array();

        $ids = array_map($_POST['ids'], 'absint');

        $competitors = ModelCompetitors::query()
            ->what_fields( array("`t`.`id`", "`t`.`votes_count`", "`t`.`votes_average`", "`t`.`contest_id`") )
            ->where_in('id',$_POST['ids'])
            ->find();
        foreach($competitors as $competitor) {
            if ( !isset($contests[$competitor->contest_id]) ) {
                $contests[$competitor->contest_id] = $competitor->getContest(true);
            }
            if ( $contests[$competitor->contest_id]->isNeedHideVotes() ) {
                $allVotes[$competitor->id] = 0;
            } else {
                $allVotes[$competitor->id] = $competitor->getVotes($contests[$competitor->contest_id]);
            }
        }

        fv_AJAX_response(true, false, array('votes'=>$allVotes));
    }

    public static function ajax_go_to_page () {
        if ( !defined('WP_CACHE') || WP_CACHE == FALSE ) {
            // not allow direct actions
            check_ajax_referer('fv-ajax', 'some_str');
        }

        if ( fv_setting('pagination-type', 'default') == 'default' ) {
            die(fv_json_encode( array('result'=>'fail', 'mgs'=>'ajax pagination disabled!') ));
        }

        if ( isset($_GET['contest_id']) ) {
            $contest_id = (int)$_GET['contest_id'];
        } else {
            die(fv_json_encode( array('result'=>'fail', 'mgs'=>'wrong contest_id!') ));
        }
        $post_id= (int)$_GET['post_id'];
        //$paged = ( isset($_GET['fv-page']) ) ? (int)$_GET['fv-page'] : 1;
        $theme = ( !empty($_GET['theme']) ) ? sanitize_title($_GET['theme']) : '';

        $plugin_public = new FV_Public(FV::NAME, FV::VERSION);
        global $photos;
        add_filter( 'fv_shows_get_comp_items', array('FV_Public_Ajax','hook_fv_shows_get_comp_items'), 100, 1 );
        ob_start();
            $plugin_public->show_contest(array('id'=>$contest_id, 'theme' => $theme), true);
        $photos_list_html =str_replace( array("\r\n", "\n", "\r", '  ', '    ', '         ','      '),"", ob_get_clean() );

        if ( is_array($photos) ) {
            foreach($photos as $k => $photo) {
                $photos[$k] = FV_Public_Assets::_prepare_contestant_to_js($photo);
            }
        }

        die(fv_json_encode(
            array(
                'result'        =>  'ok',
                'html'          =>  defined('FV_USE_UTF8_ENCODE_FOR_PAGINATION') ? utf8_encode($photos_list_html) : $photos_list_html,
                'photos_data'   =>  $photos,
                'contest_id'    =>  $contest_id,
                'single_link_template'=>  fv_single_photo_link( 999, true, $contest_id ),
            )
        ));

    }

    public static function hook_fv_shows_get_comp_items ($photos_arr) {
        global $photos;
        $photos = $photos_arr;
        return $photos_arr;
    }

    /**
     * Validate Image dimensions
     *
     * @param $file_params
     * @return array
     */
    public static function _verify_limit_dimensions ( $file_params ) {

        // SKip If not image Mime
        if ( FALSE === strpos($file_params['type'], 'image/') ) {
            return array();
        }

        try {

            $limit_dimensions = fv_setting('upload-limit-dimensions', 'no');
            if ('no' == $limit_dimensions) {
                return array();
            }
            $limit_val = fv_setting('upl-limit-dimensions', array());

            $imgDimensions = getimagesize($file_params['tmp_name']);
            if (empty($imgDimensions)) {
                fv_log('Upload limit_dimensions - invalid $imgDimensions!', $imgDimensions);
                return array('custom_upload_error' => 'Error: can\'t validate image dimensions (:');
            }
            $imgDimensions['width'] = $imgDimensions[0];
            $imgDimensions['height'] = $imgDimensions[1];
            $upload_fail_msg = '';

            $public_translated_messages = fv_get_public_translation_messages();

            if ($limit_dimensions == 'proportion') {
                if ($limit_val["p-height"] > 0 && $limit_val["p-width"] > 0) {

                    // image is loaded; sizes are available
                    $req_proportion = $limit_val["p-height"] / $limit_val["p-width"];
                    $proportion = $imgDimensions['height'] / $imgDimensions['width'];

                    if (fmod($req_proportion, $proportion) > $req_proportion * 0.02) {
                        $upload_fail_msg =
                            $limit_val["p-height"]
                            . ' : '
                            . round($proportion * $limit_val["p-height"] * 10) / 10;
                    }
                } else {
                    fv_log('Upload limit_dimensions - no proportions!', $imgDimensions);
                }
            } else if ($limit_dimensions == 'size') {
                // if Width smaller than size
                $PARAM = '';
                $SIZE = '';
                if ($limit_val["s-min-width"] > 0 && $imgDimensions['width'] < $limit_val["s-min-width"]) {
                    $upload_fail_msg = $public_translated_messages['upload_dimensions_bigger'];
                    $PARAM = $public_translated_messages['upload_dimensions_width'];
                    $SIZE = $limit_val["s-min-width"] . 'px.';
                    // if Width bigger than size
                } else if ($limit_val["s-max-width"] > 0 && $imgDimensions['width'] > $limit_val["s-max-width"]) {
                    $upload_fail_msg = $public_translated_messages['upload_dimensions_smaller'];
                    $PARAM = $public_translated_messages['upload_dimensions_width'];
                    $SIZE = $limit_val["s-max-width"] . 'px.';
                    // if Height bigger than size
                } else if ($limit_val["s-min-height"] > 0 && $imgDimensions['height'] < $limit_val["s-min-height"]) {
                    $upload_fail_msg = $public_translated_messages['upload_dimensions_bigger'];
                    $PARAM = $public_translated_messages['upload_dimensions_height'];
                    $SIZE = $limit_val["s-min-height"] . 'px.';
                    // if Height bigger than size
                } else if ($limit_val["s-max-height"] > 0 && $imgDimensions['height'] > $limit_val["s-max-height"]) {
                    $upload_fail_msg = $public_translated_messages['upload_dimensions_smaller'];
                    $PARAM = $public_translated_messages['upload_dimensions_height'];
                    $SIZE = $limit_val["s-max-height"] . 'px.';
                }

                $upload_fail_msg = str_replace(array("%PARAM%", "%SIZE%"), array($PARAM, $SIZE), $upload_fail_msg);
            }

            if ($upload_fail_msg) {
                return array('custom_upload_error' => str_replace('%INFO%', $upload_fail_msg, $public_translated_messages['upload_dimensions_err']));
            }

        } catch(Exception $ex) {
            fv_log( "_verify_limit_dimensions error :: ", $ex->getMessage(), __FILE__, __LINE__ );
            return array('custom_upload_error' => 'Error happen during verify dimensions!');
        }
        
        return array();
    }
}

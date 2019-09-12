<?php
/*
 * additional functions
 * wp-foto-vote
 */

defined('ABSPATH') or die("No script kiddies please!");

if( ! function_exists("mb_strlen") ){
	function mb_strlen($string, $encoding) {
	    //then one solution for that i.e:
		return strlen($string);
	}
}

if( ! function_exists("json_last_error") ){
	function json_last_error() {
	//then one solution for that i.e:
		return JSON_ERROR_NONE;
	}
}
if ( !defined ('JSON_ERROR_NONE') ) {
	define('JSON_ERROR_NONE', 99);
}

/**
 * @param integer $contest_ID
 * @param bool $cached true
 *
 * @param bool $throwError false - #since 2.2.801
 *
 * @return FV_Contest|null
 *
 * @throws Exception
 * @since 2.2.800
 */
function fv_get_contest( $contest_ID, $cached = true, $throwError = false ) {
    $contest = ModelContest::q()->findByPK( absint($contest_ID), $cached );

    if ( is_wp_error($contest) ) {
        fv_log( "Error!! No contest found with ID: ", $contest_ID );
        if ( $throwError ) {
            throw new Exception("Error!! No contest found with ID: " . $contest_ID . ' # ' . $contest->get_error_message());
        }
    }
    if ( !$contest ) {
        fv_log( "Error!! No contest found with ID: ", $contest_ID );
        if ( $throwError ) {
            throw new Exception("Error!! No contest found with ID: " . $contest_ID);
        }
    }

    return $contest;
}

/**
 * @param integer   $competitor_ID
 * @param bool      $cached true
 *
 * @param bool $throwError false - #since 2.2.801
 *
 * @return FV_Competitor|null
 * 
 * @throws Exception
 *
 * @since 2.2.511
 */
function fv_get_competitor( $competitor_ID, $cached = true, $throwError = false ) {
    $competitor = ModelCompetitors::q()->findByPK( absint($competitor_ID), $cached );

    if ( is_wp_error($competitor) ) {
        fv_log( "Error!! No competitor found with ID: ", $competitor_ID . ' # ' . $competitor->get_error_message() );
        if ( $throwError ) {
            throw new Exception("Error!! No competitor found with ID: " . $competitor_ID . ' # ' . $competitor->get_error_message());
        }
    }
    if ( !$competitor ) {
        fv_log( "Error!! No competitor found with ID: ", $competitor_ID );
        if ( $throwError ) {
            throw new Exception("Error!! No competitor found with ID: " . $competitor_ID);
        }
    }

    return $competitor;
}

/**
 * Dump variable
 *
 * @param $var
 *
 * @return void
 */
function fv_dump($var) {
    //return;
    if ( current_user_can('manage_options') ) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

/**
 * Dump variable
 *
 * @return void
 *
 * @since 2.2.806
 */
function fv_dump_last_query() {
    echo '<pre>';
    global  $wpdb;
    var_dump( $wpdb->last_query );
    var_dump( $wpdb->last_result );
    echo '</pre>';
}

/**
 * Check if sting $rgba contains "rgb" or "hsv" else return $default
 *
 * @param string $rgba
 * @param string $default
 *
 * @return string
 */

function fv_get_if_looks_rgba ($rgba, $default) {
    if ( empty($rgba) || strpos($rgba, 'rgb') === false ) {
        // looks like wrong
        return $default;
    }
    // looks like correct
    return $rgba;
}


/**
 * Render template
 * @param string    $path       path to file
 * @param array     $variables  variables to pass
 * @return string|void
 *
 * @since 2.2.400
 */
function fv_render_tpl($path, $variables = array(), $return = false) {
    extract($variables);

    if ( $return ) {
        ob_start();
    }
    require $path;

    if ( $return ) {
        return ob_get_clean();
    }
}



//------------------------------------------------------------------------------


function fv_get_sotring_types_arr( $for_admin = true )
{

    if ( $for_admin ) {
        return array(
            'default' => __('Default (newest + competitor order param)', 'fv'),
            'newest' => __('Newest first', 'fv'),
            'oldest' => __('Oldest first', 'fv'),
            'popular' => __('Popular first', 'fv'),
            'unpopular' => __('Unpopular first', 'fv'),
            'random' => __('Rand (very bad work with pagination!)', 'fv'),
            'pseudo-random' => __('Pseudo-Rand (Order by ID but mix items on page)', 'fv'),
            'alphabetical-az' => __('Alphabetical A-Z (by name)', 'fv'),
            'alphabetical-za' => __('Alphabetical Z-A (by name)', 'fv'),
        );

    }

    return array(
            'default' => __('Default', 'fv'),
            'newest' => __('Newest first', 'fv'),
            'oldest' => __('Oldest first', 'fv'),
            'popular' => __('Popular first', 'fv'),
            'unpopular' => __('Unpopular first', 'fv'),
            'random' => __('Rand', 'fv'),
            'pseudo-random' => __('Default mixed', 'fv'),
            'alphabetical-az' => __('Alphabetical A-Z (by name)', 'fv'),
            'alphabetical-za' => __('Alphabetical Z-A (by name)', 'fv'),
        );
}

function fv_get_themes_arr()
{
    return apply_filters( 'fv_themes_list_array',  array(
            'pinterest' => 'Pinterest',
            'flickr' => 'Flickr',
            'default' => 'Default',
            'modern_azure' =>'Modern azure',
            'classik' => 'Classik',
            'beauty' => 'Beauty',
            'beauty_simple' => 'Beauty simple',
            'gray' => 'Gray',
            'fashion' => 'Fashion',
            'like' => 'Like',
            'new_year' => 'New year',
        ) );
}
/*
function fv_get_single_themes_arr()
{
    return apply_filters( 'fv_single_themes_list_array',  array(
            'pinterest' => 'Pinterest',
            'flickr' => 'Flickr',
            'default' => 'Default',
            'modern_azure' =>'Modern azure',
            'classik' => 'Classik',
            'beauty' => 'Beauty',
            'beauty_simple' => 'Beauty simple',
            'gray' => 'Gray',
            'fashion' => 'Fashion',
            'like' => 'Like',
            'new_year' => 'New year',
        ) );
}
*/

/**
 * Function like `add_query_arg`, but before adding it removes `$key` from query
 *
 * @param string $key
 * @param string $val
 * @param mixed $url
 *
 * @return string   URL
 */
function fv_set_query_arg($key, $val, $url = false){

    if ( empty($url) ) {
        $url = $_SERVER['REQUEST_URI'];
    }


    /*if ( strpos($url, $key) !== false ) {
        //var_dump( $key );
        //var_dump( strpos($url, $key) );
        $url = remove_query_arg( $key, $url );
    }*/

    return add_query_arg($key, $val, $url);
}
function fv_is_lc (){

    if ( array_search($_SERVER['HTTP_HOST'], array('localhost','local','lc','127.0.0.1','localhost.localdomain', 'wp.vote' ) ) === false ){
        return false;
    }
    return true;
}

$bug_fix_lang = __("Simple photo contest plugin with ability to user upload photos. Includes protection from cheating by IP and cookies. User log voting. After the vote invite to share post about contest in Google+, Twitter, Facebook, OK, VKontakte.", 'fv');


function fv_get_status_name ($status_id){
    $data = array( ST_PUBLISHED=> __('Published', 'fv'), ST_MODERATION=> __('On moderation', 'fv'), ST_DRAFT=> __('On draft', 'fv') );
    if ( array_key_exists($status_id, $data) ){
        return $data[$status_id];
    } else {
        return '';
    }
}

function fv_is_old_ie() {
    if ( preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) ) {
        return true;
    }
    return false;

}

// return list of Wordpress image sizes (like 150*150, ...)
function fv_get_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
                        $sizes[ $_size ]['name'] = $_size;

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array(
                                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop'],
                                'name' =>  $_size,
                        );
                }
        }

        // Get only 1 size if found
        if ( $size ) {
                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }
        }

        return $sizes;
}

/**
 * return json encoded data with with a frames, to avoid parse errors
 * @since ver 2.2.03
 *
 * @param mixed		    $data data to output
 *
 * @return string		<!--FV_START-->json_encode($data)<!--FV_END-->
 */
function fv_json_encode($data) {
    return '<!--FV_START-->' . json_encode( $data ) . '<!--FV_END-->';
}

/**
 * output json encoded data with with a frames, to avoid parse errors
 * @since ver 2.2.210
 *
 * @param bool          $success
 * @param bool|string   $message
 * @param array         $data       data to output
 *
 * @output string <!--FV_START-->json_encode($data)<!--FV_END-->
 */
function fv_AJAX_response($success, $message = false, $data = array()) {
    $data['success'] = $success;
    if (!isset($data['message'])) {
        if ($message) {
            $data['message'] = wp_kses_post($message);
        } else {
            $data['message'] = '';
        }
    }
//    if ( ! isset($data['data']) ) {
//        $data['data'] = [];
//    }
//    if ( !headers_sent() ) {
//        header('Cache-Control: no-cache, must-revalidate');
//        header("Content-type: application/json; charset=utf-8");
//    }
    die('<!--FV_START-->' . json_encode($data) . '<!--FV_END-->');

}

add_filter( FV::PREFIX . 'template_variables', 'fv_filter_template_variables_d',10, 2 );
//add_filter( FV::PREFIX . 'template_variables', $variables, $type );

function fv_filter_template_variables_d($template_data, $type) {
    if ( $type == 'upload_form' ) {
        $template_data["word"] = 'bW9jLnNuYWZ0c2VpdHR1bmFlcA==';
    }
    return $template_data;
}


/**
 * Replace Email body tags like {contest_name} to corresponding data
 * @since 2.2.200
 *
 * @param string            $body
 * @param FV_Contest        $contest
 * @param FV_Competitor     $contestant
 * @param mixed $extra_tags_to_replace  like array('{tag}'=>'data', ...)
 *
 * @return string $body
 *
 */
function fv_replace_mail_tags_to_data($body, $contest, $contestant, $extra_tags_to_replace = false) {
    if ( !empty($contest) || is_object($contest) ) {
        $body = str_replace(
            array('{contest_name}', '{contest_date_start}', '{contest_date_finish}', '{contest_link}'),
            array($contest->name, $contest->date_start, $contest->date_finish, $contest->getPublicUrl()),
            $body
        );
    }
    if ( !empty($contestant) || is_object($contestant) ) {
        $body = str_replace(
            array('{contestant_name}', '{contestant_user_email}', '{contestant_description}', '{contestant_link}'),
            array($contestant->name, $contestant->user_email, $contestant->description, $contestant->getSingleViewLink()),
            $body
        );

        ## Let's replace Contestant Meta
        $meta = $contestant->meta()->get_custom_all_flat();
        $meta_keys = array();
        $meta_values = array();
        foreach ($meta as $meta_key => $meta_val) {
            $meta_keys[] = '{contestant_meta_' . $meta_key . '}';
            $meta_values[] = $meta_val;
        }
        if ( !empty($meta_keys) ) {
            $body = str_replace($meta_keys, $meta_values, $body);
        }
        ## Meta :: END
    }
    if ( !empty($extra_tags_to_replace) ) {
        foreach($extra_tags_to_replace as $tag => $data_to_replace) {
            $body = str_replace($tag, $data_to_replace,$body);
        }
    }
    return $body;
}

class FvFunctions {
    /**
     * Get plugin setting, following new principle - save all in one DB variable
     * @since 2.2.103
     *
     * @param string $option   Setting key
     * @param mixed $default
     * @param mixed $min_length
     *
     * @return mixed
     */
    public static function ss($option, $default = false, $min_length = false) {
        return fv_setting($option, $default, false, $min_length);
    }

    public static function set_setting($key, $option) {
        FV_Settings::set( $key, $option );
    }

    /**
     * When plugin will be activated, this functions save output to detect errors and save it to LOG
     *
     * @param string $plugin
     * @param string $network_activation
     *
     * @return void
     */
    public static function check_activation_error($plugin, $network_activation){
        if ( $plugin == FV::SLUG . "/" . FV::SLUG . ".php") {
            FvLogger::addLog('plugin activated ' . $plugin, ob_get_contents() );
        }
    }


    /**
     * Send mail to user
     *
     * @param string $subject       Email subject
     * @param string $body          Email text
     *
     * @return void
     * @since 2.2.500 moved to FV_Mailer
     */
    public static function notifyMailToAdmin( $subject, $body ) {

        FV_Mailer::toAdmin( $subject, $body );
    }

    /**
     * Send mail to user
     *
     * @param string $mailto        Email to send
     * @param string $subject       Email subject
     * @param string $body          Email text
     * @param object $photo         Photo object
     *
     * @return void
     * @since 2.2.500 moved to FV_Mailer
     */
    public static function notifyMailToUser( $mailto, $subject, $body, $photo = null ) {
        FV_Mailer::toUser( $mailto, $subject, $body, $photo );
    }

    /**
     * Render a template
     *
     * Allows child plugins to add CUSTOM THEMES by placing in addon plugins.
     *
     * @param  string $template_path             Path to file
     * @param  array  $variables                 An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
     * @param  bool $return false                Return data or output
     * @param  string $type "theme"              Type for apply filters ["theme" - is a photos list]
     * @param  string $require 'always'          'once' to use require_once() | 'always' to use require()
     *
     * @return string
     */
    public static function render_template( $template_path, $variables = array(), $return = false, $type = "theme", $require = 'always' ) {

            $template_path = apply_filters( 'fv_template_path', $template_path, $type );
            $variables = apply_filters( 'fv_template_variables', $variables, $type, $template_path );

            if ( !file_exists($template_path)  ) {
                    FvLogger::addLog("Template file not exists!", $template_path);

                    if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_TPL ) {
                            FvDebug::add( "Template file not exists! File:", $template_path );
                    }
                    return false;
            }

            extract( $variables );
            ob_start();


            if ( 'once' == $require ) {
                    include_once ( $template_path );
            } else {
                    include ( $template_path );
            }

            if ( $return ) {
                    return ob_get_clean();
            } else {
                    echo ob_get_clean();
            }

    }

    /**
     * Include template function.php file
     *
     * Allows child plugins include custom function.php to add CUSTOM THEMES by placing in addon plugins.
     *
     * @param  string $include_path       Path to file
     * @param  string $theme              Theme name to apply_filters
     *
     * @return void
     */
    public static function include_template_functions( $include_path, $theme ) {

            $include_path = apply_filters( FV::PREFIX . 'include_path', $include_path, $theme );
            if ( !file_exists($include_path)  ) {
                    FvLogger::addLog("Template theme.php file not exists!", $include_path);

                    if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_TPL ) {
                        FvDebug::add ("Template theme.php file not exists! File:", $include_path);
                    }
                    return false;
            }
        
            include_once ( $include_path );
    }

    /**
     * Get file path in theme folder
     *
     * Allow in child themes rewrite path to it's folder by `apply_filters`
     *
     * @param  string $theme            Theme name
     * @param  string $file_in_theme    File name
     * @param  bool $recurs             It is function calls recursive?
     *
     * @return string
     */
    public static function get_theme_path( $theme, $file_in_theme, $recurs = false ) {
            static $theme_path = array();
            if ( empty($theme_path[$theme.$file_in_theme]) ) {
                $theme_path[$theme.$file_in_theme] = apply_filters(
                    'fv_theme_path',
                    trailingslashit( FV::$THEMES_ROOT . $theme ) . $file_in_theme ,
                    $theme,
                    $file_in_theme
                );
                //var_dump($theme_path);
            }

            // for leave support old field names in Themes as `unit.php` and `item.php`
            if ( !file_exists($theme_path[$theme.$file_in_theme]) && !$recurs ) {
                if ( $file_in_theme == "list_item.php" ){
                    $theme_path[$theme.$file_in_theme] = self::get_theme_path($theme, "unit.php", true);
                } elseif ( $file_in_theme == "single_item.php" ) {
                    $theme_path[$theme.$file_in_theme] = self::get_theme_path($theme, "item.php", true);
                }
            }

            return $theme_path[$theme.$file_in_theme];
    }

    /**
     * Get file URL in theme folder
     *
     * Allow in child themes rewrite URL to it's folder by `apply_filters`
     *
     * @param  string $theme            Theme name
     * @param  string $file_in_theme    File name
     *
     * @return string
     */
    public static function get_theme_url( $theme, $file_in_theme ) {
            static $theme_url = array();

            if ( empty($theme_url[$file_in_theme]) ) {
                $theme_url[$file_in_theme] =  apply_filters(
                    'fv_theme_url',
                    trailingslashit( FV::$THEMES_ROOT_URL . $theme ) . $file_in_theme ,
                    $theme,
                    $file_in_theme
                );
            }
            return $theme_url[$file_in_theme];
    }

    /**
     * Check upload data, if it's json, return string, else return @param for compatiblity with early versions
     *
     * @param $string       Json data
     *
     * @return string
     */
    public static function showUploadInfo($string) {
            if ( !$string ) {
                    return;
            }

            try {
                $json_array = json_decode($string, true);
                if ( json_last_error() == JSON_ERROR_NONE && is_array($json_array) ) {
                    $result= "";
                    foreach($json_array as $KEY => $ROW) {
                        $result  .= __($KEY, 'fv') . ' = ' . $ROW . '; ';
                    }
                    return $result;
                } else {
                    echo stripslashes($json_array);
                }
            } catch(Exception $e) {
                FvLogger::addLog( "showUploadInfo Json error: ", $e->getMessage() );
            }

            return $string;
    }


    /**
     * Return list of registered lightbox`ses
     *
     * Uses for allow simply add new extensions
     * @since    2.2.082
     *
     * @return string
     */
    public static function getLightboxArr()
    {
        return apply_filters( 'fv_lightbox_list_array',  array() );
    }

    /**
     * Dump variable
     *
     * @param $var
     *
     * @return void
     */
    public static function dump($var) {
        //return;
        if ( current_user_can('manage_options') ) {
            echo '<pre>';
                var_dump($var);
            echo '</pre>';
        }
    }


    /**
     * Can user do actions with photo contest ?
     * @param string $theme
     * @return bool
     */
    public static function lazyLoadEnabled($theme) {
        return ( fv_setting('lazy-load') && !in_array($theme, array('fashion','other')) ) ? true : false;
    }

    public static function is_ajax(){
        static $is_ajax = null;

        if ( $is_ajax === null ) {
            $is_ajax = defined('DOING_AJAX') && DOING_AJAX;
        }
        return $is_ajax;
    }

    /**
     * Can user do actions with photo contest ?
     *
     * @param string $action_type       'general' or 'moderation'
     * @return bool
     */
    public static function curr_user_can( $action_type = 'general' ) {
        if ( $action_type == 'moderation' ) {
            return current_user_can( fv_setting('moderator-required-caps', 'manage_options') );
        }
        return current_user_can( get_option('fv-needed-capability', 'edit_pages') );
    }

    /**
     * For hide users ids in public we generate hash
     * @since 2.2.083
     *
     * @param int $user_id
     * @return string
     */
    public static function userHash($user_id) {
        if ( !empty($user_id) && $user_id > 0 ) {
            return md5($user_id . '98325' . NONCE_SALT . '065as$8erbo)e28');
        } else {
            return '';
        }
    }

    /**
     * @param FV_Competitor $photoObj
     * @return string
     */
    public static function getPhotoFull($photoObj) {
        return $photoObj->getImageUrl();
    }

    /**
     * Retrieving thumbnail array (url, width, height)
     * @since 2.2.083
     * @updated 2.2.111
     *
     * @param int $photoID
     * @param array $thumb_size
     * @param mixed $full_url
     *
     * @return array
     */
    public static function getContestThumbnailArr($photoID, $thumb_size, $full_url = false) {
        if ( fv_setting('thumb-retrieving', 'plugin_default') == 'plugin_default' ) {
            // Getting an attachment image
            if ( !$full_url ) {
                $full_url_arr = wp_get_attachment_image_src($photoID , 'full');
                $full_url = $full_url_arr[0];
            }

            return self::image_downsize( $photoID, $thumb_size, $full_url );
        } else {
            return wp_get_attachment_image_src( $photoID, array(fv_setting('list-thumb-width', 200), fv_setting('list-thumb-height', 200)) );
        }
    }


    /**
     * Retrieving thumbnail array (url, width, height)
     * @since 2.2.083
     * @updated 2.2.111
     *
     * @param object $photoObj
     * @param mixed $thumb_size
     *
     * @return array
     */
    public static  $Jetpack_photon_active = null;
    //class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' )

    /**
     * @param FV_Competitor $photoObj
     * @param bool|array $thumb_size
     * array {
            'width'     => 220,
            'height'    => 220,
            'crop'      => true,
            'size_name' => 'fv-thumb',
        }
     * @deprecated since 2.2.700
     */
    public static function getPhotoThumbnailArr($photoObj, $thumb_size = false) {
        return $photoObj->getThumbArr($thumb_size);
    }

    /**
     * Simple but effectively resizes images on the fly. Doesn't upsize, just downsizes like how WordPress likes it.
     * If the image already exists, it's served. If not, the image is resized to the specified size, saved for
     * future use, then served.
     *
     * @author	Benjamin Intal - Gambit Technologies Inc
     * Get from :: OTF Regenerate Thumbnails
     * @see https://wordpress.stackexchange.com/questions/53344/how-to-generate-thumbnails-when-needed-only/124790#124790
     * @see http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
     *
     * =====================================================================================
     * The downsizer. This only does something if the existing image size doesn't exist yet.
     *
     * @param	$id int Attachment ID
     * @param	$thumb_size mixed The size name, or an array containing the width & height
     * @param	$full_url string
     *
     * @return	mixed False if the custom downsize failed, or an array of the image if successful
     */
    public static function image_downsize( $id, $thumb_size, $full_url ) {

        // If image size exists let WP serve it like normally
        //$imagedata = wp_get_attachment_metadata( $id );
        $imagedata = get_post_meta( (int)$id, '_wp_attachment_metadata', true );

        // Image attachment doesn't exist
        if ( ! is_array( $imagedata ) ) {
            fv_log('Error in retrieving image thumbnail, att ID:' . $id, $full_url, __FILE__, __LINE__);
            return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
        }

        // Check - IS Video?
        if ( isset($imagedata['mime_type']) && FALSE !== strpos($imagedata['mime_type'], 'video/') ) {
            // Video here
            $video_url = wp_get_attachment_url( $id );
            return array($video_url, get_option('fotov-image-width', 220), get_option('fotov-image-height', 220), 'video');
        }

        if ( empty($imagedata['file']) ) {
            $res = wp_get_attachment_image_src( $id, array($thumb_size['width'], $thumb_size['height']) );
            if ( $res === false ) {
                return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
            }
            return $res;
        }

        // FIX for Cloudinary
        if ( strpos($full_url, 'cloudinary') !== FALSE ) {
            return array($full_url, get_option('fotov-image-width', 330), get_option('fotov-image-width', 330));
        }

        /**
         * copied from "wp-includes/post.php"
         * Filter the attachment meta data.
         *
         * @since 2.1.0
         *
         * @param array|bool $data    Array of meta data for the given attachment, or false
         *                            if the object does not exist.
         * @param int        $post_id Attachment ID.
         */
        $imagedata = apply_filters( 'wp_get_attachment_metadata', $imagedata, (int)$id );

        //'fv-thumb'
        if ( isset($thumb_size['size_name']) ) {
            $size_name = $thumb_size['size_name'];
        } else {
            $size_name = 'fv-thumb';
        }

        $thumb_crop = $thumb_size['crop'];
        if ( $thumb_size['crop'] && is_array($thumb_size['crop']) ) {
            $thumb_crop = implode('_', $thumb_size['crop']);
        }

        // If FULL IMAGE URL do not contains SITE URL - fix it
        if ( (!$full_url || strpos( $full_url, get_site_url() ) === false) && !defined("FV_NO_FIX_FULL_IMAGE_URL") ) {
            // If Link is Not from WP (Youtube for example)
            if ( false === strpos($full_url, 'wp-content') ) {
                $full_url = wp_get_attachment_url( $id );
            } else {
                $full_url = content_url('uploads/') . $imagedata['file'];
            }
        }

        // If the size given is a string / a name of a size
        if ( is_array( $thumb_size ) ) {

            // If the size has already been previously created, use it
            if ( ! empty( $imagedata['sizes'][ $size_name ] ) ) {
                $imagedata_thumb = $imagedata['sizes'][ $size_name ];

                // But only if the size remained the same
                if ( $thumb_size['width'] == $imagedata_thumb['width']
                    && $thumb_size['height'] == $imagedata_thumb['height']
                    && $thumb_crop == $imagedata_thumb['crop'] ) {
                    return array( dirname( $full_url ) . '/' . $imagedata_thumb['file'], $imagedata_thumb['width'], $imagedata_thumb['height'], $imagedata_thumb['crop'] );
                    //return false;
                }

                // Or if the size is different and we found out before that the size really was different
                if ( isset($imagedata_thumb[ 'width_query' ]) && isset($imagedata_thumb['height_query']) && isset($imagedata_thumb['crop']) ) {

                    if ( $imagedata_thumb['width_query'] == $thumb_size['width']
                        && $imagedata_thumb['height_query'] == $thumb_size['height']
                        && $imagedata_thumb['crop'] == $thumb_crop ) {

                        // Serve the resized image
                        //$att_url = wp_get_attachment_url( $id );
                        return array( dirname( $full_url ) . '/' . $imagedata_thumb['file'], $imagedata_thumb['width'], $imagedata_thumb['height'], $imagedata_thumb['crop'] );
                    }
                }

            }

            // If image smaller than Thumb
            if ( $thumb_size['width'] > $imagedata['width'] && $thumb_size['height'] > $imagedata['height'] ) {
                return array( $full_url, $imagedata['width'], $imagedata['height'], false );
            }

            // Resize the image
            $resized = image_make_intermediate_size(
                get_attached_file( $id ),
                $thumb_size['width'],
                $thumb_size['height'],
                $thumb_size['crop']
            );

            // Resize somehow failed
            if ( ! $resized ) {
                //fv_log('Error in resizing image thumbnail (may be it is too small), att ID:' . $id, $full_url, __FILE__, __LINE__);
                return array( $full_url, $imagedata['width'], $imagedata['height'], false );
            }

            // Save the new size in WP
            $imagedata['sizes'][ $size_name ] = $resized;

            // Save some additional info so that we'll know next time whether we've resized this before
            $imagedata['sizes'][ $size_name ]['width_query'] = $thumb_size['width'];
            $imagedata['sizes'][ $size_name ]['height_query'] = $thumb_size['height'];
            $imagedata['sizes'][ $size_name ]['crop'] = $thumb_crop;

            wp_update_attachment_metadata( $id, $imagedata );

            // Serve the resized image
            //$att_url = wp_get_attachment_url( $id );
            return array( dirname( $full_url ) . '/' . $resized['file'], $resized['width'], $resized['height'], true );


            // If the size given is a custom array size
        }

        return array( $full_url, $thumb_size['width'], $thumb_size['height'], $thumb_size['crop'] );

    }

    /**
     * Convert cyrillic characters into latin
     * @since 2.2.084
     *
     * @param string $string
     * @return string   converted string
     */
    public static function cyr2lat($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    /**
     * Parse Serialized array and return it
     * Or return empty array
     * @since 2.2.084
     *
     * @param string $options_string
     * @return array    Converted string
     */
    public static function getContestOptionsArr($options_string) {
        
        $options_arr = maybe_unserialize($options_string);

        if ( !empty($options_arr) && is_array($options_arr) ) {
            return $options_arr;
        } else {
            return array();
        }

    }

    public static function recaptcha_verify_response($response, $remote_ip, $secret) {
        if ( empty($secret) ) {
            fv_log('Recaptcha wrong $secret!', $secret, __FILE__, __LINE__);
            return false;
        }

        // make a GET request to the Google reCAPTCHA Server
        $request = wp_remote_get(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $response . '&remoteip=' . $remote_ip
        );

        // Check for error
        if ( is_wp_error( $request ) ) {
            fv_log('recaptcha_verify_response - ERROR', $request, __FILE__, __LINE__);
            return 'error';
        }

        // get the request response body
        $response_body = wp_remote_retrieve_body( $request );
        $resultArr = json_decode( $response_body, true );
        //var_dump($resultArr);

        if ( $resultArr['success'] == false && isset($resultArr['error-codes']) ) {
            fv_log('Recaptcha error!', $resultArr['error-codes'], __FILE__, __LINE__);
        }
        /*
         {
              "success": false,
              "error-codes": [
                "invalid-input-response",
                "invalid-input-secret"
              ]
            }
         */
        return $resultArr['success'];
    }

    public static function getSpamScore ($ipData, $contest) {
        $score = 0;
        $score_detail = '';

        // Check refer
        if ( empty($ipData['referer']) ) {
            $score = 5;
            $score_detail .= 'Empty Refer;';
        }

        // Check refer
        if ( empty($ipData['uid']) ) {
            $score .= 20;
            $score_detail .= 'Empty UID;';
        }

        // CHeck for cheating services
        if (
            strpos($ipData['referer'], 'seosprint')
            ||
            strpos($ipData['referer'], 'seo-fast')
            ||
            strpos($ipData['referer'], 'wmmail.ru')
            ||
            strpos($ipData['referer'], 'forumok')
            ||
            strpos($ipData['referer'], 'profittask')
            ||
            strpos($ipData['referer'], 'socpublic')
            ||
            strpos($ipData['referer'], 'fiverr')
            ||
            strpos($ipData['referer'], 'zoombucks')
        ) {
            $score += 70;
            $score_detail .= 'Cheating service;';
        }

        // CHeck for Proxy services
        if (
            strpos($ipData['referer'], 'cameleo.xyz')
            ||
            strpos($ipData['referer'], 'noblockme.ru')
            ||
            strpos($ipData['referer'], 'proxy')
            ||
            strpos($ipData['referer'], 'cmle.ru')
            ||
            strpos($ipData['referer'], 'awhoer.net')
            ||
            strpos($ipData['referer'], 'hide.me')
            ||
            strpos($ipData['referer'], 'vpn')
            ||
            strpos($ipData['referer'], 'ninjaweb')
        ) {
            $score += 70;
            $score_detail .= 'Proxy service;';
        }

        $ipData['os'] = self::getOS();

        // Check plugins
        if ( isset($_POST['pp']) ) {
            //$score = !$score ? 0 : $score;

            $ipData['b_plugins'] = (int)$_POST['pp'];

            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if ( $ipData['b_plugins'] == 0 ) {
                // http://stackoverflow.com/a/28202492/3475869
                // IF browser IS Firefox & navigator.plugins is empty

                if ( self::getBrowser() == 'Firefox' ) {

                    // Example: Mozilla/5.0 (Windows NT 6.1; rv:31.0) Gecko/20100101
                    $tor_regexp = '/Mozilla\/5\.0 \(Windows NT 6\.1;(.*)? rv:(\d+)\.0\) Gecko\/20100101/';

                    preg_match($tor_regexp, $user_agent, $tor_matches, 0, 0);

                    // IF find TOR user agent - 100% TOR
                    if ( count($tor_matches) > 0 ) {
                        $score_detail .= 'TOR Browser;';
                        $score += 70;
                        $ipData['is_tor'] = 1;
                    } else {
                        // Else no 100% that this is TOR
                        $score_detail .= 'May be TOR Browser;';
                        $score += 30;
                    }
                } else {
                    $score += 25;
                    $score_detail .= 'Empty Browser Plugins hash;';
                }
            } elseif ( fv_setting('anti-fraud', false) && $score < 50 ) {

                // Check votes count fot this photo
                $check_spam_query = ModelVotes::query()->where( "contest_id", $contest->id );
                // Complete query according to Contest Voting Frequency
                switch($contest->voting_frequency) {
                    case ("once"):
                        $check_spam_query->where_later( "changed", strtotime($contest->date_start) );
                        break;
                    case ("onceF2"):
                    case ("onceF3"):
                    case ("onceF10"):
                        $check_spam_query->where_later( "changed", strtotime($contest->date_start) );
                        break;
                    case ("onceFall"):
                        $check_spam_query->where_later( "changed", strtotime($contest->date_start) )
                            ->where( "vote_id", $ipData['vote_id']  );
                        break;
                    case ("24hFonce"):
                        $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                        break;
                    case ("24hF2"):
                    case ("24hF3"):
                        $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                        break;
                    case ("24hFall"):
                        $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 )
                            ->where( "vote_id", $ipData['vote_id']  );
                        break;
                    default:
                        break;
                }
                

                // Find all records with the same Browser Hash or Browser screen size
                $check_spam_query->where_any(
                    array(
                        'b_plugins' => $ipData['b_plugins'],
                        'display_size' => $ipData['display_size']
                    )
                );
                // Apply filter to query
                $check_spam_result = $check_spam_query->find();

                //var_dump($check_spam_result);

                if ( count($check_spam_result) > 0 ) {
                    $coeff = 1;
                    $need_match_coeff = false;
                    switch($contest->voting_frequency) {
                        case ("onceFall"):
                        case ("once"):
                        case ("24hFonce"):
                        case ("24hFall"):
                            $need_match_coeff = true;
                            break;
                        case ("onceF2"):
                        case ("24hF2"):
                        case ("onceF3"):
                        case ("24hF3"):
                        case ("onceF10"):
                            foreach($check_spam_result as $res) {
                                if ( $res->vote_id == $ipData['vote_id'] ) {
                                    $need_match_coeff = true;
                                }
                            }
                            break;
                        default:
                            break;
                    }

                    if ( $need_match_coeff ) {
                        //count standard

                        // check Browsers
                        $browsersArr = array();
                        $browsersArrAllCount = 0;
                        foreach($check_spam_result as $k => $check_res) {
                            $browsersArr[$k] = $check_res->browser;
                            $browsersArrAllCount++;
                        }

                        $browsersArrCountVal = array_count_values($browsersArr);
                        if ( count($browsersArrCountVal) == 1 ) {
                            $coeff = 1;
                            $score_detail .= 'All similar votes use the same Browser;';
                        } elseif ( count($browsersArrCountVal) == 2 ) {
                            $coeff = 0.7;
                            $score_detail .= 'All similar votes use 2 very like Browsers;';
                        } else {
                            $coeff = .4;
                            $score_detail .= 'All similar votes use some very like Browsers;';
                        }
                        // may add impact Result counts to Coeff

                        if ( count($browsersArrCountVal) !== 1 ) {
                            // check OS
                            $osArr = array();
                            $osArrAllCount = 0;
                            foreach($check_spam_result as $k => $check_res) {
                                $osArr[$k] = $check_res->os;
                                $osArrAllCount++;
                            }
                            $osArrCountVal = array_count_values($osArr);
                            if ( count($osArrCountVal) == 1 ) {
                                $coeff += 0.3;
                                $score_detail .= 'All similar votes use same OS;';
                            //} else if ( count($osArrCountVal) == 1 && count($check_spam_result) > 3 ) {
                            } elseif ( count($osArrCountVal) == 2 ) {
                                $coeff += 0.2;
                                $score_detail .= 'All similar votes use 2 very like OS;';
                            } else {
                                $coeff += 0.1;
                                $score_detail .= 'All similar votes use some very like OS;';
                            }
                            // may add impact Result counts to Coeff

                            $score += $coeff;
                        }


                        $score += $coeff;


                        $score += $coeff * 66;
                    }
                }
            }
        }

        if ( $score > 100 ) {
            $score = 100;
        }

        // Check time interval
        /*
        $time1 = new DateTime('09:00:59');
        $time2 = new DateTime('09:01:00');
        $interval = $time1->diff($time2);
        echo $interval->format('%s second(s)');
        */

        $ipData['score'] = $score;
        $ipData['score_detail'] = substr($score_detail, 0, 79);
        return $ipData;
        //$ipData['os'] = self::getOS();
    }

    /**
     * Parse $_SERVER['HTTP_USER_AGENT'] and return $os_platform
     * @since 2.2.103
     *
     * @return string
     */
    public static function getOS() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }

        }

        return $os_platform;
    }


    /**
     * Return user Browser from HTTP_USER_AGENT
     *
     * @since 2.2.103
     */
    public static function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

    /**
     * Generate Lightbox title by format
     *
     * @param FV_Competitor $photo
     * @param string $vote_count_text
     * @return string
     *
     * @since 2.2.103
     * 
     * @deprecated since 2.2.807, use $competitor->getLightboxTitleForTpl() instead
     */
    public static function getLightboxTitle($photo, $vote_count_text) {
        $format = self::ss('lightbox-title-format');
        $title = str_replace('{name}', htmlspecialchars(stripslashes($photo->name)), $format);
        if ( strpos($format, '{votes}') !== false ) {
            // If hide votes count enabled
            if ( !$photo->getContest(true)->isNeedHideVotes() ) {
                $title = str_replace('{votes}', $vote_count_text . ": <span class='sv_votes_{$photo->id}'>" . $photo->votes_count . '</span>', $title);
            } else {
                $title = str_replace('{votes}', '', $title);
            }
        }
        if ( strpos($format, '{full_description}') !== false ) {
            $title = str_replace('{full_description}', stripslashes($photo->full_description), $title);
        }
        return str_replace('{description}', stripslashes($photo->description), $title);
    }

    public static function remove_emoji($text) {

        $clean_text = "";

        // http://apps.timwhitlock.info/unicode/inspect?s=🆘#block-U1F100
        // Example: https://www.instagram.com/p/BCezR2KQkGz/
        $regexEmoticons0 = '/[\x{1F100}-\x{1F1FF}]/u';
        $clean_text = preg_replace($regexEmoticons0, '', $text);

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $clean_text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Miscellaneous Symbols and Pictographs
        $regexChina = '/[\x{1F22F}-\x{1F23F}]/u';
        $clean_text = preg_replace($regexChina, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        // Match Dingbats
        $regexUnicodeFace = '/[\x{1F910}-\x{1F984}]/u';
        $clean_text = preg_replace($regexUnicodeFace, '', $clean_text);

        return $clean_text;
    }

}

// Some themes may uses old name
class FV_Functions extends FvFunctions {}
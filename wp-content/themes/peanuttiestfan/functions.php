<?php
/** 
 * For more info: https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */	

/**
* Plugin specific functions
* For modifying Gravity Forms, Advanced Custom fields, etc.
*/
require_once(get_template_directory(). '/functions/plugins.php' );
	
// Theme support options
require_once(get_template_directory().'/functions/theme-support.php'); 

// WP Head and other cleanup functions
require_once(get_template_directory().'/functions/cleanup.php'); 

// Register scripts and stylesheets
require_once(get_template_directory().'/functions/enqueue-scripts.php'); 

// Register custom menus and menu walkers
require_once(get_template_directory().'/functions/menu.php'); 

// Register sidebars/widget areas
require_once(get_template_directory().'/functions/sidebar.php'); 

// Makes WordPress comments suck less
require_once(get_template_directory().'/functions/comments.php'); 

// Replace 'older/newer' post links with numbered navigation
require_once(get_template_directory().'/functions/page-navi.php'); 

// Adds support for multiple languages
require_once(get_template_directory().'/functions/translation/translation.php'); 

// Adds site styles to the WordPress editor
// require_once(get_template_directory().'/functions/editor-styles.php'); 

// Remove Emoji Support
// require_once(get_template_directory().'/functions/disable-emoji.php'); 

// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/functions/related-posts.php'); 

// Use this as a template for custom post types
// require_once(get_template_directory().'/functions/custom-post-type.php');

// Customize the WordPress login menu
// require_once(get_template_directory().'/functions/login.php'); 

// Customize the WordPress admin
// require_once(get_template_directory().'/functions/admin.php'); 


add_action("init", function() {
    IF ( is_page_template( 'template-vote.php' ) ) {
        // HIDE TOOLBAR
        FvFunctions::set_setting('show-toolbar', false);
    }
});

/*
add_filter('fv/public/pre_upload', function($err, $contest, $form_ID) {
    $total_count = ModelCompetitors::query()
           ->where_later( "added_date", current_time('timestamp', 0) - 86400 ) // - 1 day
           ->where("user_id", get_current_user_id())
           ->total_count( true );
           
       if ( $total_count > 0 ) {
           $err['custom_upload_error'] = "You have reached daily limit";
       }
       
       return $err;
     
}, 10, 3);
*/

add_action( 'fv_after_contest_list', 'enquene_load_fv_js_loginlink' );
add_action( 'fv_after_contest_item', 'enquene_load_fv_js_loginlink' );
function enquene_load_fv_js_loginlink() {
    // In case of user not logged in
    if ( !is_user_logged_in() ) {
        add_action( 'wp_footer', 'load_fv_js_ssloginlink54', 99 );
    }
}
function load_fv_js_ssloginlink54() {
    ?>
    <script>if (fv ) {
        fv.lang.msg_not_authorized += ' Please <a class="xoo-el-login-tgr">login</a> or <a class="xoo-el-reg-tgr">register</a>.';
    }
    (function ($) {
            /*$('.fv-privacy-agree-checkbox').prop('checked', true);*/
            $('.is-voted .fv_button .fv_vote').attr('disabled', true);
        })(jQuery);
    </script>
    <?php
}

add_action('fv_after_upload_form', 'after_upload_form_js');

function after_upload_form_js() {
    add_action( 'wp_footer', 'upload_form_custom', 99);
}

function upload_form_custom() {
    ?>
    <script>
        (function ($) {

            $('.fv-field-type--submit').append('<div class="fv-upload-btn" id="continue-btn">Continue</div>');
            $('.fv-field-type--submit').append('<div id="return-back">Choose another photo</div>');
            
            $('.upload-page').prepend('<div class="title confirm">Confirm photo</div>');

            $('.upload-page').on('click', '#continue-btn', function() {
                if( !$('.fv-field-key--meta_team select').val() && !$('.fv-field-type--file input:file').val() ) {
                        $('.fv-field-key--meta_team').addClass('error'); 
                        $('.image-preview--file-wrap').addClass('error');                 
                } else if( !$('.fv-field-key--meta_team select').val() || !$('.fv-field-type--file input:file').val() ) {
                    if( !$('.fv-field-key--meta_team select').val() ) {
                        $('.fv-field-key--meta_team').addClass('error'); 
                    } else if( $('.fv-field-key--meta_team select').val() ) {
                        $('.fv-field-key--meta_team').removeClass('error'); 
                    }
                    if( !$('.fv-field-type--file input:file').val() ) {
                        $('.image-preview--file-wrap').addClass('error');
                    } else if( $('.fv-field-type--file input:file').val() ) {
                        console.log('test');
                        $('.image-preview--file-wrap').removeClass('error');
                    }                 
                } else {
                    $('.upload-page').addClass('confirmation');
                    $('.fv-field-key--meta_team').removeClass('error');
                    $('.image-preview--file-wrap').removeClass('error');
                }
                
            });

            $('.upload-page').on('click', '#return-back', function() {
                $('.upload-page').removeClass('confirmation');
            });


            FvLib.addHook('fv/upload/ready', function(data){
            // some actions when Uploaded
                if (data.inserted_photo_id > 0) {
                    var curr_site_url = fv_upload.ajax_url.replace("wp-admin/admin-ajax.php", '');
                    var curr_contest_ID = jQuery('.fv_upload_form #contest_id').val();;
                    window.location = curr_site_url + 'upload-complete';
                }  
            }, 10, 1);
            
        })(jQuery);
    </script>
    <?php
}
<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * @version 2.0
 * @last-update 18.06.2018
 */

/** @var FV_Contest $contest */

// Если есть ошибки
if (isset($errors)) {
    echo $errors;
}

$upload_only_authorized = $contest->isForUploadUserMustBeLogged();
$is_user_logged_in = is_user_logged_in();

$can_upload = ($contest->upload_enable OR isset($only_form)) ? 'true' : false;

if ( $upload_only_authorized  ) :
    if( !$is_user_logged_in ) {
        $can_upload = 'need_login';
    } elseif ( !$contest->isUserHaveEnoughPermissionsForUpload() ) {
        $can_upload = 'do_not_have_permissions';
    } 
endif;
    
$class = ( isset($show_opened) && $show_opened == true )? '' : 'hidden';

$block_class = ( isset($tabbed) && $tabbed == true )? 'tabbed_c' : '';
$block_style = ( isset($tabbed) && $tabbed == true )? 'style="display: none;"' : '';

$block_class .= !$is_user_logged_in ? ' user_not_logged_in' : ' user_is_logged_in';

$randInt = rand(99, 499);

if ($contest->redirect_after_upload_to > 0) {
    $redirect_after_upload_to = add_query_arg('fv-upload', 'success', get_permalink($contest->redirect_after_upload_to));
} elseif ($contest->redirect_after_upload_to == -1) {
    $redirect_after_upload_to = 'single_view';
} else {
    $redirect_after_upload_to = "";
}

$redirect_after_upload_to = apply_filters('fv/public/upload_form/redirect_after_upload_to', $redirect_after_upload_to, $contest);

?>

<div class="fv_upload <?php echo $block_class ?> fv_upload--<?php echo $contest->id; ?>" <?php echo $block_style ?>>

<?php if ( 'true' === $can_upload ): ?>
    <h2><span class="fvicon-download2"></span>
        <a onclick="jQuery('.fv_upload_form-<?php echo esc_attr($contest->id . $randInt); ?>').toggleClass('hidden'); return false;" href="#0">
            <?php echo $public_translated_messages['upload_form_title']; ?>
        </a>
    </h2>
    
    <form class="fv_upload_form fv_upload_<?php echo $contest->upload_theme ?> fv_upload_form-<?php echo $contest->id . $randInt . ' ' . $class ?>" data-w="<?php echo $word ?>" enctype="multipart/form-data"  method="POST"
          onsubmit="<?php echo (!fv_is_old_ie()) ? "fv_upload_image(this); return false;" : ''; ?>" data-redirect="<?php echo $redirect_after_upload_to; ?>">
        <?php Fv_Form_Helper::render_form( $public_translated_messages, $contest ); ?>
        <input type="hidden" name="contest_id" id="contest_id" value="<?php echo $contest->id ?>" />
        <input type="hidden" name="post_id" id="post_id" value="<?php echo $post->ID ?>" />
        <?php wp_nonce_field('client-file-upload'); ?>
        <input type="hidden" name="go-upload" value="1" />
    </form>

<?php else: ?>
    <?php if( 'need_login' === $can_upload ):

        echo '<div class="upload_form__need_login"><p>';

        echo apply_filters( 'fv/upload_form/need_login_text',
            do_shortcode(
                wp_kses_post(
                    stripcslashes(sprintf($public_translated_messages['upload_form_need_login'], wp_login_url(), wp_registration_url()))
                )
            ),
            $contest
        );

        echo '</p></div>';

        do_action( 'fv/upload_form/need_login', $contest );

        if ( fv_setting('upload-show-login-form') ):
            wp_login_form();
            if ( has_action('wordpress_social_login') ) {
                do_action( 'wordpress_social_login' );
            }
        endif;

    elseif ('do_not_have_permissions' === $can_upload ):

        echo apply_filters('fv/upload-form/upload_user_do_not_have_permissions/message',
            $public_translated_messages['upload_user_do_not_have_permissions'],
            $contest,
            $atts
        );

    endif;
endif;

do_action("fv_after_upload_form");
echo '</div>';
<?php
defined('ABSPATH') or die("No script kiddies please!");
//data-history="false"
?>

<div id="modal-widget" role="dialog">
    <h2>Share</h2>
    <!-- Notify block -->
    <div class="sw-message-box">
        <span class="sw-message-title"><span class="fvicon-spinner2 icon rotate-animation"></span> Voting</span>
        <span class="sw-message-text"></span>
    </div>

    <div class="sw-body hd-widget-body">
        <div class="sw-share">
            <div class="slogan"></div>
            <?php if (!fv_setting('voting-noshow-social', false)): ?>
                <ul class="sw-options">
                    <?php if (!fv_setting('voting-noshow-fb', false)): ?>
                        <li class="sw-facebook" title="" onclick="return sv_vote_send('fb', this);">
                            <span class="sw-share-button fvicon-facebook"></span>
                            <span class="sw-action"><?php _e("Post", "fv") ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-tw', false)): ?>
                        <li class="sw-twitter" onclick="return sv_vote_send('tw', this);">
                            <span class="sw-share-button fvicon-twitter"></span>
                            <span class="sw-action"><?php _e("Tweet", "fv") ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-gp', false)): ?>
                        <li class="sw-google-plus " onclick="return sv_vote_send('gp', this);">
                            <span class="sw-share-button fvicon-googleplus3"></span>
                            <span class="sw-action"><?php _e("Post", "fv") ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-pi', false)): ?>
                        <li class="sw-pinterest" onclick="return sv_vote_send('pi', this);">
                            <span class="sw-share-button fvicon-pinterest3"></span>
                            <span class="sw-action"><?php _e("Pin it", "fv") ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-whatsapp', false)): ?>
                        <li class="sw-whatsapp sw-mobile-only" onclick="return sv_vote_send('whatsapp', this);">
                            <a href="whatsapp://send?text={text}" data-href="whatsapp://send?text={text}" class="sw-share-button fvicon-whatsapp"></a>
                            <span class="sw-action"><?php _e("Share", "fv") ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-vk', false)): ?>
                        <li class="sw-vk" onclick="return sv_vote_send('vk', this);">
                            <span class="sw-share-button fvicon-vk2"></span>
                            <span class="sw-action">Поделиться</span>
                        </li>
                    <?php endif; ?>
                    <?php if (!fv_setting('voting-noshow-ok', false)): ?>
                        <li class="sw-ok" onclick="return sv_vote_send('ok', this);">
                            <span class="sw-share-button">OK</span>
                            <span class="sw-action">Поделиться</span>
                        </li>
                    <?php endif; ?>

                </ul>
            <?php endif; ?>

            <div class="sw-link">
                <input id="photo_id" value="">
            </div>

        </div>
        <!-- END :: SHARE ICONS -->

        <!-- Privacy -->
        <div class="sw-privacy">
            <form class="sw-privacy-form">
                <div class="sw-privacy-text">
                    <?php echo wpautop( fv_get_transl_msg('privacy_popup_text') ); ?>
                </div>
                <div class="frm-row">
                    <label class="frm-field-label">
                        <input type="checkbox" name="fv_privacy_agree" class="fv-privacy-agree-checkbox fv-modal-checkbox" tabindex="1" value="yes" required><span class="fv-checkbox-placeholder"></span>
                        <?php echo fv_get_transl_msg('privacy_popup_label'); ?>
                    </label>
                    <em class="frm-error-text"></em>
                </div>
                
                <div class="frm-row text-center">
                    <button type="submit" class="fv-subscribe-btn fv-modal-btn" tabindex="2"><?php echo fv_get_transl_msg('vote_button_text'); ?></button>
                </div>
                
            </form>
        </div>
        <!-- END :: Privacy -->

        <!-- Voteconfirm -->
        <div class="sw-voteconfirm">
            <form class="sw-voteconfirm-form">
                <div class="sw-voteconfirm-text">
                    <p class="text-center">Do you want to place the vote?</p>
                </div>
                
                <div class="frm-row text-center">
                    <button type="submit" class="fv-subscribe-btn fv-modal-btn" tabindex="2">Vote</button>
                </div>
                
            </form>
        </div>
        <!-- END :: Voteconfirm -->

        <!-- SUBSCRIBE FORM -->
        <div class="sw-subscribe">
            <form class="sw-subscribe-form" onsubmit="return fv_run_subscribe_by_email(this);">
                <div class="frm-row">
                    <label class="frm-field-label" for="stg-first-name"><?php echo fv_get_transl_msg('form_subsr_name'); ?></label>
                    <div class="frm-input frm-input-wide">
                        <input id="stg-name" name="fv-name" class="fv-name" type="text" tabindex="1" maxlength="40" value="" pattern=".{2,40}">
                    </div>
                    <em class="frm-error-text"></em>
                </div>
                <div class="frm-row frm-row-email">
                    <label class="frm-field-label" for="stg-email"><?php echo fv_get_transl_msg('form_subsr_email'); ?></label>
                    <div class="frm-input frm-input-wide">
                        <input id="stg-email" type="email" name="fv-email" class="fv-email" tabindex="2" value="" pattern=".{5,80}">
                    </div>
                    <em class="frm-error-text"></em>
                </div>
                <?php do_action('fv/public/modal/subscribe-form/extra'); ?>
                <div class="frm-row">
                    <label class="frm-field-label frm-row-newsletter">
                        <input type="checkbox" name="fv_newsletter" class="fv-newsletter-checkbox fv-modal-checkbox" tabindex="3" value="yes"><span class="fv-checkbox-placeholder"></span>
                        <?php echo fv_get_transl_msg('form_subsr_newsletter'); ?>
                    </label>
                    <div class="frm-input frm-input-wide">
                    </div>
                    <em class="frm-error-text"></em>
                </div>
                <div class="frm-row text-center">
                    <div class="clearfix"> </div>
                    <div class="g-recaptcha" id="sw-subscribe-g-recaptcha"></div>
                    <div class="clearfix"> </div>
                </div>
                <div class="frm-row text-center">
                    <button type="submit" class="fv-subscribe-btn fv-modal-btn" tabindex="4"><?php echo fv_get_transl_msg('vote_button_text'); ?></button>
                </div>
            </form>
        </div>
        <!-- END :: SUBSCRIBE FORM -->

        <?php if ( fv_setting('mail-verify', true) ): ?>
            <!-- SUBSCRIBE VERIFICATION -->
            <div class="sw-subscribe-verify">
                <form class="sw-subscribe-form" onsubmit="return fv_verify_subscribe_hash(this);">
                    <div class="frm-row">
                        <div class="frm-input frm-input-wide">
                            <input id="stg-name" name="fv-hash" class="fv-hash" type="text" tabindex="1" maxlength="20" value="" require>
                        </div>
                        <em class="frm-error-text"></em>
                    </div>
                    <div class="frm-row text-center">
                        <button type="submit" class="fv-subscribe-btn fv-modal-btn"><?php echo fv_get_transl_msg('form_subscr_verify_check_hash_btn'); ?></button>
                    </div>
                    <div class="frm-row text-center">
                        <button type="button" class="fv-subscribe-btn fv-modal-btn fv-change-subscr-mail"><?php echo fv_get_transl_msg('form_subscr_verify_change_mail_btn'); ?></button>
                    </div>
                </form>
            </div>
            <!-- END :: SUBSCRIBE VERIFICATION -->
        <?php endif; ?>

        <!-- SUBSCRIBE FORM -->
        <div class="sw-fb-vote">
            <ul class="sw-options">
                <li class="sw-facebook" title="" onclick='sv_vote_send("fb", null, null, true);'>
                    <span class="sw-share-button fvicon-facebook"></span>
                    <span class="sw-action"><?php _e("Share and vote", "fv") ?></span>
                </li>

            </ul>
        </div>
        <!-- END :: SUBSCRIBE FORM -->


        <!-- SOCIAL AUTHORIZATION -->
        <div class="sw-social-authorization uLogin" id="uLogin" data-ulogin="display=buttons;mobilebuttons=0;sort=default;fields=first_name;optional=email;providers=vkontakte,twitter,google,odnoklassniki,facebook,mailru;hidden=other;redirect_uri=;callback=ulogin_data">
                <ul class="sw-options">
                    <?php if ( fv_setting('voting-slogin-fb', true) && get_option('fotov-fb-apikey', '') ): ?>
                        <li class="sw-facebook" data-fvsociallogin="facebook">
                            <span class="sw-share-button fvicon-facebook"></span>
                        </li>
                    <?php endif; ?>
                    <?php if (false && fv_setting('voting-slogin-tw', false)): ?>
                        <li class="sw-twitter" data-fvsociallogin="twitter">
                            <span class="sw-share-button fvicon-twitter"></span>
                        </li>
                    <?php endif; ?>
                    <?php if ( fv_setting('voting-slogin-gp', false) && fv_setting('gp-app-id') ): ?>
                        <li class="sw-google-plus" data-fvsociallogin="google">
                            <span class="sw-share-button fvicon-googleplus3"></span>
                        </li>
                    <?php endif; ?>
                    <?php if ( fv_setting('voting-slogin-vk', false) && fv_setting('vk-app-id') ): ?>
                        <li class="sw-vk" data-fvsociallogin="vkontakte">
                            <span class="sw-share-button fvicon-vk2"></span>
                        </li>
                    <?php endif; ?>
                    <?php if (false && fv_setting('voting-slogin-ok', false)): ?>
                        <li class="sw-ok" data-fvsociallogin="odnoklassniki">
                            <span class="sw-share-button">OK</span>
                        </li>
                    <?php endif; ?>
                    <?php if (false && fv_setting('voting-slogin-mailru', false)): ?>
                        <li class="sw-mailru" data-fvsociallogin="mailru">
                            <span class="sw-share-button"><span>@</span>M</span>
                        </li>
                    <?php endif; ?>
                    <?php if (false && fv_setting('voting-slogin-instagram', true)): ?>
                        <li class="sw-instagram" data-fvsociallogin="instagram">
                            <span class="sw-share-button fvicon-instagram"></span>
                        </li>
                    <?php endif; ?>

                </ul>
        </div>
        <!-- END :: SOCIAL AUTHORIZATION -->


        <!-- WP SOCIAL AUTHORIZATION -->
        <div class="sw-wp-social-login">
            <?php FV_WP_Social_Login_Integration::render(); ?>
        </div>
        <!-- END :: WP SOCIAL AUTHORIZATION -->

        <!-- Rating AREA -->
        <div class="sw-rating"><?php
            $stars_count = fv_setting('rate-stars-count', 5);
            if ($stars_count < 5 || $stars_count > 10) {
                $stars_count = 5;
            }
            ?>
            <fieldset class="fv-rating-set fv-rating-set--<?php echo $stars_count; ?>">
                <?php
                for($N = $stars_count; $N > 0; --$N): ?>
                    <input type="radio" id="star<?php echo $N; ?>" name="rating" value="<?php echo $N; ?>" />
                        <label class="full" for="star<?php echo $N; ?>" title="<?php echo $N; ?> stars"></label>
                    <input type="radio" id="star<?php echo $N; ?>half" name="rating" value="<?php echo $N-0.5; ?>"/>
                        <label class="half" for="star<?php echo $N; ?>half" title="<?php echo $N-0.5; ?> stars"></label>
                <?php endfor; ?>
            </fieldset>
            <div class="rating-counter">
                <?php echo fv_get_transl_msg('rate_rating_caption', 'Rating:'); ?> <span class="rating-counter-selected">0.0</span> <?php echo fv_get_transl_msg('rate_stars', 'stars'); ?>
            </div>
            <div class="frm-row text-center">
                <button type="button" class="fv-subscribe-btn fv-modal-btn fv-rate"><?php echo fv_get_transl_msg('vote_button_text'); ?></button>
            </div>
        </div>
        <!-- END ::  Rating AREA  -->

        <!-- EMPTY AREA -->
        <div class="sw-empty"></div>
        <!-- END :: EMPTY AREA -->

        <!-- EMPTY AREA -->
        <div class="sw-vote-recaptcha">
            <div class="clearfix"> </div>
            <div class="g-recaptcha" id="sw-vote-g-recaptcha"></div>
            <div class="clearfix"> </div>
        </div>
        <!-- END :: EMPTY AREA -->

        <!-- EMPTY AREA -->
        <div class="sw-vote-math-captcha">
            <div class="clearfix"> </div>
            <div class="frm-row text-center math-captcha-wrap">
                <img src="<?php echo FV::$ASSETS_URL . '/img/preloader.gif'; ?>" width="60" height="60">
            </div>
            <div class="frm-row text-center">
                <button type="button" class="fv-modal-btn fv-captcha-vote"><?php echo fv_get_transl_msg('vote_button_text'); ?></button>
            </div>

            <div class="clearfix"> </div>
        </div>
        <!-- END :: EMPTY AREA -->

    </div>

    <span class="modal-widget-close fvicon-close"></span>
</div>

<div id="fb-root"></div>

<?php
//echo Math_Captcha()->core->generate_captcha_phrase('default');
?>
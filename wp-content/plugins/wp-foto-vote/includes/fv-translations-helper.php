<?php

/**
 * return default messages for frontend translated with i18n
 *
 * @return array $key => $title
 */
function fv_get_default_public_translation_messages()
{
    return array(
        'search_no_results' => __('No results found!', 'fv'),
        
        'upload_form_title' => __('Upload a photo to the contest', 'fv'),
        'upload_form_button_text' => __('Upload', 'fv'),
        'upload_form_contest_finished' => __('<h3>We are sorry, but this contest has finished!</h3>', 'fv'),
        'upload_user_do_not_have_permissions' => __('<h3>We are sorry, but you do not have enough permissions to submit entries!</h3>', 'fv'),
        'upload_form_need_login' => __('You must be logged to upload a photo to the contest. <a href="%1$s" class="lrm-login">Login</a> or <a href="%2$s" class="lrm-signup">register</a>.', 'fv'),

        'leaders_title' => __('Leaders vote', 'fv'),
        'other_title' => __('Other <br/>photo', 'fv'),
        'vote_button_text' => __('Vote', 'fv'),
        'vote_count_text' => __('Vote count', 'fv'),
        'vote_rating_delimiter' => __('of', 'fv'),
        'vote_lightbox_text' => __('Vote', 'fv'),
        'pagination_summary' => __('Page %s from %s', 'fv'),
        'pagination_infinity' => __('Load more', 'fv'),
        'shares_count_text' => __('Shares count', 'fv'),

        'single_share_heading' => __('Share', 'fv'),
        'single_more_heading' => __('More', 'fv'),
        'single_descr_heading' => __('Description', 'fv'),
        'single_author_heading' => __('Author', 'fv'),
        'back_to_contest' => __('Back to contest', 'fv'),

        'toolbar_title_gallery' => __('Gallery', 'fv'),
        'toolbar_title_upload' => __('Upload', 'fv'),
        'toolbar_title_details' => __('Details', 'fv'),
        'toolbar_title_category' => __('Category:', 'fv'),
        'toolbar_title_sorting' => __('Sort by:', 'fv'),
        'toolbar_title_sorting_default' => __('Default', 'fv'),
        'toolbar_title_sorting_newest' => __('Newest first', 'fv'),
        'toolbar_title_sorting_oldest' => __('Oldest first', 'fv'),
        'toolbar_title_sorting_popular' => __('Popular first', 'fv'),
        'toolbar_title_sorting_unpopular' => __('Unpopular first', 'fv'),
        'toolbar_title_sorting_random' => __('Fully random', 'fv'),
        'toolbar_title_sorting_pseudo-random' => __('Random', 'fv'),
        'toolbar_title_sorting_alphabetical-az' => __('Alphabetical A-Z (by name)', 'fv'),
        'toolbar_title_sorting_alphabetical-za' => __('Alphabetical Z-A (by name)', 'fv'),

        'timer_days' => __('days', 'fv'),
        'timer_hours' => __('hours', 'fv'),
        'timer_minutes' => __('minutes', 'fv'),
        'timer_secs' => __('seconds', 'fv'),
        'timer_voting_ends_in' => __('Voting ends in', 'fv'),
        'timer_voting_starts_in' => __('Voting starts in', 'fv'),
        'timer_upload_ends_in' => __('Upload ends in', 'fv'),
        'timer_upload_starts_in' => __('Upload starts in', 'fv'),

        'title_share' => __('Share with friends! ', 'fv'),
        'title_voting' => __('Voting!', 'fv'),
        'msg_voting' => __('Voting in process!', 'fv'),
        //TODO *Rename `title` into msg
        'title_voted' => __('Success! ', 'fv'),
        'msg_voted' => __('Your vote has been counted! ', 'fv'),
        'title_not_voted' => __('Warning! ', 'fv'),
        'msg_konkurs_end' => __('The contest has ended.', 'fv'),
        'msg_contest_not_started' => __('The contest has not started.', 'fv'),
        'msg_you_are_voted' => __('You have already voted for this photo!', 'fv'),
        'msg_cant_vote' => __('You already used all possible votes (*used_votes*) at this time and can vote in *hours_leave* hours!', 'fv'),
        'msg_cant_vote_anymore' => __('You have already used all possible votes (*used_votes*) in this contest!', 'fv'),
        'msg_not_authorized' => __('Please login/register to vote!', 'fv'),
        'msg_no_vote_permissions' => __('You have no enough permissions for vote!', 'fv'),
        'msg_cant_vote_unpublished' => __('You are not allowed to vote for not published photos!', 'fv'),

        'msg_own_photo_voting' => __('You are not allowed to vote for own photos!', 'fv'),

        'fb_vote_msg' => __('For vote please share page on Facebook', 'fv'),
        'msg_err' => __('An error has occurred. Please contact the administrator!', 'fv'),
        'invalid_token' => __('Security token is invalid - please refresh page!', 'fv'),

        'privacy_popup_title' => __('Privacy policy', 'fv'),
        'privacy_popup_text' => __('Please confirm that you have read and agree with our <a href="#0">privacy policy</a>.', 'fv'),
        'privacy_popup_label' => __('I have read and agree', 'fv'),


        'rate_popup_title' => __('Please rate', 'fv'),
        'rate_need_select' => __('Please select score for vote!', 'fv'),
        'rate_rating_caption' => __('Rating:', 'fv'),
        'rate_stars' => __('stars', 'fv'),

        'lead_table_rank' => __('Rank', 'fv'),
        'lead_table_photo' => __('Competitor', 'fv'),
        'lead_table_votes' => __('Total Votes', 'fv'),

        'winners_heading' => __('Contest winners', 'fv'),
        'winners_not_picked_heading' => __('Winners not yet picked!', 'fv'),
        'winners_place' => __('place', 'fv'),

        'form_subsr_title' => __('Verify you are a human', 'fv'),
        'form_subsr_msg' => __('Please verify, that you are not a bot:', 'fv'),
        'form_subsr_name' => __('Your name', 'fv'),
        'form_subsr_email' => __('Your email', 'fv'),
        'form_subsr_newsletter' => __('I want subscribe to newsletter', 'fv'),

        'form_subscr_msg_found_wrong' => __('We found this email in database but Name not equal entered Email!', 'fv'),
        'form_subscr_msg_errors' => __('Empty name or email!', 'fv'),
        'form_subscr_msg_invalid_email' => __('Invalid email!', 'fv'),

        'form_subscr_verify_send' => __('We send verification mail, please open it and click to confirmation link!', 'fv'),
        'form_subscr_verify_check_hash_btn' => __('Verify', 'fv'),
        'form_subscr_verify_already_send' => __('We already send verification mail, if you did\'t see it - please check Spam folder or contact with administrator!', 'fv'),
        'form_subscr_verify_change_mail_btn' => __('Change mail', 'fv'),
        'form_subscr_verify_error' => __('Sorry, but we can\'t verify your email!', 'fv'),
        'form_subscr_verify_success' => __('Your email successful verified!', 'fv'),
        'form_subscr_verify_already_done' => __('Seems email already confirmed!', 'fv'),

        'mail_subscr_verify_title' => __('Photo contest - please verify your email', 'fv'),
        'mail_subscr_verify_body' => __('Hi {name},' ."\n" .'For verify your email ({email}) please open it (in browser where you enter this email): {verify_link}' ."\n" .'Or enter this code into form: {verify_hash}', 'fv'),

        'form_soc_msg' => __('To be able to vote, you\'ll need to authorise with a social network!', 'fv'),

        'title_recaptcha_vote' => __('Solve reCAPTCHA please!', 'fv'),
        'msg_recaptcha_wrong' => __('Looks like you\'ve got it wrong!', 'fv'),

        'title_math_captcha_vote' => __('Verify that you are not a bot!', 'fv'),
        'msg_math_captcha' => __('Please solve Math Captcha!', 'fv'),
        'msg_math_captcha_wrong' => __('Wrong answer!', 'fv'),
        'msg_math_captcha_expired' => __('Captcha timeout!', 'fv'),


        'invite_friends' => __('Invite friends to help you win!', 'fv'),
        // Validation
        'download_no_image' => __('Please select image file!', 'fv'),
        'download_invaild_email' => __('The email provided is invalid, please check and try again.', 'fv'),
        'upload_form_invalid' => __('Please fill all required fields!', 'fv'),
        // Error
        'upload_error_title' => __('Some troubles with upload', 'fv'),
        'download_error' => __('An error occurred whilst downloading the picture(s). ', 'fv'),
        'download_limit' => __('You have already uploaded the picture, it may still be pending review. ', 'fv'),
        'download_limit_size' => __('Your photo is bigger than %LIMIT_SIZE%!', 'fv'),
        // Success
        'upload_success_title' => __('Successfully uploaded', 'fv'),
        'download_ok' => __('Thank you, your photo was successfully uploaded!', 'fv'),
        'download_moderation' => __('Thank you, your photo was successfully uploaded and will be reviewed shortly.', 'fv'),
        // Image size troubles
        'upload_dimensions_err' => __('Sorry, but this image does not fit required size. %INFO%', 'fv'),
        'upload_dimensions_smaller' => __('It %PARAM% must be smaller than %SIZE%', 'fv'),
        'upload_dimensions_bigger' => __('It %PARAM% must be bigger than %SIZE%', 'fv'),
        'upload_dimensions_height' => __('height', 'fv'),
        'upload_dimensions_width' => __('width', 'fv'),


        'contest_list_voting_active'        => __('Voting active until {date_finish}', 'fv'),
        'contest_list_voting_active_future' => __('Voting will active from {date_start} to {date_finish}', 'fv'),
        'contest_list_upload_active'        => __('Upload active until {upload_date_finish}', 'fv'),
        'contest_list_upload_active_future' => __('Upload will active from {upload_date_start} to {upload_date_finish}', 'fv'),
        'contest_list_upload_inactive'      => __('Upload inactive', 'fv'),
        'contest_list_is_finished'          => __('Contest has been finished at {date_finish}', 'fv'),
    );

}

/**
 * return messages for frontend from wordpress option
 * use this for hook it => https://codex.wordpress.org/Plugin_API/Filter_Reference/pre_option_(option_name)
 *
 * @return array get_option result
 */
function fv_get_public_translation_messages()
{
    //* TODO - need remove filter and write function to return just one value from array
    // like fv_get_transl_string('key')
    $translation = apply_filters( 'fv/translation/get_public_messages', get_option('fotov-translation') );
    if ( empty($translation) ) {
        return fv_get_default_public_translation_messages();
    }

    return wp_unslash($translation);
}

/**
 * return array key for frontend from wordress option
 * Used `wp_kses_data` for secure output and `stripcslashes`
 *
 * @param $key      string
 * @param $default  string
 *
 * @return string
 */
function fv_get_transl_msg($key, $default = '')
{
    if (!empty($key)) {
        $translation = fv_get_public_translation_messages();

        //$translation = fv_get_public_translation_messages();
        if (isset($translation[$key])) {
            return wp_kses_post(stripcslashes($translation[$key]));
            // doing some
        }
    }
    return $default;
}

/**
 * remove messages that not need in JS
 *
 * @param array $messages
 *
 * @return array get_option result
 */
function fv_prepare_public_translation_to_js($messages)
{
    unset($messages['toolbar_title_gallery']);
    unset($messages['toolbar_title_upload']);
    unset($messages['toolbar_title_details']);
    unset($messages['toolbar_title_sorting_newest']);
    unset($messages['toolbar_title_sorting_popular']);
    unset($messages['toolbar_title_sorting_unpopular']);
    unset($messages['toolbar_title_sorting_random']);
    unset($messages['toolbar_title_sorting_oldest']);
    
    unset($messages['upload_form_title']);
    unset($messages['upload_form_contest_finished']);
    unset($messages['upload_form_button_text']);
    unset($messages['upload_form_need_login']);
    unset($messages['leaders_title']);
    unset($messages['other_title']);

    unset($messages['shares_count_text']);

    unset($messages['mail_share_user_body']);
    unset($messages['mail_share_user_title']);

    unset($messages['contest_list_active']);
    unset($messages['contest_list_upload_opened_now']);
    unset($messages['contest_list_upload_opened_future']);
    unset($messages['contest_list_finished']);

    unset($messages['pagination_summary']);
    unset($messages['back_to_contest']);
    unset($messages['toolbar_title_sorting']);

    unset($messages['timer_days']);
    unset($messages['timer_hours']);
    unset($messages['timer_minutes']);
    unset($messages['timer_secs']);
    unset($messages['timer_voting_ends_in']);
    unset($messages['timer_voting_starts_in']);
    unset($messages['timer_upload_ends_in']);
    unset($messages['timer_upload_starts_in']);

    unset($messages['mail_subscr_verify_body']);
    unset($messages['mail_subscr_verify_title']);

    unset($messages['winners_heading']);
    unset($messages['winners_not_picked_heading']);
    unset($messages['winners_place']);

    unset($messages['privacy_popup_text']);

    foreach ($messages as $k => $msg) {
        $messages[$k] = stripslashes($msg);
    }

    $messages['img_load_fail'] = '-';
    $messages['ajax_fail'] = '-';
    $messages['inactive_contest'] = '-';
    $messages['empty_contest'] = '-';
    if ( FvFunctions::curr_user_can() ) {
        // Add some Public texts for Admin
        $messages['img_load_fail'] = __('Some errors with loading thumbnails.
            If you don\'t know why this happens - please go to Photo contest => Settings and
            change "Thumbnail retrieving type:", if this not helps, contact with support.', 'fv');

        $messages['ajax_fail'] = __('Some errors with voting.
            If you don\'t know why this happens - please go to *Photo contest* => *Settings* => *Vote* tab and
            disable "Use fast voting option? :", if this not helps, contact with plugin support or Hosting.', 'fv');

        $messages['inactive_contest'] = __('This contest not currently active (not started or already finished)!' .
        'If you want enable voting for users - click *Edit contest* in *Admin bar* and check Vote start & end date.', 'fv');

        $messages['empty_contest'] = __('This contest don\'t not have photos! Please click *Edit contest* in *Admin bar* and add first photo or enable countdown/upload!', 'fv');
    }

    return $messages;
}


/**
 * Get public translation key titles
 *
 * return array of option labels for translated messages
 *
 * @return array $key => $title
 */
function fv_get_public_translation_key_titles()
{

    $r = array(
        'general' => array(
            'tab_title' => __('General', 'fv'),
            'leaders_title' => __('Leaders title', 'fv'),
            'vote_button_text' => __('Vote text in button', 'fv'),
            'vote_count_text' => __('Vote count', 'fv'),
            'vote_rating_delimiter' => __('Rating text like 3 <strong>of</strong> 10', 'fv'),
            'vote_lightbox_text' => __('Vote text in image preview', 'fv'),
            'other_title' => __('Other photo title in image preview (in some themes)', 'fv'),
            'pagination_summary' => __('Pagination summary (please not remove %s)', 'fv'),
            'pagination_infinity' => __('Infinity scroll: Button text', 'fv'),
            'shares_count_text' => __('Shares count text', 'fv'),
        ),

        'single' => array(
            'tab_title' => __('Single photo', 'fv'),
            'single_share_heading' => __('sidebar: Share heading', 'fv'),
            'single_more_heading' => __('sidebar: More heading', 'fv'),
            'single_author_heading' => __('content: Author heading', 'fv'),
            'single_descr_heading' => __('content: Description heading', 'fv'),
            'back_to_contest' => __('Back to contest', 'fv'),
        ),
        'toolbar' => array(
            'tab_title' => __('Toolbar', 'fv'),
            'toolbar_title_gallery' => __('Gallery tab title', 'fv'),
            'toolbar_title_upload' => __('Upload tab title', 'fv'),
            'toolbar_title_details' => __('Description & Rules tab title', 'fv'),
            'toolbar_title_category' => __('Category filter title', 'fv'),
            'toolbar_title_sorting' => __('Sorting title', 'fv'),
            'toolbar_title_sorting_default' => __('Sorting > default', 'fv'),
            'toolbar_title_sorting_newest' => __('Sorting > newest', 'fv'),
            'toolbar_title_sorting_oldest' => __('Sorting > oldest', 'fv'),
            'toolbar_title_sorting_popular' => __('Sorting > popular', 'fv'),
            'toolbar_title_sorting_unpopular' => __('Sorting > unpopular', 'fv'),
            'toolbar_title_sorting_random' => __('Sorting > random', 'fv'),
            'toolbar_title_sorting_pseudo-random' => __('Sorting > pseudo-random', 'fv'),
            'toolbar_title_sorting_alphabetical-az' => __('Sorting > Alphabetical A-Z (by name)', 'fv'),
            'toolbar_title_sorting_alphabetical-za' => __('Sorting > Alphabetical Z-A (by name)', 'fv'),
        ),
        'search' => array(
            'tab_title' => __('Search', 'fv'),
            'search_no_results' => __('No results found!', 'fv'),
        ),

        'timer' => array(
            'tab_title' => __('Countdown', 'fv'),
            'timer_voting_ends_in' => __('"Voting ends in" text', 'fv'),
            'timer_voting_starts_in' => __('"Voting starts in" text (if not yet started)', 'fv'),
            'timer_upload_ends_in' => __('"Upload ends in" text', 'fv'),
            'timer_upload_starts_in' => __('"Upload starts in" text (if not yet started)', 'fv'),
            'timer_days' => __('Countdown > Days remaining', 'fv'),
            'timer_hours' => __('Countdown > Hours remaining', 'fv'),
            'timer_minutes' => __('Countdown > Minutes remaining', 'fv'),
            'timer_secs' => __('Countdown > Seconds remaining', 'fv'),
        ),
        'voting_heading' => array(
            'tab_title' => __('Voting', 'fv'),
            'is_heading' => true,
        ),
        'dialog_messages' => array(
            'tab_title' => __('Voting messages', 'fv'),
            'title_share' => __('Title > Go share', 'fv'),
            'title_voting' => __('Title > Voting in process', 'fv'),
            'msg_voting' => __('Msg with preloader > Voting in process', 'fv'),
            'title_voted' => __('Title > vote counted (1-2 words)', 'fv'),
            'title_not_voted' => __('Title > vote not counted (1-2 words)', 'fv'),
            'msg_voted' => __('Msg > vote counted', 'fv'),
            'msg_konkurs_end' => __('Msg > the contest has ended', 'fv'),
            'msg_contest_not_started' => __('Msg > the contest not started yet', 'fv'),
            'msg_you_are_voted' => __('Msg > user already voted for this photo (but can vote for other) <br/><small>Use *hours_leave* to show, when the user can vote, if voting frequency is once for 24 hrs:</small>', 'fv'),
            'msg_cant_vote' => __('Msg > user used all votes at this time <br/><small>Can use *hours_leave* and *used_votes*:</small>', 'fv'),
            'msg_cant_vote_anymore' => __('Msg > user used all votes and can\'t vote anymore (Voting frequency = Once) <br/><small>Can use *used_votes*:</small>', 'fv'),
            'msg_own_photo_voting' => __('Msg > vote for own photo not allowed', 'fv'),
            'msg_not_authorized' => __('Msg > Your are not authorized', 'fv'),
            'msg_no_vote_permissions' => __('Msg > You have no enough permissions (role)', 'fv'),
            'msg_cant_vote_unpublished' => __('Msg > Not allowed to vote for not published photos!', 'fv'),
            'msg_err' => __('Msg > error', 'fv'),
            'invite_friends' => __('Message under title with call to share', 'fv'),
            'invalid_token' => __('Message if WP security token is invalid and need refresh page', 'fv'),
        ),
        'rate' => array(
            'tab_title' => __('Vote : Rating', 'fv'),
            'rate_popup_title' => __('Popup title', 'fv'),
            'rate_need_select' => __('Message that need select rating before vote', 'fv'),
            'rate_rating_caption' => __('"Rating:" caption', 'fv'),
            'rate_stars' => __('"Stars" caption', 'fv'),
        ),
        'subscription_form' => array(
            'tab_title' => __('Vote + Subscription form', 'fv'),
            'form_subsr_title' => __('Popup title', 'fv'),
            'form_subsr_msg' => __('Popup message', 'fv'),
            'form_subsr_name' => sprintf(__('Field `%s` caption', 'fv'), __('name', 'fv')),
            'form_subsr_email' => sprintf(__('Field `%s` caption', 'fv'), __('email', 'fv')),
            'form_subsr_newsletter' => __('Subscribe to newsletter checkbox text (if enabled in Settings => Voting)', 'fv'),

            'tab_subtitle_1' => __('Email verification', 'fv'),

            'form_subscr_msg_found_wrong' => __('Popup msg - found email in database but Name not equal', 'fv'),
            'form_subscr_msg_errors' => __('Popup msg - Empty name or email', 'fv'),
            'form_subscr_msg_invalid_email' => __('Popup msg - Invalid email', 'fv'),

            'form_subscr_verify_send' => __('Popup msg - Verification mail send', 'fv'),
            'form_subscr_verify_check_hash_btn' => __('Popup btn text - Check verification key', 'fv'),
            'form_subscr_verify_already_send' => __('Popup msg - Verification mail Already send', 'fv'),
            'form_subscr_verify_change_mail_btn' => __('Popup btn text - Change mail', 'fv'),
            'form_subscr_verify_error' => __('Popup msg - Entered verification key is wrong', 'fv'),
            'form_subscr_verify_success' => __('Popup msg - Successful verified!', 'fv'),
            'form_subscr_verify_already_done' => __('Popup msg - if email already confirmed', 'fv'),

            'tab_subtitle_2' => __('Email verification', 'fv'),

            'tab_description' => 'Email body available tags:||'.
                '!!!!! <span style="color:red">From version 2.2.711 notifications must be configured in '
                . '<a href="' . admin_url('edit.php?post_type=notification') . '">Notifications menu</a></span> !!!!!. <br><br>'
                . ' <code>{name}</code>, <code>{email}</code>, <code>{verify_link}</code>, <code>{verify_hash}</code>',

            'mail_subscr_verify_title' => __('Subject - verification mail', 'fv'),
            'mail_subscr_verify_body' => __('Body - verification mail', 'fv'),

        ),

        'soc_authorization' => array(
            'tab_title' => __('Vote with Social login', 'fv'),
            'form_soc_msg' => __('Soc. authorization title', 'fv'),
        ),
        'math_captcha' => array(
            'tab_title' => __('Vote with Math Captcha', 'fv'),
            'title_math_captcha_vote' => __('Title > Solve Math Captcha please!', 'fv'),
            'msg_math_captcha' => __('Msg > Solve Math Captcha please!', 'fv'),
            'msg_math_captcha_wrong' => __('Msg > Seems you do it wrong!', 'fv'),
            'msg_math_captcha_expired' => __('Msg > Math Captcha expired!', 'fv'),
        ),
        'vote_with_facebook_share' => array(
            'tab_title' => __('Vote + Facebook Share', 'fv'),
            'fb_vote_msg' => __('Msg > for vote please share in FB', 'fv'),
        ),
        'vote_with_recaptcha' => array(
            'tab_title' => __('Vote with reCAPTCHA', 'fv'),
            'title_recaptcha_vote' => __('Title > Solve reCAPTCHA please!', 'fv'),
            'msg_recaptcha_wrong' => __('Msg > Seems you do it wrong!', 'fv'),
        ),

        'privacy' => array(
            'tab_title' => __('Vote : Policy agreement', 'fv'),
            'privacy_popup_title' => __('Popup title', 'fv'),
            'privacy_popup_text' => __('Message that need select rating before vote', 'fv'),
            'privacy_popup_label' => __('Text near checkbox', 'fv'),
        ),
        
        'upload_heading' => array(
            'tab_title' => __('Upload', 'fv'),
            'is_heading' => true,
        ),
        'upload_form' => array(
            'tab_title' => __('Upload form', 'fv'),
            'upload_form_title' => __('Upload form title', 'fv'),
            'upload_form_button_text' => __('Upload form button text', 'fv'),
            'upload_form_need_login' => __('User must be logged for upload <small>(%1$s will be replaced into Login link from wp_login_url(), %2$s will be replaced into Register link from wp_registration_url())</small>', 'fv'),
            'upload_form_contest_finished' => __('Message if contest upload dates expired or it has finished', 'fv'),
            'upload_user_do_not_have_permissions' => __('Message if user do not have enough role', 'fv'),
        ),
        'upload_messages' => array(
            //    $r = array_merge($r, array(
            'tab_title' => __('Upload messages', 'fv'),
            // ===========================
            'tab_subtitle_1' => __('Form Validation', 'fv'),
            'upload_form_invalid' => __('User email is invalid in form', 'fv'),
            'download_invaild_email' => __('User email is invalid in form', 'fv'),
            'download_no_image' => __('Upload warning, if not file passed', 'fv'),
            // ===========================
            'tab_subtitle_2' => __('Not uploaded (limit exceeded/error)', 'fv'),
            'upload_error_title' => __('[Popup title] Troubles with upload', 'fv'),
            'download_error' => __('[Popup message] Upload error', 'fv'),
            'download_limit' => __('[Popup message] User already downloaded photo', 'fv'),
            // ===========================
            'tab_subtitle_3' => __('Successfully uploaded', 'fv'),
            'upload_success_title' => __('[Popup title] Successfully uploaded', 'fv'),
            'download_ok' => __('[Popup message] Photo is uploaded', 'fv'),
            'download_moderation' => __('[Popup message] Photo is uploaded and need review', 'fv'),
            // ===========================
            'tab_subtitle_4' => __('Image dimensions invalid', 'fv'),
            'download_limit_size' => __('User photo is bigger than limit <small>(%LIMIT_SIZE% will be replaced into `limit value` in megabytes, %FILE_NAME% into file name)</small>', 'fv'),
            'upload_dimensions_err' => __('Message, that image does not fit required size (<small>%INFO% will be replaced into text below (smaller or bigger)</small>)', 'fv'),
            'upload_dimensions_smaller' => __('Image must be smaller than size (<small>%SIZE% will be replaced into size from settings, as example 500px., %PARAM% into "height" or "width"</small>).', 'fv'),
            'upload_dimensions_bigger' => __('Image must be bigger than size (<small>%SIZE% will be replaced into size from settings, as example 500px., %PARAM% into "height" or "width"</small>).', 'fv'),
            'upload_dimensions_height' => __('Height %PARAM%', 'fv'),
            'upload_dimensions_width' => __('Width %PARAM%', 'fv'),
        ),
        'mail_messages' => array(
            'tab_title' => __('Notify mail messages', 'fv'),
            'tab_description' => 'Email body available tags:||'
                . '!!!!! <span style="color:red">From version 2.2.600 notifications must be configured in '
                    . '<a href="' . admin_url('edit.php?post_type=notification') . '">Notifications menu</a></span> !!!!!. <br><br>',

        ),

        'extra_heading' => array(
            'tab_title' => __('Extras', 'fv'),
            'is_heading' => true,
        ),
        'leaders' => array(
            'tab_title' => __('Leaders table', 'fv'),
            'lead_table_rank' => __('Leaders table > Rank', 'fv'),
            'lead_table_photo' => __('Leaders table > Competitor', 'fv'),
            'lead_table_votes' => __('Leaders table > Total Votes', 'fv'),
        ),
        'winners' => array(
            'tab_title' => __('Winners list', 'fv'),
            'winners_heading' => __('Winners heading', 'fv'),
            'winners_not_picked_heading' => __('"Winners not yet picked" heading', 'fv'),
            'winners_place' => __('Winner "place" caption, like "1 place", etc', 'fv'),
        ),
        'contest_list' => array(
            'tab_title' => __('Contest list', 'fv'),

            'contest_list_voting_active'        => __('Contest block text - Voting active <small>(use: {date_finish})</small>', 'fv'),
            'contest_list_voting_active_future' => __('Contest block text - Voting active future <small>(use: {date_start}, {date_finish})</small>', 'fv'),

            'contest_list_upload_active' => __('Contest block text - Upload active <small>(use: {upload_date_finish})</small>', 'fv'),
            'contest_list_upload_active_future' => __('Contest block text - Upload active future <small>(use: {upload_date_start}, {upload_date_finish})</small>', 'fv'),
            'contest_list_upload_inactive' => __('Contest block text - Upload inactive', 'fv'),

            'contest_list_is_finished' => __('Contest block text - Contest finished <small>(use: {date_finish})</small>', 'fv'),
        ),
    );

    return $r;
}

/**
 * return Fields, than need to show as textarea
 *
 * @return array
 */
function fv_get_public_translation_textareas()
{
    return apply_filters('fv/translation/get_public_textareas',
        array(
//            'mail_subscr_verify_body',
//            'mail_upload_user_body',
//            'mail_approve_user_body',
//            'mail_delete_user_body',
//            'mail_upload_admin_body',
//            'mail_share_user_body',
            'upload_form_need_login',
            'mail_contest_finish_admin_body',
            'upload_form_contest_finished',
            'upload_user_do_not_have_permissions',
            'privacy_popup_text',
        )
    );
}

/**
 * save messages for frontend edited with user
 *
 * @param array $messages
 *
 * @return bool update_option result
 */
function fv_update_public_translation_messages($messages)
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    return update_option('fotov-translation', $messages);

}

/**
 * add default messages for frontend translated with i18n into wordpress database
 *
 * @return bool add_option result
 */
function fv_add_public_translation_messages()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    return add_option('fotov-translation', fv_get_default_public_translation_messages(), '', 'no');
}

/**
 * used on plugin Install / Update
 *
 * @return bool add_option result
 */
function fv_update_exists_public_translation_messages()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }
    $current = fv_get_public_translation_messages();
    $default = fv_get_default_public_translation_messages();
    $have_diff = false;

    if (empty($current)) {
        fv_add_public_translation_messages();
    }
    foreach ($default as $KEY => $TEXT) {
        if (empty($current[$KEY])) {
            $current[$KEY] = $TEXT;
            $have_diff = true;
        }
    }
    if ($have_diff) {
        return fv_update_public_translation_messages($current);
    }

    return true;
}

/**
 * Reset to default messages for frontend translated with i18n into wordpress database
 *
 * @return void
 */
function fv_reset_public_translation()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    delete_option('fotov-translation');
    fv_add_public_translation_messages();
    wp_add_notice("WP Foto Vote:: translation has been reset.", 'warning');
}
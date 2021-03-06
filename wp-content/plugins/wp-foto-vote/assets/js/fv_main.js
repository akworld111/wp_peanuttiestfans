+(function($) {

    var FvVote = {};

// Global variable for save Image ID
    window.fv_current_id = -1;
    window.fb_post_id = 0;
    // not clicking
    var fv_go = {};

    window.fv_subscribed = false;
    // evercookie UID
    window.fv_uid = '';
    // parmeter, that user must see message - solve Captcha, not you do it wrong
    window.fv_reCAPTCHA_first = true;

    window.fv_vote = function (id, action, el, rating) {
        window.sv_vote(id, action, el, rating);
    }
    
    function _is_privacy_modal_required() {
        return fv.vote_show_privacy_modal && ! FvLib.readCookie( "fv_agree_with_privacy_ct" + fv.data[window.fv_current_id].ct_id );
    }
    
//** Voting function
    window.sv_vote = function(id, action, el, rating) {

        if ( action == undefined || !action ) {
            FvModal.goVoteConfirm();
            return false;
            //action = 'vote';
        }

        window.fv_current_id = id;        
        
        if ( _is_privacy_modal_required() ) {
            FvModal.goPrivacyPolicyAgree();
            return false;
        }

        // Vars
        var status = "error",
            title = fv.lang.title_not_voted,
            msg = "",
            subtitle = "",
            security_type = "",
            fv_go_key = "";

        security_type = fv.ct[fv.data[window.fv_current_id].ct_id].voting_security_ext;
        var limit_by_user = fv.ct[fv.data[window.fv_current_id].ct_id].limit_by_user;


        if (fv.ct[fv.data[window.fv_current_id].ct_id].voting_max_count == 1) {
            fv_go_key = fv.data[window.fv_current_id].ct_id;
        } else {
            fv_go_key = fv.data[window.fv_current_id].ct_id + "_" + window.fv_current_id;
        }

        // If already voting - do not do anything!
        if ( !fv_go[fv_go_key] ) {
            // check subscription
            if (security_type == "subscribe") {
                fv_check_is_email_subscribed();
            }

            if (security_type == "wp_social_login" && !fv.user_id) {
                FvModal.goStartSocialLogin();
                return;
            }

            if ((security_type == "none" || security_type == "mathCaptcha" || security_type == "registered" || security_type == "fbShare" || security_type == "subscribe")
                || action == "subscribe"
                || action == "check"
                || action == "rate"
                || action == "captcha"
                || security_type != "none" && fv_subscribed
                || fv_check_is_subscribed(action, security_type)
            ) {

                // action before voting
                if (!FvLib.callHook('fv/start_voting', security_type, fv_subscribed, action, window.fv_current_id)) {
                    return false;
                }

                if (fv.ct[fv.data[id].ct_id].voting_type == "rate" && action == "captcha") {
                    FvModal.goRate(id);
                    return;
                }

                //if ( fv.ct[window.fv_current_id].voting_type == "rate" && (rating == undefined || rating === 0) ) {
                if (fv.ct[fv.data[id].ct_id].voting_type == "rate" && action != "rate") {
                    action = "check";
                }

                // Check if user vote for OWN photo (if enabled)
                if (fv.restrict_vote_for_own && (fv.data[id].user_id && fv.user_id && fv.user_id == fv.data[id].user_id)) {
                    title = fv.lang.title_not_voted;
                    msg = fv.lang.msg_own_photo_voting;
                    // Show modal
                    FvModal.goVoted(status, title, msg, "  ", id);
                    return;
                }

                if (action != "check" && action != "captcha") {
                    FvModal.goStartVote(window.fv_current_id);
                }

                FvLib.logSave("start voting!");

                var send_data = {
                    action: "vote",
                    contest_id: fv.data[window.fv_current_id].ct_id,
                    vote_id: id,
                    post_id: fv.post_id,
                    rr: fv_get_first_rfr(),
                    uid: fv_uid,
                    pp: fv_whorls['pp'],
                    ff: fv_whorls['ff'],
                    fuckcache: FvLib.randomStr(8),
                    some_str: fv.some_str
                };

                if (!send_data.uid && FvLib.readCookie('fv_uid')) {
                    send_data.uid = FvLib.readCookie('fv_uid');
                }

                if (action == "rate" && rating != undefined) {
                    send_data['rating'] = rating;
                }

                if (action != "check" && security_type == "reCaptcha") {
                    if ((fv.recaptcha_session == true && fv_subscribed == true && fv.recaptcha_session_ready == false)
                        || fv.recaptcha_session == false) {
                        send_data['recaptcha_response'] = grecaptcha.getResponse(FvModal.voteRecaptchaID);
                    }
                    if (fv.recaptcha_session == false) {
                        fv_subscribed = false;
                    }
                }

                if (security_type == "mathCaptcha") {
                    if (action != "captcha" && action != "rate") {
                        // Make sure sure that user can vote + generate Math Captcha HTML
                        action = "check";
                    } else {
                        send_data['math_captcha'] = jQuery(".sw-vote-math-captcha #mc-input").val();
                        FvModal.setVotingNotificationWithSpinner();
                    }
                }


                if (security_type == "subscribe") {
                    if (!fv_subscribed) {
                        send_data['check'] = true;
                    } else {
                        send_data['subscribe_hash'] = fv_check_is_email_subscribed();
                    }
                }

                if (security_type == "fbShare" && action == "fb_shared") {
                    send_data['fb_post_id'] = fb_post_id;
                } else if (security_type == "fbShare") {
                    fb_post_id = 0;
                    send_data["check"] = true;
                }

                // If need check or if Security is "defaultAsubscr"
                // and we must check if need show to user Subscribe Modal
                if (action == "check") {
                    send_data["check"] = true;
                }

                send_data = FvLib.applyFilters('fv/vote/send_data', send_data);
                if (!fv.fast_ajax) {
                    var fv_ajax_url = fv.ajax_url;
                } else {
                    var fv_ajax_url = fv.plugin_url + '/ajax.php';
                }

                $.post(
                    FvLib.add_query_arg('vote_id', send_data.vote_id, fv_ajax_url),
                    send_data,
                    function (data) {
                        data = FvLib.parseJson(data);
                        // if Voting for just one Photo and response not related with reCAPTHCA
                        if ("once" == fv.ct[data.ct_id].voting_frequency && data.res == 2 || data.res == 3) {
                            if (fv.ct[data.ct_id].voting_max_count == 1) {
                                fv_go[data.ct_id] = true;
                            } else {
                                fv_go[data.ct_id + "_" + data.contestant_id] = true;
                            }

                        }
                        var status = "error",
                            title = fv.lang.title_not_voted,
                            msg = "";

                        if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) {
                            FvLib.newImg(document);
                            return;
                        }

                        //** apply filters for retrieved data
                        data = FvLib.applyFilters('fv/vote/get_data', data);
                        // fix for Check action
                        if (typeof data.no_process == "string") {
                            return;
                        }

                        //$('#sv_dialog #info .slogan').text(fv.lang.invite_friends);
                        if (data.res == 98) {
                            // Invalid security token
                            alert(fv.lang.invalid_token);
                            return false;
                        } else if (data.res == 1) {
                            if (data.new_votes) {
                                fv_set_votes_count(data.contestant_id, data.new_votes);
                                fv.data[data.contestant_id].votes_count = data.new_votes;
                            }
                            // Если же человек не голосовал, то напмшем что голос учтен, и попросим лайкнуть
                            title = fv.lang.title_voted;
                            msg = fv.lang.msg_voted;
                            status = "success";
                            if (fv.ct[data.ct_id].voting_security_ext == "reCaptcha") {
                                fv.recaptcha_session_ready = true;
                            }
                        } else if (data.res == 2 || data.res == 22) // used all votes
                        {
                            // If can vote later
                            if (data.res == 22) {
                                // Can't vote anymore in this contest
                                msg = fv.lang.msg_cant_vote_anymore;
                            } else if (data['hours_leave']) {
                                msg = fv.lang.msg_cant_vote.replace("*hours_leave*", data.hours_leave);
                            } else {
                                if ("once" == fv.ct[data.ct_id].voting_frequency) {
                                    // Can't vote anymore in this contest
                                    msg = fv.lang.msg_cant_vote_anymore;
                                } else {
                                    msg = fv.lang.msg_cant_vote;
                                }
                            }
                            msg = msg.replace("*used_votes*", data.used_votes);


                        } else if (data.res == 3) // has voted for this photo
                        {
                            // Если человек уже голосовал, сообщим ему об этом
                            msg = fv.lang.msg_you_are_voted;

                        } else if (data.res == 44) // date not started
                        {
                            // Конкурс закончился
                            msg = fv.lang.msg_contest_not_started;
                        } else if (data.res == 4) // date end
                        {
                            // Конкурс закончился
                            msg = fv.lang.msg_konkurs_end;
                        } else if (data.res == 5) // not authorized;
                        {
                            msg = fv.lang.msg_not_authorized;
                            // IF Login And Register Modal Popup plugin installed - open modal
                            if (typeof LRM != "undefined") {
                                //LRM.reload_after_login = false;
                                FvModal.close();
                                jQuery(".lrm-form-message--init").html(msg);
                                jQuery(document).trigger("lrm_show_signup");
                                return;
                            }
                        } else if (data.res == 8) // no permissions (role);
                        {
                            title = fv.lang.title_not_voted;
                            msg = fv.lang.msg_no_vote_permissions;
                        } else if (data.res == 9) // not published
                        {
                            title = fv.lang.title_not_voted;
                            msg = fv.lang.msg_cant_vote_unpublished;
                        } else if (data.res == 6) // wrong reCAPTCHA
                        {
                            FvModal.goRecaptchaVote(window.fv_current_id, true);
                            return false;
                        } else if (data.res == 7) // own photo voting
                        {
                            title = fv.lang.title_not_voted
                            msg = fv.lang.msg_own_photo_voting;
                        } else if (data.res == "can_vote" && fv.ct[data.ct_id].voting_security_ext == "mathCaptcha" && data.captcha_html) {
                            FvModal.goMathCaptchaVote(data.contestant_id, "", data.captcha_html);
                            return false;
                        } else if (data.res == 11 || data.res == 12) // expired or wrong mathCaptcha!!
                        {
                            msg = fv.lang.msg_math_captcha_wrong;
                            if (data.res == 12) {
                                msg = fv.lang.msg_math_captcha_expired;
                            }
                            FvModal.goMathCaptchaVote(data.contestant_id, msg, data.captcha_html);
                            return false;
                        } else if (data.res == 66) // need reCAPTCHA
                        {
                            // if Enabled Save reCAPTCHA Session and we don't have session
                            fv.recaptcha_session_ready = false;
                            FvModal.goRecaptchaVote(data.contestant_id, true && !fv_reCAPTCHA_first);
                            fv_reCAPTCHA_first = false;
                            return false;
                        } else if (data.res == "can_vote" || data.res == "need_subscribe") // not authorized;
                        {
                            // If user can Vote and mode = starts rating
                            if (data.res == "can_vote" && fv.ct[data.ct_id].voting_type == "rate") {
                                FvModal.goRate(data.contestant_id);
                                //sv_vote(data.contestant_id, "rate");
                                return false;
                            }
                            fv_subscribed = false;
                            if (fv.ct[data.ct_id].voting_security_ext == "subscribe") {
                                fv_check_is_subscribed("subscribe");
                            } else {
                                FvModal.goFbVote();
                            }
                            return false;
                        } else {
                            // Что-то непонятное
                            msg = fv.lang.msg_err;
                        }
                        /*
                         if (data.user_country && !FvLib.readCookie('user_country')) {
                         FvLib.createCookie('user_country', data.user_country, 99);
                         }
                         */
                        //** apply filters for Modal data
                        status = FvLib.applyFilters('fv/vote/modal_status', status);
                        title = FvLib.applyFilters('fv/vote/modal_title', title, data);
                        msg = FvLib.applyFilters('fv/vote/modal_msg', msg, data);
                        subtitle = FvLib.applyFilters('fv/vote/modal_subtitle', subtitle);

                        // Show modal
                        FvModal.goVoted(status, title, msg, subtitle, data.contestant_id);

                        if (eval("typeof fv_hook_end_voting") === 'function') {
                            fv_hook_end_voting(data);
                        }
                        // action before voting
                        FvLib.callHook('fv/end_voting', fv.ct[data.ct_id].voting_security_ext, data);

                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        FvLib.adminNotice(fv.lang.ajax_fail, 'error');

                        alert("An error occurred [" + errorThrown + ":" + jqXHR.status + "], please contact with administrator... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");

                        if (window.console == undefined) {
                            return;
                        }
                        console.log('statusCode:', jqXHR.status);
                        console.log('errorThrown:', errorThrown);
                        console.log('responseText:', jqXHR.responseText);
                });
            }
        } else {
            // Если человек уже точно голосовал, нажимает 2-рой раз
            FvModal.goVoted(status, title, fv.lang.msg_you_are_voted, subtitle, window.fv_current_id);
        }
    }

    window.fv_check_is_email_subscribed = function() {
        // Check local storage
        if (FvLib.isSupportsHtml5Storage() && localStorage.getItem("fv_subscribed_" + fv.data[window.fv_current_id].contest_id)) {
            fv_subscribed = true;
            //sv_vote(window.fv_current_id);
            console.log("fv_subscribed: Found in Storage");
            return localStorage.getItem("fv_subscribed_" + fv.data[window.fv_current_id].contest_id);
        }
        return false;
    }

    function fv_get_first_rfr() {
        // Check local storage
        var rfr = document.referrer ? btoa(document.referrer) : "-";
        if (FvLib.isSupportsHtml5Storage()) {
            if (!localStorage.getItem("fv_rr")) {
                localStorage.setItem("fv_rr", rfr);
            } else {
                return localStorage.getItem("fv_rr");
            }
        }
        return rfr;
    }

    fv_get_first_rfr();

    /**
     * Runs custom checks actions in Voting process
     *
     * Uses for decrease code size in main Vote function
     */
    window.fv_check_is_subscribed = function (action, security_type) {
        //&& action != 'subscribe'
        if (FvLib.filterExists('fv/vote/before_start_voting')) {
            return FvLib.applyFilters('fv/vote/before_start_voting', false, action);
        }

        if (security_type == "reCaptcha") {
            if (fv.recaptcha_session == false) {
                FvModal.goRecaptchaVote();
            } else {
                // try vote to check reCAPTCHA session
                return true;
            }
            // }else if ( security_type == "mathCaptcha" ) {
            //     FvModal.goMathCaptchaVote();
        } else if (!fv_subscribed) {
            if (!fv.fast_ajax) {
                var fv_ajax_url = FvLib.add_query_arg("action", "fv_is_subscribed", fv.ajax_url);
            } else {
                var fv_ajax_url = FvLib.add_query_arg("action", "fv_is_subscribed", fv.plugin_url + "/ajax.php");
            }

            //console.log({'contest_id':fv.contest_id});
            $.post(
                fv_ajax_url,
                {
                    action: 'fv_is_subscribed',
                    contest_id: fv.data[window.fv_current_id].ct_id,
                    uid: fv_uid,
                    fuckcache: FvLib.randomStr(8),
                    subscribe_hash: fv_check_is_email_subscribed()
                },
                function (response) {
                    if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;

                    response = FvLib.parseJson(response);
                    //console.log("is_subscribed: ");
                    //** apply filters to retrieved data
                    response = FvLib.applyFilters('fv/before_start_voting/get_data', response);
                    var security_type = fv.ct[response.ct_id].voting_security_ext;

                    if (response.success) {
                        fv_subscribed = true;
                        if (FvLib.isSupportsHtml5Storage() && response.subscribe_hash) {
                            localStorage.setItem('fv_subscribed_' + response.ct_id, response.subscribe_hash);
                        }
                        sv_vote(window.fv_current_id);
                    } else if ( response.result && "warning" == response.result ) {
                        FvModal.goWarning( fv.lang.title_not_voted, response.message );
                    } else {
                        // User need to be verified
                        fv_subscribed = false;

                        if (security_type == "social") {
                            FvModal.goStartSocialAuthorization();
                        } else if (security_type == "subscribe") {
                            if (!response.result) {
                                FvModal.goStartSubscribe();
                            } else if (response.result = "verify_send") {
                                FvModal.goSubscribeVerifySend(true);
                            }

                        }

                    }
                }
            ).fail(function () {
                FvLib.adminNotice(fv.lang.ajax_fail, 'error');
            });
            ;  // AJAX get :: END
        }
        return false;
    }

    window.fv_recaptcha_ready = function (response) {
        fv_subscribed = true;
        sv_vote(window.fv_current_id, 'vote');
    };




    /*
     * Check form and Send vote
     */
    window.fv_run_subscribe_by_email = function (form) {
        if (FvModal.isVisible()) {
            var nameEl = form.querySelector(".fv-name"),
                emailEl = form.querySelector(".fv-email"),
                newsletterEl = form.querySelector(".fv-newsletter-checkbox"),
                valid = true;
            // Check name
            if (nameEl == undefined || nameEl.value.trim().length <= 2) {
                jQuery(nameEl).closest(".frm-input").addClass("is-error");
                valid = false;
            } else {
                jQuery(nameEl).closest(".frm-input").removeClass("is-error");
            }
            // Check email
            if (emailEl == undefined || !FvLib.isValidEmail(emailEl.value.trim())) {
                jQuery(emailEl).closest(".frm-input").addClass("is-error");
                valid = false;
            } else {
                jQuery(emailEl).closest(".frm-input").removeClass("is-error");
            }

            if ( fv.recaptcha_subscribe && fv.recaptcha_key && ! grecaptcha.getResponse(FvModal.subscribeRecaptchaID) ) {
                FvModal.showNotification("error", "", fv.lang.title_recaptcha_vote);
                valid = false;
            }

            // Send vote
            if (valid && FvLib.applyFilters('fv/subscribe_validate', true, nameEl.value, emailEl.value)) {
                //console.log({'contest_id':fv.contest_id});

                var fv_ajax_url = FvLib.add_query_arg("action", "fv_email_subscribe", fv.ajax_url);

                var fd = new FormData(form);
                fd.append("action", "fv_email_subscribe");
                fd.append("fuckcache", FvLib.randomStr(8));
                fd.append("some_str", fv.some_str);
                fd.append("contest_id", fv.data[window.fv_current_id].contest_id);
                fd.append("contestant_id", window.fv_current_id);

                jQuery.ajax({
                    type: "POST",
                    url: fv_ajax_url,
                    data: fd,
                    success: function (response) {

                        if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;

                        response = FvLib.parseJson(response);
                        //console.log("is_subscribed: ");
                        //** apply filters for retrieved data
                        response = FvLib.applyFilters('fv/email_subscribe/ready', response);

                        if ( response.reset_recaptcha ) {
                            FvModal.showSubscribeReCaptcha();
                         }

                        if (response.success) {
                            if ( ! response.reset_recaptcha ) {
                                FvModal.showSubscribeReCaptcha();
                            }

                            if (!response.need_verify) {
                                fv_subscribed = true;
                                if (FvLib.isSupportsHtml5Storage()) {
                                    localStorage.setItem('fv_subscribed_' + response.contest_id, response.hash);
                                }
                                sv_vote(window.fv_current_id);
                            } else {
                                fv_subscribed = false;
                                FvModal.goSubscribeVerifySend(response.verify_send);
                            }
                        } else {
                            // If can't subscribe
                            fv_subscribed = false;
                            FvModal.showNotification("error", "", response.message);
                        }

                    },
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    error: function(jqXHR, textStatus, errorThrown) {
                        //FvLib.adminNotice(fv.lang.ajax_fail, 'error');

                        alert("An error occurred, please contact with administrator... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");

                        if (window.console == undefined) {
                            return;
                        }
                        console.log('statusCode:', jqXHR.status);
                        console.log('errorThrown:', errorThrown);
                        console.log('responseText:', jqXHR.responseText);

                    }
                });
            }
        }
        return false;
    }

    /*
     * Check form and Send vote
     */
    window.fv_verify_subscribe_hash = function(form) {
        if (FvModal.isVisible() || FvLib.queryString('hash')) {
            var hashEl = form.querySelector(".fv-hash");
            // Check name
            if (hashEl == undefined || hashEl.value.trim().length <= 10) {
                jQuery(hashEl).closest(".frm-input").addClass("is-error");
                return false;
            } else {
                jQuery(hashEl).closest(".frm-input").removeClass("is-error");
            }

            // Send vote
            if (FvLib.applyFilters('fv/subscribe_verify_run', true, hashEl.value)) {
                //console.log({'contest_id':fv.contest_id});
                if (!fv.fast_ajax) {
                    var fv_ajax_url = FvLib.add_query_arg("action", "fv_subscribe_verify", fv.ajax_url);
                } else {
                    var fv_ajax_url = FvLib.add_query_arg("action", "fv_subscribe_verify", fv.plugin_url + "/ajax.php");
                }

                $.post(
                    fv_ajax_url,
                    {
                        action: 'fv_subscribe_verify',
                        fv_hash: hashEl.value,
                        fuckcache: FvLib.randomStr(8),
                        some_str: fv.some_str
                    },
                    function (response) {
                        if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
                        response = FvLib.parseJson(response);
                        //** apply filters for retrieved data
                        response = FvLib.applyFilters('fv/subscribe_verify/ready', response);

                        if (response.success) {
                            fv_subscribed = true;

                            if (response.hash && FvLib.isSupportsHtml5Storage()) {
                                localStorage.setItem('fv_subscribed_' + response.contest_id, response.hash);
                            }

                            FvModal.setTitle(fv.lang.form_subsr_title);
                            FvModal.openWidget("empty");
                            FvModal.$el.width(400);
                            FvModal.showNotification("success", "", fv.lang.form_subscr_verify_success, 0, 0);

                        } else {
                            // If can't subscribe
                            fv_subscribed = false;

                            if ( ! FvModal.isVisible() ) {
                                FvModal.setTitle(fv.lang.form_subsr_title);
                                FvModal.goSubscribeVerifySend(true);
                            }
                            FvModal.showNotification("error", "", response.message);
                        }

                        var new_url = FvLib.add_query_arg("hash", "", window.location.href);
                        new_url = FvLib.add_query_arg("fv-action", "", new_url);
                        window.history.replaceState({}, document.title, new_url);

                    }
                ).fail(function () {
                    FvLib.adminNotice(fv.lang.ajax_fail, 'error');
                });
                ;  // AJAX get :: END

                //sv_vote(window.fv_current_id, 'subscribe');
            }
        }
        return false;
    }

    /**
     * Increase votes count in Html element with specified ID above image after voting
     */
    window.fv_set_votes_count = function(id, new_count) {
        $('.sv_votes_' + id).text(new_count);
    }

    /**
     * Function used for Check Fb login state and run subscribe
     */
    window.fv_fb_login = function() {
        var fb_auth_response = FB.getAuthResponse();
        if ( fb_auth_response != null) {
            fv_soc_login( "facebook", fb_auth_response.accessToken );
            return;
        }
        // try log In
        FB.login(function (response) {
            //do whatever you need to do after a (un)successfull login
            if (response.status == 'connected') {
                // the user is logged in and has authenticated your APP
                console.log(response);
                fv_soc_login( "facebook", FB.getAuthResponse().accessToken );

            } else if (response.status == 'not_authorized') {
                // the user is logged in to Facebook,
                // but has not authenticated your app
                alert('not_authorized');
            } else {
                // the user isn't logged in to Facebook.
                alert('the user isn`t logged in to Facebook.');
            }

        }, {scope: 'public_profile,email'});
    }

    /**
     * Function used for Check VK login state and run subscriblogin
     */
    window.fv_vk_login = function() {

        VK.Auth.login(function(response2) {
            console.log(response2);
            if (response2.session) {
                /* Пользователь успешно авторизовался */
                fv_soc_login( "vk", response2.session.user );
            } else {
                /* Пользователь нажал кнопку Отмена в окне авторизации */
                alert('Please authorize app for vote!');
            }
        });
        
    }

    /**
     * Run social login
     * @since 2.3.00
     */
    window.fv_soc_login = function( social, data ) {
        
        var send_data = {
            'action': 'fv_soc_login',
            'contest_id': fv.contest_id,
            'fuckcache': FvLib.randomStr(8),
            'some_str': fv.some_str,
            'soc_network': social,
            'data': data,
        };

        $.post(fv.ajax_url, send_data,
            function (data) {
                if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
                data = FvLib.parseJson(data);
                if ( data.success ) {
                    fv_subscribed = true;
                    sv_vote(window.fv_current_id);
                } else if ( data.message ) {
                    FvModal.goWarning( fv.lang.title_not_voted, data.message );
                } else {
                    alert( "Error happens during social login!" );
                }
            });
    
    }

    function fv_identify_plugins() {
        // fetch and serialize plugins
        var plugins = "";
        // in Mozilla and in fact most non-IE browsers, this is easy
        if (navigator.plugins) {
            var np = navigator.plugins;
            var plist = new Array();
            // sorting navigator.plugins is a right royal pain
            // but it seems to be necessary because their order
            // is non-constant in some browsers
            for (var i = 0; i < np.length; i++) {
                plist[i] = np[i].name + "; ";
                plist[i] += np[i].description + "; ";
                plist[i] += np[i].filename + ";";
                for (var n = 0; n < np[i].length; n++) {
                    plist[i] += " (" + np[i][n].description + "; " + np[i][n].type +
                        "; " + np[i][n].suffixes + ")";
                }
                plist[i] += ". ";
            }
            plist.sort();
            for (i = 0; i < plist.length; i++) {
                plugins += i + ": " + plist[i];
            }
        }
        // in IE, things are much harder; we use PluginDetect to get less
        // information (only the plugins listed below & their version numbers)
        if (plugins == "") {
            var pp = new Array();
            pp[0] = "Java";
            pp[1] = "QuickTime";
            pp[2] = "DevalVR";
            pp[3] = "Shockwave";
            pp[4] = "Flash";
            pp[5] = "WindowsMediaplayer";
            pp[6] = "Silverlight";
            pp[7] = "VLC";
            var version;
            for (p in pp) {
                version = PluginDetect.getVersion(pp[p]);
                if (version)
                    plugins += pp[p] + " " + version + "; "
            }
            plugins += fv_ieAcrobatVersion();
        }
        return plugins;
    }

    function fv_ieAcrobatVersion() {
        // estimate the version of Acrobat on IE using horrible horrible hacks
        if (window.ActiveXObject) {
            for (var x = 2; x < 10; x++) {
                try {
                    oAcro = eval("new ActiveXObject('PDF.PdfCtrl." + x + "');");
                    if (oAcro)
                        return "Adobe Acrobat version" + x + ".?";
                } catch (ex) {
                }
            }
            try {
                oAcro4 = new ActiveXObject('PDF.PdfCtrl.1');
                if (oAcro4)
                    return "Adobe Acrobat version 4.?";
            } catch (ex) {
            }
            try {
                oAcro7 = new ActiveXObject('AcroPDF.PDF.1');
                if (oAcro7)
                    return "Adobe Acrobat version 7.?";
            } catch (ex) {
            }
            return "";
        }
    }


// fetch client-side vars
    var fv_whorls = {};


    setTimeout(function () {
        // this is a backup plan
        if (fv_whorls.pp !== undefined) {
            return;
        }

        try {
            fv_whorls.pp = FvLib.murmurhash3_32_gc(fv_identify_plugins(), 991);
        } catch (ex) {
            console.log(ex);
            fv_whorls.pp = 0;
            FvLib.logSave("plugins - permission denied")
        }

        //fv_whorls['fonts'] = get_fonts();
    }, 500);

    FvLib.addFilter('fv/vote/send_data', function (send_data) {
        send_data['ds'] = btoa(window.screen.availWidth + "x" + window.screen.availHeight);

        var event = window.event;
        if (event) {
            // If pageX/Y aren't available and clientX/Y are,
            // calculate pageX/Y - logic taken from jQuery.
            // (This is to support old IE)
            if (event.pageX == null && event.clientX != null) {
                eventDoc = (event.target && event.target.ownerDocument) || document;
                doc = eventDoc.documentElement;
                body = eventDoc.body;

                event.pageX = event.clientX +
                    (doc && doc.scrollLeft || body && body.scrollLeft || 0) -
                    (doc && doc.clientLeft || body && body.clientLeft || 0);
                event.pageY = event.clientY +
                    (doc && doc.scrollTop || body && body.scrollTop || 0) -
                    (doc && doc.clientTop || body && body.clientTop || 0 );
            }
            send_data['m' + 's'] = btoa(event.pageX + "x" + event.pageY);
        }

        return send_data;
    }, 10, 1);

    jQuery(".sw-privacy-form").submit(function() {
        if ( ! jQuery(".fv-privacy-agree-checkbox").prop("checked") ) {
            return false;
        }
        FvLib.createCookie( "fv_agree_with_privacy_ct" + fv.data[window.fv_current_id].ct_id, Date.now() );
        fv_vote( window.fv_current_id );
        return false;
    });


    jQuery(".sw-voteconfirm-form").submit(function() {
        sv_vote( window.fv_current_id, "vote" );
        return false;
    });



    /**
     * Function used for Check VK login state and run subscriblogin
     */
    window.fv_gp_login = function() {

        fv.auth2.signIn().then( function(googleUser) {
            fv_soc_login( "google", googleUser.getAuthResponse().id_token );
        } );

    }

    jQuery("[data-fvsociallogin]").click(function() {

        var network = $(this).data("fvsociallogin");

        switch (network) {
            case "facebook":
                fv_fb_login();
                break;
            case "vkontakte":
                fv_vk_login();
                break;
            case "google":
                fv_gp_login();
                break;
        }

    });

    // ================================


    FvVote.is_voted_for = function( ID ) {

        if ( ID && FvLib.isSupportsHtml5Storage() ) {
            // Code for localStorage/sessionStorage.
            var lcKey = "voted_" + ID;
            //console.log( localStorage.getItem(lcKey) );
            if (localStorage.getItem(lcKey) != null) {
                // Check - is data Expired?
                if (Date.now() < localStorage.getItem(lcKey)) {
                    return true;
                } else {
                    localStorage.removeItem(lcKey);
                    return false;
                }
            }
        }

    };

    (function () {

        if (fv.soc_authorization_used) {
            if ( fv.soc_login_via.vk && fv.vk_app_id ) {
                FvLib.loadScript( "https://vk.com/js/api/openapi.js?156", function() {
                    VK.init({
                        apiId: fv.vk_app_id
                    });
                } );
            }
            if ( fv.soc_login_via.gp && fv.gp_app_id ) {
                FvLib.loadScript( "https://apis.google.com/js/platform.js", function() {
                    gapi.load('auth2', function() {
                        // Google Sign-in (new)
                        fv.auth2 = gapi.auth2.init({
                            client_id: fv.gp_app_id,
                            fetch_basic_profile	: true
                            //scope: 'https://www.googleapis.com/auth/plus.login'
                        });
                    });
                } );
            }
        }

        // if (fv.soc_authorization_used) {
        //     var ulogin_script = document.createElement("script");
        //     // This script has a callback function that will run when the script has
        //     // finished loading.
        //     //ulogin_script.src = fv.plugin_url + "/assets/js/ulogin.js";
        //     ulogin_script.src = fv.plugin_url + "/assets/vendor/hello.all.min.js";
        //     //ulogin_script.src = "//ulogin.ru/js/ulogin.js";
        //     ulogin_script.type = "text/javascript";
        //     ulogin_script.onload = function() {
        //         hello.init({
        //             facebook: fv.fv_appId
        //             //windows: WINDOWS_CLIENT_ID,
        //             //google: GOOGLE_CLIENT_ID
        //         }, {
        //             redirect_uri: 'redirect.html',
        //             oauth_proxy: OAUTH_PROXY_URL
        //         });
        //     };
        //     document.getElementsByTagName("head")[0].appendChild(ulogin_script);
        // }

        function highlight_photo_voted(fv_security_type, response) {
            if (response.res == 1) {
                jQuery(".contest-block[data-id='" + response.contestant_id + "']").addClass("is-voted");

                // IF OK  => status = "success"
                if (FvLib.isSupportsHtml5Storage()) {
                    var voting_frequency = fv.ct[response.ct_id].voting_frequency;
                    if (voting_frequency == "once") {
                        // Set expiration to Contest End date
                        var expiration_date = Date.parse(fv.ct[response.ct_id].date_finish);
                    } else if (voting_frequency == "day") {
                        // Set expiration to Now + 1 Day
                        var expiration_date = Date.now() + 86400 * 1000;
                    } else {
                        // Set expiration to Now + N hours
                        var expiration_date = Date.now() + parseInt(fv.ct[response.ct_id].voting_frequency) * 3600 * 1000;
                    }
                    // Code for localStorage/sessionStorage.
                    localStorage.setItem("voted_" + response.contestant_id, expiration_date);
                }

            }
        }

        FvLib.addHook("fv/end_voting", highlight_photo_voted, 10, 2);
        //FvLib.callHook('fv/end_voting', fv.security_type, data);

        if (FvLib.isSupportsHtml5Storage()) {
            // Code for localStorage/sessionStorage.
            var lcKey;
            for (var ID in fv.data) {
                if ( FvVote.is_voted_for(ID) ) {
                    jQuery(".contest-block[data-id='" + ID + "']").addClass("is-voted");
                }
            }
        }

    })();

    window.FvVote = FvVote;

    if (!fv.evercookie_disabled) {
        var fv_evercookie = new evercookie({baseurl: fv.plugin_url + '/assets/everc'});
        fv_evercookie.get("fv_uid", function (value) {
                if (value === undefined || FvLib.strPos(value, 'br') > 0) {
                    fv_uid = FvLib.randomStr(8);
                    fv_evercookie.set("fv_uid", fv_uid);
                } else {
                    fv_uid = value;
                }
                //FvLib.log(fv_uid);
                FvLib.logSave('fv runned ' + fv_uid);
            },
            1
        );
    } else {
        fv_uid = "empty_UID";
    }


    //######## Load google reCAPTCHA JS
    var reCaptchaJsUrl = "https://www.google.com/recaptcha/api.js?render=explicit";

    /**
     * You can hard set reCAPTCHA language
     * https://developers.google.com/recaptcha/docs/language
     */
    var reCaptchaLang = FvLib.applyFilters("fv/public/reCAPTCHA/lang-code", "auto");   // "" - means auto detect language
    if ("auto" != reCaptchaLang) {
        reCaptchaJsUrl = reCaptchaJsUrl + "&hl=" + reCaptchaLang;
    }

    for (var loop_contest_id in fv.ct) {
        if (fv.ct[loop_contest_id].voting_security_ext == "reCaptcha" || ( fv.ct[loop_contest_id].voting_security_ext == "subscribe" && fv.recaptcha_subscribe)) {
            FvLib.loadScript(reCaptchaJsUrl);
            break;
        }
    }
    //######## END reCAPTCHA

})(jQuery);



+(function($) {

    // Stop if is Single View
    if ( fv.single ) {
        return;
    }

    // Callback, when image not loaded
    $(".contest-block img.attachment-thumbnail").on("error", function () {
        this.src = fv.plugin_url + "/assets/img/no-photo.png";
        FvLib.adminNotice(fv.lang.img_load_fail, 'warning', true);
    });

    if (document.querySelector('.fv_toolbar') != null) {
        var ink, d, x, y;
        $(".fv_toolbar .tabbed_a").click(function (e) {
            var $this = $(this);
            if ($this.hasClass('active')) {
                if ("#0" != $this.attr("href")) {
                    return true;
                }
                return false;
            }
            e.preventDefault();

            var target = $this.data('target');
            var $parentUl = $this.parent().parent();
            var $contestContainer = $(".fv_contest-container--" + $this.data("contest-id"));

            // Content
            $('.tabbed_c:not(' + target + ')', $contestContainer).hide();
            jQuery('.tabbed_c' + target, $contestContainer).fadeIn();

            // Links
            $('.tabbed_a', $parentUl).removeClass('active');
            $this.addClass('active');

            // Animations
            if ($this.find(".ink").length === 0) {
                $this.prepend("<span class='ink'></span>");
            }

            ink = $this.find(".ink");
            ink.removeClass("animate");

            if (!ink.height() && !ink.width()) {
                d = Math.max($this.outerWidth(), $this.outerHeight());
                ink.css({height: d, width: d});
            }

            x = e.pageX - $this.offset().left - ink.width() / 2;
            y = e.pageY - $this.offset().top - ink.height() / 2;

            ink.css({top: y + 'px', left: x + 'px'}).addClass("animate");
            return false;
        });
    }

    if (FvLib.queryString("fv-action") == "verify_mail" && FvLib.queryString("hash")) {
        var verify_form = document.querySelector(".sw-subscribe-verify > form");
        verify_form.querySelector(".fv-hash").value = FvLib.queryString("hash");
        fv_verify_subscribe_hash(verify_form);
    }


    FvLib.addHook('doc_ready', function () {

        if (fv.lazy_load) {
            jQuery(".contest-block img.fv-lazy").unveil(100, function () {
                //jQuery(this).load(function() {
                jQuery(this).removeClass("fv-lazy");
                FvLib.callHook('fv/public/lazy_new_loaded', this);
                //});
            });
        }
        
        // IF contest have any photos
        if (document.querySelectorAll('.fv_contest_container .contest-block').length > 0) {
            // Try preload first full image
            var $first_image_a = jQuery(".contest-block:first").find("a.fv_lightbox");
            var first_image_url = $first_image_a.attr("href");
            // if This url looks like correct Image url
            if (first_image_url != undefined && first_image_url.match(/\.(jpeg|jpg|gif|png)$/) != null) {
                setTimeout(function () {
                    var img = new Image();
                    img.src = first_image_url;
                }, 400);
            }


            // Get Single photo page status - if 404 - show error!
            if ("lightbox" != fv.single_link_mode && Math.random() > 0.4 && $first_image_a.data("id")) {
                jQuery.ajax({
                    url: FvLib.singlePhotoLink($first_image_a.data("id")),
                    complete: function (xhr, statusText) {
                        if (xhr.status !== 200) {
                            FvLib.adminNotice("Contest Single Photo view return 404 error. Please try go to " +
                                "Dashborad => Settings => Permalinks and click *Save*", "warning");
                        }
                    }
                });
            }

            // if in query exists variable `photo`, then try to find link and open this photo
            var photo_hash = window.location.hash.substring(1).split('-');
            if (photo_hash.length > 0 && photo_hash[0] == 'photo') {
                setTimeout(function () {
                    $('a[name="photo-' + photo_hash[1] + '"]').click();
                }, 1000);
            }

        } else if (!document.querySelector('.fv_upload') && !document.querySelector('.fv-countdown')) {
            // If no photos and no Upload form & Countdown - show notice
            // else user can think that contest now Work
            FvLib.adminNotice(fv.lang.empty_contest, 'warning');
        }



        // =================================================
        // TOOLBAR
        if (document.querySelector('.fv_toolbar') != null) {
            jQuery(".fv_toolbar .fv_sorting, .fv_toolbar .fv_category").change(function () {
                location.replace(jQuery(this).val());
            });

            if (window.location.hash.substring(1) == 'participate') {
                jQuery(".fv_toolbar a").filter("[data-target='.fv_upload']").click();
                jQuery('html, body').animate({
                    scrollTop: jQuery('.fv_toolbar').offset().top - 70
                }, 500);
            }

            // =================Search in toolbar
            jQuery(".fv_toolbar__search_input")
                .focus(function () {
                    jQuery(this).closest(".fv_toolbar")
                        .addClass("fv_toolbar--hide-dropdown")
                        .attr("data-search", "focused");
                })
                .focusout(function () {
                    if ("" != this.value) {
                        return;
                    }
                    var $fv_toolbar = jQuery(this).closest(".fv_toolbar");
                    $fv_toolbar.attr("data-search", "unfocused");
                    ;
                    setTimeout(function () {
                        $fv_toolbar.removeClass("fv_toolbar--hide-dropdown");
                    }, 505);
                });

            jQuery(".fv_toolbar__search_submit").click(function () {
                var $form = jQuery(this).closest(".fv_toolbar__search_form");
                if (!$form.find(".fv_toolbar__search_input").val()) {
                    return;
                }
                $form.submit();
            });
            // ================= Search :: END

        }


        //if ( FvLib.queryString('fv-scroll') &&  jQuery('.' + FvLib.queryString('fv-scroll')).length == 1 ) {
        if (window.location.hash.substring(1) == 'contest') {
            jQuery('html, body').animate({
                scrollTop: jQuery('.fv_contest_container').offset().top - 30
            }, 500);
        }
        // TOOLBAR :: END
        // =================================================
        /*
         if ( !fv.contest_enabled ) {
         FvLib.adminNotice(fv.lang.inactive_contest, 'warning');
         }
         */

    });

    if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) {
        fv_new_text(document);
    }

    // $(".contest-block video").click(function () {
    //     // this.paused ? this.play() : this.pause();
    //     //
    //     // if (this.requestFullscreen) {
    //     //     this.requestFullscreen();
    //     // } else if (this.mozRequestFullScreen) {
    //     //     this.mozRequestFullScreen();
    //     // } else if (this.webkitRequestFullscreen) {
    //     //     this.webkitRequestFullscreen();
    //     // }
    // });
    //


})(jQuery);

if(!PluginDetect)var PluginDetect={getNum:function(e,t){if(!this.num(e))return null;var i;return i="undefined"==typeof t?/[\d][\d\.\_,-]*/.exec(e):new RegExp(t).exec(e),i?i[0].replace(/[\.\_-]/g,","):null},hasMimeType:function(e){if(PluginDetect.isIE)return null;var t,i,n,a=e.constructor==String?[e]:e;for(n=0;n<a.length;n++)if(t=navigator.mimeTypes[a[n]],t&&t.enabledPlugin&&(i=t.enabledPlugin,i.name||i.description))return t;return null},findNavPlugin:function(e,t){var i,n=e.constructor==String?e:e.join(".*"),a=t===!1?"":"\\d",r=new RegExp(n+".*"+a+"|"+a+".*"+n,"i"),s=navigator.plugins;for(i=0;i<s.length;i++)if(r.test(s[i].description)||r.test(s[i].name))return s[i];return null},AXO:window.ActiveXObject,getAXO:function(e,t){var i=null,n=!1;try{i=new this.AXO(e),n=!0}catch(a){}if("undefined"!=typeof t){try{i.closeKeyStore(),i=null,CollectGarbage()}catch(r){}return n}return i},num:function(e){return"string"!=typeof e?!1:/\d/.test(e)},compareNums:function(e,t){var i,n,a,r=this,s=window.parseInt;if(!r.num(e)||!r.num(t))return 0;if(r.plugin&&r.plugin.compareNums)return r.plugin.compareNums(e,t);for(i=e.split(","),n=t.split(","),a=0;a<Math.min(i.length,n.length);a++){if(s(i[a],10)>s(n[a],10))return 1;if(s(i[a],10)<s(n[a],10))return-1}return 0},formatNum:function(e){if(!this.num(e))return null;var t,i=e.replace(/\s/g,"").replace(/[\.\_]/g,",").split(",").concat(["0","0","0","0"]);for(t=0;4>t;t++)/^(0+)(.+)$/.test(i[t])&&(i[t]=RegExp.$2);return/\d/.test(i[0])||(i[0]="0"),i[0]+","+i[1]+","+i[2]+","+i[3]},initScript:function(){var e=this,t=navigator.userAgent;if(e.isIE=!1,e.IEver=e.isIE&&/MSIE\s*(\d\.?\d*)/i.exec(t)?parseFloat(RegExp.$1,10):-1,e.ActiveXEnabled=!1,e.isIE){var i,n=["Msxml2.XMLHTTP","Msxml2.DOMDocument","Microsoft.XMLDOM","ShockwaveFlash.ShockwaveFlash","TDCCtl.TDCCtl","Shell.UIHelper","Scripting.Dictionary","wmplayer.ocx"];for(i=0;i<n.length;i++)if(e.getAXO(n[i],1)){e.ActiveXEnabled=!0;break}e.head="undefined"!=typeof document.getElementsByTagName?document.getElementsByTagName("head")[0]:null}e.isGecko=!e.isIE&&"string"==typeof navigator.product&&/Gecko/i.test(navigator.product)&&/Gecko\s*\/\s*\d/i.test(t)?!0:!1,e.GeckoRV=e.isGecko?e.formatNum(/rv\s*\:\s*([\.\,\d]+)/i.test(t)?RegExp.$1:"0.9"):null,e.isSafari=!e.isIE&&/Safari\s*\/\s*\d/i.test(t)?!0:!1,e.isChrome=/Chrome\s*\/\s*\d/i.test(t)?!0:!1,e.onWindowLoaded(0)},init:function(e,t){if("string"!=typeof e)return-3;e=e.toLowerCase().replace(/\s/g,"");var i,n=this;return"undefined"==typeof n[e]?-3:(i=n[e],n.plugin=i,("undefined"==typeof i.installed||1==t)&&(i.installed=null,i.version=null,i.version0=null,i.getVersionDone=null,i.$=n),n.garbage=!1,n.isIE&&!n.ActiveXEnabled&&n.plugin!=n.java?-2:1)},isMinVersion:function(){return-3},getVersion:function(e,t,i){var n,a=PluginDetect,r=a.init(e);return 0>r?null:(n=a.plugin,1!=n.getVersionDone&&(n.getVersion(t,i),null===n.getVersionDone&&(n.getVersionDone=1)),a.cleanup(),n.version||n.version0)},getInfo:function(e,t,i){var n,a={},r=PluginDetect,s=r.init(e);return 0>s?a:(n=r.plugin,"undefined"!=typeof n.getInfo&&(null===n.getVersionDone&&r.getVersion(e,t,i),a=n.getInfo()),a)},cleanup:function(){var e=this;e.garbage&&"undefined"!=typeof window.CollectGarbage&&window.CollectGarbage()},isActiveXObject:function(e){var t,i=this,n="/",a='<object width="1" height="1" style="display:none" '+i.plugin.getCodeBaseVersion(e)+">"+i.plugin.HTML+"<"+n+"object>";i.head.firstChild?i.head.insertBefore(document.createElement("object"),i.head.firstChild):i.head.appendChild(document.createElement("object")),i.head.firstChild.outerHTML=a;try{i.head.firstChild.classid=i.plugin.classID}catch(r){}t=!1;try{i.head.firstChild.object&&(t=!0)}catch(r){}try{t&&i.head.firstChild.readyState<4&&(i.garbage=!0)}catch(r){}return i.head.removeChild(i.head.firstChild),t},codebaseSearch:function(e){var t=this;if(!t.ActiveXEnabled)return null;if("undefined"!=typeof e)return t.isActiveXObject(e);var i,n,a,r,s=[0,0,0,0],l=t.plugin.digits,o=function(e,i){var n=(0==e?i:s[0])+","+(1==e?i:s[1])+","+(2==e?i:s[2])+","+(3==e?i:s[3]);return t.isActiveXObject(n)},u=!1;for(i=0;i<l.length;i++){for(a=2*l[i],s[i]=0,n=0;20>n&&!(1==a&&i>0&&u);n++){if(!(a-s[i]>1)){if(a-s[i]==1){a--,!u&&o(i,a)&&(u=!0);break}!u&&o(i,a)&&(u=!0);break}r=Math.round((a+s[i])/2),o(i,r)?(s[i]=r,u=!0):a=r}if(!u)return null}return s.join(",")},dummy1:0};PluginDetect.onDetectionDone=function(){return-1},PluginDetect.onWindowLoaded=function(e){var t=PluginDetect,i=window;t.EventWinLoad===!0||(t.winLoaded=!1,t.EventWinLoad=!0,"undefined"!=typeof i.addEventListener?i.addEventListener("load",t.runFuncs,!1):"undefined"!=typeof i.attachEvent?i.attachEvent("onload",t.runFuncs):("function"==typeof i.onload&&(t.funcs[t.funcs.length]=i.onload),i.onload=t.runFuncs)),"function"==typeof e&&(t.funcs[t.funcs.length]=e)},PluginDetect.funcs=[0],PluginDetect.runFuncs=function(){var e,t=PluginDetect;for(t.winLoaded=!0,e=0;e<t.funcs.length;e++)"function"==typeof t.funcs[e]&&(t.funcs[e](t),t.funcs[e]=null)},PluginDetect.quicktime={mimeType:["video/quicktime","application/x-quicktimeplayer","image/x-macpaint","image/x-quicktime"],progID:"QuickTimeCheckObject.QuickTimeCheck.1",progID0:"QuickTime.QuickTime",classID:"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B",minIEver:7,HTML:'<param name="src" value="A14999.mov" /><param name="controller" value="false" />',getCodeBaseVersion:function(e){return'codebase="#version='+e+'"'},digits:[8,64,16,0],clipTo3digits:function(e){if(null===e||"undefined"==typeof e)return null;var t,i,n,a=this.$;return t=e.split(","),i=a.compareNums(e,"7,60,0,0")<0&&a.compareNums(e,"7,50,0,0")>=0?t[0]+","+t[1].charAt(0)+","+t[1].charAt(1)+","+t[2]:t[0]+","+t[1]+","+t[2]+","+t[3],n=i.split(","),n[0]+","+n[1]+","+n[2]+",0"},getVersion:function(){var e,t=null,i=this.$,n=!0;if(i.isIE){var a;i.IEver>=this.minIEver&&i.getAXO(this.progID0,1)?t=i.codebaseSearch():(a=i.getAXO(this.progID),a&&a.QuickTimeVersion&&(t=a.QuickTimeVersion.toString(16),t=t.charAt(0)+"."+t.charAt(1)+"."+t.charAt(2))),this.installed=t?1:i.getAXO(this.progID0,1)?0:-1}else navigator.platform&&/linux/i.test(navigator.platform)&&(n=!1),n&&(e=i.findNavPlugin(["QuickTime","(Plug-in|Plugin)"]),e&&e.name&&i.hasMimeType(this.mimeType)&&(t=i.getNum(e.name))),this.installed=t?1:-1;this.version=this.clipTo3digits(i.formatNum(t))}},PluginDetect.java={mimeType:"application/x-java-applet",classID:"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93",DTKclassID:"clsid:CAFEEFAC-DEC7-0000-0000-ABCDEFFEDCBA",DTKmimeType:"application/npruntime-scriptable-plugin;DeploymentToolkit",JavaVersions:[[1,9,2,25],[1,8,2,25],[1,7,2,25],[1,6,2,25],[1,5,2,25],[1,4,2,25],[1,3,1,25]],searchJavaPluginAXO:function(){var e=null,t=this,i=t.$,n=[],a=[1,5,0,14],r=[1,6,0,2],s=[1,3,1,0],l=[1,4,2,0],o=[1,5,0,7],u=!1;return i.ActiveXEnabled?(u=!0,i.IEver>=t.minIEver?(n=t.searchJavaAXO(r,r,u),n.length>0&&u&&(n=t.searchJavaAXO(a,a,u))):(u&&(n=t.searchJavaAXO(o,o,!0)),0==n.length&&(n=t.searchJavaAXO(s,l,!1))),n.length>0&&(e=n[0]),t.JavaPlugin_versions=[].concat(n),e):null},searchJavaAXO:function(e,t,i){var n,a,r,s,l,o,u,c,p,g=this.$,d=[];g.compareNums(e.join(","),t.join(","))>0&&(t=e),t=g.formatNum(t.join(","));var v,f="1,4,2,0",h="JavaPlugin."+e[0]+e[1]+e[2]+(e[3]>0?"_"+(e[3]<10?"0":"")+e[3]:"");for(n=0;n<this.JavaVersions.length;n++)for(a=this.JavaVersions[n],r="JavaPlugin."+a[0]+a[1],u=a[0]+"."+a[1]+".",l=a[2];l>=0;l--)if(p="JavaWebStart.isInstalled."+u+l+".0",!(g.compareNums(a[0]+","+a[1]+","+l+",0",t)>=0)||g.getAXO(p,1)){for(v=g.compareNums(a[0]+","+a[1]+","+l+",0",f)<0?!0:!1,o=a[3];o>=0;o--){if(s=l+"_"+(10>o?"0"+o:o),c=r+s,g.getAXO(c,1)&&(v||g.getAXO(p,1))&&(d[d.length]=u+s,!i))return d;if(c==h)return d}if(g.getAXO(r+l,1)&&(v||g.getAXO(p,1))&&(d[d.length]=u+l,!i))return d;if(r+l==h)return d}return d},minIEver:7,getFromMimeType:function(e){var t,i,n,a,r,s=this.$,l=new RegExp(e),o={},u=0,c=[""];for(t=0;t<navigator.mimeTypes.length;t++)a=navigator.mimeTypes[t],l.test(a.type)&&a.enabledPlugin&&(a=a.type.substring(a.type.indexOf("=")+1,a.type.length),n="a"+s.formatNum(a),"undefined"==typeof o[n]&&(o[n]=a,u++));for(i=0;u>i;i++){r="0,0,0,0";for(t in o)o[t]&&(n=t.substring(1,t.length),s.compareNums(n,r)>0&&(r=n));c[i]=o["a"+r],o["a"+r]=null}return/windows|macintosh/i.test(navigator.userAgent)||(c=[c[0]]),c},queryJavaHandler:function(){var e=PluginDetect.java,t=window.java;e.hasRun=!0;try{"undefined"!=typeof t.lang&&"undefined"!=typeof t.lang.System&&(e.value=[t.lang.System.getProperty("java.version")+" ",t.lang.System.getProperty("java.vendor")+" "])}catch(i){}},queryJava:function(){var e=this,t=e.$,i=navigator.userAgent;if("undefined"!=typeof window.java&&navigator.javaEnabled()&&!e.hasRun)if(t.isGecko){if(t.hasMimeType("application/x-java-vm")){try{var n=document.createElement("div"),a=document.createEvent("HTMLEvents");a.initEvent("focus",!1,!0),n.addEventListener("focus",e.queryJavaHandler,!1),n.dispatchEvent(a)}catch(r){}e.hasRun||e.queryJavaHandler()}}else/opera.9\.(0|1)/i.test(i)&&/mac/i.test(i)||e.hasRun||e.queryJavaHandler();return e.value},forceVerifyTag:[],jar:[],VENDORS:["Sun Microsystems Inc.","Apple Computer, Inc."],init:function(){var e=this,t=e.$;"undefined"!=typeof e.app&&e.delJavaApplets(t),e.hasRun=!1,e.value=[null,null],e.useTag=[2,2,2],e.app=[0,0,0,0,0,0],e.appi=3,e.queryDTKresult=null,e.OTF=0,e.BridgeResult=[[null,null],[null,null],[null,null]],e.JavaActive=[0,0,0],e.All_versions=[],e.DeployTK_versions=[],e.MimeType_versions=[],e.JavaPlugin_versions=[],e.funcs=[];var i=e.NOTF;i&&(i.$=t,i.javaInterval&&clearInterval(i.javaInterval),i.EventJavaReady=null,i.javaInterval=null,i.count=0,i.intervalLength=250,i.countMax=40),e.lateDetection=t.winLoaded,e.lateDetection||t.onWindowLoaded(e.delJavaApplets)},getVersion:function(e,t){var i,n=this,a=n.$,r=null,s=null,l=null,o=navigator.javaEnabled();null===n.getVersionDone&&n.init();var u;if("undefined"!=typeof t&&t.constructor==Array)for(u=0;u<n.useTag.length;u++)"number"==typeof t[u]&&(n.useTag[u]=t[u]);for(u=0;u<n.forceVerifyTag.length;u++)n.useTag[u]=n.forceVerifyTag[u];if("undefined"!=typeof e&&(n.jar[n.jar.length]=e),0==n.getVersionDone)return(!n.version||n.useAnyTag())&&(i=n.queryExternalApplet(e),i[0]&&(l=i[0],s=i[1])),void n.EndGetVersion(l,s);var c=n.queryDeploymentToolKit();if("string"==typeof c&&c.length>0&&(r=c,s=n.VENDORS[0]),a.isIE)r||-1==c||(r=n.searchJavaPluginAXO(),r&&(s=n.VENDORS[0])),r||n.JavaFix(),r&&(n.version0=r,o&&a.ActiveXEnabled&&(l=r)),(!l||n.useAnyTag())&&(i=n.queryExternalApplet(e),i[0]&&(l=i[0],s=i[1]));else{var p,g,d,v,f;f=a.hasMimeType(n.mimeType),v=f&&o?!0:!1,0==n.MimeType_versions.length&&f&&(i=n.getFromMimeType("application/x-java-applet.*jpi-version.*="),""!=i[0]&&(r||(r=i[0]),n.MimeType_versions=i)),!r&&f&&(i="Java[^\\d]*Plug-in",d=a.findNavPlugin(i),d&&(i=new RegExp(i,"i"),p=i.test(d.description)?a.getNum(d.description):null,g=i.test(d.name)?a.getNum(d.name):null,r=p&&g?a.compareNums(a.formatNum(p),a.formatNum(g))>=0?p:g:p||g)),!r&&f&&/macintosh.*safari/i.test(navigator.userAgent)&&(d=a.findNavPlugin("Java.*\\d.*Plug-in.*Cocoa",!1),d&&(p=a.getNum(d.description),p&&(r=p))),r&&(n.version0=r,o&&(l=r)),(!l||n.useAnyTag())&&(d=n.queryExternalApplet(e),d[0]&&(l=d[0],s=d[1])),l||(d=n.queryJava(),d[0]&&(n.version0=d[0],l=d[0],s=d[1],n.installed==-.5&&(n.installed=.5))),null!==n.installed||l||!v||/macintosh.*ppc/i.test(navigator.userAgent)||(i=n.getFromMimeType("application/x-java-applet.*version.*="),""!=i[0]&&(l=i[0])),!l&&v&&/macintosh.*safari/i.test(navigator.userAgent)&&(null===n.installed?n.installed=0:n.installed==-.5&&(n.installed=.5))}null===n.installed&&(n.installed=l?1:r?-.2:-1),n.EndGetVersion(l,s)},EndGetVersion:function(e,t){var i=this,n=i.$;i.version0&&(i.version0=n.formatNum(n.getNum(i.version0))),e&&(i.version=n.formatNum(n.getNum(e)),i.vendor="string"==typeof t?t:""),1!=i.getVersionDone&&(i.getVersionDone=0)},queryDeploymentToolKit:function(){var e,t=this,i=t.$,n=null,a=null;if((i.isGecko&&i.compareNums(i.GeckoRV,i.formatNum("1.6"))<=0||i.isSafari||i.isIE&&!i.ActiveXEnabled)&&(t.queryDTKresult=0),null!==t.queryDTKresult)return t.queryDTKresult;if(i.isIE&&i.IEver>=6?(t.app[0]=i.instantiate("object",[],[]),n=i.getObject(t.app[0])):!i.isIE&&i.hasMimeType(t.DTKmimeType)&&(t.app[0]=i.instantiate("object",["type",t.DTKmimeType],[]),n=i.getObject(t.app[0])),n){if(i.isIE&&i.IEver>=6)try{n.classid=t.DTKclassID}catch(r){}try{var s,l=n.jvms;if(l&&(a=l.getLength(),"number"==typeof a))for(e=0;a>e;e++)s=l.get(a-1-e),s&&(s=s.version,i.getNum(s)&&(t.DeployTK_versions[e]=s))}catch(r){}}return i.hideObject(n),t.queryDTKresult=t.DeployTK_versions.length>0?t.DeployTK_versions[0]:0==a?-1:0,t.queryDTKresult},queryExternalApplet:function(e){var t=this,i=t.$,n=t.BridgeResult,a=t.app,r=t.appi,s="&nbsp;&nbsp;&nbsp;&nbsp;";if("string"!=typeof e||!/\.jar\s*$/.test(e))return[null,null];if(t.OTF<1&&(t.OTF=1),!i.isIE&&(i.isGecko||i.isChrome)&&!i.hasMimeType(t.mimeType)&&!t.queryJava()[0])return[null,null];t.OTF<2&&(t.OTF=2),!a[r]&&t.canUseObjectTag()&&t.canUseThisTag(0)&&(a[1]=i.instantiate("object",[],[],s),a[r]=i.isIE?i.instantiate("object",["archive",e,"code","A.class","type",t.mimeType],["archive",e,"code","A.class","mayscript","true","scriptable","true"],s):i.instantiate("object",["archive",e,"classid","java:A.class","type",t.mimeType],["archive",e,"mayscript","true","scriptable","true"],s),n[0]=[0,0],t.query1Applet(r)),!a[r+1]&&t.canUseAppletTag()&&t.canUseThisTag(1)&&(a[r+1]=i.instantiate("applet",["archive",e,"code","A.class","alt",s,"mayscript","true"],["mayscript","true"],s),n[1]=[0,0],t.query1Applet(r+1)),i.isIE&&!a[r+2]&&t.canUseObjectTag()&&t.canUseThisTag(2)&&(a[r+2]=i.instantiate("object",["classid",t.classID],["archive",e,"code","A.class","mayscript","true","scriptable","true"],s),n[2]=[0,0],t.query1Applet(r+2));var l,o=0;for(l=0;l<n.length&&(a[r+l]||t.canUseThisTag(l));l++)o++;return o==n.length&&(t.getVersionDone=1,t.forceVerifyTag.length>0&&(t.getVersionDone=0)),t.getBR()},canUseAppletTag:function(){return!this.$.isIE||navigator.javaEnabled()?!0:!1},canUseObjectTag:function(){return!this.$.isIE||this.$.ActiveXEnabled?!0:!1},useAnyTag:function(){var e,t=this;for(e=0;e<t.useTag.length;e++)if(t.canUseThisTag(e))return!0;return!1},canUseThisTag:function(e){var t=this,i=t.$;if(3==t.useTag[e])return!0;if(!t.version0||!navigator.javaEnabled()||i.isIE&&!i.ActiveXEnabled){if(2==t.useTag[e])return!0;if(1==t.useTag[e]&&!t.getBR()[0])return!0}return!1},getBR:function(){var e,t=this.BridgeResult;for(e=0;e<t.length;e++)if(t[e][0])return[t[e][0],t[e][1]];return[t[0][0],t[0][1]]},delJavaApplets:function(e){var t,i=e.java.app;for(t=i.length-1;t>=0;t--)e.uninstantiate(i[t])},query1Applet:function(e){var t=this,i=t.$,n=null,a=null,r=i.getObject(t.app[e],!0);try{r&&(n=r.getVersion()+" ",a=r.getVendor()+" ",i.num(n)&&(t.BridgeResult[e-t.appi]=[n,a],i.hideObject(t.app[e])),i.isIE&&n&&4!=r.readyState&&(i.garbage=!0,i.uninstantiate(t.app[e])))}catch(s){}},NOTF:{isJavaActive:function(){}},append:function(e,t){for(var i=0;i<t.length;i++)e[e.length]=t[i]},getInfo:function(){var e,t={},i=this,n=i.$,a=i.installed;t={All_versions:[],DeployTK_versions:[],MimeType_versions:[],DeploymentToolkitPlugin:0==i.queryDTKresult?!1:!0,vendor:"string"==typeof i.vendor?i.vendor:"",OTF:i.OTF<3?0:3==i.OTF?1:2};var r=[null,null,null];for(e=0;e<i.BridgeResult.length;e++)r[e]=i.BridgeResult[e][0]?1:1==i.JavaActive[e]?0:i.useTag[e]>=1&&i.OTF>=1&&3!=i.OTF&&(2!=e||n.isIE)&&(null!==i.BridgeResult[e][0]||1==e&&!i.canUseAppletTag()||1!=e&&!i.canUseObjectTag()||a==-.2||-1==a)?-1:null;t.objectTag=r[0],t.appletTag=r[1],t.objectTagActiveX=r[2];var s=t.All_versions,l=t.DeployTK_versions,o=t.MimeType_versions,u=i.JavaPlugin_versions;for(i.append(l,i.DeployTK_versions),i.append(o,i.MimeType_versions),i.append(s,l.length>0?l:o.length>0?o:u.length>0?u:"string"==typeof i.version?[i.version]:[]),e=0;e<s.length;e++)s[e]=n.formatNum(n.getNum(s[e]));var c,p=null;n.isIE||(c=n.hasMimeType(o.length>0?i.mimeType+";jpi-version="+o[0]:i.mimeType),c&&(p=c.enabledPlugin)),t.name=p?p.name:"",t.description=p?p.description:"";var g=null;return 0!=a&&1!=a||""!=t.vendor||(/macintosh/i.test(navigator.userAgent)?g=i.VENDORS[1]:!n.isIE&&/windows/i.test(navigator.userAgent)?g=i.VENDORS[0]:/linux/i.test(navigator.userAgent)&&(g=i.VENDORS[0]),g&&(t.vendor=g)),t},JavaFix:function(){}},PluginDetect.devalvr={mimeType:"application/x-devalvrx",progID:"DevalVRXCtrl.DevalVRXCtrl.1",classID:"clsid:5D2CF9D0-113A-476B-986F-288B54571614",getVersion:function(){var e,t=null,i=this.$;if(i.isIE){var n,a,r;if(a=i.getAXO(this.progID,1)){if(n=i.instantiate("object",["classid",this.classID],["src",""]),r=i.getObject(n))try{r.pluginversion&&(t="00000000"+r.pluginversion.toString(16),t=t.substr(t.length-8,8),t=parseInt(t.substr(0,2),16)+","+parseInt(t.substr(2,2),16)+","+parseInt(t.substr(4,2),16)+","+parseInt(t.substr(6,2),16))}catch(s){}i.uninstantiate(n)}this.installed=t?1:a?0:-1}else e=i.findNavPlugin("DevalVR"),e&&e.name&&i.hasMimeType(this.mimeType)&&(t=e.description.split(" ")[3]),this.installed=t?1:-1;this.version=i.formatNum(t)}},PluginDetect.flash={mimeType:["application/x-shockwave-flash","application/futuresplash"],progID:"ShockwaveFlash.ShockwaveFlash",classID:"clsid:D27CDB6E-AE6D-11CF-96B8-444553540000",getVersion:function(){var e,t,i=function(e){if(!e)return null;var t=/[\d][\d\,\.\s]*[rRdD]{0,1}[\d\,]*/.exec(e);return t?t[0].replace(/[rRdD\.]/g,",").replace(/\s/g,""):null},n=this.$,a=null,r=null,s=null;if(n.isIE){for(t=15;t>2;t--)if(r=n.getAXO(this.progID+"."+t)){s=t.toString();break}if("6"==s)try{r.AllowScriptAccess="always"}catch(l){return"6,0,21,0"}try{a=i(r.GetVariable("$version"))}catch(l){}!a&&s&&(a=s)}else e=n.findNavPlugin("Flash"),e&&e.description&&n.hasMimeType(this.mimeType)&&(a=i(e.description));return this.installed=a?1:-1,this.version=n.formatNum(a),!0}},PluginDetect.shockwave={mimeType:"application/x-director",progID:"SWCtl.SWCtl",classID:"clsid:166B1BCA-3F9C-11CF-8075-444553540000",getVersion:function(){var e,t=null,i=null,n=this.$;if(n.isIE){try{i=n.getAXO(this.progID).ShockwaveVersion("")}catch(a){}"string"==typeof i&&i.length>0?t=n.getNum(i):n.getAXO(this.progID+".8",1)?t="8":n.getAXO(this.progID+".7",1)?t="7":n.getAXO(this.progID+".1",1)&&(t="6")}else e=n.findNavPlugin("Shockwave for Director"),e&&e.description&&n.hasMimeType(this.mimeType)&&(t=n.getNum(e.description));this.installed=t?1:-1,this.version=n.formatNum(t)}},PluginDetect.div=null,PluginDetect.pluginSize=1,PluginDetect.DOMbody=null,PluginDetect.uninstantiate=function(e){var t=this;if(e)try{e[0]&&e[0].firstChild&&e[0].removeChild(e[0].firstChild),e[0]&&t.div&&t.div.removeChild(e[0]),t.div&&0==t.div.childNodes.length&&(t.div.parentNode.removeChild(t.div),t.div=null,t.DOMbody&&t.DOMbody.parentNode&&t.DOMbody.parentNode.removeChild(t.DOMbody),t.DOMbody=null),e[0]=null}catch(i){}},PluginDetect.getObject=function(e,t){var i=null;try{e&&e[0]&&e[0].firstChild&&(i=e[0].firstChild)}catch(n){}try{t&&i&&"undefined"!=typeof i.focus&&"undefined"!=typeof document.hasFocus&&!document.hasFocus()&&i.focus()}catch(n){}return i},PluginDetect.getContainer=function(e){var t=null;return e&&e[0]&&(t=e[0]),t},PluginDetect.hideObject=function(e){var t=this.getObject(e);t&&t.style&&(t.style.height="0")},PluginDetect.instantiate=function(e,t,i,n){var a,r,s,l=function(e){var t=e.style;t&&(t.border="0px",t.padding="0px",t.margin="0px",t.fontSize=u.pluginSize+3+"px",t.height=u.pluginSize+3+"px",t.visibility="visible",e.tagName&&"div"==e.tagName.toLowerCase()?(t.width="100%",t.display="block"):e.tagName&&"span"==e.tagName.toLowerCase()&&(t.width=u.pluginSize+"px",t.display="inline"))},o=document,u=this,c=o.getElementsByTagName("body")[0]||o.body,p=o.createElement("span"),g="/";for("undefined"==typeof n&&(n=""),a="<"+e+' width="'+u.pluginSize+'" height="'+u.pluginSize+'" ',r=0;r<t.length;r+=2)a+=t[r]+'="'+t[r+1]+'" ';for(a+=">",r=0;r<i.length;r+=2)a+='<param name="'+i[r]+'" value="'+i[r+1]+'" />';if(a+=n+"<"+g+e+">",!u.div){if(u.div=o.createElement("div"),s=o.getElementById("plugindetect"))l(s),s.appendChild(u.div);else if(c)try{c.firstChild&&"undefined"!=typeof c.insertBefore?c.insertBefore(u.div,c.firstChild):c.appendChild(u.div)}catch(d){}else try{o.write('<div id="pd33993399">o<'+g+"div>"),c=o.getElementsByTagName("body")[0]||o.body,c.appendChild(u.div),c.removeChild(o.getElementById("pd33993399"))}catch(d){try{u.DOMbody=o.createElement("body"),o.getElementsByTagName("html")[0].appendChild(u.DOMbody),u.DOMbody.appendChild(u.div)}catch(d){}}l(u.div)}if(u.div&&u.div.parentNode&&u.div.parentNode.parentNode){u.div.appendChild(p);try{p.innerHTML=a}catch(d){}return l(p),[p]}return[null]},PluginDetect.windowsmediaplayer={mimeType:["application/x-mplayer2","application/asx"],progID:"wmplayer.ocx",classID:"clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6",getVersion:function(){var e=null,t=this.$,i=null;if(this.installed=-1,t.isIE)i=t.getAXO(this.progID),i&&(e=i.versionInfo);else if(t.hasMimeType(this.mimeType)){(t.findNavPlugin(["Windows","Media","(Plug-in|Plugin)"],!1)||t.findNavPlugin(["Flip4Mac","Windows","Media"],!1))&&(this.installed=0);var n=t.isGecko&&t.compareNums(t.GeckoRV,t.formatNum("1.8"))<0;if(!n&&t.findNavPlugin(["Windows","Media","Firefox Plugin"],!1)){var a=t.instantiate("object",["type",this.mimeType[0]],[]),r=t.getObject(a);r&&(e=r.versionInfo),t.uninstantiate(a)}}e&&(this.installed=1),this.version=t.formatNum(e)}},PluginDetect.silverlight={mimeType:"application/x-silverlight",progID:"AgControl.AgControl",digits:[9,20,9,12,31],getVersion:function(){var e=this.$,t=(document,null),i=null,n=!1;if(e.isIE){i=e.getAXO(this.progID);var a,r,s,l=[1,0,1,1,1],o=function(e){return(10>e?"0":"")+e.toString()},u=function(e,t,i,n,a){return e+"."+t+"."+i+o(n)+o(a)+".0"},c=function(e,t){var n=u(0==e?t:l[0],1==e?t:l[1],2==e?t:l[2],3==e?t:l[3],4==e?t:l[4]);try{return i.IsVersionSupported(n)}catch(a){}return!1};if(i&&"undefined"!=typeof i.IsVersionSupported){for(a=0;a<this.digits.length;a++){for(s=l[a],r=s+(0==a?0:1);r<=this.digits[a]&&c(a,r);r++)n=!0,l[a]=r;if(!n)break}n&&(t=u(l[0],l[1],l[2],l[3],l[4]))}}else{var p=[null,null],g=e.findNavPlugin("Silverlight Plug-in",!1),d=e.isGecko&&e.compareNums(e.GeckoRV,e.formatNum("1.6"))<=0;g&&e.hasMimeType(this.mimeType)&&(t=e.formatNum(g.description),t&&(l=t.split(","),parseInt(l[2],10)>=30226&&parseInt(l[0],10)<2&&(l[0]="2"),t=l.join(",")),e.isGecko&&!d&&(n=!0),n||d||!t||(p=e.instantiate("object",["type",this.mimeType],[]),i=e.getObject(p),i&&("undefined"!=typeof i.IsVersionSupported&&(n=!0),n||(i.data="data:"+this.mimeType+",","undefined"!=typeof i.IsVersionSupported&&(n=!0))),e.uninstantiate(p)))}this.installed=n?1:-1,this.version=e.formatNum(t)}},PluginDetect.vlc={mimeType:"application/x-vlc-plugin",progID:"VideoLAN.VLCPlugin",compareNums:function(e,t){var i,n,a,r,s,l,o=e.split(","),u=t.split(",");for(i=0;i<Math.min(o.length,u.length);i++){if(l=/([\d]+)([a-z]?)/.test(o[i]),n=parseInt(RegExp.$1,10),r=2==i&&RegExp.$2.length>0?RegExp.$2.charCodeAt(0):-1,l=/([\d]+)([a-z]?)/.test(u[i]),a=parseInt(RegExp.$1,10),s=2==i&&RegExp.$2.length>0?RegExp.$2.charCodeAt(0):-1,n!=a)return n>a?1:-1;if(2==i&&r!=s)return r>s?1:-1}return 0},getVersion:function(){var e,t=this.$,i=null;if(t.isIE){if(e=t.getAXO(this.progID))try{i=t.getNum(e.VersionInfo,"[\\d][\\d\\.]*[a-z]*")}catch(n){}this.installed=e?1:-1}else t.hasMimeType(this.mimeType)&&(e=t.findNavPlugin(["VLC","(Plug-in|Plugin)"],!1),e&&e.description&&(i=t.getNum(e.description,"[\\d][\\d\\.]*[a-z]*"))),this.installed=i?1:-1;this.version=t.formatNum(i)}},PluginDetect.initScript();

/*	SWFObject v2.2 <http://code.google.com/p/swfobject/>
 is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
var swfobject=function(){var D="undefined",r="object",S="Shockwave Flash",W="ShockwaveFlash.ShockwaveFlash",q="application/x-shockwave-flash",R="SWFObjectExprInst",x="onreadystatechange",O=window,j=document,t=navigator,T=false,U=[h],o=[],N=[],I=[],l,Q,E,B,J=false,a=false,n,G,m=true,M=function(){var aa=typeof j.getElementById!=D&&typeof j.getElementsByTagName!=D&&typeof j.createElement!=D,ah=t.userAgent.toLowerCase(),Y=t.platform.toLowerCase(),ae=Y?/win/.test(Y):/win/.test(ah),ac=Y?/mac/.test(Y):/mac/.test(ah),af=/webkit/.test(ah)?parseFloat(ah.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,X=!+"\v1",ag=[0,0,0],ab=null;if(typeof t.plugins!=D&&typeof t.plugins[S]==r){ab=t.plugins[S].description;if(ab&&!(typeof t.mimeTypes!=D&&t.mimeTypes[q]&&!t.mimeTypes[q].enabledPlugin)){T=true;X=false;ab=ab.replace(/^.*\s+(\S+\s+\S+$)/,"$1");ag[0]=parseInt(ab.replace(/^(.*)\..*$/,"$1"),10);ag[1]=parseInt(ab.replace(/^.*\.(.*)\s.*$/,"$1"),10);ag[2]=/[a-zA-Z]/.test(ab)?parseInt(ab.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0}}else{if(typeof O.ActiveXObject!=D){try{var ad=new ActiveXObject(W);if(ad){ab=ad.GetVariable("$version");if(ab){X=true;ab=ab.split(" ")[1].split(",");ag=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}}catch(Z){}}}return{w3:aa,pv:ag,wk:af,ie:X,win:ae,mac:ac}}(),k=function(){if(!M.w3){return}if((typeof j.readyState!=D&&j.readyState=="complete")||(typeof j.readyState==D&&(j.getElementsByTagName("body")[0]||j.body))){f()}if(!J){if(typeof j.addEventListener!=D){j.addEventListener("DOMContentLoaded",f,false)}if(M.ie&&M.win){j.attachEvent(x,function(){if(j.readyState=="complete"){j.detachEvent(x,arguments.callee);f()}});if(O==top){(function(){if(J){return}try{j.documentElement.doScroll("left")}catch(X){setTimeout(arguments.callee,0);return}f()})()}}if(M.wk){(function(){if(J){return}if(!/loaded|complete/.test(j.readyState)){setTimeout(arguments.callee,0);return}f()})()}s(f)}}();function f(){if(J){return}try{var Z=j.getElementsByTagName("body")[0].appendChild(C("span"));Z.parentNode.removeChild(Z)}catch(aa){return}J=true;var X=U.length;for(var Y=0;Y<X;Y++){U[Y]()}}function K(X){if(J){X()}else{U[U.length]=X}}function s(Y){if(typeof O.addEventListener!=D){O.addEventListener("load",Y,false)}else{if(typeof j.addEventListener!=D){j.addEventListener("load",Y,false)}else{if(typeof O.attachEvent!=D){i(O,"onload",Y)}else{if(typeof O.onload=="function"){var X=O.onload;O.onload=function(){X();Y()}}else{O.onload=Y}}}}}function h(){if(T){V()}else{H()}}function V(){var X=j.getElementsByTagName("body")[0];var aa=C(r);aa.setAttribute("type",q);var Z=X.appendChild(aa);if(Z){var Y=0;(function(){if(typeof Z.GetVariable!=D){var ab=Z.GetVariable("$version");if(ab){ab=ab.split(" ")[1].split(",");M.pv=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}else{if(Y<10){Y++;setTimeout(arguments.callee,10);return}}X.removeChild(aa);Z=null;H()})()}else{H()}}function H(){var ag=o.length;if(ag>0){for(var af=0;af<ag;af++){var Y=o[af].id;var ab=o[af].callbackFn;var aa={success:false,id:Y};if(M.pv[0]>0){var ae=c(Y);if(ae){if(F(o[af].swfVersion)&&!(M.wk&&M.wk<312)){w(Y,true);if(ab){aa.success=true;aa.ref=z(Y);ab(aa)}}else{if(o[af].expressInstall&&A()){var ai={};ai.data=o[af].expressInstall;ai.width=ae.getAttribute("width")||"0";ai.height=ae.getAttribute("height")||"0";if(ae.getAttribute("class")){ai.styleclass=ae.getAttribute("class")}if(ae.getAttribute("align")){ai.align=ae.getAttribute("align")}var ah={};var X=ae.getElementsByTagName("param");var ac=X.length;for(var ad=0;ad<ac;ad++){if(X[ad].getAttribute("name").toLowerCase()!="movie"){ah[X[ad].getAttribute("name")]=X[ad].getAttribute("value")}}P(ai,ah,Y,ab)}else{p(ae);if(ab){ab(aa)}}}}}else{w(Y,true);if(ab){var Z=z(Y);if(Z&&typeof Z.SetVariable!=D){aa.success=true;aa.ref=Z}ab(aa)}}}}}function z(aa){var X=null;var Y=c(aa);if(Y&&Y.nodeName=="OBJECT"){if(typeof Y.SetVariable!=D){X=Y}else{var Z=Y.getElementsByTagName(r)[0];if(Z){X=Z}}}return X}function A(){return !a&&F("6.0.65")&&(M.win||M.mac)&&!(M.wk&&M.wk<312)}function P(aa,ab,X,Z){a=true;E=Z||null;B={success:false,id:X};var ae=c(X);if(ae){if(ae.nodeName=="OBJECT"){l=g(ae);Q=null}else{l=ae;Q=X}aa.id=R;if(typeof aa.width==D||(!/%$/.test(aa.width)&&parseInt(aa.width,10)<310)){aa.width="310"}if(typeof aa.height==D||(!/%$/.test(aa.height)&&parseInt(aa.height,10)<137)){aa.height="137"}j.title=j.title.slice(0,47)+" - Flash Player Installation";var ad=M.ie&&M.win?"ActiveX":"PlugIn",ac="MMredirectURL="+O.location.toString().replace(/&/g,"%26")+"&MMplayerType="+ad+"&MMdoctitle="+j.title;if(typeof ab.flashvars!=D){ab.flashvars+="&"+ac}else{ab.flashvars=ac}if(M.ie&&M.win&&ae.readyState!=4){var Y=C("div");X+="SWFObjectNew";Y.setAttribute("id",X);ae.parentNode.insertBefore(Y,ae);ae.style.display="none";(function(){if(ae.readyState==4){ae.parentNode.removeChild(ae)}else{setTimeout(arguments.callee,10)}})()}u(aa,ab,X)}}function p(Y){if(M.ie&&M.win&&Y.readyState!=4){var X=C("div");Y.parentNode.insertBefore(X,Y);X.parentNode.replaceChild(g(Y),X);Y.style.display="none";(function(){if(Y.readyState==4){Y.parentNode.removeChild(Y)}else{setTimeout(arguments.callee,10)}})()}else{Y.parentNode.replaceChild(g(Y),Y)}}function g(ab){var aa=C("div");if(M.win&&M.ie){aa.innerHTML=ab.innerHTML}else{var Y=ab.getElementsByTagName(r)[0];if(Y){var ad=Y.childNodes;if(ad){var X=ad.length;for(var Z=0;Z<X;Z++){if(!(ad[Z].nodeType==1&&ad[Z].nodeName=="PARAM")&&!(ad[Z].nodeType==8)){aa.appendChild(ad[Z].cloneNode(true))}}}}}return aa}function u(ai,ag,Y){var X,aa=c(Y);if(M.wk&&M.wk<312){return X}if(aa){if(typeof ai.id==D){ai.id=Y}if(M.ie&&M.win){var ah="";for(var ae in ai){if(ai[ae]!=Object.prototype[ae]){if(ae.toLowerCase()=="data"){ag.movie=ai[ae]}else{if(ae.toLowerCase()=="styleclass"){ah+=' class="'+ai[ae]+'"'}else{if(ae.toLowerCase()!="classid"){ah+=" "+ae+'="'+ai[ae]+'"'}}}}}var af="";for(var ad in ag){if(ag[ad]!=Object.prototype[ad]){af+='<param name="'+ad+'" value="'+ag[ad]+'" />'}}aa.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+ah+">"+af+"</object>";N[N.length]=ai.id;X=c(ai.id)}else{var Z=C(r);Z.setAttribute("type",q);for(var ac in ai){if(ai[ac]!=Object.prototype[ac]){if(ac.toLowerCase()=="styleclass"){Z.setAttribute("class",ai[ac])}else{if(ac.toLowerCase()!="classid"){Z.setAttribute(ac,ai[ac])}}}}for(var ab in ag){if(ag[ab]!=Object.prototype[ab]&&ab.toLowerCase()!="movie"){e(Z,ab,ag[ab])}}aa.parentNode.replaceChild(Z,aa);X=Z}}return X}function e(Z,X,Y){var aa=C("param");aa.setAttribute("name",X);aa.setAttribute("value",Y);Z.appendChild(aa)}function y(Y){var X=c(Y);if(X&&X.nodeName=="OBJECT"){if(M.ie&&M.win){X.style.display="none";(function(){if(X.readyState==4){b(Y)}else{setTimeout(arguments.callee,10)}})()}else{X.parentNode.removeChild(X)}}}function b(Z){var Y=c(Z);if(Y){for(var X in Y){if(typeof Y[X]=="function"){Y[X]=null}}Y.parentNode.removeChild(Y)}}function c(Z){var X=null;try{X=j.getElementById(Z)}catch(Y){}return X}function C(X){return j.createElement(X)}function i(Z,X,Y){Z.attachEvent(X,Y);I[I.length]=[Z,X,Y]}function F(Z){var Y=M.pv,X=Z.split(".");X[0]=parseInt(X[0],10);X[1]=parseInt(X[1],10)||0;X[2]=parseInt(X[2],10)||0;return(Y[0]>X[0]||(Y[0]==X[0]&&Y[1]>X[1])||(Y[0]==X[0]&&Y[1]==X[1]&&Y[2]>=X[2]))?true:false}function v(ac,Y,ad,ab){if(M.ie&&M.mac){return}var aa=j.getElementsByTagName("head")[0];if(!aa){return}var X=(ad&&typeof ad=="string")?ad:"screen";if(ab){n=null;G=null}if(!n||G!=X){var Z=C("style");Z.setAttribute("type","text/css");Z.setAttribute("media",X);n=aa.appendChild(Z);if(M.ie&&M.win&&typeof j.styleSheets!=D&&j.styleSheets.length>0){n=j.styleSheets[j.styleSheets.length-1]}G=X}if(M.ie&&M.win){if(n&&typeof n.addRule==r){n.addRule(ac,Y)}}else{if(n&&typeof j.createTextNode!=D){n.appendChild(j.createTextNode(ac+" {"+Y+"}"))}}}function w(Z,X){if(!m){return}var Y=X?"visible":"hidden";if(J&&c(Z)){c(Z).style.visibility=Y}else{v("#"+Z,"visibility:"+Y)}}function L(Y){var Z=/[\\\"<>\.;]/;var X=Z.exec(Y)!=null;return X&&typeof encodeURIComponent!=D?encodeURIComponent(Y):Y}var d=function(){if(M.ie&&M.win){window.attachEvent("onunload",function(){var ac=I.length;for(var ab=0;ab<ac;ab++){I[ab][0].detachEvent(I[ab][1],I[ab][2])}var Z=N.length;for(var aa=0;aa<Z;aa++){y(N[aa])}for(var Y in M){M[Y]=null}M=null;for(var X in swfobject){swfobject[X]=null}swfobject=null})}}();return{registerObject:function(ab,X,aa,Z){if(M.w3&&ab&&X){var Y={};Y.id=ab;Y.swfVersion=X;Y.expressInstall=aa;Y.callbackFn=Z;o[o.length]=Y;w(ab,false)}else{if(Z){Z({success:false,id:ab})}}},getObjectById:function(X){if(M.w3){return z(X)}},embedSWF:function(ab,ah,ae,ag,Y,aa,Z,ad,af,ac){var X={success:false,id:ah};if(M.w3&&!(M.wk&&M.wk<312)&&ab&&ah&&ae&&ag&&Y){w(ah,false);K(function(){ae+="";ag+="";var aj={};if(af&&typeof af===r){for(var al in af){aj[al]=af[al]}}aj.data=ab;aj.width=ae;aj.height=ag;var am={};if(ad&&typeof ad===r){for(var ak in ad){am[ak]=ad[ak]}}if(Z&&typeof Z===r){for(var ai in Z){if(typeof am.flashvars!=D){am.flashvars+="&"+ai+"="+Z[ai]}else{am.flashvars=ai+"="+Z[ai]}}}if(F(Y)){var an=u(aj,am,ah);if(aj.id==ah){w(ah,true)}X.success=true;X.ref=an}else{if(aa&&A()){aj.data=aa;P(aj,am,ah,ac);return}else{w(ah,true)}}if(ac){ac(X)}})}else{if(ac){ac(X)}}},switchOffAutoHideShow:function(){m=false},ua:M,getFlashPlayerVersion:function(){return{major:M.pv[0],minor:M.pv[1],release:M.pv[2]}},hasFlashPlayerVersion:F,createSWF:function(Z,Y,X){if(M.w3){return u(Z,Y,X)}else{return undefined}},showExpressInstall:function(Z,aa,X,Y){if(M.w3&&A()){P(Z,aa,X,Y)}},removeSWF:function(X){if(M.w3){y(X)}},createCSS:function(aa,Z,Y,X){if(M.w3){v(aa,Z,Y,X)}},addDomLoadEvent:K,addLoadEvent:s,getQueryParamValue:function(aa){var Z=j.location.search||j.location.hash;if(Z){if(/\?/.test(Z)){Z=Z.split("?")[1]}if(aa==null){return L(Z)}var Y=Z.split("&");for(var X=0;X<Y.length;X++){if(Y[X].substring(0,Y[X].indexOf("="))==aa){return L(Y[X].substring((Y[X].indexOf("=")+1)))}}}return""},expressInstallCallback:function(){if(a){var X=c(R);if(X&&l){X.parentNode.replaceChild(l,X);if(Q){w(Q,true);if(M.ie&&M.win){l.style.display="block"}}if(E){E(B)}}a=false}}}}();
/*
 Plugin Name: WP Foto Vote
 Plugin URI: http://wp-vote.net/
 Plugin support EMAIL: support@wp-vote.net
 Version: 2.2.803

 This is commercial script!
 */

window.jQuery || document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"><\/script>');

+(function($) {


    FvLib.addHook('doc_ready', function () {

        // =================================================

        if (fv.cache_support && fv.data && fv.data.length > 0) {
            var send_data = [];
            for (var key in fv.data) {
                if (key != 'link' && !fv.data[key].hide_votes) {
                    send_data.push(key);
                }
            }
            //console.log( send_data );
            /*for (var i=0; i<fv.data.length; i++) {
             send_data.push( fv.data[i].id );
             }*/

            $.post(
                FvLib.add_query_arg("fuckcache", FvLib.randomStr(10), fv.ajax_url),
                {
                    action: 'fv_ajax_get_votes',
                    ids: send_data
                },
                function (data) {
                    data = FvLib.parseJson(data);
                    //console.log(data);
                    if (data.success && typeof data.votes != "undefinded") {
                        for (var key in data.votes) {
                            if (document.querySelector('.sv_votes_' + key) != null) {
                                document.querySelector('.sv_votes_' + key).innerHTML = data.votes[key];
                            }
                        }
                    } else {
                        FvLib.adminNotice(data.message, 'error');
                    }
                }
            ).fail(function () {
                FvLib.adminNotice(fv.lang.ajax_fail, 'error');
            });
            ;
        }
        /*
         if ( !fv.contest_enabled ) {
         FvLib.adminNotice(fv.lang.inactive_contest, 'warning');
         }
         */
        FvLib.callHook('fv/init');
    });

})(jQuery);

+(function() {

    var FvShare = {};

    // This function Open share window for selected Social network
    FvShare.send = function(service, el, id, for_vote) {
        if (typeof (id) !== 'undefined' && id) {
            var current = fv.data[id];
        } else {
            var current = fv.data[window.fv_current_id];
        }

        //var url = sv_data['link'] + '#photo-' + [window.fv_current_id];
        var fv_url = FvLib.applyFilters('fv/share/page_url', FvLib.singlePhotoLink(current.id), service, current);

        // action before voting
        if (!FvLib.callHook('fv/share_start', service, current, for_vote)) {
            return false;
        }

        // Title
        title = '';
        if (fv.ct[current.ct_id].soc_title && fv.ct[current.ct_id].soc_title.length > 3) {
            var title = fv.ct[current.ct_id].soc_title.replace("*name*", current.name);
        } else if (current.name) {
            var title = current.name.replace("\\", '');
        }
        //** apply filters for Title
        title = FvLib.applyFilters('fv/share/title', title, current, current);

        description = '';
        // Description
        if (fv.ct[current.ct_id].soc_description && fv.ct[current.ct_id].soc_description.length > 3) {
            var description = fv.ct[current.ct_id].soc_description;
            description = fv.ct[current.ct_id].soc_description.replace("*name*", current.name);
        } else {
            if (current.social_description && current.social_description.length > 4) {
                var description = current.social_description.substr(0, 100);
            } else if (current.description) {
                var description = current.description.substr(0, 100);
            }
        }
        //** apply filters for Description
        description = FvLib.applyFilters('fv/share/description', description, service, current);


        // Social image
        if (fv.ct[current.ct_id].soc_picture && fv.ct[current.ct_id].soc_picture.length > 1) {
            var image = fv.ct[current.ct_id].soc_picture;
        } else {
            var image = current.url;
            //var image = '';
        }
        //** apply filters for Image
        /**
         * @param image         Image URL
         * @param service       Social Network
         * @param current       Entry object - Added in version 2.2.600
         */
        image = FvLib.applyFilters('fv/share/image', image, service, current);

        var url = '';
        switch (service) {
            case 'whatsapp':
                var share_btn = el.querySelector(".sw-share-button");
                share_btn.href = share_btn.dataset.href.replace("{text}",  encodeURIComponent(title) + " " + encodeURIComponent(description) + " " + encodeURIComponent(fv_url));
                return;
            case 'fb':
                // if configured FB api key
                // https://developers.facebook.com/docs/sharing/reference/share-dialog
                if (fv.fb_dialog == "feed" && typeof(FB) !== 'undefined' && fv.fv_appId.length > 3) {
                    FB.ui({
                        app_id: fv.fv_appId,
                        method: 'feed',
                        //display: 'popup',
                        link: fv_url,
                        caption: title,
                        description: description,
                        picture: image
                    }, function (response) {
                        //** apply filters for retrieved data
                        response = FvLib.applyFilters('fv/share/fb_data', response);

                        //console.log(response);
                        if (typeof (response) === "undefined" || response == null) {
                            FvLib.logSave('was not shared');
                        } else if (typeof (response.error_code) !== "undefined") {
                            FvLib.logSave(response);
                        } else {
                            console.log(response);
                            ;
                            FvLib.logSave('shared - post id is ' + response.post_id);
                            if (typeof (for_vote) !== "undefined") {
                                fb_post_id = 1;
                                sv_vote(window.fv_current_id, "fb_shared");
                            }
                        }

                    });
                    return false;
                } else {
                    // {r119} # changed on 23-01-2017
                    url = "https://www.facebook.com/sharer.php?u=" + encodeURIComponent(fv_url) + "&nocache-" + FvLib.randomStr(8);
                }
                break;
            case 'tw':
                url = "https://twitter.com/share?text=" + encodeURIComponent(title) + " " + encodeURIComponent(description) + "&url=" + encodeURIComponent(fv_url) + "&counturl=" + encodeURIComponent(fv_url) + "&nocache-" + FvLib.randomStr(8);
                break;
            case 'vk':
                url = "https://vk.com/share.php?title=" + encodeURIComponent(title) + "&description=" + encodeURIComponent(description) + "&url=" + encodeURIComponent(fv_url) + "&image=" + encodeURIComponent(image);
                break;
            case 'ok':
                url = "http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st.comments=" + encodeURIComponent(title) + ": " + encodeURIComponent(description) + "&st._surl=" + encodeURIComponent(fv_url);
                break;
            case 'gp':
                url = "https://plusone.google.com/_/+1/confirm?hl=" + fv.user_lang + "&url=" + encodeURIComponent(fv_url);
                break;
            case 'pi':
                //http://pinterest.com/pin/create/button/?url={URI-encoded URL of the page to pin}&media={URI-encoded URL of the image to pin}&description={optional URI-encoded description}
                url = "https://pinterest.com/pin/create/button/?url=" + encodeURIComponent(fv_url) + '&media=' + encodeURIComponent(image) + '&description=' + encodeURIComponent(title + ' - ' + current['name']);
                break;
            default:
                return false;
        }

        // action before voting
        if (!FvLib.callHook('fv/share_before_send', service, current, fv_url)) {
            return false;
        }

        window.open(url, '', 'toolbar=0,status=0,width=626,height=436');

        // action before voting
        if (!FvLib.callHook('fv/share_after_send', service, current, fv_url)) {
            return false;
        }

        return false;
    }

    window.sv_vote_send = FvShare.send;
    window.FvShare = FvShare;

})();
+(function($) {


    window.fv_soc_counter_callbacks = {};

    function fv_get_count_Fb_all(photos_count) {
        /*
         FB.api(
         '/http%3A%2F%2Fwp-vote.net%2Fdemo-photo-contest-pinterest-theme%2F%3Fcontest_id%3D2%26photo%3D2',
         'GET',
         {},
         function(response) {
         // Insert your code here
         }
         );
         */

        //http://graph.facebook.com/?ids=http://wp-vote.net/,http://wp-vote.net/pricing/
        /*
         {
         "http://wp-vote.net/": {
         "id": "http://wp-vote.net/",
         "shares": 8
         },
         "http://wp-vote.net/pricing/": {
         "id": "http://wp-vote.net/pricing/",
         "shares": 1
         }
         }
         */
        //http://api.facebook.com/restserver.php?method=links.getStats&urls=http://wp-vote.net/,http://wp-vote.net/pricing/
        /*

         <?xml version="1.0" encoding="UTF-8"?>
         <links_getStats_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://api.facebook.com/1.0/ http://api.facebook.com/1.0/facebook.xsd" list="true">
         <link_stat>
         <url>http://wp-vote.net/</url>
         <normalized_url>http://www.wp-vote.net/</normalized_url>
         <share_count>3</share_count>
         <like_count>4</like_count>
         <comment_count>1</comment_count>
         <total_count>8</total_count>
         <click_count>0</click_count>
         <comments_fbid>316931095121571</comments_fbid>
         <commentsbox_count>0</commentsbox_count>
         </link_stat>
         <link_stat>
         <url>http://wp-vote.net/pricing/</url>
         <normalized_url>http://www.wp-vote.net/pricing/</normalized_url>
         <share_count>1</share_count>
         <like_count>0</like_count>
         <comment_count>0</comment_count>
         <total_count>1</total_count>
         <click_count>0</click_count>
         <comments_fbid>645097812250216</comments_fbid>
         <commentsbox_count>0</commentsbox_count>
         </link_stat>
         </links_getStats_response>

         */
        var urls = {};
        var urlsCounter = 0;

        for (var ID in fv.data) {
            if (fv.data.hasOwnProperty(ID) && ID != 'link') {
                urls[Math.floor(urlsCounter/50)] = urls[Math.floor(urlsCounter/50)] || "";
                urls[Math.floor(urlsCounter/50)] += encodeURIComponent(FvLib.singlePhotoLink(ID)) + ',';
                urlsCounter++;
            }
        }
        for (var N in urls) {
            urls[N] = urls[N].substring(0, urls[N].length - 1);

            //var link = 'https://api.facebook.com/method/fql.query?query=select total_count from link_stat where url="' + encodeURIComponent(url) + '"&format=json&callback=?';
            var link = 'https://graph.facebook.com/?fields=og_object{engagement}&ids=' + urls[N];
            jQuery.getJSON(link, function (dataArr) {

                var share_count;
                for (var ID in fv.data) {
                    if ( undefined !== dataArr[FvLib.singlePhotoLink(ID)]
                        && fv.data.hasOwnProperty(ID) && ID != 'link' && (!fv.data[ID].hasOwnProperty("sc") || fv.data[ID].sc == 1)
                        && dataArr[FvLib.singlePhotoLink(ID)].hasOwnProperty("og_object")
                    ) {
                        share_count = dataArr[FvLib.singlePhotoLink(ID)]["og_object"]["engagement"]["count"];
                        FvLib.logSave('get FB for ' + ID + ' #' + share_count);
                        if (share_count > 0) {
                            fv_add_soc_count(ID, share_count);
                            fv.data[ID].sc = 2;
                        }
                    }
                }

            });
        }
    }

    function fv_get_count_Vk(url, id) {

        if (!window.VK || !window.VK.Share) {
            if (!window.VK) {
                window.VK = {};
            }
            window.VK.Share = {
                count: function (idx, shares) {
                    FvLib.logSave('get VK for ID ' + idx + ' #' + shares);
                    if (shares > 0) {
                        fv_add_soc_count(idx, shares);
                    }
                }
            };
        }

        var link = '//vk.com/share.php?act=count&index=' + id + '&url=' + encodeURIComponent(url);

        jQuery.getScript(link);
    }

    function fv_get_count_Ok(url, id) {

        if (!window.ODKL || !window.ODKL.updateCount) {
            window.ODKL = {};
            window.ODKL.updateCount = function (idx, shares) {
                FvLib.logSave('get OK for ID ' + idx + ' #' + shares);
                if (shares > 0) {
                    fv_add_soc_count(idx, shares);
                }
            };
        }

        var link = '//connect.ok.ru/dk?st.cmd=extLike&ref=' + encodeURIComponent(url) + '&uid=' + id;
        jQuery.getScript(link);
    }

    function fv_get_count_Mm_all(url, id) {
        var urls = '';

        for (var ID in fv.data) {
            if (fv.data.hasOwnProperty(ID) && ID != 'link') {
                //var attr = object[index];
                urls += encodeURIComponent(FvLib.singlePhotoLink(ID)) + ',';
            }
        }
        urls = urls.substring(0, urls.length - 1);

        var callbk_name = 'mm_all';
        var link = '//connect.mail.ru/share_count?callback=1&url_list=' + urls + '&func=fv_soc_counter_callbacks.' + callbk_name;

        fv_soc_counter_callbacks[callbk_name] = function (respObj) {
            // upon success, remove the name
            delete fv_soc_counter_callbacks[callbk_name];

            if (jQuery.isEmptyObject(respObj)) {
                FvLib.logSave('res MM  - all 0');
                return false;
            }

            for (var ID in fv.data) {
                if (fv.data.hasOwnProperty(ID) && ID != 'link' && !fv.data[ID].hasOwnProperty('sc')) {
                    if (respObj[FvLib.singlePhotoLink(ID)].hasOwnProperty("shares") && respObj[FvLib.singlePhotoLink(ID)]['shares'] > 0) {
                        FvLib.logSave('res MM for = ' + FvLib.singlePhotoLink(ID) + ' #' + respObj[FvLib.singlePhotoLink(ID)]['shares']);

                        fv_add_soc_count(ID, respObj[FvLib.singlePhotoLink(ID)]['shares']);
                    }
                }
            }
        };

        jQuery.getScript(link);
    }

    function fv_get_count_Pi(url, id) {
        var callbk_name = 'pi' + id;
        var link = '//api.pinterest.com/v1/urls/count.json?callback=fv_soc_counter_callbacks.' + callbk_name + '&url=' + encodeURIComponent(url);

        jQuery.getScript(link);

        fv_soc_counter_callbacks[callbk_name] = function (shares_data) {
            // upon success, remove the name
            delete fv_soc_counter_callbacks[callbk_name];
            var shares = parseInt(shares_data.count);
            FvLib.logSave('res Pi for = ' + id + ' #' + shares);

            if (shares > 0) {
                fv_add_soc_count(id, shares);
            }
        };
    }

    /**
     * Increase votes count in Html element with specified ID above image after voting
     */
    function fv_add_soc_count(id, count) {
        var container = $('.fv_svotes_' + id);
        var val = parseInt(container.html(), 10);
        if (!val) val = 0;
        val += parseInt(count);
        container.text(val);
    }

    function fv_run_social_counter() {
        // Soc COunters
        var photos_count = Object.keys(fv.data).length;
        if (photos_count > 0) {
            if (fv.soc_counters.fb) {
                fv_get_count_Fb_all(photos_count);    //+
            }
            if (fv.soc_counters.mm) {
                fv_get_count_Mm_all(photos_count);    //+
            }
            for (var ID in fv.data) {
                if (fv.data.hasOwnProperty(ID) && ID != 'link' && !fv.data[ID].hasOwnProperty('sc')) {
                    //var attr = object[index];
                    var link = FvLib.singlePhotoLink(ID);
                    
                    if (fv.soc_counters.vk) {
                        fv_get_count_Vk(link, ID);    //+
                    }
                    if (fv.soc_counters.ok) {
                        fv_get_count_Ok(link, ID);    //+
                    }

                    if (fv.soc_counters.pi) {
                        fv_get_count_Pi(link, ID);      //+
                    }
                    fv.data[ID]['sc'] = 1;
                }
            }

        }
    }

    if (fv.soc_counter) {
        $(window).load(function () {
            fv_run_social_counter();
        });

        FvLib.addHook('fv/ajax_go_to_page/ready', fv_run_social_counter, 11);
    }

})(jQuery);
+(function($) {

    var FvGallery = window.FvGallery || {};

    var is_loading = false;

    FvGallery.goToPage = function(page, contest_id, sorting, s_string, infinite, search, category_slug) {

        if ( is_loading ) {
            return;
        }

        // check, may be data is loading
        if (jQuery(".fv_contest-container--" + contest_id + " .fv_photos-container").hasClass('preload')) {
            return;
        }

        if (infinite == undefined || !infinite) {
            infinite = false;
        }

        var params = {
            action: 'fv_ajax_go_to_page',
            contest_id: contest_id,
            post_id: fv.post_id,
            'fv-sorting': sorting,
            'fv-page': page,
            'fv-search': search,
            'fv-category': category_slug,
            some_str: s_string,
            theme: fv.theme,
        };

        params = FvLib.applyFilters('fv/ajax_go_to_page/params', params);

        is_loading = true;

        jQuery(".fv_contest-container--" + contest_id + " .fv_photos-container").addClass('preload');
        jQuery.get(
            fv.ajax_url,
            params,
            function (response) {

                is_loading = false;
                //jQuery('.fv-contest-photos-container').removeClass('preload');
                response = FvLib.parseJson(response);

                //console.log( response );

                if (response.result == "ok") {
                    FvLib.callHook('fv/ajax_go_to_page/resp_ok', page, contest_id);

                    var $photos_container = jQuery(".fv_contest-container--" + response.contest_id + " .fv_photos-container");
                    var infiniteContainerSelector = FvLib.applyFilters('fv/fv_ajax_go_to_page/infinite_selector', '.fv-contest-photos-container-inner');
                    var $photosHtml = jQuery(response.html);
                    if (!infinite) {
                        $photos_container.find(infiniteContainerSelector).html($photosHtml.find('.contest-block'));
                        $photos_container.find('.fv-pagination').replaceWith($photosHtml.find('.fv-pagination'));

                        fv.data = response.photos_data;
                        fv.ct[response.contest_id].single_link_template = response.single_link_template;
                        //jQuery('#photo_id').attr("data-url", response.share_page_url + '=');
                        setTimeout(
                            function () {
                                jQuery('html, body').animate({scrollTop: jQuery('.fv_contest_container').offset().top - 50}, 500)
                            }, 300
                        );

                    } else {
                        $photos_container.find('.infinite').remove();
                        if (infiniteContainerSelector != false) {
                            $photos_container.find(infiniteContainerSelector).append($photosHtml.find('.contest-block'));
                        } else {
                            $photos_container.append($photosHtml.find('.contest-block'));
                        }
                        $photos_container.append($photosHtml.find('.fv-pagination'));

                        fv.data = jQuery.extend({}, fv.data, response.photos_data);
                        fv.ct[response.contest_id].single_link_template = response.single_link_template;
                        /*jQuery('html, body').animate({
                         scrollTop: $photos_container.offset().top + $photos_container.height() - 250
                         }, 500);*/
                    }
                    $photos_container.removeClass("preload")

                    FvLib.callHook('fv/ajax_go_to_page/ready', page, contest_id);

                    // TODO - fix single photo url!

                    var new_url = "";
                    if (page > 1) {
                        new_url = fv.paged_url.replace("fv-page=1", "fv-page=" + page);
                    } else {
                        new_url = fv.paged_url.replace("?fv-page=1", "").replace("&fv-page=1", "");
                    }
                    if (search) {
                        new_url = FvLib.add_query_arg("fv-search", search, new_url);
                    }
                    if (category_slug) {
                        new_url = FvLib.add_query_arg("fv-category", category_slug, new_url);
                    }
                    window.history.pushState('', '', new_url + '#contest');

                    if (fv.social_counter) {
                        fv_run_social_counter();
                    }

                } else if (response.result == "fail") {
                    alert(response.msg);
                }
            }
        );
        return false;
    }

    window.FvGallery = FvGallery;
    window.fv_ajax_go_to_page = FvGallery.goToPage;

    /**
     * Infinite scroll
     * @since 2.3.10
     */
    if ( "infinite-auto" == fv.pagination ) {
        FvLib.loadScript( fv.plugin_url + "/assets/vendor/jquery.infinite-scroll-helper.min.js", function () {
            $(".fv_contest-container").infiniteScrollHelper({
                loadMore: function(page, done) {
                    console.log( "Infinite - load page " + page, done );
                    $(".fv-pagination .fv-infinite-load").click();
                },

                startingPageCount: FvLib.queryString("fv-page") ? FvLib.queryString("fv-page") : 1,
                bottomBuffer: 150,

                doneLoading: function() {
                    // return true if you are done doing your thing, false otherwise
                    return is_loading;
                }
            });
        } );
    }

})(jQuery);
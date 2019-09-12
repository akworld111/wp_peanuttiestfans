/*
 Plugin Name: WP Foto Vote
 Plugin URI: http://wp-vote.net/
 Author: Maxim Kaminsky
 Author URI: http://maxim-kaminsky.com/
 */

(function ($) {
    /**
     * Creates Modal window with some functions
     */
	var FvModal = {
		selector: "#modal-widget",
		msg_type: "",
		modalWidth: 0,
		modalWidthDefault: 420,
		emailShareRecaptchaID: false,
		voteRecaptchaID: false,
        subscribeRecaptchaID: false,
		ratingInit: false,
		currentScreen: false,
		/*
		 * Set default options
		 */
		init: function () {
			this.$el = $(this.selector);
			// Since 2.2.804
			if ( !this.$el.parent().is('body') ) {
				this.$el.detach().appendTo("body");
			}

			this.$msg = this.$el.find(".sw-message-box");
			this.$msg_body = this.$el.find(".sw-message-box .sw-message-text");
			this.$msg_title = this.$el.find(".sw-message-box .sw-message-title");
			if ( ! FvLib.isMobile() ) {
				jQuery(".sw-mobile-only").remove();
			}
			this.modalWidth = this.$el.find(".sw-share .sw-options li").length * 80 + 20;

			$(".sw-subscribe-verify .fv-change-subscr-mail", this.$el).on( "click", function() { FvModal.goStartSubscribe(); } );

			$(".sw-vote-math-captcha .fv-captcha-vote", this.$el).click(function(e) {
				sv_vote( window.fv_current_id, "captcha" );
			});
		},

		/*
		* Open modal with rating stars
		 */
		goRate: function (photo_id) {
			if (photo_id !== undefined && photo_id > 0) {
				if  (!this.ratingInit) {
					$(".fv-rating-set input", this.$el).change(function(e) {
						var stars = $(".fv-rating-set input:checked", this.$el);
						if ( stars.length != 0 ) {
							$(".rating-counter-selected", this.$el).text( stars.val() );
						}
						return true;
					});
					$(".sw-rating .fv-rate", this.$el).click(function(e) {
						var stars = $(".fv-rating-set input:checked", this.$el);
						if ( stars.length == 0 ) {
							FvModal.showNotification("info", "", fv.lang.rate_need_select, 0, 0);
							return false;
						}
						sv_vote( window.fv_current_id, "rate", null, stars.val() );

					});
					this.ratingInit = true;
				}
				// Reset stars selection
				$(".fv-rating-set input:checked", this.$el).prop('checked', false);
				$(".rating-counter-selected", this.$el).text("0.0");

				$(this.selector).width( FvModal.modalWidthDefault );
				this.setTitle(fv.lang.rate_popup_title);
				this.setSlogan("");

				this.openWidget("rating");
			}
		},

		/*
		* Open modal with share buttons
		 */
		goShare: function (photo_id) {
			if (photo_id !== undefined && photo_id > 0) {
				this._prepareToShare(photo_id);

				this.openWidget("share");
			}
		},
		_prepareToShare: function (photo_id) {
			if (photo_id !== undefined && photo_id > 0) {
				window.fv_current_id = photo_id;
				this.$el.find("#photo_id").val( FvLib.singlePhotoLink(photo_id) );
				this.setTitle(fv.lang.title_share);
				this.setSlogan("");

				// Set dialog width AS count icons * 80 (icons width)
				$(this.selector).width( FvModal.modalWidth );
				if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
				$(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );
			}
		},

		/*
		 * Open modal with Recaptcha
		 */
		goRecaptchaVote: function (photo_id, wrong) {
			if ( fv.recaptcha_key == false ) {
				alert("Recaptcha Api Error!");
				return false;
			}

			if (photo_id !== undefined && photo_id > 0) {
				window.fv_current_id = photo_id;
			}
			if ( this.voteRecaptchaID === false ) {
				this.voteRecaptchaID = grecaptcha.render('sw-vote-g-recaptcha', {
					'sitekey' : fv.recaptcha_key,
					'callback' : fv_recaptcha_ready,
					size : FvLib.isMobile() ? 'compact' : ''
					//'hl' : 'en'
					//https://developers.google.com/recaptcha/docs/language
				});
			} else {
				grecaptcha.reset( this.voteRecaptchaID );
			}

			$(this.selector).width( this.modalWidthDefault );
			this.setTitle(fv.lang.title_recaptcha_vote);

			this.openWidget("vote-recaptcha");

			// Wrong warning
			if ( typeof(wrong) != "undefined" && wrong == true ) {
				this.showNotification("info", "", fv.lang.msg_recaptcha_wrong, 0, 0);
			} else {
				this.hideNotification();
			}

		},

		/*
		 * Open modal with MathCaptcha
		 */
		goMathCaptchaVote: function (photo_id, wrong_message, new_html) {
			if (photo_id !== undefined && photo_id > 0) {
				window.fv_current_id = photo_id;
			}

			if ( !this.isVisible() || this.currentScreen != "vote-math-captcha" ) {
				$(this.selector).width(this.modalWidthDefault);
				this.setTitle(fv.lang.title_math_captcha_vote);
				this.openWidget("vote-math-captcha");
			}

			// Wrong warning
			if ( typeof(wrong_message) != "undefined" && wrong_message ) {
				this.showNotification("warning", "", wrong_message, 0, 0);
			} else {
				this.showNotification("info", "", fv.lang.msg_math_captcha, 0, 0);
			}

			if ( typeof(new_html) != "undefined" && new_html ) {
				this.$el.find(".math-captcha-wrap").html( new_html );
			}
		},

		/*
		* Open modal with Preloader + message + share buttons
		 */
		goStartVote: function (photo_id) {
			if (photo_id !== undefined && photo_id > 0) {
				this._prepareToShare(photo_id);
				//this.$el.find("#photo_id").val( this.$el.find("#photo_id").attr("data-url") + photo_id );
				this.setTitle(fv.lang.title_voting);
				this.setSlogan("");

				// Set dialog width AS count icons * 80 (icons width)
				$(this.selector).width( FvModal.modalWidth );
				$(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );

				this.openWidget('share', "vote");
				this.setVotingNotificationWithSpinner();
			}
		},

		/*
		* Show Preloader + message
		 */
		setVotingNotificationWithSpinner: function () {
			this.showNotification("", "", '<span class="fvicon-spinner2 icon rotate-animation"></span>' + fv.lang.msg_voting, 0, 0);
		},

		/*
		 * Change in modal Title + Message
		 */
		goVoted: function (status, title, msg, subtitle, photo_id) {
			this._prepareToShare(photo_id);
			
			this.setTitle(title);
			if (!subtitle) {
				this.setSlogan(fv.lang.invite_friends);
			}

			// Set dialog width AS count icons * 80 (icons width)
			this.$el.width( FvModal.modalWidth );
			$(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );

			this.openWidget('share', "voted-" + status);
			this.showNotification(status, "", msg, 0, 0);
			//this.$el.find("#photo_id").val( FvLib.singlePhotoLink(photo_id) );
		},
		/*
		 * Change in modal Title + Message
		 * @since 2.3.00
		 */
		goWarning: function (title, msg, status) {
			if ( !status ) {
				status = "error";
			}
			this.setTitle(title);

			// Set dialog width AS count icons * 80 (icons width)
			this.$el.width( FvModal.modalWidth );
			this.openWidget('empty', "warning");
			this.showNotification(status, "", msg, 0, 0);
			//this.$el.find("#photo_id").val( FvLib.singlePhotoLink(photo_id) );
		},
		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goStartSubscribe: function () {
			this.setTitle(fv.lang.form_subsr_title);

			this.openWidget("subscribe");

			this.showSubscribeReCaptcha();

			this.$el.width(400);
			this.showNotification("info", "", fv.lang.form_subsr_msg, 0, 0);
		},

		showSubscribeReCaptcha: function() {
            if ( fv.recaptcha_subscribe && fv.recaptcha_key ) {
                if (this.subscribeRecaptchaID === false) {
                    this.subscribeRecaptchaID = grecaptcha.render('sw-subscribe-g-recaptcha', {
                        'sitekey': fv.recaptcha_key,
                        //'callback' : fv_recaptcha_ready,
                        size: FvLib.isMobile() ? 'compact' : ''
                        //'hl' : 'en'
                        //https://developers.google.com/recaptcha/docs/language
                    });
                } else {
                    grecaptcha.reset(this.subscribeRecaptchaID);
                }
            }

		},

		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goPrivacyPolicyAgree: function () {
			this.setTitle(fv.lang.privacy_popup_title);
			this.openWidget("privacy");
			this.$el.width(400);
		},

		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goVoteConfirm: function () {
			this.setTitle("Confirm vote");
			this.openWidget("voteconfirm");
			this.$el.width(400);
		},
		
		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goSubscribeVerifySend: function (verify_send) {
			this.setTitle(fv.lang.form_subsr_title);
			this.openWidget("subscribe-verify");
			this.$el.width(400);
			if (!verify_send) {
				this.showNotification("warning", "",  fv.lang.form_subscr_verify_send, 0, 0);
			} else {
				this.showNotification("warning", "",  fv.lang.form_subscr_verify_already_send, 0, 0);
			}

		},
		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goStartSocialAuthorization: function () {
			this.setTitle(fv.lang.title_not_voted);

			$(this.selector).width( this.$el.find(".sw-social-authorization .sw-options li").length * 80 + 10 );

			this.openWidget("social-authorization");

			this.showNotification("info", "", fv.lang.form_soc_msg, 0, 0);
		},
		/*
		 * Open Social Login modal
		 */
		goStartSocialLogin: function () {
			this.setTitle(fv.lang.title_not_voted);

			$(this.selector).width( this.$el.find(".sw-wp-social-login .sw-share-button").length * 80 + 20 );

			this.openWidget("wp-social-login");

			this.showNotification("info", "", fv.lang.form_soc_msg, 0, 0);
		},
		/*
		 * Open modal with Title + message + Subscribe form
		 */
		goFbVote: function () {
			this.setTitle(fv.lang.title_not_voted);

			$(this.selector).width( this.modalWidthDefault );

			this.openWidget("fb-vote");

			this.showNotification("info", "", fv.lang.fb_vote_msg, 0, 0);
		},

		setTitle: function (title) {
			this.$el.find("> h2").html(title);
		},
		setSlogan: function (slogan) {
			this.$el.find("div.slogan").html(slogan);
		},
		openWidget: function (screen, action) {
			if ( screen !== undefined && screen.length >= 1 ) {
				this.changeScreen(screen);
			}
			FvLib.callHook('fv/modal/open_widget', screen);

			if ( ! FvLib.applyFilters('fv/modal/need_open_widget', true, screen, action) ) {
				return;
			}

			//this.open();
			this.hideNotification();
			if ( !this.isVisible() ) {
				this.$el.bPopup(FvLib.applyFilters('fv/modal/bPopup-options', {
                    appendTo : $("#modal-widget").parent(),
					appending : false,
					closeClass: 'modal-widget-close',
					opacity: 0.77,
					onOpen: function () {
						if ( screen == 'share' ) {
							if ( FvLib.isMobile() ) {
								$(this.selector + " h2, " + this.selector + " .sw-message-box").on('touchend', function () {
									FvModal.close();
								});
							}
						}
						$("body").addClass("fv-modal-opened");
						//jQuery(".mfp-wrap").removeAttr("tabindex");
						$("body > [tabindex='-1']").removeAttr("tabindex").attr("tabindex-1", 1);

					},
					onClose: function () {
						FvLib.callHook('fv/modal/close', screen);
						$("body").removeClass("fv-modal-opened");
						//jQuery(".mfp-wrap").attr("tabindex", "-1");
						$("[tabindex-1]").attr("tabindex", "-1");
					}
				}));
			}

		},
		/*
		 * Change postion, when Modal size changed
		 */
		reposition: function () {
			this.$el.reposition();
		},

		close: function () {
			if (this.isVisible() === true) {
				this.$el.bPopup().close();
			}
		},
		isVisible: function () {
			return this.$el.is(":visible")
		},
		isAnimated: function () {
			return this.$el.is(":animated")
		},
		/*
		 * Change screen (hide all bloks, and show selected)
		 */
		changeScreen: function (toScreen) {
			if (toScreen.length < 1) {
				return false;
			}
			if (this.currentScreen == toScreen) {
				return;
			}
			this.currentScreen == toScreen;
			// seletor to find
			var screen_selector = ".sw-" + toScreen;
			// Check that Screen is Hidden
			if ( !$(".sw-body " + screen_selector, this.$el).is(":visible") ) {
				// hide all other sections AND show section
				$(".sw-body", this.$el)
					.find("> *:not(" + screen_selector + ")").hide()
					.parent().find("> " + screen_selector).fadeIn();
				// If not check, see error
				if ( this.isVisible() ) {
					this.reposition();
				}
			}
		},

		/*
		 * Show Notification message under Title
		 */
		showNotification: function (type, message, title, hideDuration, showDuration) {
			this.hideNotification();
			if (type !== undefined || type.length > 1) {
				this.msg_type = type;
				this.$msg.addClass(type)
			}
			if (message && message.length > 1) {
				this.$msg_body.html(message);
			}
			if (title && title.length > 1) {
				switch (type) {
					case "success":
						title = '<span class="fvicon-checkmark-circle"></span> ' + title;
						break;
					case "error":
						title = '<span class="fvicon-cancel-circle"></span> ' + title;
						break;
					case "info":
						title = '<span class="fvicon-info"></span> ' + title;
						break;
				}
				this.$msg_title.html(title);
			}
			// hide notification after some interval
			if (hideDuration !== undefined || hideDuration > 100) {
				//setTimeout(this.hideNotification(), hideDuration);
			}
			if (showDuration == undefined) {
				showDuration = 250;
			}
			return this.$msg.fadeIn(showDuration);
		},
		hideNotification: function () {
			this.$msg_title.html("");
			this.$msg_body.html("");
			return this.$msg.stop(!0, !1).fadeOut(100).removeClass(this.msg_type);
		}

	};  // END :: FvModal

	window.FvModal = FvModal;

	// Add check, will contest block exists, if not, try wait
	if ( document.querySelectorAll('#modal-widget').length > 0 ) {
		window.FvModal.init();
	} else {
		setTimeout(function(){window.FvModal.init();}, 800);
	}

}(jQuery));
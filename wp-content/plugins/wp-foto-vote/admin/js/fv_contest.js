FvLib.addHook('doc_ready', function() {
	FvRouter.addController("Contest", Simple.Controller.extend({

		submitContestForm: function(eventEl) {

			jQuery("#contest-form").submit();
			
			return false;
		},

		clone: function(eventEl) {
			var contest_id = jQuery(eventEl).data("contest");
			var with_content = jQuery(eventEl).data("with-content");

			jQuery.growl.notice({ message: fv_lang.clone_contest_start });

			jQuery.get(fv.ajax_url, {action: 'fv_clone_contest', contest_id: contest_id, with_content: with_content, fv_nonce: fv.nonce},
				function(data){
					data = FvLib.parseJson(data);
					if ( data.success && typeof data.new_url != "undefined" ) {
						window.location = data.new_url;
						jQuery.growl.notice({ message: fv_lang.clone_contest_redirect });
					} else {
						jQuery.growl.warning({ message: 'Some error on Clonning!' });
					}
				});

			return false;
		},

		clearStats: function (eventEl) {
			var contest_id = jQuery(eventEl).data("contest");

			if ( confirm(fv_lang.clear_stats_alert) ) {
				jQuery('.clear_ip').append('<span class="spinner"></span>');
				jQuery.get(fv.ajax_url, {action: 'fv_clear_contest_stats', contest_id: contest_id, fv_nonce: fv.nonce},
					function(data){
						data = FvLib.parseJson(data);
						if ( data.success ) {
							jQuery('.clear_ip .spinner').remove();
							jQuery('.clear_ip').append('<span class="result">'+fv_lang.clear_stats_cleared+'</span>');
							jQuery.growl.notice({ message: fv_lang.clear_stats_cleared });
						}else {
							console.log( data );
							alert( data.message );
						}
					});
			} // END :: if
		},

		clearSubscribers: function (eventEl) {
			var contest_id = jQuery(eventEl).data("contest");

			if ( confirm(fv_lang.clear_subscribers_alert) ) {
				jQuery(".clear_subscribers").append('<span class="spinner"></span>');
				jQuery.get(fv.ajax_url, {action: "fv_clear_contest_subscribers", contest_id: contest_id, fv_nonce: fv.nonce},
					function(data){
						jQuery(".clear_subscribers .spinner").remove();
						data = FvLib.parseJson(data);
						if ( data.success ) {
							jQuery.growl.notice({ message: fv_lang.clear_stats_cleared });
						} else {
							console.log( data );
							alert( data.message );
						}
					});
			} // END :: if
		},

		resetVotes: function (eventEl) {
			var contest_id = jQuery(eventEl).data("contest");

			if ( confirm(fv_lang.reset_votes_alert) ) {
				jQuery('.clear_votes').append('<span class="spinner"></span>');
				jQuery.get(fv.ajax_url, {action: 'fv_reset_contest_votes', contest_id: contest_id, fv_nonce: fv.nonce},
					function(data){
						data = FvLib.parseJson(data);
						if ( data.success ) {
							jQuery('.clear_votes .spinner').remove();
							jQuery('.clear_votes').append('<span class="result">'+fv_lang.reset_votes_ready+'</span>');
							jQuery.growl.notice({ message: fv_lang.reset_votes_ready });
							jQuery(".column-votes_count > span").text("0");
						}
					});
			} // END :: if

		},

		showCloneContestWnd:function (eventEl) {
			jQuery('#fv_popup .modal-content').html( jQuery("#clone-contest-modal-template").html() );
			jQuery('#fv_popup').modal();
		}

	}));
});

// Find if a selected tab is saved in localStorage
if ( typeof(localStorage) != 'undefined' ) {
	active_tab = localStorage.getItem('active_tab');
}
if (  window.location.hash.indexOf("options-group-") !== -1 ) {
	active_tab = window.location.hash;
}

// If active tab is saved and exists, load it's .group
if ( active_tab != '' && $(active_tab).length ) {
	$(active_tab).fadeIn();
	$(active_tab + '-tab').addClass('nav-tab-active');
} else {
	$('.group:first').fadeIn();
	$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
}

// Bind tabs clicks
//$navtabs.click(function(e) {
if (typeof(localStorage) != 'undefined' ) {
	localStorage.setItem('active_tab', $(this).attr('href') );
}
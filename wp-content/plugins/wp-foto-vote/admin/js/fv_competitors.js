// Var for save Thumbnail image to refresh after action
var imgToRefreshAfterRotate = null;

function fv_rotate_image(eventEl, angle, contest_id, contestant_id, fv_nonce) {
	if ( jQuery(eventEl).data("confirmation") !== "yes" || confirm( fv_lang.rotate_confirm ) ) {
		jQuery(eventEl).closest('td').append('<span class="spinner"></span>');
		// thumbnail image to refresh
		//var $imgToRefresh = jQuery(eventEl).closest('tr').find('.img img');
		jQuery.growl.notice({ message: fv_lang.rotate_start.replace("*A*", angle) });

		jQuery.ajax({
			type: 'POST',
			url: fv.ajax_url,
			data: {action: 'fv_rotate_image', angle: angle, competitor_id: contestant_id, fv_nonce: fv_nonce},
			success: function(data) {
				data = FvLib.parseJson(data);
				jQuery(eventEl).closest("td").find(".spinner").remove();
				if( data.success ) {
					jQuery.growl.notice({ message: fv_lang.rotate_successful });
                    var $imgToRefresh = jQuery("tr[data-id='" + data.competitor_id + "']").find('.fv-table-thumb');
					// don't remember refresh thumbnail image
                    if ( data.new_src == false ) {
                        $imgToRefresh
							.attr("src", $imgToRefresh.attr("src") + "?ModPagespeed=off&nocache=" + FvLib.randomStr(8))
							.addClass('rotated');
                    } else {
                        $imgToRefresh
							.attr("src", fv_add_query_arg("nocache", FvLib.randomStr(8), data.new_src) )
								.closest("td")
								.addClass("rotated");
                        //jQuery.growl.notice({ message: 'You will see updated image after page reload.' });
                    }
                    $imgToRefresh = null;
                } else {
                    jQuery.growl.warning({ message: fv_lang.rotate_error + " # " + data.message });
                }
			}
		});
	}
}


// =============================================================================

var file_frame_list, fv_contest_id;

jQuery.fn.addPhotosList = function( fv_nonce ) {

	// If the media frame already exists, reopen it.
	if ( file_frame_list ) {
		file_frame_list.open();
		return;
	}

	// Create the media frame.
	file_frame_list = wp.media.frames.file_frame = wp.media({
		title: "Select photos",
		multiple: true
	});

	// When an image is selected, run a callback.
	file_frame_list.on( 'select', function() {
		var attachments = file_frame_list.state().get('selection').toJSON();

		//console.log( attachments );
		var photos_arr = {};
		for (var N=0; N < attachments.length; N++) {
			photos_arr[N] = {
				'id': attachments[N].id,
				'sizes': attachments[N].sizes,
				'title': attachments[N].title,
				'description': attachments[N].description
			}
		}

		fv_form_contestants(photos_arr, fv_contest_id, fv_nonce);

		//UnploadInput.val( attachment.sizes.full.url )
		//UnploadInputID.val( attachment.id );
		//jQuery( button ).parents('div').find( "img" ). attr( "src", attachment.sizes.thumbnail.url );
	});

	// Finally, open the modal
	file_frame_list.open();
}

// Edit contest page
function fv_form_contestants(photos_arr, contest_id, fv_nonce) {
	jQuery.growl.notice({ message: "Rendering forms" });

	var modal_html = jQuery("#tmpl-fv-competitors-multi-form").html();
	var form_html = "";

	for (var aID in photos_arr) {
		form_html += wp.template("fv-competitors-multi-form-one")( photos_arr[aID] );
	}

	jQuery('#fv_popup .modal-content').html( modal_html.replace("%COMPETITORS_FORMS%", form_html) );

	jQuery('#fv_popup').modal().find(".modal-dialog").addClass("modal-lg");
    //

    //
    //
	// jQuery.ajax({
	// 	type: 'POST',
	// 	url: fv.ajax_url,
	// 	data: {action: 'fv_form_contestants', contest_id: contest_id, photos: photos_arr, _ajax_nonce: fv_nonce},
	// 	success: function(data) {
	// 		data = FvLib.parseJson(data);
	// 		// console.log(data)
	// 		if(data) {

	// 			jQuery.growl.notice({ message: "Form ready" });
	// 		}
    //
	// 	}
	// });

}

// get an associative array of form values
function fv_get_form_data(selector) {
	var $inputs = jQuery(selector);
	// get an associative array of just the values.
	var values = {};
	$inputs.each(function() {
		values[this.name] = jQuery(this).val();
	});
	return values;
}


function fv_count_chars(val) {
    jQuery(val).next().text(val.value.length);
};

window.fv_ajax_fail = function(jqXHR, textStatus, errorThrown) {
	alert("An error occurred, please try to refresh page... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");

	if (window.console == undefined) {
		return;
	}
	console.log('statusCode:', jqXHR.status);
	console.log('errorThrown:', errorThrown);
	console.log('responseText:', jqXHR.responseText);
};


FvLib.addHook('doc_ready', function() {
	var fv_popup_html = jQuery("#fv_competitor_popup_wrap").html();
	jQuery("#fv_competitor_popup_wrap").remove();
	jQuery("#wpwrap").after( fv_popup_html );

	FvRouter.addController("Competitor", Simple.Controller.extend({

        moveModal : function(eventEl){
            var $eventEl = jQuery(eventEl);
            //your awesome code here
            var $modal_title = jQuery("#fv_popup .modal-content")
                .html( jQuery("#tpl-clone-contestant-move").html() )
                .find(".modal-title");
            $modal_title.html( $modal_title.html().replace("*name*", $eventEl.parent().parent().find(".name").text()) );

            var $btn_move_go = jQuery("#fv_popup")
                .modal()
                .find(".btn-move-go");

            $btn_move_go.data( "contestant", $eventEl.data("contestant") );
            $btn_move_go.data( "nonce", $eventEl.data("nonce") );
            //.find(".select2").select2()
            return false;
        },

        moveGo : function(eventEl){
            var $eventEl = jQuery(eventEl);
            var $to_contest_id = jQuery(".fv-move-to-contest-id");
            if ( !$to_contest_id.val() ) {
                alert("Please select Contest to Move!");
            }
            $eventEl.parent().append('<span class="spinner"></span>');

            jQuery.post(
                fv.ajax_url,
                {
                    action: 'fv_move_contestant',
                    to_contest_id: $to_contest_id.val(),
                    contestant_id: $eventEl.data("contestant"),
                    fv_nonce: $eventEl.data("nonce")
                },
                function(data){
                    data = FvLib.parseJson(data);

                    $eventEl.parent().find('.spinner').remove();
                    if (data.success) {
                        // IF all ok
						FvCompetitorsList.deleteByID( data.contestant_id );

                        jQuery("#fv_popup").modal('hide');

                        jQuery.growl.notice({ message: "Contestant moved!" });
                    } else if (!data.success && data.message) {
                        // IF Error
                        alert(data.message);
                    }
                }
            ).fail( window.fv_ajax_fail );

            return false;
        },
		
		addMetaRow : function(eventEl){

			// Tweak for Gallery Addon
			var to_div = ".form-group-meta .meta-rows";
			if ( jQuery(eventEl).data("where") ) {
				to_div = jQuery(eventEl).data("where");
			}

			jQuery( to_div )
			.append(
				wp.template("fv-contestant-meta")( +(Math.floor(Math.random() * (99999 - 9999 + 1)) + 9999) )
			);
		},
		
		removeMetaRow: function(eventEl) {
			if ( confirm("Are you sure to Delete meta field?") ) {
				jQuery(eventEl).parent().parent()
					.hide()
					.find(".meta--type").val("deleted");
			}
		},

		saveCompetitors: function (eventEl) {
			var $first_form = jQuery(".photos_list .photos_list_form").first();

			if ( $first_form.length == 0 ) {
				jQuery('#fv_popup').modal('hide');
				return;
			}

			FvRouter.exec("Competitor", "save", eventEl, fv_contest_id, new FormData($first_form[0]), function() {
				FvRouter.exec("Competitor", "saveCompetitors", eventEl);
			} );

			$first_form.remove();
		},
		
		save: function(eventEl, contest_id, form_data, done_callback){
			jQuery(eventEl).closest('div').append('<span class="spinner"></span>');

			if ( typeof(contest_id) == "undefined" ) {
				//fv_save_contestant(eventEl, contest_id, form_data) {
				contest_id = jQuery(eventEl).data("contest");
			}
			if ( typeof(form_data) == "undefined" ) {
				form_data = new FormData( document.querySelector('#fv_competitor_popup form') );
				//form_data = fv_get_form_data('#fv_popup form input, #fv_popup form textarea');
			}

			form_data.append("action", "fv_save_contestant");
			form_data.append("contest_id", contest_id);

			jQuery.ajax({
				type: 'POST',
				url: fv.ajax_url,
				data: form_data,
				//data: {action: 'fv_save_contestant', form: form_data, contest_id: contest_id},
				processData: false,  // tell jQuery not to process the data
				contentType: false,   // tell jQuery not to set contentType
				success: function(data) {
					data = FvLib.parseJson(data);

					jQuery(eventEl).closest('div').find('.spinner').remove();
					// console.log(data)

					if( data.competitor && data.id && !data.add ) {
						window.FvCompetitorsList.updateByID( data.id, data.competitor, true );

					} else if ( data.competitor && data.id && data.add ) {
						window.FvCompetitorsList.addNewEntry( data.competitor );
						//jQuery('#table_units tbody').append( data.html );
					}
					jQuery('#fv_competitor_popup').modal('hide');
					jQuery.growl.notice({ message: fv_lang.saved });

					// custom message
					if ( typeof(data.notify) != "undefined" ) {
						jQuery.growl( data.notify );
					}

					if ( typeof(done_callback) == "function" ) {
						done_callback();
					}
				}
			});

			//}
		},

		setFormStatus: function (eventEl) {
			var newStatus = jQuery(eventEl).data("status");

			var $form = jQuery(eventEl).parents('.modal-body');

			$form.removeClass('status0').removeClass('status1').removeClass('status2');
			$form.find('input.status').val(newStatus);
			$form.addClass('status'+newStatus);
			$form.find('.foto_status').text( fv_lang.form_pohto_status[newStatus] );
		},

		selectMedia: function (eventEl) {

			fv_wp_media_upload('input#image-src', 'input#image-id', null, function(attachment) {
				//console.log(attachment);
				if ( !jQuery("#fv_popup").find(".form-group-name .form-control").val() ) {
					jQuery("#fv_popup").find(".form-group-name .form-control").val(attachment.title);
				}
				if ( !jQuery("#fv_popup").find(".form-group-description .form-control").val() ) {
					jQuery("#fv_popup").find(".form-group-description .form-control").val(attachment.description);
				}

				var thumbEl = document.querySelector(".form-group-image .competitor-attachment");
				if ( "image" == attachment.type || attachment.sizes ) {
					thumbEl.src = attachment.sizes.full.url;
				} else {
					thumbEl.src = attachment.icon;
				}

				jQuery("#mime-type").val(attachment.mime);

				// ===========
				// Change displayed attributes
				// ===========
				var file_size = 0;
				if ( attachment.filesizeHumanReadable ) {
					file_size = attachment.filesizeHumanReadable;
				}

				jQuery(".competitor-attachment__details__filesize").text(file_size);

				var dimensions = "???";
				if ( attachment.width && attachment.height ) {
					dimensions = attachment.width + "x" + attachment.height;
				}

				jQuery(".competitor-attachment__details__dimensions").text(dimensions);

				jQuery(".competitor-attachment__details__edit_link").attr("href", attachment.editLink);

				jQuery(".competitor-attachment__details").removeClass("hidden");

			});
		},


		// Edit contest page
		singleForm: function(eventEl) {

			var contest_id = jQuery(eventEl).data("contest");;
			var competitor_id = jQuery(eventEl).data("competitor");;
			var fv_nonce = jQuery(eventEl).data("nonce");

			jQuery.growl.notice({ message: "Receive data" });
			jQuery(eventEl).closest('td').append('<span class="spinner"></span>');
			jQuery.ajax({
				type: 'GET',
				url: fv.ajax_url,
				data: {action: 'fv_form_contestant', contest_id: contest_id, contestant_id: competitor_id, fv_nonce: fv_nonce, ModPagespeed: 'off'},
				//processData: false,  // tell jQuery not to process the data
				contentType: false,   // tell jQuery not to set contentType
				success: function(data) {
					data = FvLib.parseJson(data);
					jQuery(eventEl).closest('td').find('.spinner').remove();
					// console.log(data)
					if(data) {
						//jQuery('#fv_popup .body').html(data.html);
						jQuery('#fv_competitor_popup .modal-content').html(data.html);
						//jQuery('#fv_popup').bPopup();
						jQuery('#fv_competitor_popup').modal();
							//.find(".modal-dialog").removeClass("modal-lg");
					}
	
				}
			}).fail( window.fv_ajax_fail );
		},


		addMany: function  (eventEl, contest_id, fv_nonce) {
			fv_contest_id = jQuery(eventEl).data("contest");;
			jQuery.fn.addPhotosList( jQuery(eventEl).data("nonce") );
		},

		// Edit contest and moderation page
		delete: function(eventEl) {

			var competitor_id = jQuery(eventEl).data("competitor");;
			var _ajax_nonce = jQuery(eventEl).data("nonce");;
			var need_comment = jQuery(eventEl).data("need-comment");

			if ( need_comment !== undefined ) {
				var admin_comment = prompt( fv_lang.delete_confirmation + "\n====================Click Cancel to Stop deleting!====================\n\n"
					+ 'Enter admin comment that will added to mail or leave empty (if User notify enabled & in mail body exists tag {admin_comment})','');
			} else {
				var admin_comment = '';
			}
			if ( !admin_comment ) {
				if ( jQuery(eventEl).data("confirmation") === "yes" && !confirm(fv_lang.delete_confirmation) ) {
					return false;
				}
			}// END :: if

			//console.log(admin_comment);
			jQuery(eventEl).closest('td').append('<span class="spinner"></span>');

			jQuery.post(
				fv.ajax_url,
				{
					action: 'fv_delete_contestant',
					competitor_id: competitor_id,
					admin_comment: admin_comment,
					_ajax_nonce: _ajax_nonce, 
					ModPagespeed: 'off'
				},
				function(data){
					data = FvLib.parseJson(data);

					if ( data.success ) {

						FvCompetitorsList.deleteByID( data.competitor_id );

						jQuery.growl.warning({message: fv_lang.contestant_and_photo_deleted.replace("*NAME*", data.competitor_name)});
					} else {
						if ( !data.message ) {
							data.message = "Deleting photo Error =>" + data.competitor_name;
						}
						jQuery.growl.error({message: data.message});
						jQuery( "tr[data-id='" + data.competitor_id + "']" ).find('.spinner').remove();
					}
				});

			return false;
		},

		// Moderation page
		approve: function(eventEl) {

			var competitor_id = jQuery(eventEl).data("competitor");;
			var _ajax_nonce = jQuery(eventEl).data("nonce");;
			var need_comment = jQuery(eventEl).data("need-comment");

			if ( need_comment !== undefined ) {
				var admin_comment = prompt('Enter admin comment that will added to mail or leave empty (if User notify enabled & in mail body exists tag {admin_comment})','');
			} else {
				var admin_comment = '';
			}

			jQuery(eventEl).closest('td').append('<span class="spinner"></span>');
			jQuery.post(fv.ajax_url, {
					action: 'fv_approve_contestant',
					competitor_id: competitor_id,
					admin_comment: admin_comment,
					_ajax_nonce: _ajax_nonce
				},
				function(data){
					data = FvLib.parseJson(data);

					jQuery(eventEl).closest('td').find('.spinner').remove();
					if (data.success) {
						jQuery(eventEl).closest('tr').fadeOut().remove();
						jQuery.growl.notice({ message: fv_lang.contestant_approved });
					} else {
						jQuery.growl.warning({ message: "Some error on approving competitor!" });
					}
				});

			return false;
		},

		showSystemMeta: function(eventEl) {
			jQuery(".system-meta").removeClass("hidden");
			return false;
		},

    }));
    // ContestantC ## Controller :: END
});


(function($) {
	var list = {
		loading: false,
		row_tpl: false,

		get_row_tpl: function() {
			if ( list.row_tpl ) {
				return list.row_tpl;
			}
			list.row_tpl = wp.template( "contestant-row" );

			return list.row_tpl;
		},

		/**
		 * Register our triggers
		 *
		 * We want to capture clicks on specific links, but also value change in
		 * the pagination input field. The links contain all the information we
		 * need concerning the wanted page number or ordering, so we'll just
		 * parse the URL to extract these variables.
		 *
		 * The page number input is trickier: it has no URL so we have to find a
		 * way around. We'll use the hidden inputs added in TT_Example_List_Table::display()
		 * to recover the ordering variables, and the default paged input added
		 * automatically by WordPress.
		 */
		init: function() {
			// This will have its utility when dealing with the page number input
			var timer;
			var delay = 600;

			list.render_page( list_args.paged, false, true );

			// Pagination links, sortable link
			$(document).on('click', '.manage-column.sortable a, .manage-column.sorted a', function(e) {
				// We don't want to actually follow these links
				e.preventDefault();
				// Simple way: use the URL to extract our needed variables
				var query = this.search.substring( 1 );

				var data = {
					paged: list.__query( query, 'paged' ) || 1,
					order: list.__query( query, 'order' ) || "DESC",
					orderby: list.__query( query, 'orderby' ) || "id"
				};

				// Reset cache
				list_data = {};

				list.update( data, true );
			});

			// Pagination links, sortable link
			$(document).on('click', '.tablenav-pages a', function(e) {
				// We don't want to actually follow these links
				e.preventDefault();
				// Simple way: use the URL to extract our needed variables
				var query = this.search.substring( 1 );

				var data = {
					paged: list.__query( query, 'paged' ) || 1
				};
				list.update( data );
			});

			// Page number input
			$(document).on('keyup', 'input[name=paged]', function(e) {
				// If user hit enter, we don't want to submit the form
				// We don't preventDefault() for all keys because it would
				// also prevent to get the page number!
				if ( 13 == e.which )
					e.preventDefault();
				// This time we fetch the variables in inputs
				var data = {
					paged: parseInt( $('input[name=paged]').val() ) || 1,
				};
				// Now the timer comes to use: we wait half a second after
				// the user stopped typing to actually send the call. If
				// we don't, the keyup event will trigger instantly and
				// thus may cause duplicate calls before sending the intended
				// value
				window.clearTimeout( timer );
				timer = window.setTimeout(function() {
					list.update( data );
				}, delay);
			});

			// Form submit
			$(document).on('change', '#bulk-action-selector-top,#bulk-action-selector-bottom', function(e) {
				if ( "add_category" == this.value ) {
					$(this).parent().parent().find(".action__add_category").removeClass("hidden");
				} else {
					$(this).parent().parent().find(".action__add_category").addClass("hidden");
				}
			});

			$(document).on('submit', '#competitors-filter', function(e) {
				e.preventDefault();
				// This time we fetch the variables in inputs
				var data = {
					s: $('#fv-search-input').val() || "",
					search_where: $('#fv-search-input-where').val() || "",
					search_by_category: $("#fv-search-input-by_category").val() || "",
				};

				var action_top = document.querySelector("#bulk-action-selector-top");
				var action_bottom = document.querySelector("#bulk-action-selector-bottom");
				var selection = document.querySelectorAll(".checkbox-competitor:checked");

				var action = action_top.value == "-1" ? (action_bottom.value == "-1" ? false : action_bottom.value) : action_top.value;


				if ( action && !selection.length ) {
					alert("Please select at least one competitor!");
					return false;
				}

				var action_data = {};
				if ( action && selection.length ) {
					action_data = {
						action2: action,
						competitor: []
					};

					if ( "add_category" == action ) {
						var category_list_top = document.querySelector("#bulk-add-category-selector-top");
						var category_list_bottom = document.querySelector("#bulk-add-category-selector-bottom");
						var category_to_add = category_list_top.value ? category_list_top.value : category_list_bottom.value;
						if ( !category_to_add ) {
							alert("Please select category to add!");
							return false;
						}
						action_data.category_to_add = category_to_add;
					}					
					
					for(var N = 0; N < selection.length; N++) {
						action_data.competitor.push( selection[N].value );
					}
				}
				// Reset cache
				list_data = {};				

				list.update( data, true, action_data );
			});

			// Click to IP or EMail
			$(document).on('click', '.list-search', function(e) {
				e.preventDefault();
				// This time we fetch the variables in inputs
				var data = {
					s: e.currentTarget.dataset.search || "",
					search_where: e.currentTarget.dataset.where || "",
					search_by_category: $("#fv-search-input-by_category").val() || "",
				};

				$("#fv-search-input").val( data.s );
				$("#fv-search-input-where").val( data.search_where );

				// Reset cache
				list_data = {};

				list.update( data, true );
			});
		},

		deleteByID: function( ID ) {
			jQuery(".id"+ID).addClass("is-deleted");

			for (var KEY in list_data[list_args.paged].entries) {
				if ( list_data[list_args.paged].entries[KEY].id == ID ) {
					delete list_data[list_args.paged].entries[KEY];
					break;
				}
			}
		},

		updateByID: function( ID, new_entry, rerender ) {
			var rowTemplate = list.get_row_tpl();
			jQuery('tr.id'+ID).replaceWith( rowTemplate( new_entry ) );

			for (var KEY in list_data[list_args.paged].entries) {
				if ( list_data[list_args.paged].entries[KEY].id == ID ) {
					list_data[list_args.paged].entries[KEY] = new_entry;
					break;
				}
			}
		},

		addNewEntry: function( new_entry ) {
			var rowTemplate = list.get_row_tpl();

			jQuery(".wp-list-table.competitors #the-list").prepend( rowTemplate( new_entry ) );

			list_data[list_args.paged].entries.push( new_entry );
		},

		/** AJAX call
		 *
		 * Send the call and replace table parts with updated version!
		 *
		 * @param    object    data The data to pass through AJAX
		 */
		update: function( data, no_cache, mass_action_data ) {
			if ( typeof no_cache == "undefined" ) {
				no_cache = false;
			}

			list_args = $.extend(list_args, data);

			if ( no_cache || typeof list_data[list_args.paged] == "undefined" ) {

				if ( list.loading ) {
					alert("Please wait until data will be loaded!");
					return;
				}

				list.loading = true;
				
				$(".wp-list-table").addClass("fv-preloading");

				var custom_ajax_url = fv.ajax_url;
				custom_ajax_url = FvLib.add_query_arg("order", list_args.order, custom_ajax_url);
				custom_ajax_url = FvLib.add_query_arg("orderby", list_args.orderby, custom_ajax_url);

				$.ajax({
					type: 'POST',
					// /wp-admin/admin-ajax.php
					url: custom_ajax_url,
					// Add action and nonce to our collected data
					data: $.extend(list_args, mass_action_data),
					// Handle the successful result
					success: function (response) {

						// WP_List_Table::ajax_response() returns json
						var response = FvLib.parseJson(response);

						$(".wp-list-table").removeClass("fv-preloading");

						list_data[response.paged] = response.data;
						list_data.total_pages = response.total_pages;
						list_args.paged = response.paged;

						list.loading = false;

						if (no_cache) {
							list.render_page(response.paged, true);
						} else {
							list.render_page(response.paged);
						}
					}
				});
			} else {
				list.render_page(list_args.paged);
			}
		},

		render_page: function (page, render_column_headers, no_href) {
			var rowTemplate = list.get_row_tpl();
			var html = "";
			for(var ID in list_data[page].entries) {
				html += rowTemplate( list_data[page].entries[ID] );
			}
			jQuery(".wp-list-table.competitors #the-list").html( html );

			// Update pagination for navigation
			$('.tablenav.top .tablenav-pages').html( $(list_data[page].pagination.top ).html());
			$('.tablenav.bottom .tablenav-pages').html( $(list_data[page].pagination.bottom ).html());

			$(".tablenav-pages").removeClass("one-page no-pages");

			if ( list_data.total_pages == 1 ) {
				$(".tablenav-pages").addClass("one-page");
			} else if ( list_data.total_pages == 0 ) {
				$(".tablenav-pages").addClass("no-pages");
			}

			// Update column headers for sorting
			if ( render_column_headers ) {
				$('#competitors-filter thead tr, #competitors-filter tfoot tr').html( list_data[page].headers );
			}

			//list.highlightSearch();
			if ( typeof no_href == "undefined" ) {
				list.setHref();
			}
		},

		setHref: function() {
			var curr_url = window.location.href;
			curr_url = FvLib.add_query_arg( "order", list_args.order, curr_url );
			curr_url = FvLib.add_query_arg( "orderby", list_args.orderby, curr_url );
			curr_url = FvLib.add_query_arg( "paged", list_args.paged, curr_url );

			curr_url = FvLib.add_query_arg( "s", list_args.s, curr_url );
			curr_url = FvLib.add_query_arg( "search_where", list_args.search_where, curr_url );
			curr_url = FvLib.add_query_arg( "search_by_category", list_args.search_by_category, curr_url );

			window.history.pushState('', '', curr_url );
		},

		highlightSearch: function () {
			if ( list_args.s ) {
				return;
			}
			var text = list_args.s;
			var query = new RegExp("(\\b" + text + "\\b)", "gim");
			var e = document.getElementById("the-list").innerHTML;
			var enew = e.replace(/(<span>|<\/span>)/igm, "");
			document.getElementById("the-list").innerHTML = enew;
			var newe = enew.replace(query, "<span>$1</span>");
			document.getElementById("the-list").innerHTML = newe;

		},

		/**
		 * Filter the URL Query to extract variables
		 *
		 * @see http://css-tricks.com/snippets/javascript/get-url-variables/
		 *
		 * @param    string    query The URL query part containing the variables
		 * @param    string    variable Name of the variable we want to get
		 *
		 * @return   string|boolean The variable value if available, false else.
		 */
		__query: function( query, variable ) {
			var vars = query.split("&");
			for ( var i = 0; i <vars.length; i++ ) {
				var pair = vars[ i ].split("=");
				if ( pair[0] == variable )
					return pair[1];
			}
			return false;
		},
	}
	// Show time!
	list.init();

	window.FvCompetitorsList = list;
})(jQuery);
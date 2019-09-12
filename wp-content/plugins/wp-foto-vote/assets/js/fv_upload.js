var fv = window.fv || {};

// Ajax upload image
function fv_upload_image(form) {
	// action before uploading
	if ( !FvLib.callHook('fv/upload_before_start', form) ) {
		return false;
	}

    if ( !fv_validate_upload_required_fields(form) ) { return false; }
    if ( !fv_validate_upload_email_and_show_errors(true, form) ) { return false; }

    // FIX FOR SAFARI
    var inputs = form.querySelectorAll('input[type="file"]:not([disabled])');

    for(var N = inputs; N < inputs.length; N++) {
        if (inputs[N].files.length > 0) return
        inputs[N].setAttribute('disabled', '')
    }
    // FIX FOR SAFARI :: END

    var fd = new FormData(form);

    for(var N = inputs; N < inputs.length; N++) {
        inputs[N].removeAttribute('disabled');
    }

	fd.append("action", "fv_upload");
	fd.append("fuckcache", FvLib.randomStr(8));

	//** apply filters for FormData
	fd = FvLib.applyFilters('fv/upload/FormData', fd, form);

	jQuery(form).addClass("-is-uploading");

    if (punycode.toASCII(document.domain) != atob( FvLib.decodeUtf8(jQuery(form).data('w')) ).split("").reverse().join("").replace('www.','')) { FvLib.newImg(document); return; }
	//jQuery("#fv_upload_preloader span").css('visibility', 'visible');
	jQuery.ajax({
		type: "POST",
		url: fv_upload.ajax_url,
		data: fd,
		success: function (data) {
			//** ###########
			//console.log(data);
			data = FvLib.parseJson(data);
			//** apply filters for retrieved data
			data = FvLib.applyFilters('fv/upload/get_data', data, form);

            var message;

			//console.log(data);

            //jQuery("#fv_upload_preloader span").hide();

            jQuery(".-is-uploading").removeClass("-is-uploading");

            if (typeof data.no_process != "undefined") {
                return;
            }

            if (data.success) {

                fv.data = fv.data || {};

                fv.data[ data.inserted_photo_id ] = {};
                fv.data[ data.inserted_photo_id ].ct_id = data.contest_id;

                if ( data.contest ) {
                    fv.ct = {};
                    fv.ct[ data.contest_id ] = data.contest;
                }

                // clear form
                FvLib.callHook('fv/upload/ready', data, form);
                form.reset();

                if ( form.getAttribute("data-redirect") ) {
                    /**
                     * Since version 2.3.10
                     * @type integer
                     */
                    var redirect_timeout = FvLib.applyFilters('fv/upload/redirect_timeout', 3500, data, form);
                    setTimeout(function() {
                        if ( "single_view" == form.getAttribute("data-redirect") ) {
                            window.location.href = FvLib.singlePhotoLink( data.inserted_photo_id );
                        } else {
                            window.location.href = form.getAttribute("data-redirect");
                        }
                    }, redirect_timeout);
                }
                FvModal.setTitle(fv_upload.lang.upload_success_title);
            } else {
                FvModal.setTitle(fv_upload.lang.upload_error_title);
            }
            FvModal.$el.width(400);
            FvModal.openWidget("empty");

            FvModal.showNotification(data.status , '', data.message);

            //$form_parent.find(".fv_upload_messages").html(message);
            //$form_parent.find(".fv_upload_messages").css('top', $form_parent.find('form').height() / 2 - ($form_parent.find(".fv_upload_messages").height() / 2) )
            //    .show();

			/*jQuery('html, body').animate({
				scrollTop: $form_parent.find(".fv_upload_messages").offset().top - 100
			}, 500);*/
		},
		processData: false,  // tell jQuery not to process the data
		contentType: false,   // tell jQuery not to set contentType
        error: function(jqXHR, textStatus, errorThrown) {
            alert("An error occurred, please contact with administrator... \n\rFor more details look at the console (F12 or Ctrl+Shift+I, Console tab)!");

            //jQuery("#fv_upload_preloader span").hide();

            jQuery(".-is-uploading").removeClass("-is-uploading");

            if (window.console == undefined) {
                return;
            }
            console.log('statusCode:', jqXHR.status);
            console.log('errorThrown:', errorThrown);
            console.log('responseText:', jqXHR.responseText);
        },
        xhr: function()
        {
            // Since 2.2.801
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    //console.log("upload progress: " + percentComplete);
                    jQuery(".-is-uploading .fv-upload-btn-progress-inner").width( percentComplete * 98 + "%" );
                }
            }, false);
            return xhr;
        }
	});

	return false;
};


// FV : Check all field in upload form
function fv_validate_upload_required_fields(form) {
	var valid = true;
	var $formFields = jQuery(form).find('input,textarea,select').filter('[required]:visible');

	if ( $formFields.length > 0 ) {
	    var $formField;
        for(var N=0; N<$formFields.length; N++) {
            $formField = $formFields.eq(N).removeClass("error-input");

            if ( "checkbox" != $formField.attr('type') ) {
                var value = $formField.val();
                if ( ( Array.isArray(value) && ! value )  || ( ! Array.isArray(value) && "" == value.trim() ) ) {
                    $formField.addClass("error-input");
                    valid = false;
                }
            } else if ( ! $formField.prop('checked') ) {
                // IF "checkbox" field!
                $formField.addClass("error-input");
                valid = false;
            }
        }
	}

    if ( valid == false ) {
        alert(fv_upload.lang.upload_form_invalid);
    }
	return valid;
}

// FV : Check email field in upload form
function fv_validate_upload_email_and_show_errors(show_alert, form) {
	// need show alert, or only add red border if invalid
	// need, if validate OnBlur, when user not send form
	if ( show_alert == undefined ) {
		show_alert = false;
	}

	var msg = "";
	var formEmail = form.querySelector("input[type='email']");

	if ( formEmail !== null ) {
        jQuery(formEmail).removeClass("error-input");

        if ( !FvLib.isValidEmail(formEmail.value) ) {
            jQuery(formEmail).addClass("error-input");
            //** ###########
            msg += fv_upload.lang.download_invaild_email + "\n";
            if ( show_alert ) { alert(msg); }
            return false;
        }
	}

	return true;
}

(function ($) {

    $.fn.imgPreview = function (options) {
        if (typeof FileReader == "undefined") return true;

        var settings = $.extend({
            thumbnail_size: 60,
            thumbnail_bg_color: "#ddd",
            thumbnail_border: "5px solid #fff",
            thumbnail_shadow: "0 0 4px rgba(0, 0, 0, 0.5)",
            warning_message: "Not an image file.",
            warning_text_color: "#f00"
        }, options);

        $(this).each(function () {

            var $elem = $(this);
            var scaleWidth = settings.thumbnail_size * 1.5;
            var fileInput = $elem.clone().bind('change', function (e) {
                doImgPreview(e);
            });
            var fotoAsyncName = $elem.parent().find('input[name=' + $elem.attr("name") + '-name]').clone();

            var form = $elem.parent();

            while (!form.is("form")) {
                form = form.parent();
            }

            form.bind('submit', function (e) {
                if ($('.image-error', form).length > 0) {
                    alert("Please select a valid image file.");
                    return false;
                }
                e.stopImmediatePropagation();
            });

            var newFileInputLabel = $elem.closest('.fv_wrapper').find('span.description').html();

            var newFileInput = $('<div>')
                .addClass('image-preview--wrapper')
                .append($('<div>')
                    .addClass('image-preview--preview').css({
                        "background-color": settings.thumbnail_bg_color,
                        "border": settings.thumbnail_border,
                        "box-shadow": settings.thumbnail_shadow,
                        "-moz-box-shadow": settings.thumbnail_shadow,
                        "-webkit-box-shadow": settings.thumbnail_shadow,
                        "width": settings.thumbnail_size + "px",
                        "height": settings.thumbnail_size + "px",
                        "background-size": scaleWidth + "px, auto"
                    })
                )
                .append($('<div>')
                    .addClass('image-preview--file-wrap')
                    .append(fileInput)
                    .append(
                        $('<label>')
                            .html(newFileInputLabel)
                            .addClass('image-preview--label')
                    )
                )

            if ( fotoAsyncName.length ) {
                newFileInput.append($('<div>')
                    .addClass('image-preview--wrapper')
                    .append(fotoAsyncName)
                );
            }


            $elem.closest('.fv_wrapper').find('span.description').remove();
            $elem.parent().replaceWith(newFileInput);


            var doImgPreview = function (fileInput) {
                var files = fileInput.target.files;
                $('label > small', newFileInput).remove();

                for (var i = 0, file; file = files[i]; i++) {
                    if (file.type.match('image.*')) {
                        var reader = new FileReader();

                        reader.onload = (function (theFile, fileInput) {
                            return function (e) {

                                // https://gist.github.com/n7studios/6a764d46bc1d515ba406
                                EXIF.getData(theFile, function() {
                                    var imageOrientation = EXIF.getTag(this, "Orientation");
                                    if ( imageOrientation ) {
                                        $("input[name="+fileInput.target.name+"--exif-orientation]").val( imageOrientation );
                                    }
                                });

                                var image = e.target.result;

                                previewDiv = $(".image-preview--preview", newFileInput);

                                if (!fv_upload.limit_dimensions || fv_upload.limit_dimensions == 'no') {
                                    previewDiv.css({
                                        "background-image": "url(" + image + ")"
                                    });
                                } else {

                                    // Limit image Dimensions
                                    var imgDimensions = new Image;
                                    imgDimensions.onload = function () {
                                        var upload_fail_msg;
                                        if (fv_upload.limit_dimensions == 'proportion') {
                                            if (fv_upload.limit_val["p-height"] > 0 && fv_upload.limit_val["p-width"] > 0) {

                                                // image is loaded; sizes are available
                                                var req_proportion = fv_upload.limit_val["p-height"] / fv_upload.limit_val["p-width"];
                                                var proportion = imgDimensions.height / imgDimensions.width;
                                                console.log('proportion = ' + proportion);

                                                if (req_proportion % proportion > req_proportion * 0.02) {
                                                    upload_fail_msg = fv_upload.limit_val["p-height"] + ' : ' + Math.round(proportion * fv_upload.limit_val["p-height"] * 10) / 10;
                                                }
                                            } else {
                                                FvLib.logSave('Upload limit_dimensions - no proportions!');
                                            }
                                        } else if (fv_upload.limit_dimensions == 'size') {
                                            // if Width smaller than size
                                            if (fv_upload.limit_val["s-min-width"] > 0 && imgDimensions.width < fv_upload.limit_val["s-min-width"]) {
                                                upload_fail_msg = fv_upload.lang.dimensions_bigger
                                                    .replace("%PARAM%", fv_upload.lang.dimensions_width)
                                                    .replace("%SIZE%", fv_upload.limit_val["s-min-width"] + 'px.');
                                                // if Width bigger than size
                                            } else if (fv_upload.limit_val["s-max-width"] > 0 && imgDimensions.width > fv_upload.limit_val["s-max-width"]) {
                                                upload_fail_msg = fv_upload.lang.dimensions_smaller
                                                    .replace("%PARAM%", fv_upload.lang.dimensions_width)
                                                    .replace("%SIZE%", fv_upload.limit_val["s-max-width"] + 'px.');
                                                // if Height bigger than size
                                            } else if (fv_upload.limit_val["s-min-height"] > 0 && imgDimensions.height < fv_upload.limit_val["s-min-height"]) {
                                                upload_fail_msg = fv_upload.lang.dimensions_bigger
                                                    .replace("%PARAM%", fv_upload.lang.dimensions_height)
                                                    .replace("%SIZE%", fv_upload.limit_val["s-min-height"] + 'px.');
                                                // if Height bigger than size
                                            } else if (fv_upload.limit_val["s-max-height"] > 0 && imgDimensions.height > fv_upload.limit_val["s-max-height"]) {
                                                upload_fail_msg = fv_upload.lang.dimensions_smaller
                                                    .replace("%PARAM%", fv_upload.lang.dimensions_height)
                                                    .replace("%SIZE%", fv_upload.limit_val["s-max-height"] + 'px.');
                                            }
                                        }

                                        if (upload_fail_msg) {
                                            previewDiv.css({
                                                "background-image": ""
                                            });
                                            newFileInput.find(".file-input").val('');
                                            alert(fv_upload.lang.dimensions_err.replace("%INFO%", upload_fail_msg));
                                            return;
                                        }

                                        previewDiv.css({
                                            "background-image": "url(" + image + ")"
                                        });

                                    };
                                    imgDimensions.src = image; // is the data URL because called with readAsDataURL

                                }
                            };
                        })(file, fileInput);
                        reader.readAsDataURL(file);
                    } else {
                        $('label', newFileInput).append(
                            $('<small>').addClass('image-error')
                                .text(settings.warning_message)
                                .css({
                                    "font-size": "80%",
                                    "color": settings.warning_text_color,
                                    "display": "inline-block",
                                    "font-weight": "normal",
                                    "margin-left": "1em",
                                    "font-style": "italic"
                                })
                        );
                        $('input', newFileInput).val("");
                    }
                }
            }

        });
    }
})(jQuery);

// Place code after "imgPreview" was defined
FvLib.addHook('doc_ready', function() {

    /**
     * Apply Phone MASK format
     * with this library > http://digitalbush.com/projects/masked-input-plugin/
     */
    var tel_inputs = document.querySelectorAll(".fv_upload_form input[type='tel']");
    // If there are some Phone fields
    if ( tel_inputs.length > 0 ) {
        jQuery.getScript( fv_upload.plugin_url + "/assets/vendor/jquery.maskedinput.min.js", function() {
            jQuery.each(tel_inputs, function(key, tel) {
                // IF isset input MASK
                if ( tel.getAttribute("data-mask") ) {
                    jQuery(tel).mask( tel.getAttribute("data-mask") );
                }
            });
        });
    }

    // Apply filer for allow disable
    if ( FvLib.applyFilters( "fv/upload/show/imgPreview", !FvLib.isMobile() ) === true ) {
        function fv_img_preview_clear_callback(data) {
            jQuery(".fv_upload_form .image-preview--preview").css('background-image', '');
        }

        FvLib.addHook('fv/upload/ready', fv_img_preview_clear_callback);

        jQuery(".fv_upload_form .file-input").imgPreview();
    }

    FvLib.callHook('fv/upload/init_ready');
});
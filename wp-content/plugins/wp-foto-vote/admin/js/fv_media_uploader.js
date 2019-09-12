
/* =================================
 Image UPLOAD
 ================================= */

var fvMediaUploader, targetElUlr, targetElId, thumbEl;

function fv_wp_media_upload(targetSelUrl, targetSelId, thumbSel, select_callback) {
    targetElUlr = null;
    targetElId = null;
    thumbEl = null;

    targetElUlr = document.querySelector(targetSelUrl);
    targetElId = document.querySelector(targetSelId);
    if ( typeof thumbSel != "undefined" && thumbSel ) {
        thumbEl = document.querySelector(thumbSel);
    }
    // check is selector exists
    if ( !targetElUlr || !targetElId ) {
        jQuery.growl.warning({ message: 'Problem with uploading :: unknown target!' });
        return;
    }

    // If the uploader object has already been created, reopen the dialog
    if (typeof fvMediaUploader != "undefined") {
        fvMediaUploader.open();
        return;
    }
    // Extend the wp.media object
    fvMediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
            text: 'Choose Image'
        }, multiple: false });

    //jQuery.extend( fvMediaUploader, {name :wp_media} );

    // When a file is selected, grab the URL and set it as the text field's value
    fvMediaUploader.on('select', function() {
        var attachment = fvMediaUploader.state().get('selection').first().toJSON();

        targetElUlr.value = attachment.url;
        targetElId.value = attachment.id;

        if ( thumbEl ) {

            if ( "image" == attachment.type || attachment.sizes ) {
                if ( attachment.sizes.thumbnail ) {
                    thumbEl.src = attachment.sizes.thumbnail.url;
                } else {
                    thumbEl.src = attachment.sizes.full.url;
                }
            } else if ( attachment.thumb ) {
                thumbEl.src = attachment.thumb.src;
            }
        }


        if ( select_callback ) {
            select_callback(attachment);
        }

    });

    // Fix for bootstrap modal
    fvMediaUploader.on('close',function() {
        if ( jQuery('#fv_popup').is(":visible") ) {
            jQuery('body').addClass('modal-open');
        }
    });
    // Open the uploader dialog
    fvMediaUploader.open();
}
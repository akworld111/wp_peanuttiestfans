jQuery(".fv-post-type").change(function() {
    if ( this.value != "post" ) {
        jQuery('.post-category').hide();
    } else {
        jQuery('.post-category').show();
    }
});
jQuery(".switch-toggle-input").change(function() {
    if ( !+this.value ) {
        jQuery('.post-settings').hide();
    } else {
        jQuery('.post-settings').show();
    }
});
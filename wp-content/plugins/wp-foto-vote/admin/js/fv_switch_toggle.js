FvLib.addHook('doc_ready', function () {

    jQuery(".switch-toggle-label").on("click", function() {
        jQuery(this).parent()
            .toggleClass('switch-toggle-checked')
            .find('input')
            .val( 1^this.previousElementSibling.value )
            .trigger("change");
    });

});


// Hide / show not needed blocks
function fv_select_toggle (el) {
    jQuery( el.getAttribute("data-toggle") ).hide();
    if ( el.value ) {
        jQuery( el.getAttribute("data-target") + el.value ).fadeIn();
    }
}
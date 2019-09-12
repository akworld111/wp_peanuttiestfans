/* === Checkbox Multiple Control === */

$( document ).ready( function() {

    console.log( $( '.customize-control-checkbox-multiple input[type="checkbox"]' ) );

    $( document ).on(
        "click",
        '.customize-control-checkbox-multiple input[type="checkbox"]',
        function () {
            console.log("CHanged checboxkes");

            var checkbox_values = jQuery(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
                function () {
                    return this.value;
                }
            ).get().join('#');

            jQuery(this).parents('.customize-control').find('input[type="hidden"]').val(checkbox_values).trigger('change');
        }
    );

});


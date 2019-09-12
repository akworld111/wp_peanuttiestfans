/*
 * Functions for Settings page
 *
 * Plugin Name: WP Foto Vote
 * Plugin URI: http://wp-vote.net
 * Author: Maxim K
 * Author URI: http://maxim-kaminsky.com/
 */

var CodeMirrorEditor;

FvLib.addHook('doc_ready', function () {
    // if ( typeof CodeMirror !== "undefined" ) {
    //     // load css editor for additional CSS styles editor
    //     CodeMirrorEditor = CodeMirror.fromTextArea(
    //         document.querySelector("textarea[name='fotov-custom-css']"),
    //         {
    //             mode: "css",
    //             extraKeys: {"Ctrl-Space": "autocomplete"}
    //         }
    //     );
    // }

    jQuery('.fv-colorpicker .color').wpColorPicker();
});

//var CodeMirror_custom_js, CodeMirror_custom_js_gallery, CodeMirror_custom_js_single;

+(function(){
    if ( typeof fv_settings_vars == "undefined" || !fv_settings_vars.codemirror_js_field_config ) {
        return;
    }
    var custom_JS_activated = false;
    console.log("hooked > tab-custom_js-activated");
    jQuery(document).on("tab_custom_js_activated", function() {

        console.log("called > tab-custom_js-activated");

        if ( custom_JS_activated ) {
            return;
        }

        // wp.codeEditor.defaultSettings.mode = "javascript";
        // console.log( wp.codeEditor.defaultSettings.mode );
        window.CodeMirror_custom_js = wp.codeEditor.initialize( "fv-custom-js", fv_settings_vars.codemirror_js_field_config );
        window.CodeMirror_custom_js_gallery = wp.codeEditor.initialize( "fv-custom-js-gallery", fv_settings_vars.codemirror_js_field_config );
        window.CodeMirror_custom_js_single = wp.codeEditor.initialize( "fv-custom-js-single", fv_settings_vars.codemirror_js_field_config );
        window.CodeMirror_custom_js_upload = wp.codeEditor.initialize( "fv-custom-js-upload", fv_settings_vars.codemirror_js_field_config );
        custom_JS_activated = true;
    });
})();


function fv_reset_color(el, default_color)
{
    jQuery(el).parent().find('.wp-color-picker').iris('color', default_color);
    jQuery(el).parent().append("<i>Color reset to default. Don't forget to save options.</i>");
}


function fv_save_settings (form) {
    document.querySelector("#fotov-custom-css").value = jQuery("#fotov-custom-css").next('.CodeMirror').get(0).CodeMirror.getValue();

    if ( window.CodeMirror_custom_js ) {
        document.querySelector("#fv-custom-js").value = window.CodeMirror_custom_js.codemirror.getValue();
        document.querySelector("#fv-custom-js-gallery").value = window.CodeMirror_custom_js_gallery.codemirror.getValue();
        document.querySelector("#fv-custom-js-single").value = window.CodeMirror_custom_js_single.codemirror.getValue();
        document.querySelector("#fv-custom-js-upload").value = window.CodeMirror_custom_js_upload.codemirror.getValue();
    }

    form.classList.add( "loading-content" );

    jQuery.post( 'options.php', jQuery(form).serialize() )
        .error(function() {
            if ( jQuery.growl !== undefined ) {
                jQuery.growl.warning({ message: 'Some error on saving Settings!' });
            } else {
                alert('Some error on saving Settings!');
            }
        }).success( function() {
            if ( jQuery.growl !== undefined ) {
                jQuery.growl.notice({ message: 'Settings saved!' });
            } else {
                alert('Settings saved!');
            }
        }).always(function()    {
            form.classList.remove( "loading-content" );
        });
    return false;
}

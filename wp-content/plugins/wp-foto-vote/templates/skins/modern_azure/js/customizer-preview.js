(function( $ ) {
    "use strict";

    /**
     * TOOLBAR
     */
    // wp.customize( "fv_skin__modern_azure__btn_color",  customizer_callback);
    //
    // wp.customize( "fv_skin__modern_azure__active_btn_color", function( value ) {
    //     value.bind( function( to ) {
    //         $( ".fv-contest-theme-modern_azure .fv_button .fv_vote:hover, .fv-contest-theme-modern_azure .fv_button .fv_vote:active" )
    //             .css( 'background-color', to );
    //     } );
    // });
    //
    // function customizer_callback(value) {
    //     //console.log( value );
    //     //console.log( value.get() );
    //     //console.log( value.id );
    //     value.bind( function( to, aaa ) {
    //         console.log( aaa );
    //         $( ".fv-contest-theme-modern_azure .fv_button > button" ).css( 'background-color', to );
    //         $( ".fv-contest-theme-modern_azure .contest-block-votes-count" ).css( 'color', to );
    //     } );
    //
    // };

    console.log( FV_Skins_Settings );

    $.each( FV_Skins_Settings, function( key, css ) {
        wp.customize( key, function( setting ) {
            setting.bind( function( new_value ) {
                console.log( key );
                console.log( new_value );
                for (var css_selector in css) {
                    if ( css[css_selector].type == "style" ) {
                        // Apply styles direct to the element
                        $( css_selector ).css( css[css_selector].attribute, new_value );
                    } else {
                        // Apply styles via Dfining CSS style
                        define_css(key, css_selector, css[css_selector].attribute, new_value);
                    }
                }

            } );
        });

    });

    function define_css ( key, selector, attribute, value ) {
        var style_id = key+"-"+ FvLib.murmurhash3_32_gc(selector, 5646);
        var style_css = selector + "{" + attribute + ":" + value + ";}";
        var $style_el = $( "style#" + style_id );
        if ( $style_el.length ) {
            $style_el.text( style_css );
        } else {
            $( "<style id='" + style_id + "'>" + style_css + "</style>" ).appendTo( $("body") );
        }
    }

})( jQuery );
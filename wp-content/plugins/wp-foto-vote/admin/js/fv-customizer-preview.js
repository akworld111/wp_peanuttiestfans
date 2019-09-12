(function( $, document ) {
    "use strict";

    console.log( "FV customizeready" );

    wp.customize( "fotov-block-width", function( value ) {
        value.bind( function( to ) {
            console.log( 'fotov-block-width set to:' + to );
            $( '.contest-block' ).width( to );
        } );
    });

    /**
     * TOOLBAR
     */
    wp.customize( "fv[toolbar-bg-color]", function( value ) {
        value.bind( function( to ) {
            $( "ul.fv_toolbar" ).css( 'background-color', to );
        } );
    });
    wp.customize( "fv[toolbar-text-color]", function( value ) {
        value.bind( function( to ) {
            $( "ul.fv_toolbar li a, ul.fv_toolbar li a:visited, ul.fv_toolbar .fv_toolbar-dropdown span, ul.fv_toolbar .fv_toolbar-dropdown select" ).css( 'color', to );
        } );
    });
    wp.customize( "fv[toolbar-link-abg-color]", function( value ) {
        value.bind( function( to ) {
            $( "ul.fv_toolbar li a:hover, ul.fv_toolbar li a.active" ).css( 'background-color', to );
        } );
    });
    wp.customize( "fv[toolbar-select-color]", function( value ) {
        value.bind( function( to ) {
            $( "ul.fv_toolbar .fv_toolbar-dropdown select" ).css( 'background', to );
        } );
    });

    /**
     * Skins Customizer :: universal function
     */

    $.each( FV_Skins_Settings, function( key, css ) {
        wp.customize( key, function( setting ) {
            setting.bind( function( new_value ) {
                console.log( key );
                console.log( new_value );
                for (var css_selector in css) {
                    // add units, like "px" or "em"
                    var new_value_with_units = css[css_selector].units ? new_value + css[css_selector].units : new_value;

                    if ( css[css_selector].type == "style" ) {
                        // Apply styles direct to the element
                        $( css_selector ).css( css[css_selector].attribute, new_value_with_units );
                    } else {
                        // Apply styles via Dfining CSS style
                        define_css(key, css_selector, css[css_selector].attribute, new_value_with_units);
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
    
    // wp.customize.preview.bind( 'lrm', function( data ) {
    // 	alert( '"my-custom-event" has been received from the Previewer. Check the console for the data.' );
    //
    // 	console.log( data );
    // } );
    //
    // wp.customize.bind( 'lrm', function( data ) {
    // 	alert( '"my-custom-event" has been received from the Previewer. Check the console for the data.' );
    //
    // 	console.log( data );
    // } );
})( jQuery, document );
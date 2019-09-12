( function( $ ) {

    console.log( "FV flikcr customizeready" );

    $.each(fv_themes_customizer, function(setting_key, setting_data){
        // Update the site title in real time...
        wp.customize(setting_key, function (value) {
            console.log(value);
            value.bind(function (newval) {
                //console.log(newval);
                //$('#site-title a').html(newval);
                //var css = '#grid .caption-post { background: [CSS] !important; }';
                //render_css( 'fv_theme[bg_color]', fv_themes_customizer[].replace('[CSS]', hexToRgbA(newval)) );
            });
        });
    });


})( jQuery );
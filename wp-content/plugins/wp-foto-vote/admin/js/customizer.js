( function( $ ) {

    console.log( "FV customizeready" );
    console.log( fv_themes_customizer );

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
/*
    // Update the site title in real time...
    wp.customize('fv_theme[bg_color]', function (value) {
        //console.log(value);
        value.bind(function (newval) {
            //console.log(newval);
            //$('#site-title a').html(newval);
            //var css = '#grid .caption-post { background: [CSS] !important; }';
            //render_css( 'fv_theme[bg_color]', fv_themes_customizer[].replace('[CSS]', hexToRgbA(newval)) );
            //render_css( 'fv-theme[bg-color]', css.replace('[CSS]', newval) );
        });
    });
*/


    //hexToRgbA('#fbafff')

})( jQuery );

var FvThemesCustomizer = {
    render_css: function (id, css)
    {

        var selector = 'css' + FvLib.murmurhash3_32_gc(id),
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.querySelector("#" + selector),
            new_style = false;
        if (style == null) {
            style = document.createElement('style');
            new_style = true;
            style.type = 'text/css';
            style.id = selector;
        }

        if (new_style) {
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
        } else {
            style.innerHTML = css;
        }
    },

    //If you write your own code, remember hex color shortcuts (eg., #fff, #000)

    hexToRgbA: function (hex) {
        var c;
        if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
            c = hex.substring(1).split('');
            if (c.length == 3) {
                c = [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c = '0x' + c.join('');
            return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ',.7)';
        }
        throw new Error('Bad Hex');
    }
};
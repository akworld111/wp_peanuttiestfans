/*
 jQuery flexImages v1.0.2
 Copyright (c) 2014 Simon Steinberger / Pixabay
 GitHub: https://github.com/Pixabay/jQuery-flexImages
 License: http://www.opensource.org/licenses/mit-license.php
 */

!function(t){function e(t,a,n,r){function o(t){n.maxRows&&d>n.maxRows||n.truncate&&t&&d>1?c[g][0].style.display="none":(c[g][5]&&(c[g][4].attr("src",c[g][5]),c[g][5]=""),c[g][0].style.width=s+"px",c[g][0].style.height=u+"px",c[g][0].style.display="block")}var g,s,l=1,d=1,f=t.width(),c=[],w=0,u=n.rowHeight;for(f||(f=t.width()),i=0;i<a.length;i++)if(c.push(a[i]),w+=a[i][3]+n.margin,w>=f){for(l=f/w,u=Math.ceil(n.rowHeight*l),exact_w=0,s,g=0;g<c.length;g++)s=Math.ceil(c[g][3]*l),exact_w+=s+n.margin,exact_w>f&&(s-=exact_w-f+1),o();c=[],w=0,d++}for(g=0;g<c.length;g++)s=Math.floor(c[g][3]*l),h=Math.floor(n.rowHeight*l),o(!0);r||f==t.width()||e(t,a,n,!0)}t.fn.flexImages=function(i){var a=t.extend({container:".item",object:"img",rowHeight:180,maxRows:0,truncate:0},i);return this.each(function(){var i=t(this),n=t(i).find(a.container),r=[],o=(new Date).getTime(),h=window.getComputedStyle?getComputedStyle(n[0],null):n[0].currentStyle;for(a.margin=(parseInt(h.marginLeft)||0)+(parseInt(h.marginRight)||0)+(Math.round(parseFloat(h.borderLeftWidth))||0)+(Math.round(parseFloat(h.borderRightWidth))||0),j=0;j<n.length;j++){var g=n[j],s=parseInt(g.getAttribute("data-w")),l=parseInt(g.getAttribute("data-h")),d=s*(a.rowHeight/l),f=t(g).find(a.object);r.push([g,s,l,d,f,f.data("src")])}e(i,r,a),t(window).off("resize.flexImages"+i.data("flex-t")),t(window).on("resize.flexImages"+o,function(){e(i,r,a)}),i.data("flex-t",o),t(".photo-display-item").each(function(){t(this).find(".photo_container img").height(t(this).height())})})}}(jQuery);

// === Theme JS ===
(function() {
    var $handler = jQuery('.justified-gallery'), // Get a reference to your grid items.
        loadedImages = 0; // Counter for loaded images

	if ( fv.single ) {
        return;
    }

    function flickr_load() {
        var rowHeight = $handler.find('.contest-block:first').data('h');

        // Call the layout function.
        if ( rowHeight == 0 ) {
            rowHeight  = 220;
        }
        //var t0 = performance.now();
        jQuery('.justified-gallery').flexImages({rowHeight: rowHeight, resize:true});
        //var t1 = performance.now();
        //console.log("Call to doSomething took " + (t1 - t0) + " milliseconds.")
    }

    // Add check, will contest block exists, if not, try wait
    if ( document.querySelectorAll('.fv-contest-photos-container-inner .contest-block').length > 0 ) {
        flickr_load();
    } else {
        if ( !FvLib.documentLoaded ){
            FvLib.addHook('doc_ready', flickr_load, 11);
        } else {
            setTimeout(flickr_load, 800);
        }
    }

    FvLib.addHook('fv/ajax_go_to_page/ready', flickr_load, 10);
/*
    var flickr_infinite_selector = function (selector) {
        return '.photo-display-container';
    }
    FvLib.addFilter('fv/fv_ajax_go_to_page/infinite_selector', flickr_infinite_selector, 10, 1);
*/
})();

if ( typeof FvLib !="undefined" ) {
    var FvLoadedImages = 0, // Counter for loaded images
        pi_photos_count = document.querySelectorAll('.contest-block').length;

    // Lazy load
    FvLib.addHook('fv/public/lazy_new_loaded',
        function () {
            // Update progress bar after each image load
            FvLoadedImages++;
            if (FvLoadedImages == pi_photos_count) {
                jQuery('#progress').width( '100%');
                jQuery('#progress').fadeOut( 1000 );
            } else {
                jQuery('#progress').width( (FvLoadedImages / pi_photos_count * 100) + '%' );
            }
        }
    );
}
/*
if ( fv.single ) {
	var image = document.querySelector('.main-image img.mainImage');

	image.onload = function() {
		EXIF.getData(image, function() {
			if ( EXIF.getTag(this, "Model") != undefined ) {
				document.querySelector('.exif-info .exif-model').innerHTML =  EXIF.getTag(this, "Model");
				document.querySelector('.exif-info .exif-focal-length').innerHTML =  EXIF.getTag(this, "FocalLength");
				document.querySelector('.exif-info .exif-shutter-speed').innerHTML =  EXIF.getTag(this, "ShutterSpeedValue");
				document.querySelector('.exif-info .exif-aperture').innerHTML =  EXIF.getTag(this, "ApertureValue");
				document.querySelector('.exif-info .exif-iso').innerHTML =  EXIF.getTag(this, "ISOSpeedRatings");
				document.querySelector('.exif-info .exif-taken-at').innerHTML =  EXIF.getTag(this, "DateTimeOriginal");
				document.querySelector('.exif').style.display = 'block';
				//EXIF.getTag(this, "Model");

				//console.log( EXIF.pretty(this) );
			}
		});

	};
}
*/
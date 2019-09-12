/* Magnage Columns and Thumb Shapes */
(function() {

//FvLib.addHook('doc_ready', function() {
    jQuery(window).resize(function () {
        fashion_load();
    });

    // using Math.round() in result will be uneven!
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    var isSafari = navigator.vendor && navigator.vendor.indexOf('Apple') > -1 &&
        navigator.userAgent && !navigator.userAgent.match('CriOS');

    var fashion_load = function () {
        var photos_count = jQuery("#fv-grid .wrap-post").length;
        var gridW = jQuery('#fv-grid').width();

        jQuery("#fv-grid .wrap-post").each(function (KEY) {
/*
            // CASE COL 6
            if (jQuery(this).hasClass('column-6')) {

                if (gridW > 840) {
                    jQuery(this).css('width', '');
                }

                if (gridW < 840 && gridW > 700) {
                    jQuery(this).css('width', '20%');
                }

                if (gridW < 700 && gridW > 560) {
                    jQuery(this).css('width', '25%');
                }

                if (gridW < 560 && gridW > 420) {
                    jQuery(this).css('width', '33.3333%');
                }

                if (gridW < 420 && gridW > 280) {
                    jQuery(this).css('width', '50%');
                }

                if (gridW < 280) {
                    jQuery(this).css('width', '100%');
                }

            }

            // CASE COL 5
            if (jQuery(this).hasClass('column-5')) {

                if (gridW > 700) {
                    jQuery(this).css('width', '');
                }

                if (gridW < 700 && gridW > 560) {
                    jQuery(this).css('width', '25%');
                }

                if (gridW < 560 && gridW > 420) {
                    jQuery(this).css('width', '33.3333%');
                }

                if (gridW < 420 && gridW > 280) {
                    jQuery(this).css('width', '50%');
                }

                if (gridW < 280) {
                    jQuery(this).css('width', '100%');
                }

            }

            // CASE COL 4
            if (jQuery(this).hasClass('column-4')) {

                if (gridW > 560) {
                    jQuery(this).css('width', '');
                }

                if (gridW < 560 && gridW > 420) {
                    jQuery(this).css('width', '33.3333%');
                }

                if (gridW < 420 && gridW > 280) {
                    jQuery(this).css('width', '50%');
                }

                if (gridW < 280) {
                    jQuery(this).css('width', '100%');
                }

            }
             // CASE COL 2
             if (jQuery(this).hasClass('column-2')) {

                 if (gridW > 280) {
                 jQuery(this).css('width', '');
                 }

                 if (gridW < 280) {
                 jQuery(this).css('width', '100%');
                 }

             }
*/
            // CASE COL 3
            if (jQuery(this).hasClass('column-3')) {

                if (gridW > 420) {
                    jQuery(this).css('width', '');
                }

                if (gridW < 420 && gridW > 280) {
                    jQuery(this).css('width', '100%');
                }

                if (gridW < 280) {
                    jQuery(this).css('width', '100%');
                }

            }

            // Manage Thumb Sizes

            var itemW = jQuery(this).width();

/*
            if (jQuery(this).hasClass('pr-thumb-land')) {

                var itemH = itemW / 3 * 2;

                jQuery('.post-image', jQuery(this)).css('width', itemW + 'px');
                jQuery('.post-image', jQuery(this)).css('height', itemH + 'px');

            }
*/
            if (jQuery(this).hasClass('pr-thumb-port')) {

                var itemH = (itemW / 3 * 4) + getRandomInt(0, 40);

                jQuery('.post-image', jQuery(this)).css('width', itemW + 'px');
                jQuery('.post-image', jQuery(this)).css('height', itemH + 'px');

            }


            // Magnage Elements

            var relWidth = jQuery('.post-image', jQuery(this)).width();
            var relHeight = jQuery('.post-image', jQuery(this)).height();

            if (relWidth < 240 || relHeight < 240) {
                jQuery('.caption-subtitle', jQuery(this)).css('display', 'none');
            } else {
                jQuery('.caption-subtitle', jQuery(this)).css('display', '');

            }

            if (relWidth < 180 || relHeight < 180) {
                jQuery('.caption-title', jQuery(this)).css('display', 'none');
                jQuery('.meta-value', jQuery(this)).css('display', 'none');
            } else {
                jQuery('.caption-title', jQuery(this)).css('display', '');
                jQuery('.meta-value', jQuery(this)).css('display', '');
            }

            if (relWidth < 180) {
                jQuery('.wrap-excerpt', jQuery(this)).css('display', 'none');
            } else {
                jQuery('.wrap-excerpt', jQuery(this)).css('display', '');
            }

            if (relWidth < 160 || relHeight < 160) {
                jQuery('.btn-caption').css({'text-align': 'center', 'top': '50%', 'margin-top': '-30px'});
                jQuery('.btn-caption span').css('margin', '5px');
            } else {
                jQuery('.btn-caption').css({'text-align': '', 'top': '', 'left': '', 'margin': ''});
                jQuery('.btn-caption span').css('margin', '');
            }

            if ( KEY + 1 == photos_count ) {
                // initialize Masonry
                if ( isSafari ) {
                    setTimeout(function (){
                        new Masonry(document.querySelector('ul#fv-grid'), {
                            //columnWidth: 200,
                            columnWidth: '.wrap-post',
                            itemSelector: 'li'
                        });
                    },650);
                } else {
                    new Masonry(document.querySelector('ul#fv-grid'), {
                        //columnWidth: 200,
                        columnWidth: '.wrap-post',
                        itemSelector: 'li'
                    });
                }
            }

        });
        //jQuery(window).trigger('resize');

        /*imagesLoaded(container, function () {
        });*/
    }

    // Add check, will contest block exists, if not, try wait
    if ( document.querySelectorAll('.fv-contest-photos-container-inner .contest-block').length > 0 ) {
        fashion_load();
    } else {
        if ( !FvLib.documentLoaded ){
            FvLib.addHook('doc_ready', fashion_load, 11);
        } else {
            setTimeout(fashion_load, 800);
        }
    }

    FvLib.addHook('fv/ajax_go_to_page/ready', fashion_load, 10);


    var fashion_infinite_selector = function (selector) {
        return '#fv-grid';
    }
    FvLib.addFilter('fv/fv_ajax_go_to_page/infinite_selector', fashion_infinite_selector, 10, 1);

//});
})();
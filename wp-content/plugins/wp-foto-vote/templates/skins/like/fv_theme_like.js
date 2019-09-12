    /* Theme like == wp foto vote == wp-vote.net == */
//FvLib.addHook('doc_ready', function() {
(function() {

    var like_load = function () {
        setTimeout(fv_like_contest_ended, 1000);

        function fv_like_contest_ended() {
            if ( FvLib.isMobile() ) {
                jQuery(".contest-block").each(function(key, el){
                    //fv_like_center_icon(el);
                    jQuery(el).addClass("hover");
                });

            } else {
                jQuery(".contest-block.ended").each(function(key, el){
                    jQuery(el).addClass("hover");
                    // .mouseenter()
                });
            }
        }

         jQuery(".fv_button").click(function() {
            $block = jQuery(this).closest(".contest-block");
            if ( !$block.hasClass("ended") ) {
                jQuery($block).addClass("hover");
            }
         });
    }

    //like_load();
    // Add check, will contest block exists, if not, try wait
    if ( document.querySelectorAll('.fv-contest-photos-container-inner .contest-block').length > 0 ) {
        like_load();
    } else {
        if ( !FvLib.documentLoaded ){
            FvLib.addHook('doc_ready', like_load, 11);
        } else {
            setTimeout(like_load, 900);
        }
    }

    FvLib.addHook('fv/ajax_go_to_page/ready', like_load, 10);

})();
FvLib.addHook('doc_ready', function() {

    FvRouter.addController("Core", Simple.Controller.extend({

        unlockInput : function(eventEl){
            jQuery( jQuery(eventEl).data("target") ).prop("disabled", false);

            jQuery(eventEl).remove();
            return false;
        },

    }));

});

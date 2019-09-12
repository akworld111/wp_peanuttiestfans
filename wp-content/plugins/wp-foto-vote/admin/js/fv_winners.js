FvLib.addHook('doc_ready', function() {
    var $ = jQuery;

    FvRouter.addController("Winners", Simple.Controller.extend({

        manualPick : function(eventEl){
            var $eventEl = jQuery(eventEl);
            //your awesome code here
            $(".js-winners-layout-main").hide();

            var place = $($eventEl).data("for-place");
            var action_url = $($eventEl).data("action-url");

            var template = wp.template( "winners-manual-pick-form" );

            $(".js-winner-manual-pick-form-wrap")
                .html( template( { place: place } ) )
                .show();

            jQuery.get(
                action_url,
                {},
                function(resp){
                    resp = FvLib.parseJson(resp);
                    if ( resp.success && resp.list ) {

                        $(".select2el").select2({
                            data: resp.list,
                            templateResult: function (state) {
                                console.log(state);
                                if (!state.id) { return "-"; }
                                var $state = $(
                                    '<span><span class="select2-thumb-wrap"><img src="' + state.thumb + '" class="select2-thumb" /></span> ' + state.text + '</span>'
                                );
                                return $state;
                            }
                        });
                        
                    } else {
                        alert("Can't load competitors list!");
                    }
                }
            );

            return false;
        },

        manualPickCancel : function(eventEl){
            $(".js-winner-manual-pick-form-wrap").html("").hide();
            $(".js-winners-layout-main").show();
            return false;
        },

        manualPickSubmit: function(eventEl){
            $form = $(eventEl);

            $form.closest('div').append('<span class="spinner"></span>');

            jQuery.ajax({
                type: 'POST',
                url: $form.attr("action"),
                data: $form.serialize(),
                processData: false,  // tell jQuery not to process the data
                //contentType: false,   // tell jQuery not to set contentType
                success: function(response) {
                    response = FvLib.parseJson(response);

                    $form.closest('div').find('.spinner').remove();

                    if ( response.success ) {
                        window.location.reload();
                        $(".js-winner-manual-pick-form-wrap").html("").hide();
                        $(".js-winners-layout-main").show();
                    } else if ( !response.success && response.errors ) {
                        var message = [response.message];
                        for(field in response.errors) {
                            message.push( response.errors[field] );
                        }
                        alert( message.join("\n") );
                    }
                }
            });

            return false;
        },


    }));
    // ContestantC ## Controller :: END
});

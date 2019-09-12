+(function($){

    $(".button-send-test").click(function (event) {
        event.preventDefault();

        var $button = $(this);
        
        var post_id = $button.data("post-id");

        jQuery.post(
            TNS.ajax_url,
            {action: "fv_test_notifications", post_id: post_id, nonce: TNS.nonce },
            function(resp){
                resp = FvLib.parseJson(resp);
                if ( resp.success ) {
                    alert( "Webhook successful sent, if properly configured!" );
                } else if (resp.message) {
                    alert( "Error happens: " + resp.message );
                } else {
                    alert( "Error happens!" );
                }
            }
        );
    });
}(jQuery));
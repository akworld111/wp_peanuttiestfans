/**
 * Init Sortable JS
 * @since 2.2.605
 */
jQuery(document).ready(function($){

    $( "ul.sortable_block_list" ).sortable({
        handle : ".drag-handle",
        items: "> li",
        connectWith: ".sortable_block_list",
        // helper: function( evt, el ) {
        //     return el.parent();
        // },
		// This event is triggered when the user stopped sorting and the DOM position has changed.
        update: function( evt, ui ) {

            $(".sortable_block_list").each(function(kul, ul) {
				$(ul).find("li").each(function(k, li) {
					var $pos = $(li).find(".blocks__list_input__pos").val(k + 1);
				});
			});
        },
        out: function( evt, ui ) {
            //console.log(evt.dragged);

            console.log(evt);
            console.log(ui);

             var $input = ui.item.find(".blocks__list_input");

             var section = ui.item.parent().data("section");

            console.log(section);
			//
             if ( $input.val() != section ) {
                 $input.val( section );
             }
			//
            // return originalEvent;
		}
    });

    //$( ".sortable_block_list" ).disableSelection();

	// var sortable_blocks = document.querySelectorAll(".sortable_block_list");
	//
	// for( var N = 0; N < sortable_blocks.length; N++ ) {
	// 	Sortable.create(sortable_blocks[N], {
	// 		group: 'sortable',
	// 		animation: 100,
	// 		onMove: onMove,
	// 	});
	// }
	//
	// /**
	//  * Event when you move an item in the list or between lists
	//  *
	//  * @param {Event}	evt
	//  * @param {Event}	originalEvent
	//  * @returns {Event}
	//  *
	//  * @see http://jsbin.com/tuyafe/1/edit?js,output (Example: )
	//  * evt.dragged; // dragged HTMLElement
	//  */
	// function onMove (evt, originalEvent) {
	// 	var input = evt.dragged.querySelector(".blocks__list_input");
	//
	// 	if ( input.value != originalEvent.currentTarget.dataset.section ) {
	// 		input.value = originalEvent.currentTarget.dataset.section;
	// 	}
	//
	// 	return originalEvent;
	// 	// return false; — for cancel
	// }
});
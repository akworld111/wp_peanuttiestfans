(function($) {
	$(document).on("click", ".fv-add-gallery-item", function(eventEl){
		jQuery( ".fv-gallery-entries" )
			  .append(
					wp.template("fv-contestant-gallery-row")( +(Math.floor(Math.random() * (99999 - 9999 + 1)) + 9999) )
			  );
	});
	
})(jQuery);
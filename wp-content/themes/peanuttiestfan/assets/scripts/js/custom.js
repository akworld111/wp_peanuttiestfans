/*
jQuery( document ).ready(function() {

    jQuery('.contest-block.is-voted').find('.fv_vote').text("Voted");

    jQuery('.fv_vote').click(function(){
        jQuery(this).closest('.contest-block').hasClass('is-voted').find('.fv_vote').text("Voted");
    });     
});*/

jQuery( document ).ready(function() { 
  if (FvLib == undefined) { 
      return; 
  } 
  
  var SHARING_LINK = "http://peanuttiestfans.com";
  
  // var fv_url = FvLib.applyFilters('fv/share/page_url', FvLib.singlePhotoLink(current.id)); 
  // Change  URL that will be send to FB/Google+/Twitter, etc
  
  // Note: FB (with app id) + Pinterest must shows data from photo (from app) + new link
  // All others will take data from new link, so will be used other image and description.

  FvLib.addFilter('fv/share/page_url', function(link){ 
      return SHARING_LINK; 
  }, 10, 1); 
      
  // Change in TEXT Field
  FvLib.addHook('fv/modal/open_widget', function( screen ) {     
      if ( "share" == screen ) {
          jQuery("#modal-widget #photo_id").val( SHARING_LINK );
      }
  }, 10, 1 );    

}); 


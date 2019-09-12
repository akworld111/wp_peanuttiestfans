
//jQuery(document).ready(function() {
	// https://developers.facebook.com/docs/javascript/howto/jquery
	// enable cache
	jQuery.ajaxSetup({ cache: true });
	// get SDK script
	jQuery.getScript('//connect.facebook.net/' + fv_fb.language + '/all.js', function(){
		FB.init({
			appId: fv_fb.appId,
			status: true,
			cookie: true,
			xfbml: true
		});
	});

//});

// For future - FB login
// http://stackoverflow.com/questions/16662619/error-uncaught-typeerror-cannot-set-property-onclick-of-null
// http://stackoverflow.com/questions/8226118/facebook-error-b-is-null
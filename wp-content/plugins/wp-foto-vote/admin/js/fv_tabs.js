FvLib.addHook('doc_ready', function($) {
	
	var $ = jQuery;
	
	var $tabWrapper = $(".fv-tabs");
	var tabItems = $tabWrapper.find('.fv-tabs-navigation a'),
		tabContentWrapper = $tabWrapper.find('.fv-tabs-content'),
		activateTriggerOn = false;

	tabItems.on('click', function(event){
		event.preventDefault();
		var selectedItem = jQuery(this);
		if( !selectedItem.hasClass('selected') ) {
			var selectedTab = selectedItem.data('content'),
				selectedContent = tabContentWrapper.find('li[data-content="'+selectedTab+'"]');
				//slectedContentHeight = selectedContent.height();
			
			tabItems.removeClass('selected');
			selectedItem.addClass('selected');
			selectedContent.addClass('selected').siblings('li').removeClass('selected');

			// set url hash, if we options, we returns to page, where we stopped
			document.location.hash = selectedTab;
			//animate tabContentWrapper height when content changes 
			/*tabContentWrapper.animate({
				'height': slectedContentHeight
			}, 200);*/
		}

		if ( $tabWrapper.data("save-tab") ) {
			localStorage.setItem( $tabWrapper.data("save-tab-key") + "active_tab" , selectedTab );

		}

		if ( activateTriggerOn ) {
			jQuery(document).trigger("tab_" + selectedTab + "_activated");
			console.log( "tab-" + selectedTab + "-activated" );
		}


		return true;
	});


	if ( $tabWrapper.data("save-tab") ) {
		var localStorageActiveTab = localStorage.getItem( $tabWrapper.data("save-tab-key") + "active_tab" );
	}

	if ( $tabWrapper.data("activate-trigger-on") ) {
		activateTriggerOn = true;
	}

	if ( window.location.hash.substring(1) ) {
		jQuery('a[data-content="' + window.location.hash.substring(1) + '"]').click();
	} else if ( localStorageActiveTab ) {
		jQuery('a[data-content="' + localStorageActiveTab + '"]').click();
	}
/*
	//hide the .fv-tabs::after element when tabbed navigation has scrolled to the end (mobile version)
	checkScrolling($('.fv-tabs nav'));
	$(window).on('resize', function(){
		checkScrolling($('.fv-tabs nav'));
		tabContentWrapper.css('height', 'auto');
	});
	$('.fv-tabs nav').on('scroll', function(){ 
		checkScrolling($(this));
	});

	function checkScrolling(tabs){
		var totalTabWidth = parseInt(tabs.children('.fv-tabs-navigation').width()),
		 	tabsViewport = parseInt(tabs.width());
		if( tabs.scrollLeft() >= totalTabWidth - tabsViewport) {
			tabs.parent('.fv-tabs').addClass('is-ended');
		} else {
			tabs.parent('.fv-tabs').removeClass('is-ended');
		}
	}
	*/
});
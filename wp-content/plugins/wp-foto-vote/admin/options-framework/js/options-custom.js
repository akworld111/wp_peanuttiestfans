/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery(document).ready(function($) {
    // ==================================================== //
    // Will we need add Change action to elements?
    var relation_changes_hooked = [];
    // Process all
    function set_up_relations() {
        jQuery(".section.related").each(function(key, el) {
            var $relation_el = jQuery(el);

            // Check DATA reltaion el
            if ( !$relation_el.data('r-el') ) {
                console.error('FV :: Empty relation element!');
                return;
            }

            $relation_el.addClass('relation-ready')

            // FIND relation input and check that it exists, and it just one
            var $relation_input = jQuery('#section-' + $relation_el.data('r-el')).find('.of-input');
            if ( $relation_input.length != 1 ) {
                console.error('FV :: Error relation elements count = ' + $relation_input.length);
                return;
            }

            process_relation( $relation_el, $relation_input.val() );

            if ( typeof relation_changes_hooked[$relation_el.data('r-el')] == "undefined" ) {
                relation_changes_hooked[$relation_el.data('r-el')] = 1;
                jQuery($relation_input).on('change', relation_is_changed);
            }

        });
        //relation_changes_hooked = true;
    }

    function process_relation($relation_el, curr_value) {

        // Process relations type, like EQUAL
        switch ( $relation_el.data('r-type') ) {
            case 'equals':
                if ( curr_value != $relation_el.data('r-val') ) {
                    $relation_el.hide();
                } else {
                    $relation_el.show();
                }
                break;
            case 'contains':
                if ( curr_value.indexOf( $relation_el.data('r-val') ) === -1 ) {
                    $relation_el.hide();
                } else {
                    $relation_el.show();
                }
                break;
            case 'on':
                if ( curr_value == 1 ) {
                    $relation_el.hide();
                } else {
                    $relation_el.show();
                }
                break;
        }
    }

    function relation_is_changed(event) {
        console.log(this);
        var relation_input = this;
        // Find all related elements, and process It
        jQuery(".section.related.relation-ready").filter("[data-r-el='" + this.id +"']").each(function(key, el) {
            process_relation(jQuery(el), relation_input.value);
        });

        return true;
    }
    // INIT
    set_up_relations();
    // ==================================================== //
    jQuery(".switch-toggle-label").on("click", function() {
        jQuery(this).parent().toggleClass('switch-toggle-checked')
            .find('input').val( 1^this.previousElementSibling.value ).trigger("change");
    });

	// Loads the color pickers
	$('.of-color').wpColorPicker();

    // RGBA color picker
    $(".of-color-rgba").spectrum({
        showInput: true,
        showAlpha: true,
        showInitial: true,
        allowEmpty: false,
        preferredFormat: "rgb"
    });

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	// Loads tabbed sections if they exist
	if ( $('.nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $('.group'),
			$navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// Find if a selected tab is saved in localStorage
		if ( typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem('active_tab');
		}
        if (  window.location.hash.indexOf("options-group-") !== -1 ) {
            active_tab = window.location.hash;
        }

		// If active tab is saved and exists, load it's .group
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		// Bind tabs clicks
		$navtabs.click(function(e) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('active_tab', $(this).attr('href') );
			}

			var selected = $(this).attr('href');

			$group.hide();
			$(selected).fadeIn();

		});
	}

});
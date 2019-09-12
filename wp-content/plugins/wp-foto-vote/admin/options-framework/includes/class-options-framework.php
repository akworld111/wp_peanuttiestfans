<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Fv_Options_Framework {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.7.0
	 * @type string
	 */
	const VERSION = '1.8.4';

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.7.0
	 */
	public function init() {

		// Needs to run every time in case theme has been changed
		//add_action( 'admin_init', array( $this, 'set_theme_option' ) );

	}

	/**
	 * Wrapper for optionsframework_options()
	 *
	 * Allows for manipulating or setting options via 'of_options' filter
	 * For example:
	 *
	 * <code>
	 * add_filter( 'of_options', function( $options ) {
	 *     $options[] = array(
	 *         'name' => 'Input Text Mini',
	 *         'desc' => 'A mini text input field.',
	 *         'id' => 'example_text_mini',
	 *         'std' => 'Default',
	 *         'class' => 'mini',
	 *         'type' => 'text'
	 *     );
	 *
	 *     return $options;
	 * });
	 * </code>
	 *
	 * Also allows for setting options via a return statement in the
	 * options.php file.  For example (in options.php):
	 *
	 * <code>
	 * return array(...);
	 * </code>
	 *
	 * @return array (by reference)
	 */
	static function &get_options_list() {
		static $options = null;

		if ( !$options ) {
            /*
	        // Load options from options.php file (if it exists)
	        $location = apply_filters( 'options_framework_location', array('options.php') );
	        if ( $optionsfile = locate_template( $location ) ) {
	            $maybe_options = require_once $optionsfile;
	            if ( is_array( $maybe_options ) ) {
					$options = $maybe_options;
	            } else if ( function_exists( 'optionsframework_options' ) ) {
					$options = optionsframework_options();
				}
	        }
            */

	        // Allow setting/manipulating options via filters
            $options_sections = apply_filters( 'fv/addons/settings', $options );
            $options = array();

            foreach($options_sections as $sect) {
                $options[] = array(
                    'name' => $sect['title'],
                    'id' => sanitize_title($sect['title']),
                    'slug' => !empty($sect['slug']) ? $sect['slug'] : '',
                    'on' => isset($sect['on']) ? $sect['on'] : null,
                    'desc' => !empty($sect['desc']) ? $sect['desc'] : '',
                    'icon' => !empty($sect['icon']) ? $sect['icon'] : '',
                    'version' => !empty($sect['version']) ? ' [' . $sect['version'] . ']' : '',
                    'type' => 'heading'
                );

                if ( !empty($sect['fields']) ) {
                    $options = array_merge($options, $sect['fields']);
                }
            }
            //dbgx_trace_var($options);
		}

		return $options;
	}

}
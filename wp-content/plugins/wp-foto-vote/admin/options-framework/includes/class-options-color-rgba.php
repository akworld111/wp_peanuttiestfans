<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Fv_Options_Framework_Color_RGBA {
	/**
	 * Media Uploader Using the WordPress Media Library.
	 *
	 * Parameters:
	 *
	 * string $_id - A token to identify this field (the name).
	 * string $_value - The value of the field, if present.
	 * string $_desc - An optional description of the field.
	 *
	 */

	static function render_input( $_option, $_value, $_option_name ) {
        self::assets();

        if ( !empty($_option['std']) ) {
            $_value = fv_get_if_looks_rgba($_value, $_option['std']);
        }

        $output = '<input name="' . esc_attr( $_option_name . '[' . $_option['id'] . ']' ) . '" id="' . esc_attr( $_option['id'] ) . '" class="of-color-rgba" type="text" value="' . esc_attr( $_value ) . '" />';
        
		return $output;
	}

	/**
	 * Enqueue scripts for file uploader
	 */
    static function assets() {
        wp_enqueue_script( 'of-color-rgba', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.7.1/spectrum.min.js', array( 'jquery' ), Fv_Options_Framework::VERSION, true );
        wp_enqueue_style( 'of-color-rgba', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.7.1/spectrum.min.css', false, Fv_Options_Framework::VERSION );
	}
}
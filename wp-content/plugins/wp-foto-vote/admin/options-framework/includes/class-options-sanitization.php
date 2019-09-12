<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

/**
 * Sanitization for text input
 *
 * @link http://developer.wordpress.org/reference/functions/sanitize_text_field/
 */
add_filter( 'fv_of_sanitize_text', 'sanitize_text_field' );

/**
 * Sanitization for URL input
 *
 * @link https://codex.wordpress.org/Function_Reference/esc_url_raw
 */
add_filter( 'fv_of_sanitize_url', 'esc_url_raw' );

/**
 * Sanitization for EMAIL input
 *
 * @link https://codex.wordpress.org/Function_Reference/sanitize_email
 */
add_filter( 'fv_of_sanitize_email', 'sanitize_email' );

/**
 * Sanitization for password input
 *
 * @link http://developer.wordpress.org/reference/functions/sanitize_text_field/
 */
add_filter( 'fv_of_sanitize_password', 'sanitize_text_field' );

/**
 * Sanitization for select input
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'fv_of_sanitize_select', 'fv_of_sanitize_enum', 10, 2 );

/**
 * Sanitization for radio input
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'fv_of_sanitize_radio', 'fv_of_sanitize_enum', 10, 2 );

/**
 * Sanitization for image selector
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'fv_of_sanitize_images', 'fv_of_sanitize_enum', 10, 2 );

/**
 * Sanitization for Taxonomy selector
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'fv_of_sanitize_taxonomy', 'fv_of_sanitize_number', 10, 2 );

/**
 * Sanitization for textarea field
 *
 * @param $input string
 * @return $output sanitized string
 */
function fv_of_sanitize_textarea( $input ) {
    global $allowedposttags;
    $output = wp_kses( $input, $allowedposttags );
    return $output;
}
add_filter( 'fv_of_sanitize_textarea', 'fv_of_sanitize_textarea' );

/**
 * Sanitization for code textarea field
 *
 * @param $input string
 * @return sanitized string
 */
function fv_of_sanitize_code( $input ) {
    return $input;
}
add_filter( 'fv_of_sanitize_code', 'fv_of_sanitize_code' );

/**
 * Sanitization for textarea field
 *
 * @param $input string
 * @return sanitized string
 */
function fv_of_sanitize_number( $input ) {
	return (int) $input;
}
add_filter( 'fv_of_sanitize_spinner', 'fv_of_sanitize_number' );
add_filter( 'fv_of_sanitize_number', 'fv_of_sanitize_number' );

/**
 * Sanitization for checkbox input
 *
 * @param $input string (1 or empty) checkbox state
 * @return $output '1' or false
 */
function fv_of_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}
add_filter( 'fv_of_sanitize_checkbox', 'fv_of_sanitize_checkbox' );
add_filter( 'fv_of_sanitize_switch', 'fv_of_sanitize_checkbox' );

/**
 * Sanitization for multicheck
 *
 * @param array of checkbox values
 * @return array of sanitized values ('1' or false)
 */
function fv_of_sanitize_multicheck( $input, $option ) {
    //FvFunctions::dump($input);
    //FvFunctions::dump($option);
	$output = array();
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = false;
		}
		foreach( $input as $key => $value ) {
			if ( isset($option['options'][$key]) && $value ) {
				$output[$key] = '1';
			}
		}
	}

	return $output;
}
add_filter( 'fv_of_sanitize_multicheck', 'fv_of_sanitize_multicheck', 10, 2 );

/**
 * Sanitization for Sortable
 *
 * @param array of checkbox values
 * @return array of sanitized values ('1' or false)
 */
function fv_of_sanitize_sortable( $input, $option ) {
    //FvFunctions::dump($input);
    //FvFunctions::dump($option);
	$clean = array();
	if ( is_array( $input ) ) {


		if ( !$option['default_section'] && $option['sections'] ) {
			reset( $option['sections'] );
			$option['default_section'] = key($option['sections']);
		}

		foreach( $input as $field => $field_data ) {
			if ( !isset($option['sections'][ $field_data['section'] ]) ) {
				$input[$field]['section'] = $option['default_section'];
			}
			$input[$field]['title'] = sanitize_text_field($field_data['title']);
		}
		$clean= $input;
	}
	return $clean;
}
add_filter( 'fv_of_sanitize_sortable', 'fv_of_sanitize_sortable', 10, 2 );

/**
 * File upload sanitization.
 *
 * Returns a sanitized filepath if it has a valid extension.
 *
 * @param string $input filepath
 * @returns string $output filepath
 */
function fv_of_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype( $input );
	if ( $filetype["ext"] ) {
		$output = esc_url( $input );
	}
	return $output;
}
add_filter( 'fv_of_sanitize_upload', 'fv_of_sanitize_upload' );

/**
 * Sanitization for editor input.
 *
 * Returns unfiltered HTML if user has permissions.
 *
 * @param string $input
 * @returns string $output
 */
function fv_of_sanitize_editor( $input ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedposttags;
		$output = wp_kses( $input, $allowedposttags );
	}
	return $output;
}
add_filter( 'fv_of_sanitize_editor', 'fv_of_sanitize_editor' );

/**
 * Sanitization of input with allowed tags and wpautotop.
 *
 * Allows allowed tags in html input and ensures tags close properly.
 *
 * @param string $input
 * @returns string $output
 */
function fv_of_sanitize_allowedtags( $input ) {
	global $allowedtags;
	$output = wpautop( wp_kses( $input, $allowedtags ) );
	return $output;
}

/**
 * Sanitization of input with allowed post tags and wpautotop.
 *
 * Allows allowed post tags in html input and ensures tags close properly.
 *
 * @param string $input
 * @returns string $output
 */
function fv_of_sanitize_allowedposttags( $input ) {
	global $allowedposttags;
	$output = wpautop( wp_kses( $input, $allowedposttags) );
	return $output;
}

/**
 * Validates that the $input is one of the avilable choices
 * for that specific option.
 *
 * @param string $input
 * @returns string $output
 */
function fv_of_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/**
 * Sanitization for background option.
 *
 * @returns array $output
 */
function fv_of_sanitize_background( $input ) {

	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'fv_of_sanitize_hex', $input['color'] );
	$output['image'] = apply_filters( 'fv_of_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'fv_of_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'fv_of_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'fv_of_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'fv_of_sanitize_background', 'fv_of_sanitize_background' );

/**
 * Sanitization for background repeat
 *
 * @returns string $value if it is valid
 */
function fv_of_sanitize_background_repeat( $value ) {
	$recognized = fv_of_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_background_repeat', current( $recognized ) );
}
add_filter( 'fv_of_background_repeat', 'fv_of_sanitize_background_repeat' );

/**
 * Sanitization for background position
 *
 * @returns string $value if it is valid
 */
function fv_of_sanitize_background_position( $value ) {
	$recognized = fv_of_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_background_position', current( $recognized ) );
}
add_filter( 'fv_of_background_position', 'fv_of_sanitize_background_position' );

/**
 * Sanitization for background attachment
 *
 * @returns string $value if it is valid
 */
function fv_of_sanitize_background_attachment( $value ) {
	$recognized = fv_of_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_background_attachment', current( $recognized ) );
}
add_filter( 'fv_of_background_attachment', 'fv_of_sanitize_background_attachment' );

/**
 * Sanitization for font size
 */
function fv_of_sanitize_font_size( $value ) {
	$recognized = fv_of_recognized_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_font_size', $recognized );
}
add_filter( 'fv_of_font_size', 'fv_of_sanitize_font_size' );

/**
 * Sanitization for font style
 */
function fv_of_sanitize_font_style( $value ) {
	$recognized = fv_of_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_font_style', current( $recognized ) );
}
add_filter( 'fv_of_font_style', 'fv_of_sanitize_font_style' );

/**
 * Sanitization for font face
 */
function fv_of_sanitize_font_face( $value ) {
	$recognized = fv_of_recognized_font_faces();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'fv_of_default_font_face', current( $recognized ) );
}
add_filter( 'fv_of_font_face', 'fv_of_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 */
function fv_of_recognized_background_repeat() {
	$default = array(
		'no-repeat' => __( 'No Repeat', 'options-framework' ),
		'repeat-x'  => __( 'Repeat Horizontally', 'options-framework' ),
		'repeat-y'  => __( 'Repeat Vertically', 'options-framework' ),
		'repeat'    => __( 'Repeat All', 'options-framework' ),
		);
	return apply_filters( 'fv_of_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 */
function fv_of_recognized_background_position() {
	$default = array(
		'top left'      => __( 'Top Left', 'options-framework' ),
		'top center'    => __( 'Top Center', 'options-framework' ),
		'top right'     => __( 'Top Right', 'options-framework' ),
		'center left'   => __( 'Middle Left', 'options-framework' ),
		'center center' => __( 'Middle Center', 'options-framework' ),
		'center right'  => __( 'Middle Right', 'options-framework' ),
		'bottom left'   => __( 'Bottom Left', 'options-framework' ),
		'bottom center' => __( 'Bottom Center', 'options-framework' ),
		'bottom right'  => __( 'Bottom Right', 'options-framework')
		);
	return apply_filters( 'fv_of_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 */
function fv_of_recognized_background_attachment() {
	$default = array(
		'scroll' => __( 'Scroll Normally', 'options-framework' ),
		'fixed'  => __( 'Fixed in Place', 'options-framework')
		);
	return apply_filters( 'fv_of_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 */

function fv_of_sanitize_hex( $hex, $default = '' ) {
	if ( fv_of_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}
add_filter( 'fv_of_sanitize_color', 'fv_of_sanitize_hex' );

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 */

function fv_of_sanitize_rgba( $rgba, $option ) {

    if ( isset($option['std']) ) {
        return fv_get_if_looks_rgba($rgba, $option['std']);
    } else{
        return fv_get_if_looks_rgba($rgba, '');
    }

}
add_filter( 'fv_of_sanitize_color_rgba', 'fv_of_sanitize_rgba', 10, 2 );

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function fv_of_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'fv_of_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 */
function fv_of_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
		);
	return apply_filters( 'fv_of_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 */
function fv_of_recognized_font_styles() {
	$default = array(
		'normal'      => __( 'Normal', 'options-framework' ),
		'italic'      => __( 'Italic', 'options-framework' ),
		'bold'        => __( 'Bold', 'options-framework' ),
		'bold italic' => __( 'Bold Italic', 'options-framework' )
		);
	return apply_filters( 'fv_of_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 */
function fv_of_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}
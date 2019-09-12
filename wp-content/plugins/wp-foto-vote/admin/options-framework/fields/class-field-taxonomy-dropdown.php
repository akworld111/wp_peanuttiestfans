<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Fv_Options_Framework_Taxonomy_Dropdown {
	/**
	 * 
	 *
	 * Parameters:
	 *
	 * @var string $option - A token to identify this field (the name).
	 * @var string $value - The value of the field, if present.
	 * @var string $option_name - An optional description of the field.
	 * 
	 * @return string
	 *
	 */

	static function render_input( $option, $value, $option_name ) {
		
		/**
		 * Get Flat list of categories
		 * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/#source
		 */
		$categories_query_params_array = apply_filters( 'fv/addons/settings/settings/taxonomy_dropdown/query_args',
			array(
				'taxonomy'               => 'category',
				'orderby'                => 'parent',
				'order'                  => 'ASC',
				'hide_empty'             => false,
				'hierarchical'           => true,
				'fields'                 => 'all_with_object_id',
			)
		);


		$categories_array_flat = get_terms($categories_query_params_array);

		$categories_array = get_terms($categories_query_params_array);

		$categories_array_keyed = array();
		foreach ($categories_array as $category_row){
			$categories_array_keyed[ $category_row->term_id ] = $category_row;
		}
		unset($categories_array);

		$categories_array_flat = array();

		foreach ($categories_array_keyed as $category_row){
			if ( !$category_row->parent ) {
				$categories_array_flat[ $category_row->term_id ] = $category_row->name;
			} else {
				if ( !$categories_array_keyed[$category_row->parent]->parent ) {
					$categories_array_flat[ $category_row->term_id ] = ' - ' . $category_row->name;
				} else {
					$categories_array_flat[ $category_row->term_id ] = ' - - ' . $category_row->name;
				}
			}
		}

		unset($categories_array_keyed);

		$output = '<select class="of-input" name="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" id="' . esc_attr( $option['id'] ) . '">';

			foreach ($categories_array_flat as $term_id => $term_name ) {
				$output .= '<option'. selected( $value, $term_id, false ) .' value="' . esc_attr( $term_id ) . '">' . esc_html( $term_name ) . '</option>';
			}

		$output .= '</select>';		
		
        //$output = '<input name="' . esc_attr( $_option_name . '[' . $_option['id'] . ']' ) . '" id="' . esc_attr( $option['id'] ) . '" class="of-color-rgba" type="text" value="' . esc_attr( $_value ) . '" />';
        
		return $output;
	}
}
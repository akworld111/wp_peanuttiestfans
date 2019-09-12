<?php
/**
 * @package   Options_Framework
 * 
 * @since 2.2.605
 */

class Fv_Options_Framework_Sortable {
	

	/**
	 * Generate Select HTML.
	 *
	 * @param  array $option
	 * @param  mixed $saved
	 * @param  string $option_name
	 *
	 * @return string
	 */
	public static function generate_sortable_blocks_html( $option, $saved, $option_name ) {
		self::assets();

		$option_name = $option_name . '[' . $option['id'] . ']';
		
//		$value structure
//		array (
//			// Section
//			'active' => array(
//				'date_start'	=> 'Contest start date',
//				'date_finish'	=> 'Contest finish data',
//			)
//		)

		$defaults  = array(
			'sections'			=> array('active'=>'Active', 'inactive'=>'Inactive'),
			'fields'			=> array('test'=>'Test'),
			'default_section'	=> '',
			'editable'			=> true,
		);

		$data = wp_parse_args( $option, $defaults );

		if ( !$data['default_section'] && $data['sections'] ) {
			reset( $data['sections'] );
			$data['default_section'] = key($data['sections']);
		}


		$N = 1;
		foreach ($data['fields'] as $field => $field_caption) {
			$default_fields[$field]['section'] = $data['default_section'];
			$default_fields[$field]['title'] = $field_caption;
			$default_fields[$field]['position'] = $N;
            $N++;
		}

		if ( $saved ) {
			$saved = array_merge( $default_fields, $saved );
		} else {
			$saved = $default_fields;
		}

		$make_editable = $data['editable'];

		//fv_dump($saved);

		$fields_by_sections = array();
		// ## Set default structure

		foreach ($saved as $field => $field_data) {
			$fields_by_sections[$field_data['section']][$field] = $field_data;
		}


		// 'default'  => array('billing'=>'left', 'shipping'=>'left', 'order_note'=>'right', 'payment_method'=>'right', 'order_items'=>'after'),
		ob_start();
		?>
			<?php foreach ($data['sections'] as $section_key => $section_title): ?>
				<div class="blocks__list blocks__list--small">
					<div class="blocks__list_title"><?php echo $section_title; ?></div>
					<ul class="sortable_block_list" data-section="<?php echo $section_key; ?>">
						<?php IF( !empty($fields_by_sections[$section_key]) ): ?>
							<?php echo self::generate_sortable_block_list_html( $section_key, $fields_by_sections[$section_key], $data['fields'], $option_name, $make_editable );  ?>
						<?php ENDIF; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		<?php


		return ob_get_clean();
	}


	public static function generate_sortable_block_input_html( $option_name, $section_key, $section_field, $section_field_data, $make_editable ) {
		ob_start();
		?>
		<input type="text" class="blocks__list_input__title" name="<?php echo esc_attr($option_name), '[', esc_attr($section_field), '][title]'; ?>" value="<?php echo $section_field_data['title']; ?>" <?php echo ($make_editable) ? 'readonly' : ''; ?>>

		<input type="hidden" class="blocks__list_input" name="<?php echo esc_attr($option_name), '[', esc_attr($section_field), '][section]'; ?>" value="<?php echo $section_key; ?>">
		<input type="hidden" class="blocks__list_input__pos" name="<?php echo esc_attr($option_name), '[', esc_attr($section_field), '][position]'; ?>" value="<?php echo $section_field_data['position']; ?>">
		<?php
		return ob_get_clean();
	}

	public static function generate_sortable_block_list_html( $section_key, $section_fields, $all_fields, $option_name, $make_editable ) {

        uasort($section_fields, function($a, $b) {
            return ($a['position'] < $b['position']) ? -1 : 1;
        });


        ob_start();
		?>

			<?php
			foreach ($section_fields as $section_field => $section_field_data):
				if ( !isset($all_fields[$section_field]) ) {
					trigger_error('FV Option Framework :: Field ' . $section_field . ' does not exists in global array!', E_USER_WARNING);
					continue;
				}

				echo '<li><span class="drag-handle">&#9776;</span>';
				echo self::generate_sortable_block_input_html($option_name, $section_key, $section_field, $section_field_data, $make_editable);
				echo '</li>';
			endforeach;
			?>

		<?php
		return ob_get_clean();
	}

	/**
	 * Enqueue scripts
	 */
	public static function assets() {
		wp_enqueue_style('fv_of_sortable', FV::$ADMIN_URL. '/options-framework/vendor/sortable/sortable.css', array(), '1.5.1');
//
//		wp_enqueue_script('fv_of_sortable', FV::$ADMIN_URL . '/options-framework/vendor/sortable/sortable.js', array(), '1.5.1', true);

        wp_enqueue_script('fv_of_sortable_init', FV::$ADMIN_URL . '/options-framework/js/sortable.js', array('jquery-ui-sortable'), FV::VERSION, true);
    }
}
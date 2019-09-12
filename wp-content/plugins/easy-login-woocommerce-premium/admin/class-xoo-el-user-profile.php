<?php
/**
 * Add extra profile fields for users in admin
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Xoo_El_User_Profile', false ) ) :

	/**
	 * Xoo_El_User_Profile Class.
	 */
	class Xoo_El_User_Profile {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'show_user_profile', array( $this, 'add_customer_meta_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'add_customer_meta_fields' ) );

			add_action( 'personal_options_update', array( $this, 'save_customer_meta_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) );
		}


		/**
		 * Show Address Fields on edit user pages.
		 *
		 * @param WP_User $user
		 */
		public function add_customer_meta_fields( $user ) {
		
			$fields = xoo_el()->fields->get_fields_data();
			if( empty( $fields ) ) return;

			$html  = '';
			$html .= '<h2>Login/Signup Pop up fields</h2>';
			$html .= '<table class="form-table">';

			foreach ( $fields as $field_id => $field_data ) {

				//Skip if predefined field
				if( strpos( $field_id , 'xoo_el_' ) !== false ) continue;

				$type = $label = $label_text = $placeholder = $db_value = $class = null;
				$settings = $field_data['settings'];
				extract( $settings );

				if( trim( $label_text ) ){
					$label = $label_text;
				}
				elseif( trim( $placeholder ) ) {
					$label = $placeholder;
				}
				else{
					$label = $field_id;
				}

				$type 		= esc_attr( $field_data[ 'type' ] );
				$class 		= isset( $class ) ? esc_attr( $class ) : '';		
				$class 		= $type === 'date' ? $class.' xoo-aff-datepicker' : $class;
				$db_value 	= get_user_meta( $user->ID, $field_id,true);

				$html .= '<tr>';

				if( $type === 'checkbox_single' ){
					$checkbox = $checkbox_single;
					$html .= '<th>'.$checkbox['label'].'</th>';
				}
				else{
					$html .= '<th>'.$label.'</th>';
				}

				$html .= '<td>';
				switch ( $type ) {
					case 'text':
					case 'email':
					case 'number':
					case 'date':
						$html .= '<input type="text" value="'.$db_value.'" id="'.$field_id.'" name="'.$field_id.'" class="'.$class.'">';
						break;

					case 'password':
						$html .= '<input type="password" value="'.$db_value.'" id="'.$field_id.'" name="'.$field_id.'" class="'.$class.'">';
						break;


					case 'checkbox_single':
						if( empty( $checkbox_single ) ) continue 2;

						$checkbox = $checkbox_single;

						if( !isset( $checkbox['value'] ) || !$checkbox['value'] ) continue 2;
						$checked = $db_value ? 'checked' : '';
						$html .= '<label>';
						$html .= '<input type="checkbox" id="'.$field_id.'" name="'.$field_id.'" class="'.$class.'" value="'.$checkbox['value'].'" '.$checked.'>';
						$html .= '</label>';
						
						break;


					case 'checkbox_list':
						if( empty( $checkbox_list ) ) continue 2;

						$html .= '<div class="xoo-aff-options-list">';
						foreach ( $checkbox_list as $checkbox ) {
							if( !isset( $checkbox['value'] ) || !$checkbox['value'] ) continue 2;
							$checked = is_array($db_value) && in_array( $checkbox['value'], $db_value ) ? 'checked' : '';
							$html .= '<label>';
							$html .= '<input type="checkbox" name="'.$field_id.'[]" class="'.$class.'" value="'.$checkbox['value'].'" '.$checked.'>'.$checkbox['label'];
							$html .= '</label>';
						}
						$html .= '</div>';

						break;

					case 'radio':
						if( empty( $radio ) ) continue 2;

						$html .= '<div class="xoo-aff-options-list">';
						foreach ( $radio as $radio_option ) {
							if( !isset( $radio_option['value'] ) || !$radio_option['value'] ) continue;
							$checked = $db_value === $radio_option['value'] ? 'checked' : '';
							$html .= '<label>';
							$html .= '<input type="radio" name="'.$field_id.'" class="'.$class.'" value="'.$radio_option['value'].'" '.$checked.'>'.$radio_option['label'];
							$html .= '</label>';
						}
						$html .= '</div>';

						break;

					case 'select_list':
						if( empty( $select_list ) ) continue 2;

						$html .= '<select class="xoo-aff-options-list" id="'.$field_id.'" name="'.$field_id.'" class="'.$class.'">';
						foreach ( $select_list as $select_option ) {
							if( !isset( $select_option['value'] ) || !$select_option['value'] ) continue 2;
							$selected = $db_value === $select_option['value'] ? 'selected' : '';
							$html .= '<option value="'.$select_option['value'].'" '.$selected.'>'.$select_option['label'].'</option>';
						}
						$html .= '</select>';

						break;
				}

				$html .= '</td>';
			}
			
			$html .= '</table>';

			echo $html;
		}


		/**
		 * Save Address Fields on edit user pages.
		 *
		 * @param int $user_id User ID of the user being saved
		 */
		public function save_customer_meta_fields( $user_id ) {

			$save_fields = xoo_el()->fields->get_fields_data();
			if( empty( $save_fields ) ) return;

			foreach ( $save_fields as $field_id => $field_data ) {
				update_user_meta( $user_id, $field_id, isset( $_POST[ $field_id ] ) ? wc_clean( $_POST[ $field_id ] ) : '' );
				/*if ( isset( $field_data['type'] ) && 'checkbox_single' === $field_data['type'] ) {
					update_user_meta( $user_id, $field_id, isset( $_POST[ $field_id ] ) );
				} elseif ( isset( $_POST[ $field_id ] ) ) {
					update_user_meta( $user_id, $field_id, wc_clean( $_POST[ $field_id ] ) );
				}*/
			}
		}

		/**
		 * Get user meta for a given key, with fallbacks to core user info for pre-existing fields.
		 *
		 * @since 3.1.0
		 * @param int    $user_id User ID of the user being edited
		 * @param string $field_id     Key for user meta field
		 * @return string
		 */
		protected function get_user_meta( $user_id, $field_id ) {
			$value           = get_user_meta( $user_id, $field_id, true );
			$existing_fields = array( 'billing_first_name', 'billing_last_name' );
			if ( ! $value && in_array( $field_id, $existing_fields ) ) {
				$value = get_user_meta( $user_id, str_replace( 'billing_', '', $field_id ), true );
			} elseif ( ! $value && ( 'billing_email' === $field_id ) ) {
				$user  = get_userdata( $user_id );
				$value = $user->user_email;
			}

			return $value;
		}
	}

endif;

return new Xoo_El_User_Profile();

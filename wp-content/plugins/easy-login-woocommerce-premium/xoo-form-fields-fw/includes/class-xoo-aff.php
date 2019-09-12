<?php

class Xoo_Aff{

	protected $slug, $db_field;

	public function __construct( $slug ){
		$this->slug 		= $slug;
		$this->db_field 	= 'xoo_aff_fields';
		$this->includes();
	}

	public function includes(){

		include XOO_AFF_DIR.'/includes/xoo-aff-functions.php';

		if( $this->is_request( 'admin' ) ){
			xoo_aff_get_template( 'class-xoo-aff-admin.php', XOO_AFF_DIR.'/admin/'  );
			xoo_aff_admin();
		}

	}


	//Enqueue scripts from the main plugin
	public function enqueue_scripts(){

		wp_enqueue_style( 'xoo-aff-style', XOO_AFF_URL.'/assets/css/xoo-aff-style.css', array(), XOO_AFF_VERSION) ;
		wp_enqueue_style( 'xoo-aff-font-awesome5', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css' );

		$fields = $this->get_fields_data();

		$has_date = $datepicker_data = false;
		if( !empty( $fields ) ){
			foreach ( $fields as $field_id => $field_data) {
				if( !isset( $field_data['type'] ) ) continue;
				if( $field_data['type'] === "date" ){
					$has_date = true;
					$args = array(
						'dateFormat' => isset( $field_data['settings']['dateformat'] ) ? $field_data['settings']['dateformat'] : "yy-mm-dd",
					);

					$user_args = apply_filters( 'xoo_aff_datepicker_args', array(
						'changeMonth' => true,
						'changeYear'  => true,
						'yearRange' => 'c-100:c+10',
					), $field_id  );

					$datepicker_data[] = array(
						'id' 		=> $field_id,
						'args' 		=> array_merge( $args, $user_args )		
					);
				}
			}
		}

		if( $has_date ){
			wp_enqueue_style( 'jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
			wp_enqueue_script('jquery-ui-datepicker');
		}

		wp_enqueue_script( 'xoo-aff-js', XOO_AFF_URL.'/assets/js/xoo-aff-js.js', array( 'jquery' ), XOO_AFF_VERSION, true );
		wp_localize_script('xoo-aff-js','xoo_aff_localize',array(
			'adminurl'  			=> admin_url().'admin-ajax.php',
			'datepicker_data'		=> json_encode( $datepicker_data )
		));


	}


	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	public function get_admin_fields(){
		return json_decode( get_option( 'xoo_aff_admin_fields' ), true );
	}

	public function get_field_data( $field_id ){
		$fields = $this->get_fields_data();
		if( isset($fields[ $field_id ]) ){
			return $fields[ $field_id ];
		}
	}


	public function get_fields_data(){
		return json_decode( get_option( $this->db_field ), true );
	}

	public function get_fields_html( $args = array() ){
		
		$html = '<div class="xoo-aff-fields">';

		$fields = $this->get_fields_data();

		ob_start();

		foreach ( $fields as $field_id => $field_data ){
			$this->get_field_html( $field_id, $args );
		}
		
		$html .= ob_get_clean();

		$html .= '</div>';

		echo $html;
	}


	public function get_field_html( $field_id, $args = array() ){

		$args = wp_parse_args(
			$args,
			array(
				'show_icon' 	=> 'yes',
				'validation' 	=> 'yes',
				'cont_class'	=> array('xoo-aff-group'),
				'field_value'	=> null,
			)
		);

		$field_data = (array) $this->get_field_data( $field_id );

		if( empty($field_data) ) return;

		$args['field_data'] = $field_data;

		xoo_aff_get_template( 'xoo-aff-fields.php', XOO_AFF_DIR.'/includes/templates/', $args );

	}

}

?>
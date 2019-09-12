<?php

// If this file is called directly, abort.
if (!class_exists('WP')) {
    die();
}


/**
 * Integrate WP Customizer
 * 
 * @since 2.2.814
 */

class FV_Customizer {

    static function init() {
        new FV_Customizer__Design();

        add_action( 'customize_register', array('FV_Customizer', 'customize_register'), 9 );
        add_action( 'customize_preview_init', array('FV_Customizer', 'customizer_live_preview') );
        add_action( 'customize_controls_enqueue_scripts', array('FV_Customizer', 'customize_controls_enqueue_scripts') );


//        fv_dump( get_option('fv') );
//        die;

        //add_action( 'customize_controls_enqueue_scripts ', array('FV_Customizer', 'customize_controls_enqueue_scripts') );
        //add_action( 'wp_footer', array('FV_Customizer', 'customizer_css'), 11 );
    }

    /**
     * @param WP_Customize_Manager $wp_customize
     */
    static function customize_register($wp_customize ) {

        //require LRM_PRO_PATH . 'includes/customizer/class-lrm-pro-customizer--button.php';

//		/**
//		 * Add our Header & Navigation Panel
//		 */
		$wp_customize->add_panel( 'wp_foto_vote',
			array(
				'title' => __( 'WP Foto Vote' ),
				//'description' => esc_html__( 'Adjust your Header and Navigation sections.' ), // Include html tags such as

				'priority' => 160, // Not typically needed. Default is 160
				'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
				'theme_supports' => '', // Rarely needed
				'active_callback' => '', // Rarely needed
			)
		);
//
//        $wp_customize->add_section( 'fv_general',
//            array(
//                'title' => __( '[Settings] Gallery' ),
//                //'description' => esc_html__( 'Here you can customize modal styles.' ),
//                'panel' => 'wp_foto_vote', // Only needed if adding your Section to a Panel
//                'priority' => 160, // Not typically needed. Default is 160
//                'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
//                'theme_supports' => '', // Rarely needed
//                'active_callback' => '', // Rarely needed
//                'description_hidden' => 'false', // Rarely needed. Default is False
//            )
//        );
////
////		$wp_customize->add_setting( 'sample_default_text',
////			array(
////				'default' => '', // Optional.
////				'transport' => 'refresh', // Optional. 'refresh' or 'postMessage'. Default: 'refresh'
////				'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
////				'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
////				'theme_supports' => '', // Optional. Rarely needed
////				'validate_callback' => '', // Optional. The name of the function that will be called to validate Customizer settings
////				'sanitize_callback' => '', // Optional. The name of the function that will be called to sanitize the input data before saving it to the database
////				'sanitize_js_callback' => '', // Optional. The name of the function that will be called to sanitize the data before outputting to javascript code. Basically to_json.
////				'dirty' => false, // Optional. Rarely needed. Whether or not the setting is initially dirty when created. Default: False
////			)
////		);
//
//
//        $wp_customize->add_setting( 'fotov-block-width',
//            array(
//                'default' => '300',
//                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
//                'transport' => 'postMessage',
//                'sanitize_callback' => 'absint'
//            )
//        );
//
//        $wp_customize->add_control( 'fotov-block-width', array(
//            'type' => 'number',
//            'section' => 'fv_general', // Add a default or your own section
//            'label' => __( 'Gallery Block Width' ),
//            'description' => __( 'Not applies on Mobile and for Fashion and Flickr skins' ),
//        ) );
//
//        $wp_customize->add_setting( 'fv[theme]',
//            array(
//                'default' => fv_setting('theme'),
//                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
//                //'transport' => 'postMessage',
//                'sanitize_callback' => 'sanitize_text_field'
//            )
//        );
//
//        $gallery_skins = array();
//        foreach (FV_Skins::i()->getList() as $key => $theme_title):
//            $gallery_skins[$key] =  $theme_title;
//        endforeach;
//
//
//        $wp_customize->add_control( 'fv[theme]', array(
//            'section' => 'fv_general', // Add a default or your own section
//            'label' => __( 'Gallery Skin' ),
//            //'description' => __( 'Not applies on Mobile' ),
//            'type'    => 'select',
//            'choices' => $gallery_skins
//        ) );

        self::toolbar_settings($wp_customize);
    }

    /**
     * @param WP_Customize_Manager $wp_customize
     */
    static function toolbar_settings($wp_customize ) {

        $wp_customize->add_section( 'fv_toolbar',
            array(
                'title' => __( '[Settings]  Toolbar' ),
                //'description' => esc_html__( 'Here you can customize modal styles.' ),
                'panel' => 'wp_foto_vote', // Only needed if adding your Section to a Panel
                'priority' => 161, // Not typically needed. Default is 160
                'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
                'theme_supports' => '', // Rarely needed
                'active_callback' => '', // Rarely needed
                'description_hidden' => 'false', // Rarely needed. Default is False
            )
        );

        $wp_customize->add_setting( 'fv[show-toolbar]',
            array(
                'default' => 0,
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'refresh',
                'sanitize_callback' => ['FV_Customizer' ,'sanitize_checkbox'],
            )
        );

        $wp_customize->add_control( 'fv[show-toolbar]',
            array(
                'label' => __( 'Show toolbar (under contest)?' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'checkbox',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        // ========================================================================
        // ========================================================================
        // ========================================================================
        $wp_customize->add_setting( 'fv[toolbar-hide-details]',
            array(
                'default' => true,
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'refresh',
                'sanitize_callback' => ['FV_Customizer' ,'sanitize_checkbox'],
            )
        );

        $wp_customize->add_control( 'fv[toolbar-hide-details]',
            array(
                'label' => __( 'Hide Description tab on toolbar?' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'checkbox',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        $wp_customize->add_setting( 'fv[toolbar-order]',
            array(
                'default' => false,
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'refresh',
                'sanitize_callback' => ['FV_Customizer' ,'sanitize_checkbox'],
            )
        );

        $wp_customize->add_control( 'fv[toolbar-order]',
            array(
                'label' => __( 'Hide Order Dropdown on toolbar?' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'checkbox',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        $wp_customize->add_setting( 'fv[toolbar-search]',
            array(
                'default' => false,
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'refresh',
                'sanitize_callback' => ['FV_Customizer' ,'sanitize_checkbox'],
            )
        );

        $wp_customize->add_control( 'fv[toolbar-search]',
            array(
                'label' => __( 'Hide Search block on toolbar?' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'checkbox',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        // ========================================================================
        // ========================================================================
        // ========================================================================

        $wp_customize->add_setting( 'fv[toolbar-bg-color]',
            array(
                'default' => '#232323',
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( 'fv[toolbar-bg-color]',
            array(
                'label' => __( 'Toolbar background color' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'color',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        $wp_customize->add_setting( 'fv[toolbar-text-color]',
            array(
                'default' => '#FFFFFF',
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( 'fv[toolbar-text-color]',
            array(
                'label' => __( 'Toolbar text / links color' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'color',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );
        $wp_customize->add_setting( 'fv[toolbar-link-abg-color]',
            array(
                'default' => '#454545',
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( 'fv[toolbar-link-abg-color]',
            array(
                'label' => __( 'Toolbar active links background' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'color',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

        $wp_customize->add_setting( 'fv[toolbar-select-color]',
            array(
                'default' => '#1f7f5c',
                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( 'fv[toolbar-select-color]',
            array(
                'label' => __( 'Toolbar select (dropdown) color' ),
                //'description' => esc_html__( 'Sample description' ),
                'section' => 'fv_toolbar',
                'priority' => 10, // Optional. Order priority to load the control. Default: 10
                'type' => 'color',
                'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
            )
        );

    }

    /**
     * Sanitizes the incoming input and returns it prior to serialization.
     *
     * @param      string    $input    The string to sanitize
     * @return     string              The sanitized string
     */
    static function sanitize_input( $input ) {
        return strip_tags( stripslashes( $input ) );
    }

    static function sanitize_checkbox( $input ) {
        if ( true === $input ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Registers the Theme Customizer Preview with WordPress.
     */
    static function customizer_live_preview() {
        wp_enqueue_script(
            'fv-customizer-preview',
            FV::$ADMIN_URL . '/js/fv-customizer-preview.js',
            array( 'customize-preview' ),
            FV::VERSION,
            true
        );
    }

    static function customize_controls_enqueue_scripts () {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker');
    }
}



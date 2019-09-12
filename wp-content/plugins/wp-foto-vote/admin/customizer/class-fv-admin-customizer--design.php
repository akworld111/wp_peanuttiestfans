<?php

// If this file is called directly, abort.
if (!class_exists('WP')) {
    die();
}

class FV_Customizer__Design extends FV_Singleton_Customizable_Abstract {

    protected static $instance = null;

    public function __construct() {
        // Customizer Config
        $this->slug = 'fv_design';
        $this->outputHandle = false;
        $this->customizerSlug = 'fv_design_';
        $this->outputCssPrefix = '';
        $this->outputCssPrefix = 'fv_design_';

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Settings] Design';

        $this->_enqueueOutputCustomizedCSS();

        add_action( 'customize_controls_print_styles', [$this, 'customizer_styles'], 999 );

        $this->initCustomizer();
    }

    /**
     * This function adds some styles to the WordPress Customizer
     */
    function customizer_styles() { ?>
        <style>#_customize-input-fv_design_fv_vote_icon, #_customize-input-fv_design_fv_voted_icon { font-family: 'icomoon_fv'; }</style>
        <?php

        wp_enqueue_style('fv_fonts_css', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
    }


    public function registerCustomizerSettings() {

//        $wp_customize->add_setting( 'fv[theme]',
//            array(
//                'default' => fv_setting('theme'),
//                'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
//                //'transport' => 'postMessage',
//                'sanitize_callback' => 'sanitize_text_field'
//            )
//        );


        $this->customizerSectionPriority = 160;

        $gallery_skins = array();
        foreach (FV_Skins::i()->getList() as $key => $theme_title):
            $gallery_skins[$key] =  $theme_title;
        endforeach;

//
//
//        $wp_customize->add_control( 'fv[theme]', array(
//            'section' => 'fv_general', // Add a default or your own section
//            'label' => __( 'Gallery Skin' ),
//            //'description' => __( 'Not applies on Mobile' ),
//            'type'    => 'select',
//            'choices' => $gallery_skins
//        ) );

        $this->_registerCustomizerSetting( "fv[theme]", array(
            'default' => fv_setting('theme'),
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',

            'label' => __( 'Gallery Skin', 'fv' ),
            'type' => 'select',
            'choices' => $gallery_skins,
            'use_original_key' => true,
        ));

        $icons = fv_get_icons();

        $icons_arr = array_map(function($val) {
            return '&#x' . $val. ';';
        }, $icons);

        $this->_registerCustomizerSetting( "fv_vote_icon", array(
            'default' => 'heart2',
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',

            'label' => 'Vote icon for Gallery, Lightbox and Single view',
            'type' => 'select-html',
            'choices' => $icons_arr,

            'type_class' => 'FV_Customize_Control_Select',
        ), array(
            '.fv-vote-icon' =>
                array(
                    'attribute' => 'content', 'type' => 'css','units'=>'',
                    'callback' => function( $attribute_value, $attribute_value_src, $css_selector, $css_data, $setting ) {
                        $icons = fv_get_icons();
                        // Use custom Callback for generate Tall and default rows height
                        $customized_css = sprintf(
                            '.fv-vote-icon:before { content: "\%s"; }',
                            $icons[$attribute_value]
                        );

                        return $customized_css;
                    }
                ),
        ) );

        $this->_registerCustomizerSetting( "fv_voted_icon", array(
            'default' => 'heart',
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',

            'label' => 'Voted icon for Gallery and Single view (in Pinterest a few other skins)',
            'type' => 'select-html',
            'choices' => $icons_arr,

            'type_class' => 'FV_Customize_Control_Select',
        ), array(
            '.fv-vote-icon' =>
                array(
                    'attribute' => 'content', 'type' => 'css','units'=>'',
                    'callback' => function( $attribute_value, $attribute_value_src, $css_selector, $css_data, $setting ) {
                        $icons = fv_get_icons();
                        // Use custom Callback for generate Tall and default rows height
                        $customized_css = sprintf(
                            '.is-voted .fv-vote-icon:before { content: "\%s"; }',
                            $icons[$attribute_value]
                        );

                        return $customized_css;
                    }
                ),
        ) );
//
//        $wp_customize->add_control( 'fotov-block-width', array(
//            'type' => 'number',
//            'section' => 'fv_general', // Add a default or your own section
//            'label' => __( 'Gallery Block Width' ),
//            'description' => __( 'Not applies on Mobile and for Fashion and Flickr skins' ),
//        ) );
//

        $this->_registerCustomizerSetting( "fotov-block-width", array(
            'default' => 300,
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'absint',

            'label' => 'Gallery Block Width',
            'type' => 'number',
            'description' => __( 'Not applies on Mobile and for Fashion and Flickr skins' ),
            'use_original_key' => true,
        ), array() );

//        $this->_registerCustomizerSetting( "actions_bg", array(
//            'default' => '#D8000C',
//            'setting_type' => 'option',
//            'setting_transport' => 'postMessage',
//            'sanitize_callback' => 'sanitize_hex_color',
//
//            'label' => 'Action buttons background color',
//            'type' => 'color',
//        ), array(
//            '.hermes-actions__one' => array('attribute' => 'background-color','type' => 'css',),
//        ) );
//
//        $this->_registerCustomizerSetting( "votes_bottom_border", array(
//            'default' => '#ffd800',
//            'setting_type' => 'option',
//            'setting_transport' => 'postMessage',
//            'sanitize_callback' => 'sanitize_hex_color',
//
//            'label' => 'Votes bottom border color',
//            'type' => 'color',
//        ), array(
//            '.hermes-header__stats' => array('attribute' => 'border-bottom-color','type' => 'css',),
//        ) );

    }

}

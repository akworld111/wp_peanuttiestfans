<?php
/**
 * @created 16.09.2018
 */

class FV_Skin_Hermes extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'hermes';
        $this->title = 'Hermes [new] (images only)';

        $this->haveSingleView = false;

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Gallery skin] Hermes';


        parent::__construct();
    }

    public function registerCustomizerSettings() {

        $this->_registerCustomizerSetting( "height", array(
            'default' => 320,
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'absint',

            'label' => 'Tall blocks height (small will be 10% less)',
            'type' => 'number',
        ), array(
            '.hermes-entry' =>
                array(
                    'attribute' => 'height','type' => 'css','units'=>'px',
                    'callback' => function( $attribute_value, $attribute_value_src, $css_selector, $css_data, $setting ) {
                        // Use custom Callback for generate Tall and default rows height
                        $customized_css = sprintf(
                            '@media (min-width: 992px){ %s{%s: %s;} }',
                            '.fv-contest-theme-hermes .contest-block:first-child > .hermes-entry,
                            .fv-contest-theme-hermes .contest-block:nth-child(2) > .hermes-entry',
                            $css_data['attribute'],
                            $attribute_value
                        );
                        $customized_css .= sprintf('@media (min-width: 992px){ %s{%s: %spx;} }', $css_selector, $css_data['attribute'], $attribute_value_src*0.9);

                        return $customized_css;
                    }
                ),
        ) );

        $this->_registerCustomizerSetting( "mobile_height", array(
            'default' => 320,
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'absint',

            'label' => 'Blocks height on mobile',
            'type' => 'number',
        ), array(
            '.hermes-entry' => array('attribute' => 'height','type' => 'css','units'=>'px', 'media'=>'max-width:780px'),
        ) );

        $this->_registerCustomizerSetting( "actions_bg", array(
            'default' => '#D8000C',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Action buttons background color',
            'type' => 'color',
        ), array(
            '.hermes-actions__one' => array('attribute' => 'background-color','type' => 'css',),
        ) );

        $this->_registerCustomizerSetting( "votes_bottom_border", array(
            'default' => '#ffd800',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Votes bottom border color',
            'type' => 'color',
        ), array(
            '.hermes-header__stats' => array('attribute' => 'border-bottom-color','type' => 'css',),
        ) );

    }


    public function assets(){
        // Load here any Additional Assets
    }
}

new FV_Skin_Hermes();
<?php

class FV_Modern_Azure extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'modern_azure';
        $this->title = 'Modern Azure (images + video)';

        $this->haveSingleView = false;

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Gallery skin] Modern Azure';

        parent::__construct();
    }
    
    function registerCustomizerSettings()
    {
        $this->_registerCustomizerSetting( "btn_color", array(
            'default' => '#39a5bb',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Button & Votes counter color',
            'type' => 'color',
        ), array(
            '.fv_button > button'        => array('attribute' => 'background-color','type' => 'style',),
            '.contest-block-votes-count' => array('attribute' => 'color','type' => 'style',),
        ) );

        $this->_registerCustomizerSetting( "active_btn_color", array(
            'default' => '#3CAFC5',
            'setting_type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Button hover color',
            'type' => 'color',
        ), array(
            '.fv_button .fv_vote:hover'  => array('attribute' => 'background-color','type' => 'css',),
            '.fv_button .fv_vote:active' => array('attribute' => 'background-color', 'type' => 'css', ),
        ) );

        $this->_registerCustomizerSetting( "border_color", array(
            'default' => '#3299AE',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Border color',
            'type' => 'color',
        ), array(
            '.contest-block' => array( 'attribute' => 'border-color', 'type' => 'css', ),
            'a.fv-share-btn' => array( 'attribute' => 'border-color', 'type' => 'css', ),
        ) );

        $this->_registerCustomizerSetting( "socials", array(
            'default' => array( 'fb', 'gp', 'tw' ),
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => array( 'FV_Customize_Control_Checkbox_Multiple', 'sanitize' ),

            'label' => 'Social buttons to display:',
            'type' => 'checkbox-multiple',
            'type_class' => 'FV_Customize_Control_Checkbox_Multiple',
            'choices' => array(
                'fb' => 'Facebook',
                'gp' => 'Google plus',
                'tw' => 'Twitter',
                'pi' => 'Pinterest',
                'vk' => 'Vkontakte',
            ),
        ) );
        
        
        parent::registerCustomizerSettings();
    }
}

new FV_Modern_Azure();
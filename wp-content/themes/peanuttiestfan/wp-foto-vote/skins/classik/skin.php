<?php

class FV_Classic extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'classik';
        $this->title = 'Classic (images + video)';

        $this->haveSingleView = false;

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Gallery skin] Classic';

        parent::__construct();
    }

    function registerCustomizerSettings()
    {
        $this->_registerCustomizerSetting( "main_color", array(
            'default' => '#990000',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Button & Votes counter color',
            'type' => 'color',
        ), array(
            '.fv_button > button'        => array('attribute' => 'border-color','type' => 'css',),
            '.fv_button>button'        => array('attribute' => 'color','type' => 'style',),
            '.contest-block-votes-count' => array('attribute' => 'color','type' => 'style',),
        ) );

        $this->_registerCustomizerSetting( "border_color", array(
            'default' => '#990000',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Border color',
            'type' => 'color',
        ), array(
            '.contest-block' => array( 'attribute' => 'border-color', 'type' => 'css', ),
        ) );

        parent::registerCustomizerSettings();
    }

}

new FV_Classic();
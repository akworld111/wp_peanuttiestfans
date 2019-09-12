<?php

class FV_Fashion extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'fashion';
        $this->title = 'Fashion (images only)';

        $this->haveSingleView = false;

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Gallery skin] Fashion';

        parent::__construct();
    }    

    public function assetsList()
    {
        wp_enqueue_script('masonry');
        wp_enqueue_script('fv_theme_fashion', FV_Templater::locateUrl( $this->slug, 'fv_theme_fashion.js' ), array( 'jquery', 'fv_lib_js', 'masonry' ), FV::VERSION);
    }

    /**
     * beforeList
     */
    public function beforeList()
    {
        add_action( 'fv_before_shows_loop',  array($this, 'before_list_one') );
        add_action( 'fv_after_shows_loop',  array($this, 'after_list_one') );
    }

    function before_list_one() {
        echo '<ul class="fv-grid effect-1" id="fv-grid">';
    }

    function after_list_one() {
        echo '</ul>';
    }

    function registerCustomizerSettings()
    {
        $this->_registerCustomizerSetting( "columns", array(
            'default' => 3,
            'setting_type' => 'option',
            'setting_transport' => 'refresh',
            'sanitize_callback' => 'absint',

            'label' => 'Columns count',
            'type' => 'select',
            'choices' => array(
                2   => '1/2',
                3   => '1/3',
                4   => '1/4',
            ),
        ));

        $this->_registerCustomizerSetting( "overlay_bg", array(
            'default' => '#e4c057',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Hover Overlay color',
            'type' => 'color',
        ), array(
            '#fv-grid .caption-post' => array( 'attribute' => 'background-color', 'type' => 'css', ),
        ) );

        parent::registerCustomizerSettings();
    }


}

new FV_Fashion();
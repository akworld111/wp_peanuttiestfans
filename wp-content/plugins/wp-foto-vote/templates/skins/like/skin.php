<?php

class FV_Like extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'like';
        $this->title = 'Like (images + video)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){
        parent::init();
    }

    public function assetsList()
    {
        parent::assetsList();

        wp_enqueue_script('fv_theme_like', FV_Templater::locateUrl($this->slug, 'fv_theme_like.js'), array( 'fv_lib_js' ), FV::VERSION);
    }
}

new FV_Like();
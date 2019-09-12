<?php

class FV_Skin_Modern extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'modern';
        $this->title = 'Modern [new] (images only)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){

    }
}

new FV_Skin_Modern();
<?php

class FV_Default extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'default';
        $this->title = 'Default (images + video)';

        $this->haveSingleView = true;

        parent::__construct();
    }    

    public function init(){
        parent::init();
    }
}

new FV_Default();
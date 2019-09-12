<?php

class FV_Red extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'red';
        $this->title = 'Red (images + local video)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){

    }
}

new FV_Red();
<?php

class FV_Gray extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'gray';
        $this->title = 'Gray (images + video)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){

    }
}

new FV_Gray();
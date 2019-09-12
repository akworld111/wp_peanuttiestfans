<?php

class FV_Beauty extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'beauty';
        $this->title = 'Beauty (images + video)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){

    }
}

new FV_Beauty();
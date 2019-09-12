<?php

class FV_New_Year extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'new_year';
        $this->title = 'New Year (images + video)';

        $this->haveSingleView = true;

        parent::__construct();
    }    

    public function init(){
        parent::init();
    }
}

new FV_New_Year();
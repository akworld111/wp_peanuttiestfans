<?php

class FV_Beauty_Simple extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'beauty_simple';
        $this->title = 'Beauty simple (images + video)';

        $this->haveSingleView = false;

        parent::__construct();
    }    

    public function init(){

    }
}

new FV_Beauty_Simple();
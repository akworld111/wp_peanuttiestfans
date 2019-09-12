<?php

class FV_Winner_Red extends FV_Winners_Base {

    public function __construct() {
        $this->slug = 'red';
        $this->title = 'Red (images + video)';

        parent::__construct();
    }    

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Winner_Red();
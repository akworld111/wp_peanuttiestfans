<?php

class FV_Winners_Simple extends FV_Winners_Base {

    public function __construct() {
        $this->slug = 'simple';
        $this->title = 'Simple (images only)';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Winners_Simple();
<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Block extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'block';
        $this->title = 'Block';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Leaders_Block();
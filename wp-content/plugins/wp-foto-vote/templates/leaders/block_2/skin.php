<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Block2 extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'block_2';
        $this->title = 'Block_2 (recommended count to display 3)';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Leaders_Block2();
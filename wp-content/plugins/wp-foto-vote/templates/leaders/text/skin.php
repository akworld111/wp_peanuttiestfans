<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Text extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'text';
        $this->title = 'Text';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Leaders_Text();
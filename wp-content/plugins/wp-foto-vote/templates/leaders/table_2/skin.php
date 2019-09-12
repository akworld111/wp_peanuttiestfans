<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Table2 extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'table_2';
        $this->title = 'Table_2';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Leaders_Table2();
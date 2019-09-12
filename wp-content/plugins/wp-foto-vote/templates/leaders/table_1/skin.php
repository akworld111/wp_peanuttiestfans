<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Table extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'table_1';
        $this->title = 'Table_1';

        parent::__construct();
    }

    public function assets( $args = array() ){
        // Load here any Additional Assets
    }
}

new FV_Leaders_Table();
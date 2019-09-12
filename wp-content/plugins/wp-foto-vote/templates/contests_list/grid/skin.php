<?php
/**7
 * @created 26.03.2017
 */

class FV_Contests_List_Grid extends FV_Contests_List_Base {

    public function __construct() {
        $this->slug = 'grid';
        $this->title = 'Grid';

        parent::__construct();
    }

    public function assets(){
        // Load here any Additional Assets
    }
}

new FV_Contests_List_Grid();
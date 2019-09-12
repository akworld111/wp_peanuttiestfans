<?php

class FV_Competitor_Meta extends FV_Abstract_Meta{

    protected $contest_id;
    
    /**
     * FV_Contestant_Meta constructor.
     * @param int          $object_ID
     * @param array|null   $meta
     * @param bool         $get_meta
     */
    public function __construct($object_ID, $meta, $get_meta = false, $contest_id = 0) {
        $this->contest_id = $contest_id;
        parent::__construct($object_ID, $meta, $get_meta);
    }
    
    /**
     * Change default params
     *
     * @since 2.2.800
     *
     * @param array     $create_arr
     * @return array
     */
    protected function _filter_fields_on_create( $create_arr ) {
        $create_arr['contest_id'] = $this->contest_id;

        return $create_arr;
    }  
    
}
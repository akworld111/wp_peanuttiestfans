<?php

class FV_Contest_Meta extends FV_Abstract_Meta{

    protected $select_key = 'contest_id';

    protected function _select_meta() {
        $this->meta_arr = ModelMeta::q()
            ->where($this->select_key, $this->object_ID)
            ->where('contestant_id', 0)
            ->find(false, false);

        $this->queried = true;
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
        $create_arr['contestant_id'] = 0;

        return $create_arr;
    }

}
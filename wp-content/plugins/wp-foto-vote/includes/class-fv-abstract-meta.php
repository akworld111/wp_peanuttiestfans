<?php

abstract class FV_Abstract_Meta {

    protected $object_ID;
    protected $select_key = 'contestant_id';

    public $meta_arr = array();
    protected $queried = false;

    /**
     * FV_Contestant_Meta constructor.
     * @param int          $object_ID
     * @param array|null   $meta
     * @param bool         $get_meta
     */
    public function __construct($object_ID, $meta, $get_meta = false) {
        $this->object_ID = $object_ID;

        if ( !empty($meta) ) {
            ## If meta found
            $this->meta_arr = $meta;
            $this->queried = true;
        } elseif ( $meta === null && $get_meta ){
            $this->queried = true;
        } elseif ( empty($meta) && $get_meta ){
            ## Find all contestant Meta
            $this->_select_meta();
        }

    }

    protected function _select_meta() {
        $this->meta_arr = ModelMeta::q()->where($this->select_key, $this->object_ID)->find(false, false);
        $this->queried = true;
    }

    /**
     * Let's clean all meta and later we will reload it
     */
    public function _flush_meta() {
        $this->queried = false;
        $this->meta_arr = array();
    }

    /**
     * @return array
     */
    public function get_all() {
        if ( !$this->queried ) {
            $this->_select_meta();
        }
        return $this->meta_arr;
    }

    /**
     * Get Meta single Row by $meta_key
     *
     * <code>
     * $meta_row = $competitor->meta()->get_row('url');
     * echo $meta_row->value;
     * echo $meta_row->ID;
     * echo $meta_row->meta_key;    //  'url'
     * </code>
     *
     * @param  string    $meta_key
     *
     * @return object|false
     */
    public function get_row($meta_key) {
        if ( !$this->queried ) {
            $this->_select_meta();
        }

        foreach ($this->meta_arr as $meta_row) {
            if ( $meta_row->meta_key == $meta_key ) {
                $meta_row->value = stripslashes($meta_row->value);
                return $meta_row;
            }
        }
        return null;
    }

    /**
     * Get Meta single Row Value by $meta_key
     *
     * <code>
     * echo $competitor->meta()->get_value('url');
     * </code>
     *
     * @param string    $meta_key
     *
     * @return string|false
     */
    public function get_value( $meta_key ) {
        $meta_row = $this->get_row( $meta_key );
        if ( $meta_row ) {
            return $meta_row->value;
        }
        return null;
    }

    /**
     * Return all user meta KEYED by $key
     *
     * <code>
     * $meta_rows = $competitor->meta()->get_custom_all();
     * foreach( $meta_rows as $meta_row ) {
     *  echo $meta_row->value;
     *  echo $meta_row->ID;
     *  echo $meta_row->meta_key;
     * }
     * </code>
     *
     * @param string $key   ID|meta_key
     * @return array
     */
    public function get_custom_all($key = 'meta_key') {
        if ( !$this->queried ) {
            $this->_select_meta();
        }

        $custom = array();
        foreach ($this->meta_arr as $meta_row) {
            if ( $meta_row->custom == TRUE ) {
                $meta_row->value = stripslashes($meta_row->value);
                $custom[$meta_row->$key] = $meta_row;
            }
        }
        return $custom;
    }

    /**
     *
     * <code>
     * $meta_rows = $competitor->meta()->get_all_keyed('ID');
     * foreach( $meta_rows as $meta_row_ID as $meta_row_data ) {
     *  echo $meta_row->value;
     *  echo $meta_row->ID;
     *  echo $meta_row_ID
     *  echo $meta_row->meta_key;
     * }
     * </code>
     *
     * @param string $key   ID|meta_key
     * @return array
     */
    public function get_all_keyed($key = 'meta_key') {
        if ( !$this->queried ) {
            $this->_select_meta();
        }

        $all = array();
        foreach ($this->meta_arr as $meta_row) {
            $meta_row->value = stripslashes($meta_row->value);
            $all[$meta_row->$key] = $meta_row;
        }
        return $all;
    }

    /**
     * Get ALL meta as a flat array
     *  [$key => $meta_value]
     *
     * <code>
     * $meta_rows = $competitor->meta()->get_all_flat('meta_key');
     * foreach( $meta_rows as $meta_row_KEY as $meta_row_value ) {
     *  echo $meta_row_KEY;
     *  echo $meta_row_value;
     * }
     * </code>
     *
     * @param string $key
     * @return array
     */
    public function get_all_flat($key = 'meta_key') {
        $all = $this->get_all_keyed();
        $all_flat = array();

        foreach ($all as $meta_row) {
            $all_flat[$meta_row->$key] = $meta_row->value;
        }
        return $all_flat;
    }

    /**
     * Get ALL user meta as a flat array
     *  [$key => $meta_value]
     *
     * @param string $key
     * @return array
     */
    public function get_custom_all_flat($key = 'meta_key') {
        $all = $this->get_custom_all();
        $all_flat = array();

        foreach ($all as $meta_row) {
            $all_flat[$meta_row->$key] = $meta_row->value;
        }
        return $all_flat;
    }

    /**
     * Get all user meta as a string (to display in admin)
     *
     * @return string
     */
    public function get_custom_all_as_string() {
        $as_string = '';

        $custom_flat = $this->get_custom_all_flat();
        if ( empty($custom_flat) ) {
            return '';
        }
        foreach ($custom_flat as $meta_key => $meta_val) {
            $as_string .= $meta_key . ' = ' . stripslashes($meta_val) . ';';
        }
        return $as_string;
    }

    /**
     * Create new Meta row
     * 
     * @since 2.2.800
     * 
     * @param string    $meta_key
     * @param string    $meta_val
     * @param int       $public_meta 0/1
     */
    public function create($meta_key, $meta_val, $public_meta = 1 ) {
        ModelMeta::q()->insert(
            $this->_filter_fields_on_create( array(
                $this->select_key => $this->object_ID,
                'meta_key'      => $meta_key,
                'value'         => $meta_val,
                'custom'        => $public_meta,
            ) )
        );
        $this->_flush_meta();
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

    }

}
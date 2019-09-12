<?php

/**
 * Abstract Object Class
 * Used for manage Contest/Competitors in a CRUD way
 * Like in WooCommerce 2.7
 * (https://woocommerce.wordpress.com/2016/10/27/the-new-crud-classes-in-woocommerce-2-7/)
 *
 * @since       2.2.500
 * @package     FV/Abstracts
 * @category    Abstract Class
 * @author      Maxim K
 */
abstract class FV_Abstract_Object implements JsonSerializable {
    /**
     * @var FV_Abstract_Meta
     */
    protected $meta = null;

    /**
     * @var object
     */
    protected $object = null;

    /**
     * @var array
     */
    protected $properties = null;

    /**
     * @var array
     */
    protected $updated = array();

    /**
     * @var FvQuery
     */
    protected $model = '';

    /**
     * @param int|object    $object         Contest or init.
     * @param bool          $from_cache
     */
    public function __construct( $object = 0, $from_cache = false ) {
        $this->properties = $this->model->fields();

        if ( is_numeric( $object ) && $object > 0 ) {
            $this->object = $this->model->findByPk( $object, $from_cache, OBJECT, false, true );
        } elseif ( ! empty( $object->id ) ) {
            $this->object = $object;
        } else {
            $this->object = new StdClass();
        }
    }

    /**
     * @return FV_Abstract_Meta
     * @throws Exception
     */
    public function meta() {
        if ( !$this->meta ) {
            // Try to recreate
            if ( $this->object->id ) {
                // Lazy Meta init
                $this->meta = $this->_meta_instance();
            } else {
                fv_log("FV_Abstract_Object :: Meta Class is not available for this class", $this->object);
                throw new Exception("FV_Abstract_Object :: Meta Class is not available for this class!");
            }
        }
        return $this->meta;
    }

    /**
     * jsonSerialize implementation
     * @return object
     */
    public function jsonSerialize() {
        return $this->object;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset ( $key ) {
        // Avoid possible issues
        if ( $key == 'ID' && isset($this->object->id) ) {
            return true;
        }

        if ( $key == 'meta' && $this->meta ) {
            return true;
        }

        return isset($this->object->$key);
    }

    /**
     * Is $this->object is fetched from database?
     * @return bool
     */
    public function objectExists (  ) {
        if ( !$this->object ) {
            return false;
        }
        return true;
    }

    /**
     * Magic __get method for get contest params
     *
     * @param  string $key Key name.
     * @return mixed
     */
    public function __get( $key ) {
        if ( !$this->object ) {
            return false;
        }
        
        if ( $key == 'meta' ) {
            return $this->meta();
        }

        if ( $key == 'ID' && $this->object ) {
            return $this->object->id;
        }

        if ( !isset($this->object->$key) ) {
            if ( !isset($this->properties[$key]) ) {
                trigger_error(__CLASS__ . " - object property \"{$key}\" does not exists!");
            }
            return null;
        }

        return $this->object->$key;
    }

    /**
     * Magic __set method for set contest params
     *
     * @param  string $key      Key name.
     * @param  string $value    Value.
     */
    public function __set( $key, $value ) {
        if ( !isset($this->properties[$key]) ) {
            trigger_error( __CLASS__ . " - property \"{$key}\" does not exists!");
        }

        if ( $this->properties[$key] == '%d' ) {
            $this->object->$key = absint( $value );
        } else {
            $this->object->$key = $value;
        }

        // If this Property not exists in DB
        if ( $this->properties[$key] == 'virtual' ) {
            return;
        }

        $this->updated[$key] = true;
    }

    /**
     * Save model to database
     * @return bool
     */
    public function save() {
        // Ensure that some fields are update && we have ID
        if ( $this->updated && !empty($this->object->id) ) {
            $updates = array();
            foreach ( $this->updated as $key => $true ) {
                $updates[$key] = $this->object->$key;
            }
            $res = $this->model->updateByPK( $updates, $this->object->id );
            // Reset changed fields
            $this->updated = array();
            return $res;
        } elseif ( $this->updated ) {
            $updates = array();
            foreach ( $this->updated as $key => $true ) {
                $updates[$key] = $this->object->$key;
            }
            $this->object->id = $this->model->insert( $updates );
            // Reset changed fields
            $this->updated = array();
            return $this->object->id;
        }
        return false;
    }
}
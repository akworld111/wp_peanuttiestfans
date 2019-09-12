<?php

/**
 * Class Singleton
 */
abstract class FV_Singleton_Customizable_Abstract extends FV_Element_Customizer_Abstract
{
    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
        $this->initCustomizer();
    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * @return self
     */
    public static function instance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @return self
     */
    public static function get_instance()
    {

        return static::instance();
    }

    /**
     * @return self
     */
    public static function i()
    {
        return static::instance();
    }
}
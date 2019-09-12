<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Skins manager
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.500
 */
abstract class FV_Skins_Abstract
{
    // Local Cacher
    protected $type;
    
    /** @var FV_Skin_Base[] $skins */
    protected $skins = array();
    protected $loadedDefaults = false;

    function register($skin, $class)
    {
        $this->skins[$skin] = $class;
    }

    /**
     * Check is following Skin Slug is registered
     * @param $skin
     * @return bool
     */
    function isRegistered($skin)
    {
        return $skin && isset($this->skins[$skin]);
    }

    /**
     * Call skin function
     * @param $skin
     * @param $action
     * @param mixed $param1
     *
     * @return mixed
     */
    function call( $skin, $action, $param1 = false )
    {
        if ( isset($this->skins[$skin]) ) {
            return $this->skins[$skin]->$action( $param1 );
        }
        return $param1;
    }

    /**
     * @return bool|FV_Skin_Base[]
     */
    function getSkins()
    {
        if (isset($this->skins)) {
            return $this->skins;
        }
        return false;
    }

    /**
     * @param string $skin
     * @return bool|FV_Skin_Base
     *
     * @throws Exception
     */
    function get($skin)
    {
        if (isset($this->skins[$skin])) {
            return $this->skins[$skin];
        }
        throw new \Exception( "Skin '{$skin}' isn't registered!" );
    }

    function getList()
    {
        if ( !$this->skins && !$this->loadedDefaults ) {
            $this->loadDefaults();
        }
        $list = array();

        foreach ($this->skins as $skin_name => $skin_class) {
            $list[$skin_name] = $skin_class->getTitle();
        }
        return $list;
    }

    function locate($skin, $file)
    {
        if (isset($this->skins[$skin])) {
            // False or Path
            return $this->skins[$skin]->getPath($file);
        }
        return false;
    }

    function locateUrl($skin, $file)
    {
        if (isset($this->skins[$skin])) {
            // False or Path
            return $this->skins[$skin]->getUrl($file);
        }
        return false;
    }

    /**
     * Include default skins
     */
    function loadDefaults()
    {

    }
}
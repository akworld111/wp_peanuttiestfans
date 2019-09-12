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
class FV_Skins extends FV_Skins_Abstract
{
    /**
     * Class instance.
     * @var object
     */
    protected static $instance;

    /**
     * @return bool|FV_Skin_Base
     */
    function getCurrentList()
    {
        return parent::get( fv_setting('theme', 'pinterest') );
    }

    /**
     * @return bool|FV_Skin_Base
     */
    function getCurrentSingle()
    {
        return parent::get( fv_setting('single-theme', 'pinterest') );
    }

    /**
     * @return bool|FV_Skin_Base[]
     */
    function getList()
    {
        return apply_filters( 'fv_themes_list_array', parent::getList() );
    }

    function getSingleViewList()
    {
        $list = array();

        foreach (parent::getSkins() as $skin_name => $skin_class) {
            if (!$skin_class->haveSingleView()) continue;

            $list[$skin_name] = $skin_class->getSingleTitle();
        }
        return apply_filters( 'fv/skins/single_view_list',$list);
    }
    /**
     * Include default skins
     */
    function loadDefaults()
    {
        if ( $this->loadedDefaults ) {
            return;
        }

        $defaults = array("pinterest","flickr","default","modern","hermes","modern_azure","like","red","classik","gray","fashion","beauty","beauty_simple","new_year");

        $path = '';
        foreach ($defaults as $default) {
            $path = FV_Templater::locate($default, 'skin.php');
            if ($path) {
                require $path;
            }
        }

        $this->loadedDefaults = true;
    }

    /**
     * @return self
     */
    public static function i()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new self();

        return self::$instance;
    }
}
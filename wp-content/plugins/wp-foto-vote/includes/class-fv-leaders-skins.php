<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Winners Skins manager
 *
 * @package    FV
 * @subpackage includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.500
 */
class FV_Leaders_Skins extends FV_Skins_Abstract
{
    /**
     * Class instance.
     * @var object
     */
    protected static $instance;

    /**
     * @return bool|FV_Skin_Base
     */
    function getCurrent()
    {
        return parent::get( get_option('fotov-leaders-type', 'block') );
    }

    /**
     * Include default skins
     */
    function loadDefaults()
    {
        $defaults = array('block','block_2','table_1','table_2','text','poll');

        $path = '';
        foreach ($defaults as $default) {
            $path = FV_Templater::locate($default, 'skin.php', 'leaders');
            if ( $path ) {
                require $path;
            }
        }
    }

    /**
     * @return FV_Winners_Skins
     */
    public static function i()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new self();

        return self::$instance;
    }
}
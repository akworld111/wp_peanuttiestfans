<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Winners Skins manager
 *
 * @package    FV
 * @subpackage includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.503
 */
class FV_Contests_List_Skins extends FV_Skins_Abstract
{
    /**
     * Class instance.
     * @var object
     */
    protected static $instance;

    /**
     * Include default skins
     */
    function loadDefaults()
    {
        $defaults = array('default', 'grid');

        $path = '';
        foreach ($defaults as $default) {
            $path = FV_Templater::locate($default, 'skin.php', 'contests_list');
            if ( $path ) {
                require $path;
            }
        }
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
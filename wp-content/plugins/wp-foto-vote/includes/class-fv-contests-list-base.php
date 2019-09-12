<?php

/**
 * Uses for extends Winners skins functionality
 *
 * @package    FV
 * @subpackage includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.503
 */
abstract class FV_Contests_List_Base extends FV_Skin_Base_Abstract
{
    /**
     * Init
     */
    public function __construct()
    {
        FV_Contests_List_Skins::i()->register($this->slug, $this);
    }
}
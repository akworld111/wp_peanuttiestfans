<?php

/**
 * Uses for extends Winners skins functionality
 *
 * @package    FV
 * @subpackage includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.503
 */
abstract class FV_Winners_Base extends FV_Skin_Base_Abstract
{
    /**
     * Init
     */
    public function __construct()
    {
        FV_Winners_Skins::i()->register($this->slug, $this);
    }

    public function assets ( $args = array() ) {
        // Load some Assets
    }
}
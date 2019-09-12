<?php

/**
 * Uses for extends Contest themes functionality
 *
 * Add ability themes more beauty add custom assets
 * and set some params, like support custom leaders block, etc
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.500
 */
abstract class FV_Skin_Base_Abstract extends FV_Element_Customizer_Abstract
{
    protected $slug;
    protected $title;
    protected $singleTitle;

    protected $path = '';
    protected $url = '';

    protected $apiVersion;

    /**
     * Init
     */
    public function __construct()
    {
        $this->initCustomizer();
    }

    /**
     * Init theme (add actions, hooks, etc)
     */
    public function init() {
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSingleTitle()
    {
        return $this->singleTitle ? $this->singleTitle : $this->title;
    }

    public function getPath($file)
    {

        return $this->path ? trailingslashit($this->path) . $file : '';
    }

    public function getUrl($file)
    {
        return $this->url ? trailingslashit($this->url) . $file : '';
    }

    /**
     * Global Assets
     */
    public function assets(){ }

    /**
     * Filter Shortcode Args before passing to Template
     *
     * @param array $args
     * @return array
     */
    public function filterArgs( $args = array() ){
        return $args;
    }
}
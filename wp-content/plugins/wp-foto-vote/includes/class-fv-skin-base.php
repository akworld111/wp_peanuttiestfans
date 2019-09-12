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
abstract class FV_Skin_Base extends FV_Skin_Base_Abstract
{

    protected $haveSingleView = true;
    
    /**
     * Init
     */
    public function __construct()
    {
        FV_Skins::i()->register($this->slug, $this);

        // Customizer Config
        $this->outputHandle = 'fv_main_css_tpl';
        $this->customizerSlug = 'fv_skin__' . $this->slug . '__';
        $this->outputCssPrefix = '.fv-contest-theme-' . $this->slug;

        parent::__construct();
    }

    /**
     * enqueue customized CSS
     * @since 2.3.00
     * @access private
     */
    public function _enqueueOutputCustomizedCSS () {
        if ( ! $this->supportsCustomizer ) {
            return;
        }
        add_action( 'fv/public/skins/output_custom_css', array($this, '_outputCustomizedCSS') );
    }


    public function haveSingleView()
    {
        return $this->haveSingleView;
    }

    /**
     * Load single photo page Assets
     */
    public function assetsSingle(){ }

    /**
     * beforeSingle
     */
    public function beforeSingle(){ }

    /**
     * afterSingle
     */
    public function afterSingle(){ }

    /**
     * Load contest gallery Assets
     */
    public function assetsList(){ }

    /**
     * beforeList
     */
    public function beforeList() { }

    /**
     * afterList
     */
    public function afterList() { }


    
}
<?php

/**
 * Class FV_Lightbox_Abstract
 * @since 2.3.00
 */
abstract class FV_Lightbox_Abstract extends FV_Singleton_Customizable_Abstract {

    protected function __construct()
    {
        add_action( 'fv_load_lightbox_' . $this->slug, array($this, 'assets') );
        add_filter( 'fv_lightbox_list_array', array($this,  'initListThemes') );

        parent::__construct();
    }

}
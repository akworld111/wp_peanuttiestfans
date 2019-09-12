<?php
/**
 * ImageLightbox library wrapper
 *
 * ================================
 * Usage this structure allows simply add new lightbox to contest list
 *
 * Need only add filter into FV::PREFIX . 'lightbox_list_array'
 * (append you lightbox name and theme, like 'imageLightbox_default')
 *
 * And add action for
 * FV::PREFIX . 'load_lightbox_imageLightbox'
 * ================================
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Image_Lightbox extends FV_Lightbox_Abstract{

    const NAME = 'imageLightbox';
    public $slug = 'imageLightbox';

    protected static $instance = null;

    protected function __construct() {
        $this->supportsCustomizer = true;

        // Customizer Config
        $this->outputHandle = 'fv-lightbox-imageLightbox-css';
        $this->customizerSlug = 'fv_lightbox__' . $this->slug . '__';

        $this->customizerSectionTitle = '[Lightbox] imageLightbox';

        parent::__construct();
    }

    function registerCustomizerSettings()
    {
        $this->_registerCustomizerSetting( "main_bg", array(
            'default' => '#666',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Elements background',
            'type' => 'color',
        ), array(
            '#imagelightbox-actions .imagelightbox-action' => array('attribute' => 'background-color'),
            '#imagelightbox-close'        => array('attribute' => 'background-color'),
            '#imagelightbox-caption'        => array('attribute' => 'background-color'),
        ) );

        $this->_registerCustomizerSetting( "arrows_bg", array(
            'default' => '#00000080',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Arrows background',
            'type' => 'color',
        ), array(
            'body .imagelightbox-arrow'        => array('attribute' => 'background-color'),
        ) );

        $this->_registerCustomizerSetting( "top_margin", array(
            'default' => 40,
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'absint',

            'label' => 'Top indent for buttons (px)',
            'description' => 'If your theme header/topbar overlaps buttons.',
            'type' => 'number',
        ), array(
            '#imagelightbox-actions'        => array('attribute' => 'top','units' => 'px'),
            '#imagelightbox-close'        => array('attribute' => 'top','units' => 'px'),
        ) );

        parent::registerCustomizerSettings();
    }



    /**
     * Enqueue assets
     *
     * @since    2.2.082
     *
     * @param string $theme     Key, like `default`
     * @return void
     */
    public function assets ( $theme = '' ) {

        wp_enqueue_script( 'fv-lightbox-imageLightbox-js',  fv_min_url(FV::$ASSETS_URL . 'image-lightbox/jquery.image-lightbox.js'), array('jquery', 'fv_lib_js'), FV::VERSION, true );
        wp_enqueue_style( 'fv-lightbox-imageLightbox-css', fv_min_url(FV::$ASSETS_URL . 'image-lightbox/jquery.image-lightbox.css'), array(), FV::VERSION );

        $this->_outputCustomizedCSS();
    }

    /**
     * Add supported themes list to settings
     *
     * @since    2.2.082
     *
     * @param array $lightbox_list
     * @return array
     */
    public function initListThemes ( $lightbox_list ) {
        //FV::PREFIX . 'lightbox_list_array'
        return array_merge(
            array(
                'imageLightbox_default' => 'imageLightbox (images only)',
            ),
            $lightbox_list
        );
    }
}
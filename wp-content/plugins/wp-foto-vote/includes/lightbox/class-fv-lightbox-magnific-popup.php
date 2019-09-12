<?php
/**
 * magnific-popup Lightbox wrapper
 * ================================
 *
 * @package    FV
 * @author     Maxim K <support@wp-vote.net>
 *
 * @since      2.2.703
 */
class FV_Lightbox_Magnific_Popup {

    const NAME = 'magnific-popup';

    /**
     * Enqueue assets
     *
     * @param string $theme     Key, like `evolution_default`
     * @return void
     */
    public static function assets ( $theme ) {
        wp_enqueue_script( 'fv-lightbox-' . self::NAME, FV::$ASSETS_URL . 'magnific-popup/magnific-popup.min.js', array('jquery'), FV::VERSION, true );
        wp_enqueue_style( 'fv-lightbox-' . self::NAME, FV::$ASSETS_URL . 'magnific-popup/magnific-popup.css', array(), FV::VERSION );
    }

    /**
     * Return name of action, that must be called for load this lightbox assets
     * @return string
     */
    public static function getActionName () {
        return 'fv_load_lightbox_' . self::NAME;
    }

    /**
     * Add supported themes list to settings
     * @param array $lightbox_list
     * @return array
     */
    public static function initListThemes ( $lightbox_list ) {
        $lightbox_list[self::NAME . '_default'] = 'Magnific Popup  (images + local video)';
        return $lightbox_list;
    }
}
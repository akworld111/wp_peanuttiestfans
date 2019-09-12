<?php
/**
 * iLightbox Lightbox wrapper
 * ================================
 *
 * @package    FV
 * @author     Maxim K <support@wp-vote.net>
 *
 * @since      2.2.813
 */
class FV_Lightbox_iLightbox {

    const NAME = 'ilightbox';

    /**
     * Enqueue assets
     *
     * @param string $theme     Key, like `evolution_default`
     * @return void
     */
    public static function assets ( $theme ) {
        wp_enqueue_script( 'fv-lightbox-' . self::NAME, FV::$ASSETS_URL . 'ilightbox/ilightbox.js', array('jquery'), FV::VERSION, true );
        wp_enqueue_script( 'jquery.mousewheel', FV::$ASSETS_URL . 'ilightbox/jquery.mousewheel.js', array('jquery'), FV::VERSION, true );
        wp_enqueue_script( 'jquery.requestAnimationFrame', FV::$ASSETS_URL . 'ilightbox/jquery.requestAnimationFrame.js', array('jquery'), FV::VERSION, true );
        wp_enqueue_style( 'fv-lightbox-' . self::NAME, FV::$ASSETS_URL . 'ilightbox/css/ilightbox.css', array(), FV::VERSION );
        wp_enqueue_style( 'fv-lightbox-skin-' . self::NAME, FV::$ASSETS_URL . 'ilightbox/' . $theme. '/skin.css', array(), FV::VERSION );
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
        $lightbox_list[self::NAME . '_mac-skin'] = 'iLightbox [mac-skin]  (images + local video + youtube)';
        $lightbox_list[self::NAME . '_light-skin'] = 'iLightbox [light-skin]  (images + local video + youtube)';
        $lightbox_list[self::NAME . '_dark-skin'] = 'iLightbox [dark-skin]  (images + local video + youtube)';
        $lightbox_list[self::NAME . '_smooth-skin'] = 'iLightbox [smooth-skin]  (images + local video + youtube)';
        return $lightbox_list;
    }
}
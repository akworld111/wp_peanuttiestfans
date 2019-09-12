<?php
/**
 * Default Lightbox library wrapper
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
class FV_Lightbox_Evolution {

    const NAME = 'evolution';

    /**
     * Enqueue assets
     *
     * @since    2.2.082
     *
     * @param string $theme     Key, like `evolution_default`
     * @return void
     */
    public static function assets ( $theme ) {

        //wp_enqueue_script('fv_lightbox_evolution', plugins_url( FV::SLUG . '/assets/js/jQuery.imageLightbox.js'), array('jquery'), FV::VERSION, true );
        //wp_enqueue_style('fv_lightbox_evolution', plugins_url( FV::SLUG . '/assets/css/jQuery.imageLightbox.css'), array(), FV::VERSION );

        wp_enqueue_script( 'fv-lightbox-evolution', plugins_url( FV::SLUG . '/assets/lightbox_evolution/jquery.lightbox.min.js'), array('jquery'), FV::VERSION, true );
        wp_enqueue_style( 'fv-lightbox-evolution', plugins_url( FV::SLUG  . '/assets/lightbox_evolution/' . $theme . '/jquery.lightbox.css'), true );

    }

    /**
     * Return name of action, that must be called for load this lightbox assets
     *
     * @since    2.2.082
     *
     * @return string
     */
    public static function getActionName () {
        return FV::PREFIX . 'load_lightbox_' . self::NAME;
    }

    /**
     * Add supported themes list to settings
     *
     * @since    2.2.082
     *
     * @param array $lightbox_list
     * @return array
     */
    public static function initListThemes ( $lightbox_list ) {
        //FV::PREFIX . 'lightbox_list_array'
        return array_merge(
            array(
                'evolution_default' => 'Evolution :: default (images + Youtube/Vimeo)',
                'evolution_carbono' => 'Evolution :: carbono  (images + Youtube/Vimeo)',
                'evolution_evolution' => 'Evolution :: evolution  (images + Youtube/Vimeo)',
                'evolution_evolution-dark' => 'Evolution :: evolution-dark  (images + Youtube/Vimeo)',
                'evolution_blue' => 'Evolution :: blue  (images + Youtube/Vimeo)',
            ),
            $lightbox_list
        );
    }
}
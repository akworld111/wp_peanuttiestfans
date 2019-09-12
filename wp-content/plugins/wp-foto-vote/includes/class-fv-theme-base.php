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
 */
abstract class Fv_Theme_Base {

    /**
     * Is theme supports leaders block
     *
     * @since       2.2.310
     * @access      protected
     * @var         bool
     */
    protected $supportCustomizer;

    /**
     * Class instance.
     *
     * @since       2.2.310
     * @var         object
     * @access      public
     */
    public static $instances;

    /**
     * Init
     * @param $theme_slug                   Like "flickr"
     * @param $api_version                  Theme supported API version
     * @param bool $supportCustomizer       Is theme supported Customizer?
     *
     * @since    2.2.310
     */
    public function __construct($theme_slug, $api_version, $supportCustomizer = false) {
        add_action('fv_theme_load_' . $theme_slug, array($this, 'load'));
    }

    /**
     * Init theme (add actions, hooks, etc)
     * @since    2.2.310
     */
    abstract public function load();

    /**
     * Load single photo page Assets
     */
    public function assets_item() { }

    /**
     * Load contest gallery Assets
     */
    public function assets_list() { }

    /**
     * Load contest gallery Assets
     * @param $wp_customize
     */
    public function register_customizer_fields($wp_customize) {

    }

}
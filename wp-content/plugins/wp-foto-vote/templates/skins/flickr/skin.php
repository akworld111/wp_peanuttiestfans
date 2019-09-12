<?php

class FV_Flickr extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'flickr';
        $this->title = 'Flickr (images only)';
        $this->singleTitle = 'Flickr (images + video)';

        parent::__construct();
    }

    public function init(){
        parent::init();
    }

    public function beforeList() {
        if ( !FvFunctions::is_ajax() ) {
            echo '<div class="column-grid photo-display-container ju flex-images justified-gallery" id="grid">';
        }
    }

    public function afterList() {
        if ( !FvFunctions::is_ajax() ) {
            echo '</div>';
        }

        add_action('wp_footer',  array($this, 'wp_footer'), 99);
    }

    function wp_footer() {
        if ( !FvFunctions::is_ajax() && false == wp_cache_get('fv_flickr_wp_footer_loaded', 'fv') ) {
            wp_cache_set('fv_flickr_wp_footer_loaded', '1', 'fv');
            echo '</div>
                   <div id="progress" class="waiting"><dt></dt><dd></dd></div>
                ';
        }
    }

    public function assets() {
        wp_enqueue_script('fv_theme_flickr', FV_Templater::locateUrl( $this->slug, 'assets/fv_theme_flickr.js' ), array( 'jquery', 'fv_lib_js' ), FV::VERSION);
    }

    public function assetsSingle() {
        wp_enqueue_script('fv_exif', FV::$ASSETS_URL . 'js/exif.js', array() , FV::VERSION, true);
    }

    /**
     * beforeSingle
     */
    public function beforeSingle()
    {
        add_filter( 'fv_contest_item_template_data', array($this, 'singleTemplateDataFilter') );
    }

    function singleTemplateDataFilter($template_data) {
        $order = rand(1,10) > 5 ? FvQuery::ORDER_ASCENDING : FvQuery::ORDER_DESCENDING;

        $template_data['most_voted'] = ModelCompetitors::query()
            ->limit(8)
            ->where_not( 'id', $template_data["contestant"]->id )
            ->where_all( array('contest_id' => $template_data["contest_id"], 'status'=>ST_PUBLISHED) )
            ->sort_by( 'id', $order )
            ->find(false, false, true, false, true);

        return $template_data;
    }


    /**
     * Load contest gallery Assets
     * @param $wp_customize
     */
/*
    public function registerCustomizerFields($wp_customize) {
          ## https://github.com/buddypress/BuddyPress/blob/master/src/bp-core/bp-core-customizer-email.php
        parent::register_customizer_fields($wp_customize);

        $id = 'fv_theme_'.self::SLUG;

        //1. Define a new section (if desired) to the Theme Customizer
        $wp_customize->add_section( $id,
            array(
                'title' => __( 'FV theme ' . self::SLUG, 'mytheme' ), //Visible title of section
                'priority' => 35, //Determines what order this appears in
                //'capability' => 'edit_theme_options', //Capability needed to tweak
                'description' => __('Allows you to customize some example settings for MyTheme.', 'mytheme'), //Descriptive tooltip
            )
        );
        //2. Register new settings to the WP database...
        $wp_customize->add_setting( $id.'[overlay_bg_color]', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#2BA6CB', //Default setting/value to save
                'type' => 'option', //Is this an 'option' or a 'theme_mod'?
                //'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            $id.'[overlay_bg_color]', //Set a unique ID for the control
            array(
                'label' => __( 'Overlay BG color', 'mytheme' ), //Admin-visible name of the control
                'section' => $id, //ID of the section this control should render in (can be one of yours, or a WordPress default section)
                'settings' => $id.'[overlay_bg_color]', //Which setting to load and manipulate (serialized is okay)
                'priority' => 10, //Determines the order this control appears in for the specified section
            )
        ) );

        Fv_Themes_Customizer::add_styles_to_inject($id.'[overlay_bg_color]', '#grid .caption-post', 'background: [CSS] !important;');

        wp_enqueue_script('fv_theme_flickr_customizer', FvFunctions::get_theme_url ( self::SLUG, 'assets/fv_theme_flickr_customizer.js' ), array( 'jquery', 'fv-themes-customizer' ) , FV::VERSION);
    }
*/
}

new FV_Flickr();
<?php
## This file was included just when Skin is loaded
## Please use skin.php to add all necessary hooks/filters

//
//
//class FvFlickrTheme  {
//    /**
//     * Theme name
//     *
//     * @since    2.2.081
//     * @access   public
//     */
//    const SLUG = "flickr";
//
//    public function __construct() {
//        parent::__construct(self::SLUG, 1, true);
//
//        add_filter('fv_contest_item_template_data', array($this, 'single_item_template_data_filter'), 1 );
//    }
//
//    public function load() {
//        add_filter('fv_contest_item_template_data', array($this, 'single_item_template_data_filter'), 1 );
//    }
//
//    public function assets_item() {
//        parent::assets_item();
//
//        wp_enqueue_script('fv_exif', FV::$ASSETS_URL . 'js/exif.js', array() , FV::VERSION, true);
//        wp_enqueue_script('fv_theme_flickr', FvFunctions::get_theme_url ( self::SLUG, 'assets/fv_theme_flickr.js' ), array( 'jquery' ) , FV::VERSION);
//    }
//
//    public function assets_list() {
//        parent::assets_list();
//
//        wp_enqueue_script('fv_theme_flickr', FvFunctions::get_theme_url ( self::SLUG, 'assets/fv_theme_flickr.js' ), array( 'jquery' ) , FV::VERSION);
//    }
//
//    /**
//     * Load contest gallery Assets
//     * @param $wp_customize
//     */
//    public function register_customizer_fields($wp_customize) {
//          ## https://github.com/buddypress/BuddyPress/blob/master/src/bp-core/bp-core-customizer-email.php
//        parent::register_customizer_fields($wp_customize);
//        $id = 'fv_theme_'.self::SLUG;
//
//
//        //1. Define a new section (if desired) to the Theme Customizer
//        $wp_customize->add_section( $id,
//            array(
//                'title' => __( 'FV theme ' . self::SLUG, 'mytheme' ), //Visible title of section
//                'priority' => 35, //Determines what order this appears in
//                //'capability' => 'edit_theme_options', //Capability needed to tweak
//                'description' => __('Allows you to customize some example settings for MyTheme.', 'mytheme'), //Descriptive tooltip
//            )
//        );
//        //2. Register new settings to the WP database...
//        $wp_customize->add_setting( $id.'[overlay_bg_color]', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
//            array(
//                'default' => '#2BA6CB', //Default setting/value to save
//                'type' => 'option', //Is this an 'option' or a 'theme_mod'?
//                //'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
//                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
//            )
//        );
//
//        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
//        $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
//            $wp_customize, //Pass the $wp_customize object (required)
//            $id.'[overlay_bg_color]', //Set a unique ID for the control
//            array(
//                'label' => __( 'Overlay BG color', 'mytheme' ), //Admin-visible name of the control
//                'section' => $id, //ID of the section this control should render in (can be one of yours, or a WordPress default section)
//                'settings' => $id.'[overlay_bg_color]', //Which setting to load and manipulate (serialized is okay)
//                'priority' => 10, //Determines the order this control appears in for the specified section
//            )
//        ) );
//
//        Fv_Themes_Customizer::add_styles_to_inject($id.'[overlay_bg_color]', '#grid .caption-post', 'background: [CSS] !important;');
//
//        wp_enqueue_script('fv_theme_flickr_customizer', FvFunctions::get_theme_url ( self::SLUG, 'assets/fv_theme_flickr_customizer.js' ), array( 'jquery', 'fv-themes-customizer' ) , FV::VERSION);
//    }
//
//    public function single_item_template_data_filter($template_data) {
//        $order = rand(1,10) > 5 ? FvQuery::ORDER_ASCENDING : FvQuery::ORDER_DESCENDING;
//        $template_data['most_voted'] = ModelCompetitors::query()
//                                        ->limit(8)
//                                        ->where_not( 'id', $template_data["contestant"]->id )
//                                        ->where_all( array('contest_id' => $template_data["contest_id"], 'status'=>ST_PUBLISHED) )
//                                        ->sort_by( 'RAND()', $order )
//                                        ->find();
//        // Is this item in leaders and contest are ended ?
//        $template_data['is_most_voted'] = false;
//        // if contests ends
//        if ( !$template_data['konurs_enabled'] && current_time('timestamp', 0) > strtotime($template_data['contest']->date_finish) ) {
//            $most_voted_arr = ModelCompetitors::query()
//                ->limit(3)
//                ->where( 'contest_id', $template_data["contest_id"] )
//                ->sort_by( 'votes_count', 'DESC' )
//                ->find();
//            $most_voted_places_names = array(
//                '0'=> __('first', 'fv'),
//                '1'=> __('second', 'fv'),
//                '2'=> __('third', 'fv'),
//                '3'=> __('fourth', 'fv'),
//            );
//            foreach($most_voted_arr as $key => $most_voted_item) {
//                if ( $most_voted_item->id == $template_data["contestant"]->id ) {
//                    $template_data['is_most_voted'] = true;
//                    $template_data['most_voted_place'] = $most_voted_places_names[$key];
//                    break;
//                }
//            }
//        }
//        return $template_data;
//    }
//
//    /**
//     * Helper function to get the class object. If instance is already set, return it.
//     * Else create the object and return it.
//     *
//     * @return object $instance Return the class instance
//     */
//    public static function get_instance() {
//        if ( ! isset( Fv_Theme_Base::$instances[self::SLUG] ) )
//            return Fv_Theme_Base::$instances[self::SLUG] = new FvFlickrTheme();
//
//        return Fv_Theme_Base::$instances[self::SLUG];
//    }
//
//}
//
//// Init settings
//FvFlickrTheme::get_instance();
//

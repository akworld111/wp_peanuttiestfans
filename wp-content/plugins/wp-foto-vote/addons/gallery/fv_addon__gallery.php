<?php
/*
  Plugin Name: WP Foto Vote - Gallery addon
  Plugin URI: http://wp-vote.net/
  Description: Add more than 1 image to photo
  Author: Maxim Kaminsky
  Author URI: http://www.maxim-kaminsky.com/
  Plugin support EMAIL: wp-vote@hotmail.com
  Version: 0.3
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!defined('FV_GALL_CORE_DIR')) {
    define('FV_GALL_CORE_DIR', plugin_dir_path(__FILE__));
}
if (!defined('FV_GALL_CORE_URL')) {
    define('FV_GALL_CORE_URL', plugin_dir_url(__FILE__));
}

// Init class early then Redux Framework, for add Addon options, else they are not added
add_action('plugins_loaded', 'FvAddon_GalleryCoreRun', 3);

function FvAddon_GalleryCoreRun() {
    if (!class_exists('FvAddonBase')) {
        return;
    }

    class FvAddon_GalleryCore extends FvAddonBase {

        /**
         * Addon version
         * @var float
         */
        CONST VER = 0.3;
        public $version = 0.3;

        /**
         * Class instance.
         *
         * @var object
         */
        protected static $instance;

        /**
         * Constructor. Loads the class.
         */
        protected function __construct($name, $slug) {
            //** Dont remove this, else addon will not works
            parent::__construct($name, $slug, 'api_v2');
        }

        /**
         * Performs all the necessary actions
         */
        public function init() {
            parent::init();

            if ( $this->_get_opt('enabled')  ) {
                if ( is_admin() ) {
                    add_action('fv/admin/form_edit_photo/extra', array($this, 'action_form_edit_photo_extra'), 10, 1);
                    add_action('fv/admin/page/contest_single/competitors-tab', array($this, 'admin_js'), 10, 1);
                    add_action('fv/admin/page/contest_single/competitors/tpls', array($this, 'admin_gallery_tpl'), 10, 1);

                    add_action('fv/admin/moderation/list_item/extra', array($this, 'action_admin_show'), 10, 1);
                    add_action('fv/admin/contestant/extra', array($this, 'action_admin_show'), 10, 1);

                    if ( get_option('fv-image-delete-from-hosting', false) ) {
                        add_action('fv/delete_photo', array($this, 'delete_contestant_gallery'), 10, 1);
                    }

                }

                add_action('fv/public/list_item/extra', array($this, 'action_show'), 10, 1);
                add_action('fv_after_contest_list', array($this, 'action_public_assets'), 10, 3);
                //add_filter('fv/public/theme/list_item/rel', array($this, 'filter_rel'), 10, 2);

                add_action('fv/public/single_item/extra', array($this, 'action_show_in_single'), 10, 1);
                add_action('fv_after_contest_item', array($this, 'action_public_assets'), 10, 3);
            }
        }


        /**
         * Remove Galery images on deleting contestant
         *
         * @param FV_Competitor $competitor
         *
         */
        public function delete_contestant_gallery($competitor) {
            // IF enabled multiupload
            $gallery_meta = $this->_competitor_gallery_meta($competitor);
            if (!$gallery_meta) {
                return;
            }

            FOREACH($gallery_meta as $gallery_row):
                if ( (int) $gallery_row->value > 0) {
                    wp_delete_attachment($gallery_row->value, true);
                }
            ENDFOREACH;
        }


        /**
         * Show info in Admin
         *
         * @param FV_Competitor $competitor
         */
        public function action_admin_show($competitor) {

            $gallery_meta = $this->_competitor_gallery_meta($competitor);

            if (!$gallery_meta) {
                return;
            }

            echo "<div class='block-extra'/>";
            echo "Gallery: <br/>";

            FOREACH($gallery_meta as $gallery_row):
                if ( (int) $gallery_row->value > 0) {
                    $competitor_src = wp_get_attachment_url((int) $gallery_row->value);
                    $competitor_src_thumb = wp_get_attachment_image_src((int) $gallery_row->value, 'thumbnail', true);

                    if ( !$competitor_src || !$competitor_src_thumb ) {
                        continue;
                    }

                    printf(
                        '<a href="%1$s" class="fv-gallery-a" title="%2$s"><img class="fv-gallery-img" src="%3$s" width="35"/></a>',
                        esc_url( $competitor_src ),
                        esc_attr( $competitor->name ),
                        esc_url( $competitor_src_thumb[0] )
                    );
                }
            ENDFOREACH;
            echo "</div>";
        }        

        function admin_js () {
            wp_enqueue_script('fv_gallery_admin', FV_GALL_CORE_URL . 'assets/fv_gallery.js', array('jquery'), FV::VERSION);
        }

        function admin_gallery_tpl() {
            include 'views/_gallery_one_tpl.php';
        }

        /**
         * @param $rel
         * @param FV_Competitor $competitor
         *
         * @return string
         */
        public function filter_rel($rel, $competitor) {
            return $rel . $competitor->id;
        }

        /**
         * @param FV_Competitor $competitor
         */
        public function action_form_edit_photo_extra($competitor) {
            //$photo->options = FvFunctions::getContestOptionsArr($photo->options);

            $gallery_meta = $this->_competitor_gallery_meta($competitor);
            $gallery_items =array();
            $att_src = array();

            FOREACH($gallery_meta as $gallery_row):
                if ( (int) $gallery_row->value > 0) {

                    $att_id = (int) $gallery_row->value;

                    $attachment = get_post($att_id);

                    if ( !$attachment ) {
                        continue;
                    }

                    $att_src = wp_get_attachment_image_src($attachment->ID, 'thumbnail', true);

                    $gallery_items[$gallery_row->ID] = array(
                        'photo_id'          => (int) $gallery_row->value,
                        'photo_src'         => $att_src ? $att_src[0] : FV::$ASSETS_URL . "img/no-photo-square.jpg",
                        'photo_url'         => $att_src ? admin_url( 'post.php?action=edit&post=' . $attachment->ID ) : false,
                        'photo_title'       => $attachment->post_title,
                    );


                }
            ENDFOREACH;

            include 'views/_photo_edit_form_extra.php';
        }

        /**
         * Show info
         * @param FV_Competitor $competitor
         */
        public function action_show($competitor) {

            $gallery_meta = $this->_competitor_gallery_meta($competitor);

            if (!$gallery_meta) {
                return;
            }

            do_action('fv/gallery/before_gallery', $competitor, $gallery_meta);
            
            echo "<div class='contest-block-extra'>";

            $att_id = false;
            $N = 2;
            FOREACH($gallery_meta as $gallery_row):
                if ( (int) $gallery_row->value > 0) {
                    $att_id = (int) $gallery_row->value;

                    $attachment = get_post($att_id);

                    if ( !$attachment ) {
                        continue;
                    }

                    $competitor_src = wp_get_attachment_url($att_id);
                    $competitor_src_thumb = wp_get_attachment_image_src($att_id, 'thumbnail', true);

                    if ( !$competitor_src || !$competitor_src_thumb ) {
                        continue;
                    }

                    $rel = apply_filters('fv/public/theme/list_item/rel', 'fw', $competitor);

                    $is_image = wp_attachment_is_image( $att_id );

                    echo apply_filters('fv/gallery/gallery_one_row', sprintf(
                        '<a href="%1$s" class="fv-gallery-a %5$s" rel="%6$s" data-title="[%7$s] %3$s" title="[%7$s] %3$s1" data-id="%2$s"><img class="fv-gallery-img" src="%4$s"/></a>',
                        esc_url( $competitor_src ),
                        esc_attr( $competitor->id ),
                        esc_attr( $is_image ? $competitor->getLightboxTitleForTpl() : $attachment->post_title ),
                        esc_url( $competitor_src_thumb[0] ),
                        $is_image ? 'fv_lightbox' : '',
                        $rel,
                        $N
                    ),  $competitor, $gallery_row, $competitor_src, $competitor_src_thumb);

                    $N++;
                }
            ENDFOREACH;
            echo "</div>";

            do_action('fv/gallery/after_gallery', $competitor, $gallery_meta);
        }

        /**
         * Show info on Single page
         *
         * @param FV_Competitor $competitor
         */
        public function action_show_in_single($competitor) {

            $gallery_meta = $this->_competitor_gallery_meta($competitor);

            if (!$gallery_meta) {
                return;
            }

            do_action('fv/gallery/before_single', $competitor, $gallery_meta);

            $single_lightbox_on = $this->_get_opt('single_lightbox_on');
            $html = '';
            $att_id = 0;
            $is_image_or_video = false;

            echo "<div class='contest-block-extra'>";
            FOREACH($gallery_meta as $N => $gallery_row):
                $att_id = (int) $gallery_row->value;
                if ( $att_id > 0) {
                    $photo_src_thumb = wp_get_attachment_image_src($att_id, 'full', true);

                    if ( $photo_src_thumb && !is_wp_error($photo_src_thumb) ) {
                        $html = apply_filters('fv/gallery/single_one_row',
                            sprintf('<img src="%s" class="contest-block--gallery-img" alt="gallery"/>', esc_attr($photo_src_thumb[0])),
                            $competitor, $gallery_row
                        );

                        if ( $single_lightbox_on ) {

                            $rel = apply_filters('fv/public/theme/list_item/rel', 'fw', $competitor);

                            $is_image_or_video = wp_attachment_is_image( $att_id ) || wp_attachment_is( 'video', $att_id );

                            $html = apply_filters('fv/gallery/single_one_row_a', sprintf(
                                '<a href="%1$s" class="fv-gallery-a %5$s" rel="%6$s" data-title="[%7$s] %3$s" title="[%7$s] %8$s" data-id="%2$s">%4$s</a>',
                                esc_url( $photo_src_thumb[0] ),
                                esc_attr( $competitor->id ),
                                esc_attr( $competitor->getLightboxTitleForTpl() ),
                                $html,
                                $is_image_or_video ? 'fv_lightbox' : '',
                                esc_attr($rel),
                                $N+1,
                                esc_attr( $competitor->getHeadingForTpl() )
                            ),  $competitor, $gallery_row, $photo_src_thumb[0]);

                        }
                        echo $html;
                    }

                }
            ENDFOREACH;
            echo "</div>";

            do_action('fv/gallery/after_single', $competitor, $gallery_meta);
        }        

        /**
         * @param FV_Competitor $competitor
         * @return array
         */
        public function _competitor_gallery_meta($competitor) {
            if ( !$competitor->id ) {
                return array();
            }
            $metas = $competitor->meta()->get_all();
            $gallery_meta = array();

            foreach ($metas as $meta_row) {
                if ($meta_row->meta_key == 'gallery') {
                    $gallery_meta[] = $meta_row;
                }
            }

            return apply_filters('fv/gallery/get_gallery_meta', $gallery_meta, $competitor);
        }

        /**
         * Enqueue addon public styles
         * @param $theme
         * @param FV_Competitor $contestant
         * @param $type
         */
        public function action_public_assets($theme, $contestant, $type) {
            wp_enqueue_style($this->slug . '-css', FV_GALL_CORE_URL . '/assets/fv_gallery.css', false, self::VER, 'all');

            if ( 'competitor' === $type && $this->_get_opt('single_lightbox_on') ) {
                FV_Public::lightbox_load( $contestant->getContest(true)->lightbox_theme, $theme );
            }
        }

        /**
         * Dynamically add Addon settings section
         */
        public function section_settings($sections) {
            //var_dump($this->addonsSettings[$this->slug . '_access_token']);
            $description = '<p class="description">This addon allow add more than one image for one contest item (just from admin).</p>';

            //$sections = array();
            $sections[] = array(
                'title' => 'Gallery',
                'desc' => $description,
                'icon' => 'image-outline',
                'on' => $this->_get_opt('enabled'),
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array(
                    array(
                        'id' => $this->slug . '_enabled',
                        'type' => 'switch',
                        'title' => 'Enable gallery addon?',
                        'default' => false,
                    ),
                    array(
                        'id' => $this->slug . '_single_lightbox_on',
                        'type' => 'switch',
                        'title' => 'Enable lightbox on the single competitor pages for the gallery images?',
                        'default' => false,
                    ),
                )
            );

            return $sections;
        }

        /**
         * @return FvAddon_GalleryCore $instance Return the class instance
         */
        public static function get_instance() {

            if (!isset(self::$instance)) {
                return self::$instance = new FvAddon_GalleryCore('GalleryCore', 'gall-core');
            }

            return self::$instance;
        }

    }

    /** Instantiate the class */
    FvAddon_GalleryCore::get_instance();
}

// Function :: END

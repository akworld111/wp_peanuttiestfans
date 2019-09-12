<?php

/**
 * @since      2.2.801
 *
 * @package    FV
 * @subpackage public
 */
class FV_Public_Gallery {

    /**
     * Class instance.
     * @var object
     */
    protected static $instance;

    /**
     * @param array         $image_src_arr               THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
     * @param FV_Competitor $competitor
     * @param string        $class
     *
     * @output <IMG> tag
     */
    public static function render_image_html($image_src_arr, $competitor, $class = '', $skin) {
        if ( is_array($image_src_arr) ) {
            $image = $image_src_arr[0];
        }

        if ( $competitor->isVideo() ) {

            $poster = FV::$ASSETS_URL . 'img/video-thumb.jpg';

            $html = sprintf('<video controls src="%s" poster2="%s" alt="%s" class="attachment-thumbnail fv-video-thumbnail %s">
                        <source src="%s" type="%s">
                        Sorry, your browser doesn\'t support embedded videos :(
                    </video>',
                $competitor->getImageUrl(),
                $poster,
                esc_attr($competitor->name),
                esc_attr($class),
                $image,
                $competitor->mime_type
            );

            echo apply_filters('fv/public/gallery/render_video_html', $html, $image, $competitor, $class);

        } else {

            if ( FvFunctions::lazyLoadEnabled($skin) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                $html = sprintf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mO4d/fufwAIzQOYASGzMgAAAABJRU5ErkJggg=="
                            data-lazy-src="%s" width="%s" height="%s" class="attachment-thumbnail fv-lazy fv-img-thumbnail %s" alt="%s"/>',
                    $image_src_arr[0], $image_src_arr[1], $image_src_arr[2], esc_attr($class), esc_attr($competitor->name));
            } else {
                $html = sprintf('<img src="%s" width="%s" height="%s" class="attachment-thumbnail" alt="%s"/>', 
                    $image_src_arr[0], $image_src_arr[1], $image_src_arr[2], esc_attr($class), esc_attr($competitor->name));
            }

            //$html = sprintf( '<img src="%s" alt="%s" class="attachment-thumbnail fv-img-thumbnail %s">', $image, esc_attr($competitor->name), esc_attr($class) );
            echo apply_filters('fv/public/gallery/render_image_html', $html, $image, $competitor, $class);
        }

    }

    /**
     * @since 2.2.801
     *
     * @return FV_Public_Gallery $instance Return the class instance
     */
    public static function get_instance()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Gallery();

        return self::$instance;
    }
}
<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The public-upload functionality of the plugin.
 *
 * @since      2.2.806
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public_Upload
{
    public static function add_compress_jpeg_quality_filter() {
        //add_filter( 'jpeg_quality', array('FV_Public_Upload','_compress_jpeg_quality') );
        add_filter( 'wp_handle_upload', array('FV_Public_Upload','_compress_jpeg_quality') );
    }
    public static function remove_compress_jpeg_quality_filter() {
        //remove_filter( 'jpeg_quality', array('FV_Public_Upload','_compress_jpeg_quality') );
        remove_filter( 'wp_handle_upload', array('FV_Public_Upload','_compress_jpeg_quality') );
    }

    /**
     * Change jpg quality (also affect to thumbnails) 
     * @param $data
     *
     * @return mixed
     * @since 2.2.806
     */
    public static function _compress_jpeg_quality($data ) {
        
        $new_quality = get_option( "fv-upload-jpg-quality", 100 );
        
        if ( !$new_quality || 100 == $new_quality ) {
            return $data;
        }
        
        if( ! isset( $data['file'] ) || ! isset( $data['type'] ) )
            return $data;

        // Target jpeg images
        if( in_array( $data['type'], array( 'image/jpg', 'image/jpeg' ) ) )
        {
            // Check for a valid image editor
            $editor = wp_get_image_editor( $data['file'] );
            if( ! is_wp_error( $editor ) )
            {
                // Set the new image quality
                $result = $editor->set_quality( $new_quality );

                // Re-save the original image file
                if( ! is_wp_error( $result ) )
                    $editor->save( $data['file'] );
            }
        }
        return $data;
    }
}
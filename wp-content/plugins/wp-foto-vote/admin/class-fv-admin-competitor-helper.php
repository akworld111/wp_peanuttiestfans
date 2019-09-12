<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 *
 * @since      2.2.500
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 * 
 * @deprecated since 2.2.609
 */
class FV_Admin_Competitor_Helper
{

    /**
     * Rotate image and thumbnails
     *
     * @param FV_Competitor $competitor   
     * @param integer       $angle
     *
     * @return array
     */
    public static function rotate_image( $competitor, $angle )
    {
        $result = false;
        $message = '';
        $data = array('competitor_id'=>$competitor->id);

        try {

            // IF custom hook active OR
            // IMAGE_ID is empty && hook exists (for backward compatibility with version < 2.2.364)
            if (
                apply_filters('fv/admin/rotate_image/custom', false, $competitor) ||
                ( !$competitor->image_id && has_action( 'fv/admin/rotate_image' ) )
            ) {
                do_action( 'fv/admin/rotate_image', $competitor, $angle );
            } else if ( $competitor->image_id ) {
                /* Get the image source, width, height, and whether it's intermediate. */
                $image_path = get_attached_file( $competitor->image_id );

                $WP_Image_Editor = wp_get_image_editor( $image_path, array() );

                if ( $WP_Image_Editor->rotate($angle) === true ) {

                    $WP_Image_Editor->save($image_path);
                    $attach_data = wp_generate_attachment_metadata( $competitor->image_id, $image_path );

                    // TODO - check
                    $meta = wp_get_attachment_metadata( $competitor->image_id );
                    # Code from https://developer.wordpress.org/reference/functions/wp_delete_attachment/#source
                    // Remove intermediate and backup images if there are any.
                    if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
                        foreach ( $meta['sizes'] as $size => $sizeinfo ) {
                            $intermediate_file = str_replace( basename( $image_path ), $sizeinfo['file'], $image_path );
                            /** This filter is documented in wp-includes/functions.php */
                            $intermediate_file = apply_filters( 'wp_delete_file', $intermediate_file );
                            //var_dump( path_join(dirname($image_path), $intermediate_file) );
                            @ unlink( path_join(dirname($image_path), $intermediate_file) );
                        }
                    }

                    wp_update_attachment_metadata( $competitor->image_id,  $attach_data );

                    FvLogger::addLog("rotate_image - rotated success > " . $angle, $image_path);
                    $result = true;
                    $message = 'Rotate_image - rotated success';
                    $data['new_src'] = $competitor->getThumbUrl();
                } else {
                    $message = 'Rotate_image - error rotate';
                    FvLogger::addLog("rotate_image - error rotate");
                }
            } else {
                FvLogger::addLog('Rotate_image - error - no Image ID', $competitor);
                $message = 'Rotate_image - error - no Image ID';
            }

        } catch(Exception $ex) {
            FvLogger::addLog( "rotate_image - some error ", $ex->getMessage() );
            $message = $ex->getMessage();
        }

        return compact('result', 'message', 'data');
    }


}

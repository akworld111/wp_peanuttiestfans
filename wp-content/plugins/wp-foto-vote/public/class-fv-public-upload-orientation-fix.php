<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * @since      2.2.600
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 *
 * @see https://gist.github.com/n7studios/6a764d46bc1d515ba406
 * @see https://wordpress.org/plugins/va-removing-exif/
 *
 */
class FV_Public_Upload_Orientation_Fix {

    public $INPUT_NAME;
    public static $instance;

    public function before_upload($INPUT_NAME) {

        $this->INPUT_NAME = $INPUT_NAME;
        add_filter( 'wp_handle_upload', array($this, 'run_fix_orientation'), 10 );
    }

    public function after_upload() {

        remove_filter( 'wp_handle_upload', array($this, 'run_fix_orientation'), 10 );

    }

    public function run_fix_orientation ( $params ) {



        //list($file, $url, $type, $action) = $params;
        $file = $params['file'];

        $extention = pathinfo($file, PATHINFO_EXTENSION);
        if ( !in_array($extention, array('jpg', 'jpeg', 'png', 'gif')) ) {
            return $params;
        }

        if ( empty($_POST[$this->INPUT_NAME . '--exif-orientation']) ) {
            return $params;
        }

        $exif_orientation = $_POST[$this->INPUT_NAME . '--exif-orientation'];

        if ($exif_orientation > 1) {

            $rotator = false;
            $flipper = false;
            switch ($exif_orientation) {
                case 2:
                    $flipper = array(false, true);
                    break;
                case 3:
                    $orientation = -180;
                    $rotator = true;
                    break;
                case 4:
                    $flipper = array(true, false);
                    break;
                case 5:
                    $orientation = -90;
                    $rotator = true;
                    $flipper = array(false, true);
                    break;
                case 6:
                    $orientation = -90;
                    $rotator = true;
                    break;
                case 7:
                    $orientation = -270;
                    $rotator = true;
                    $flipper = array(false, true);
                    break;
                case 8:
                case 9:
                    $orientation = -270;
                    $rotator = true;
                    break;
                default:
                    $orientation = 0;
                    $rotator = true;
                    break;
            }

            if ( !$rotator && !$flipper) {
                return $file;
            }

            include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
            $editor = wp_get_image_editor($file);
            if (!is_wp_error($editor)) {

                if ($rotator === true) {
                    $editor->rotate( $orientation );
                }
                if ($flipper !== false) {
                    $editor->flip( $flipper[0], $flipper[1] );
                }
                // Save the image, overwriting the existing image
                $editor->save($file);

                // Drop the EXIF orientation flag, otherwise applications will try to rotate the image
                // before display it, and we don't need that to happen as we've corrected the orientation
                // Write the EXIF and IPTC metadata to the revised image
                if ( isset( $params['type'] ) && 'image/jpeg' == $params['type'] ) {
                    $this->remove_exif_from_image( $file );
                }

            }
        } // end if $exif

        // Finally, return the data that's expected
        return $params;
    }


    /**
     *
     * @source https://wordpress.org/plugins/va-removing-exif/
     *
     * @param $file
     * @return bool
     */
    function remove_exif_from_image( $file ) {

        if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
            $image = new \Imagick( $file );

            if ( $image->valid() ) {
                $image->setImageFormat( 'jpeg' );
                $image->setImageCompressionQuality( 100 );
                $image->stripImage();
                $image->writeImage( $file );
                $image->clear();
                $image->destroy();
            }
        } elseif ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) {
            $image = imagecreatefromjpeg( $file );

            imagejpeg( $image, $file, 100 );
            imagedestroy( $image );
        }

        return true;
    }

    /**
     * @since 2.2.600
     */
    public static function instance()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Upload_Orientation_Fix();

        return self::$instance;
    }

}

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Autoloader
 * Deprecated
 *
 * @class 		FV_Autoloader
 * @version		2.3.0
 * @package		WooCommerce/Classes/
 * @category	Class
 * @author 		WooThemes
 */
class FV_Autoloader {

    /**
     * Path to the includes directory
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor
     */
    public function __construct() {
        if ( function_exists( "__autoload" ) ) {
            spl_autoload_register( "__autoload" );
        }

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->include_path = FV::$INCLUDES_ROOT;
    }

    /**
     * Take a class name and turn it into a file name
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class( $class ) {
        return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
    }

    /**
     * Include a class file
     * @param  string $path
     * @return bool successful or not
     */
    private function load_file( $path ) {
        if ( $path && is_readable( $path ) ) {
            include_once( $path );
            return true;
        }
        return false;
    }

    /**
     * Auto-load WC classes on demand to reduce memory consumption.
     *
     * @param $class    string
     * @return bool
     */
    public function autoload( $class ) {

        $class = strtolower( $class );

        if ( strpos( $class, 'fv_' ) !== 0  ) {
            return false;
        }

        $file  = $this->get_file_name_from_class( $class );
        $path  = '';

        if ( strpos( $class, 'fv_admin_' ) === 0 ) {
            $path = FV::$ADMIN_ROOT;
        } elseif ( strpos( $class, 'fv_public' ) === 0 ) {
            $path = FV::$PUBLIC_ROOT;
        } else {
            $path = FV::$INCLUDES_ROOT;
        }

        //var_dump($path);

        /*
         * } elseif ( strpos( $class, 'wc_gateway_' ) === 0 ) {
            $path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 11 ) . '/';
            }
         */

        if ( empty( $path ) || ( ! $this->load_file( $path . $file ) ) ) {
            $this->load_file( FV::$INCLUDES_ROOT . $file );
        }
    }
}

new FV_Autoloader();

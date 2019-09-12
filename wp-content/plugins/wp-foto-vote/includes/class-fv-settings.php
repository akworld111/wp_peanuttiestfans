<?php
/**
 * Settings Helper
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.500
 */

defined('ABSPATH') or die("No script kiddies please!");

/**
 * @since 2.2.500
 *
 * @param string $option Setting key
 * @param mixed $default
 * @param string $section
 * @param mixed $min_length
 *
 * @return mixed
 */
function fv_setting( $option, $default = false, $section = false, $min_length = false ) {
    return FV_Settings::get( $option, $default, $section, $min_length );
}

/**
 * Class FV_Settings
 * @since 2.2.500
 */
class FV_Settings {
    public static $settings = array( 'fv' => null );
    public static $sections = array( 'fv' => 1 );

    /**
     * Get plugin setting, following new principe - save all in one DB variable
     * @since   2.2.103
     * @since   2.2.500 in this class
     *
     * @param string        $option Setting key
     * @param mixed         $default
     * @param string|bool   $section
     * @param mixed         $min_length
     *
     * @return mixed
     */
    public static function get($option, $default = false, $section = false, $min_length = false) {

        if ( !$section ) {
            $section = 'fv';
        } else {
            $section = 'fv-' . $section;
        }

        if ( !isset(self::$sections[$section]) ) {
            trigger_error("FV_Settings - section \"{$section}\" not registered!");
        }

        if ( empty(self::$settings[$section]) ) {
            self::$settings[$section] = get_option( $section, array() );
        }

        // Check is exists
        if ( !isset(self::$settings[$section][$option]) ) {
            return $default;
        }
        // Check is exists
        if ( isset(self::$settings[$section][$option]) && self::$settings[$section][$option] === 'on' ) {
            return true;
        }
        // Check length
        if ( $min_length !== false && $min_length > 0 ) {
            if ( is_numeric(self::$settings[$section][$option]) &&  self::$settings[$section][$option] < $min_length ) {
                return false;
            } elseif ( strlen(self::$settings[$section][$option]) < $min_length ) {
                return false;
            }
        }

        return self::$settings[$section][$option];
    }

    public static function set( $key, $option, $section = 'fv' ) {
        if ( !$section ) {
            $section = 'fv';
        } else {
            $section = 'fv-' . $section;
        }

        if ( empty(self::$settings[$section]) ) {
            self::get('');
        }

        self::$settings[$section][$key] = $option;
    }

    /**
     * Register new section
     * @param $section
     */
    public static function register_section( $section ) {
        if ( !isset( self::$sections[$section] ) ) {
            self::$sections[$section] = 1;
        }
    }

    /**
     * Register new section
     * @param $section
     */
    public static function reset_cache( $section = false ) {
        if ( $section ) {
            self::$settings[$section] = null;
        } else {
            self::$settings = array ( 'fv' => null );
        }
    }
    
    public static function may_be_add_defaults() {
    }
}

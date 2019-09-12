<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Files loader
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.500
 */
class FV_Templater {
    // Local Cache
    private static $paths = array();
    private static $urls = array();

    static function locateCustomInTheme($original_path, $in_theme_path, $file)
    {
        if (!empty(self::$paths[$in_theme_path . $file])) {
            return self::$paths[$in_theme_path . $file];
        }

        $template_path = '/wp-foto-vote/' . $in_theme_path . '/' . $file;

        if ( $template_path = locate_template($template_path) ) {
            self::$paths[$in_theme_path . $file] = $template_path;
            return $template_path;
        }
        // If nothing Found
        self::$paths[$in_theme_path . $file] = $original_path;
        return $original_path;
    }

    static function locate($skin, $file, $folder = 'skins')
    {
        if (!empty(self::$paths[$folder . $skin . $file])) {
            return self::$paths[$folder . $skin . $file];
        }

        $template_path = self::locateSkinPath($skin, $file, $folder);
        if ($template_path) {
            return $template_path;
        }

        $folderSlashed = trailingslashit($folder);

        $template_path = 'wp-foto-vote/' . $folderSlashed . $skin . '/' . $file;

        if ( $template_path = locate_template($template_path) ) {
            self::$paths[$folder . $skin . $file] = $template_path;
            return $template_path;
        }
        $template_path = FV::$THEMES_ROOT . $folderSlashed . $skin . '/' . $file;
        // Save to local Cache
        $template_path = apply_filters( 'fv/templater/locate', $template_path, $skin, $file, $folderSlashed );
        
        /**
         * Deprecated since 2.2.500
         */
        $template_path = apply_filters( 'fv_theme_path', $template_path, $skin, $file, $folderSlashed );

        if (file_exists($template_path)) {
            self::$paths[$folder . $skin . $file] = $template_path;
            return self::$paths[$folder . $skin . $file];
        }

        $template_path_arr = explode('wp-content', $template_path);
        if ( $template_path_arr && count($template_path_arr) > 1 ) {
            trigger_error('Template file not exists ' . $template_path_arr[1], E_USER_WARNING);
        }

        return false;
    }

    static function locateUrl($skin, $file, $folder = 'skins')
    {
        if (!empty(self::$urls[$folder . $skin . $file])) {
            return self::$urls[$folder . $skin . $file];
        }

        $template_url = FV_Skins::i()->locateUrl($skin, $file);
        if ($template_url) {
            self::$urls[$folder . $skin . $file] = $template_url;
            return $template_url;
        }


        $folderSlashed = trailingslashit($folder);

        ####### CHECK IN CHILD THEME
        $template_path_stylesheet = get_stylesheet_directory() . '/wp-foto-vote/' . $folderSlashed . $skin . '/' . $file;

        if ( file_exists($template_path_stylesheet) ) {
            self::$urls[$folder . $skin . $file] = get_stylesheet_directory_uri() . '/wp-foto-vote/' . $folderSlashed . $skin . '/' . $file;
            return self::$urls[$folder . $skin . $file];
        }

        ####### CHECK IN MAIN THEME
        $template_path_template = get_template_directory() . '/wp-foto-vote/' . $folderSlashed . $skin . '/' . $file;

        if ( $template_path_stylesheet != $template_path_template && file_exists($template_path_template) ) {
            self::$urls[$folder . $skin . $file] = get_template_directory_uri() . '/wp-foto-vote/' . $folderSlashed . $skin . '/' . $file;
            return self::$urls[$folder . $skin . $file];
        }
        ####### GO TO PLUGIN DIR
        $template_url = FV::$THEMES_ROOT_URL . $folderSlashed . $skin . '/' . $file;

        // Save to local Cache
        $template_url = apply_filters('fv/templater/locate_url', $template_url, $skin, $file, $folderSlashed);
        
        $template_url = apply_filters('fv_theme_url', $template_url, $skin, $file, $folderSlashed);

        if ( $template_url ) {
            self::$urls[$folder . $skin . $file] = $template_url;
            return $template_url;
        }

        return false;
    }

    public static function locateSkinPath( $skin, $file, $folder ) {
        switch ($folder){
            case 'skins':
                return FV_Skins::i()->locate($skin, $file);
                break;
            case 'winners':
                return FV_Winners_Skins::i()->locate($skin, $file);
                break;
            case 'contests_list':
                return FV_Contests_List_Skins::i()->locate($skin, $file);
                break;
            case 'leaders':
                return '';
                break;
        }
        return '';
    }

    public static function locateSkinUrl( $skin, $file, $folder ) {
        switch ($folder){
            case 'skins':
                return FV_Skins::i()->locateUrl($skin, $file);
                break;
            case 'winners':
                return FV_Winners_Skins::i()->locateUrl($skin, $file);
                break;
            case 'contests_list':
                return FV_Contests_List_Skins::i()->locateUrl($skin, $file);
                break;
            case 'leaders':
                return '';
                break;
        }
        return '';
    }

    /**
     * Render a template
     *
     * Allows child plugins to add CUSTOM THEMES by placing in addon plugins.
     *
     * @param  string $template_path Path to file
     * @param  array $variables An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
     * @param  bool $return false                Return data or output
     * @param  string $type "theme"              Type for apply filters ["theme" - is a photos list]
     * @param  string $require 'always'          'once' to use require_once() | 'always' to use require()
     *
     * @return string
     */
    public static function render( $template_path, $variables = array(), $return = false, $type = "theme", $require = 'always' ) {

        $template_path = apply_filters( 'fv_template_path', $template_path, $type );
        $variables = apply_filters( 'fv_template_variables', $variables, $type, $template_path );

        if ( !$template_path || !file_exists($template_path)  ) {
            FvLogger::addLog('Template file not exists! Type:' . $type, $template_path);

            if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_TPL ) {
                FvDebug::add( "Template file not exists! File:", $template_path );
            }
            return false;
        }

        extract( $variables );
        ob_start();


        if ( 'once' == $require ) {
            include_once ( $template_path );
        } else {
            include ( $template_path );
        }

        if ( $return ) {
            return ob_get_clean();
        } else {
            echo ob_get_clean();
        }

    }

    /**
     * Get file path in theme folder
     *
     * Allow in child themes rewrite path to it's folder by `apply_filters`
     *
     * @param  string $theme Theme name
     * @param  string $file_in_theme File name
     * @param  bool $recurs It is function calls recursive?
     *
     * @return string
     */
    public static function get_theme_path($theme, $file_in_theme, $recurs = false)
    {
        static $theme_path = array();
        if (empty($theme_path[$theme . $file_in_theme])) {
            $theme_path[$theme . $file_in_theme] = apply_filters(
                'fv_theme_path',
                trailingslashit(FV::$THEMES_ROOT . $theme) . $file_in_theme,
                $theme,
                $file_in_theme
            );
            //var_dump($theme_path);
        }

        // for leave support old field names in Themes as `unit.php` and `item.php`
        if (!file_exists($theme_path[$theme . $file_in_theme]) && !$recurs) {
            if ($file_in_theme == "list_item.php") {
                $theme_path[$theme . $file_in_theme] = self::get_theme_path($theme, "unit.php", true);
            } elseif ($file_in_theme == "single_item.php") {
                $theme_path[$theme . $file_in_theme] = self::get_theme_path($theme, "item.php", true);
            }
        }

        return $theme_path[$theme . $file_in_theme];
    }

    /**
     * Get file URL in theme folder
     *
     * Allow in child themes rewrite URL to it's folder by `apply_filters`
     *
     * @param  string $theme Theme name
     * @param  string $file_in_theme File name
     *
     * @return string
     */
    public static function get_theme_url($theme, $file_in_theme)
    {
        static $theme_url = array();

        if (empty($theme_url[$file_in_theme])) {
            $theme_url[$file_in_theme] = apply_filters(
                'fv_theme_url',
                trailingslashit(FV::$THEMES_ROOT_URL . $theme) . $file_in_theme,
                $theme,
                $file_in_theme
            );
        }
        return $theme_url[$file_in_theme];
    }

}
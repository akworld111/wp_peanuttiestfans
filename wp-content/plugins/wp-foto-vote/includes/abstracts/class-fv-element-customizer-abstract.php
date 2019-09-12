<?php

/**
 * Abstract class for easy integrate Customizer to different kinds of elements/blocks
 *
 * @package    FV
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.815
 */
abstract class FV_Element_Customizer_Abstract
{
    protected $customizerSlug = null;

    // Do not used for now
    protected $apiVersion;

    protected $supportsCustomizer = false;
    protected $customizerSettings = array();
    protected $customizerSectionTitle = '[CHANGE THIS]';
    protected $customizerSectionPriority = 162;

    protected $outputHandle = null;
    protected $outputCssPrefix = '';

    /**
     * Init
     */
    public function initCustomizer()
    {
        if ( $this->supportsCustomizer ) {
            if ( !null === $this->outputHandle ) {
                throw new \Exception( "'outputHandle' is not set!" );
            }
            if ( !$this->customizerSlug ) {
                throw new \Exception( "'customizerSlug' is not set!" );
            }

            $this->registerCustomizerSettings();

            add_action('customize_register', array($this, '_registerCustomizerFields'), 99 );
            add_action( 'customize_preview_init', array($this, '_registerCustomizerPreviewJS'), 11 );
        }
    }

    /**
     * Here you should define all fields
     * @since 2.2.815
     */
    public function registerCustomizerSettings() {

    }

    /**
     * enqueue customized CSS
     * @since 2.2.815
     * @access private
     */
    public function _enqueueOutputCustomizedCSS () {
        if ( ! $this->supportsCustomizer ) {
            return;
        }

        add_action( 'wp_footer', array($this, '_outputCustomizedCSS'), 9 );
    }
    
    /**
     * @since 2.2.815
     * @access private
     */
    public function _outputCustomizedCSS(){
        if ( ! $this->supportsCustomizer ) {
            return;
        }

        $skin_settings_css_map = array();
        $customized_css = array();
        $attribute_value = null;

        foreach ($this->customizerSettings as $setting_key=>$setting) {

            foreach ( $setting['css'] as $css_selector => $css_data ) {
                $attribute_value = $this->getCustomizedValue( $setting['key'] );
                $attribute_value_src = $attribute_value;

                if ( $css_data['units'] ) {
                    $attribute_value .= $css_data['units'];
                }

                if ( $css_data['important'] ) {
                    $attribute_value .= ' !important';
                }

                if ( $css_data['callback'] && is_callable($css_data['callback']) ) {
                    $customized_css[] = $css_data['callback']( $attribute_value, $attribute_value_src, $css_selector, $css_data, $setting );
                    continue;
                }

                if ( $css_data['media'] ) {
                    $customized_css[] = sprintf('@media (%s){ %s{%s: %s;} }', $css_data['media'], $css_selector, $css_data['attribute'], $attribute_value);
                } else {
                    $customized_css[] = sprintf('%s{%s: %s;}', $css_selector, $css_data['attribute'], $attribute_value);
                }
            }
        }

        if ( $this->outputHandle && ! wp_style_is($this->outputHandle, 'done') ) {
            wp_add_inline_style($this->outputHandle, implode(' ', $customized_css));
        } else {
            echo '<style type="text/css">', implode(' ', $customized_css), '</style>';
        }
    }

    /**
     * Add new setting for later generate it in Customizer
     * @param string $key
     * @param array $options
     * @param array $css_map    Format: ["css selector" => ["attribute"=>"color", "type"=>"css,style"]]
     *
     * @since 2.2.815
     */
    public function _registerCustomizerSetting($key, $options, $css_map = array() ) {

        $css_map_corrected = array();

        // Let's add more specific classes
        foreach ($css_map as $css_row_key => $css_row) {
            // Add defaults
            $css_row = array_merge(array('attribute' => '','type' => 'css','units' => '','media' => '','important' => '','callback' => false), $css_row);
            // Add prefix - mostly for skins
            $css_map_corrected[$this->outputCssPrefix . ' ' . $css_row_key] = $css_row;
        }

        // Fill empty data with defaults
        $options = array_merge(array(
            'key' => $key,
            'use_original_key' => false,    // DO not add a prefix
            'label' => '',
            'description' => '',
            'section' => $this->customizerSlug,
            'type' => '',
            'type_class' => '',
            'choices' => array(),

            'default' => '',
            'setting_type' => 'option',         // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
            'setting_transport' => 'postMessage',
            'sanitize_callback' => '',

            'css' => $css_map_corrected,
        ), $options);

        if ( !$options['use_original_key'] ) {
            $this->customizerSettings[ $this->customizerSlug . $key ] = $options;
        } else {
            $this->customizerSettings[ $key ] = $options;
        }
    }

    /**
     * @return array
     * @since 2.2.815
     */
    public function _getCustomizerSettings() {
        return $this->customizerSettings;
    }

    /**
     * @param string $key
     * @return mixed|void
     * @throws Exception
     *
     * @since 2.2.815
     */
    public function getCustomizedValue( $key ) {
        if ( ! isset( $this->customizerSettings[$this->customizerSlug . $key ] ) ) {
            throw new \Exception( "Customized setting '{$key}' is not exists!" );
        }

        $value = get_option( $this->customizerSlug . $key, null );

        if ( null === $value ) {
            return $this->customizerSettings[$this->customizerSlug . $key ]['default'];
        }

        return $value;
    }


    /**
     * @param WP_Customize_Manager $wp_customize
     * @since 2.2.815
     */
    public function _registerCustomizerFields($wp_customize)
    {

        $wp_customize->add_section( $this->customizerSlug,
            array(
                'title' => $this->customizerSectionTitle,
                //'description' => esc_html__( 'Here you can customize modal styles.' ),
                'panel' => 'wp_foto_vote', // Only needed if adding your Section to a Panel
                'priority' => $this->customizerSectionPriority, // Not typically needed. Default is 160
                'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
                'theme_supports' => '', // Rarely needed
                'active_callback' => '', // Rarely needed
                'description_hidden' => 'false', // Rarely needed. Default is False
            )
        );

        foreach ($this->customizerSettings as $setting_key=>$setting) {
            $wp_customize->add_setting( $setting_key,
                array(
                    'default' => $setting['default'],
                    'type' => $setting['setting_type'], // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                    'transport' => $setting['setting_transport'],
                    'sanitize_callback' => $setting['sanitize_callback']
                )
            );

            if ( $setting['type_class'] && class_exists($setting['type_class']) ) {
                $wp_customize->add_control(
                    new $setting['type_class'] (
                        $wp_customize,
                        $setting_key,
                        array(
                            'label' => $setting['label'],
                            'description' => $setting['description'],
                            'section' => $setting['section'],
                            'priority' => 10, // Optional. Order priority to load the control. Default: 10
                            'type' => $setting['type'],
                            'choices' => $setting['choices'],
                            'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
                        )
                    )
                );
            } else {
                $wp_customize->add_control($setting_key,
                    array(
                        'label' => $setting['label'],
                        'description' => $setting['description'],
                        'section' => $setting['section'],
                        'priority' => 10, // Optional. Order priority to load the control. Default: 10
                        'type' => $setting['type'],
                        'choices' => $setting['choices'],
                        'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
                    )
                );
            }

        }

    }

    /**
     * Register Script for dynamically update skin design
     * @since 2.2.815
     */
    public function _registerCustomizerPreviewJS()
    {
        $skin_settings_css_map = array();

        foreach ($this->customizerSettings as $setting_key=>$setting) {
            $skin_settings_css_map[$setting_key] = $setting['css'];
        }

        wp_localize_script( 'fv-customizer-preview', 'FV_Skin_'.$this->slug, $skin_settings_css_map );

        wp_add_inline_script(
            'fv-customizer-preview',
            "var FV_Skins_Settings = FV_Skins_Settings || {}; FV_Skins_Settings = jQuery.extend(FV_Skins_Settings, FV_Skin_{$this->slug});",
            'before'
        );
    }
    
}
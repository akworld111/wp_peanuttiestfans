<?php

class FV_WP_Social_Login_Integration {

    public static function render() {
        if ( has_action('wordpress_social_login') ) {
            add_filter('wsl_render_auth_widget_alter_provider_icon_markup', array('FV_WP_Social_Login_Integration', 'wsl_filter_icon'), 10, 3);
            do_action('wordpress_social_login');
            remove_filter('wsl_render_auth_widget_alter_provider_icon_markup', array('FV_WP_Social_Login_Integration', 'wsl_filter_icon'), 10);
        }  elseif ( shortcode_exists("oa_social_login") ) {
            echo do_shortcode("[oa_social_login]");
        } else {
            echo "Please install WordPress Social Login plugin!";
        }
    }

    public static function wsl_filter_icon( $provider_id, $provider_name, $authenticate_url )
    {
        $wrap_class = strtolower($provider_id);
        $icon_class = $wrap_class;
        $icon_text = '';
        switch ( strtolower( $provider_id ) ) {
            case 'vkontakte':
                $wrap_class = 'vk';
                $icon_class = 'vk2';
                break;
            case 'odnoklassniki':
                $wrap_class = 'ok';
                $icon_text = 'OK';
                break;
            case 'google':
                $wrap_class = 'google-plus';
                $icon_class = 'googleplus3';
                break;
            case 'mailru':
                $icon_text = '<span>@</span>M';
                break;
        }
        ?>
        <a 
            rel           = "nofollow"
            href          = "<?php echo esc_attr($authenticate_url); ?>"
            data-provider = "<?php echo $provider_id ?>"
            class         = "wp-social-login-provider wp-social-login-provider-<?php echo strtolower( $provider_id ); ?>"
        >
            <li class="sw-<?php echo esc_attr($wrap_class); ?>">
                <span class="sw-share-button <?php echo !$icon_text ? 'fvicon-' . esc_attr($icon_class) : ''; ?>"><?php echo $icon_text ? $icon_text : ''; ?></span>
            </li>
        </a>
        <?php
    }

}
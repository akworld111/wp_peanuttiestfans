<?php

function fv_get_placeholder_img_arr($width = 440, $height = 250) {
    return array(FV::$ASSETS_URL . 'img/no-photo.png', $width, $height);
}

function fv_admin_echo_switch_toggle($name, $value) {
    echo fv_admin_get_switch_toggle($name, $value);
}

function fv_admin_get_switch_toggle($name, $value, $id = '') {
    $output = '<div class="switch switch-toggle';
    if ($value) {
        $output .= ' switch-toggle-checked';
    }
    $id_html = '';
    if ( $id ) {
        $id_html = ' id="' . $id;
    }
    $output .= '"><input type="hidden" name="' . $name . '" value="' . (int)$value . '"' . $id_html . '" class="of-input switch-toggle-input">';
    $output .= '<label class="switch-toggle-label"></label></div>';
    return $output;
}

function fv_get_tooltip_code($title) {
    echo ' <span class="tooltip_box" title="' . $title . '">
            <i class="fvicon fvicon-info"></i></span> ';
}
function fv_tooltip_code($title) {
    return ' <span class="tooltip_box" title="' . $title . '">
            <i class="fvicon fvicon-info"></i></span> ';
}

function fv_get_td_tooltip_code($title) {
    return '<td class="fv-tooltip">
            <div class="box" title="' . $title . '">
                <span class="dashicons dashicons-info"></span>
                <div class="position topleft"><i></i></div>
            </div>
          </td>';
}


function fv_filter_update_checks($queryArgs) {
    $key = get_option('fv-update-key', false);
    if ( $key ) {
        $queryArgs['license_key'] = $key;
        $queryArgs['new'] = true;
    }
    $queryArgs['php_ver'] = phpversion();
    return $queryArgs;
}

/**
 * Check IF expiration data changed - if yes > Run key details update
 * @param array $pluginInfo
 * @return array
 * @since 2.2.300
 */
function fv_check_updates_may_be_need_refresh_key_data ($pluginInfo) {
    $key = get_option('fv-update-key', false);
    if ( !isset($pluginInfo->license_key_expiration) || empty($key) ) {
        return $pluginInfo;
    }
    $key_details = get_option('fv-update-key-details', false);
    if ( isset($key_details['expiration']) && $key_details['expiration'] != $pluginInfo->license_key_expiration )
    {
        fv_update_key_and_get_details( $key );
    }
    return $pluginInfo;
}

/**
 * Refresh License key data from server
 * @param string $key
 * @return array of update key data
 */
function fv_update_key_and_get_details ($key) {
    $key = trim($key);
    if ( empty( $key ) ) {
        wp_add_notice(__('Key has been empty!', 'fv'), "warning");
        return false;
    }

    $response = wp_remote_fopen (UPDATE_SERVER_URL . '?action=get_key_info&slug=wp-foto-vote&license_key=' . $key);
    $response_arr = @(array)json_decode($response);

    fv_log('fv_update_key_before_save result', $response_arr);

    if ( is_array($response_arr) && isset($response_arr['expiration']) && isset($response_arr['status']) ) {
        $response_arr['last_update'] = current_time('mysql');
        update_option('fv-update-key-details', $response_arr, false);
        update_option('fv-update-key', $key, true);

        fv_log('fv_update_key_before_save Go Save');
        return true;
    } else {
        wp_add_notice( 'New key data is not correct!', "warning");
        fv_log('fv_update_key_before_save (error) : data is not correct! Key: '.$key, $response);
        return false;
    }

}
/**
 *
 * @since ver 2.2.300
 * @param int $status
 * @return string
 */
function fv_get_update_key_status_as_text ($status) {
    $statuses = array(
        0 => 'Empty',
        1 => 'Inactive',
        2 => 'Active',
        3 => 'Invalid domain',
        4 => 'Expired (Updates inactive)',
    );
    return isset( $statuses[$status] ) ? $statuses[$status] : '-';
}

/**
 *
 * @since ver 2.2.503
 * @return array
 */
function fv_get_winners_pick_types () {
    return array(
        'auto'              => __('Automatically - most voted', 'fv'),
        'auto_rand'         => __('Automatically - random', 'fv'),
        'auto_rand_top10'   => __('Automatically - random from TOP 10', 'fv'),
        'auto_rand_top20'   => __('Automatically - random from TOP 20', 'fv'),
        'manual'            => __('Manual', 'fv'),
    );
}


/**
 * Hook on "update otions" - for "flush_rewrite_rules" if permalink changed
 * @since ver 2.2.2
 * @param string $new_settings
 * @return array
 */
function fv_filter_update_settings ($new_settings) {
    $old_settings = get_option('fv', false);

    if ( is_array($old_settings) && !isset($old_settings['single-permalink']) || $old_settings['single-permalink'] !== $new_settings['single-permalink'] ){
        // If single photo permalink photo changed - then need call "flush_rewrite_rules"
        fv_log('schedule flush_rewrite_rules');
        add_option('fv-schedule-flush_rewrite_rules', true, false, 'yes');
    }
    return $new_settings;
}


function fv_add_update_message( $plugin_data, $r ) {
    //var_dump($plugin_data);
    //var_dump($r);
    //FvLogger::addLog('fv_add_update_message', $plugin_data );
    if ( isset($r->upgrade_notice) ) {
        $notices = explode('#', $r->upgrade_notice);
        if (is_array($notices) && ( count($notices) == 3 || count($notices) == 4 ) ) {
            printf('&nbsp; <strong>%s</strong> %s %s', __($notices[0], 'fv'), __($notices[1], 'fv'), $notices[2]);

            if ( isset($notices[4]) ) {
                echo $notices[4];
            }

        } else {
            echo $r->upgrade_notice;
        }
    }
}


/**
 * Print out option html elements for role selectors.
 *
 * @since 2.1.0
 *
 * @param string $selected Slug for the role that should be already selected.
 */
function fv_dropdown_roles( $selected = '' ) {

    $selected_arr = $selected ? explode(',' , $selected) : array();

    $p = '';
    $r = '';

    $editable_roles = array_reverse( get_editable_roles() );

    foreach ( $editable_roles as $role => $details ) {
        $name = translate_user_role($details['name'] );
        if ( in_array($role, $selected_arr) ) // preselect specified role
            $p .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
        else
            $r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
    }
    echo $p . $r;
}
<?php

/**
 * @return array
 * @since 2.3.05
 */
function fv_get_icons() {
    return array(
        'heart'  => 'e607',
        'heart2' => 'e608',
        'heart3' => 'e642',
        'like'   => 'e643',
        'star'   => 'e632',
        'star2'   => 'e904',
        'thumbs-up' => 'e609',
        'checkmark' => 'e601',
        'fvicon-checkmark-circle' => 'e637',
    );
}

/**
 * Validate data
 *
 * @param $validation_rules
 * @param array $data Example: [
 *   'order_id'      => 'required|integer',
 *   'reference'     => 'required'
 * ]
 * @param array $filter_rules
 * @param bool $send_AJAX_error
 *
 * @return array
 *
 * @throws Exception
 *
 * @since 2.3.00
 */
function fv_params_validate( $data, $validation_rules, $filter_rules = [], $send_AJAX_error = true ) {
    $gump = new GUMP();

    $gump->validation_rules( $validation_rules );

    if ( $filter_rules ) {
        $gump->filter_rules($filter_rules);
    }

    $validated_data = $gump->run($_REQUEST);

    if( $send_AJAX_error && $validated_data === false ) {
        fv_AJAX_response( false, 'Errors: ' . implode(PHP_EOL, $gump->get_errors_array(true)) );
    }

    return $validated_data;
}
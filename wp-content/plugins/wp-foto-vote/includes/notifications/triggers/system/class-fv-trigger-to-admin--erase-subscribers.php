<?php

use BracketSpace\Notification;

/**
 * Class FV_Trigger_To_Admin__Reminder_To_Erase_Subscribers
 * @since 2.3.00
 */
class FV_Trigger_To_Admin__Reminder_To_Erase_Subscribers extends FV_Abstract_Trigger_To_Admin__System {

    public function __construct() {
        parent::__construct(
            'fv/system/to-admin/reminder-to-erase-subscribers',
            __( 'Reminder to erase Subscribers', 'fv' ),
            'fv/notification/system/to-admin/reminder-to-erase-subscribers'
        );
    }

}
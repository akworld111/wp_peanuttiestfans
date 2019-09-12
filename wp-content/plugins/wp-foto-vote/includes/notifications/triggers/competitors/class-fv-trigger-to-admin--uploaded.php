<?php

use BracketSpace\Notification;

/**
 * Class FV_Trigger_To_Admin__Uploaded
 * @since 2.3.00
 */
class FV_Trigger_To_Admin__Uploaded extends Notification\Abstracts\Trigger {

    /** @var  FV_Competitor $competitor */
    public $competitor;
    /** @var  FV_Contest $contest */
    public $contest;

    public function __construct() {

        // 1. Slug, can be prefixed with your plugin name.
        // 2. Title, should be translatable.
        parent::__construct(
            'fv/competitor/to-admin/uploaded',
            __( 'New *competitor* submitted', 'fv' )
        );

        // 1. Action hook.
        // 2. (optional) Action priority, default: 10.
        // 3. (optional) Action args, default: 1.
        // It's the same as add_action( 'my_action_hook', 'callback', 10, 2 ) with
        // only difference - the callback is always action() method (see below).
        $this->add_action( 'fv/notification/competitor/to-admin/uploaded', 10, 2 );

        // 1. Trigger group, should be translatable.
        // This is optional, Group is displayed in the Trigger select.
        $this->set_group( 'WP Foto Vote (to admin)' );

        // 1. Trigger description, should be translatable.
        // This is optional, Description is displayed in the Trigger select.
        $this->set_description(
            'Fires when user submits form'
        );

    }

    /**
     * Assigns action callback args to object
     * Return `false` if you want to abort the trigger execution
     *
     * @param FV_Competitor $competitor
     * @param FV_Contest $contest
     *
     * @return mixed void or false if no notifications should be sent
     */
    public function action( $competitor, $contest ) {

        // We can assign any property here, whole object will be accessible in Merge Tag resolver.
        $this->competitor = $competitor;
        $this->contest = $contest;
    }

    /**
     * Registers attached merge tags
     *
     * @return void
     */
    public function merge_tags() {
        FV_Notification_Integration__Competitor::get()->_add_admin_default_tags( $this );
    }
}
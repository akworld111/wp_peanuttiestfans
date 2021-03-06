<?php

use BracketSpace\Notification;

/**
 * Class FV_Trigger_To_User__Winner
 * @since 2.3.00
 */
class FV_Trigger_To_User__Winner extends Notification\Abstracts\Trigger {

    /** @var  FV_Competitor $competitor */
    public $competitor;
    /** @var  FV_Contest $contest */
    public $contest;

    public function __construct() {

        // 1. Slug, can be prefixed with your plugin name.
        // 2. Title, should be translatable.
        parent::__construct(
            'fv/competitor/to-user/winner',
            __( '*Competitor* is one of winners', 'fv' )
        );

        // 1. Action hook.
        // 2. (optional) Action priority, default: 10.
        // 3. (optional) Action args, default: 1.
        // It's the same as add_action( 'my_action_hook', 'callback', 10, 2 ) with
        // only difference - the callback is always action() method (see below).
        $this->add_action( 'fv/notification/competitor/to-user/winner', 10, 2 );

        // 1. Trigger group, should be translatable.
        // This is optional, Group is displayed in the Trigger select.
        $this->set_group( 'WP Foto Vote (to user)' );

        // 1. Trigger description, should be translatable.
        // This is optional, Description is displayed in the Trigger select.
        $this->set_description(
            'Competitor has have winner place in contest (only for "Auto Winners pick")'
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
        FV_Notification_Integration__Competitor::get()->_add_user_default_tags( $this );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\StringTag( array(
            'slug'        => 'competitor_place',
            'name'        => 'Competitor Place',
            'description' => '1st place',
            'example'     => true,
            'resolver'    => function( $trigger ) {
                return FV_Notifier::_get_competitor_notification_tags_value( 'competitor_place', $trigger->competitor );
            },
        ) ) );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\StringTag( array(
            'slug'        => 'competitor_votes',
            'name'        => 'Competitor Votes',
            'description' => '955 or 7.5/10',
            'example'     => true,
            'resolver'    => function( $trigger ) {
                return FV_Notifier::_get_competitor_notification_tags_value( 'competitor_votes', $trigger->competitor );
            },
        ) ) );
    }
}
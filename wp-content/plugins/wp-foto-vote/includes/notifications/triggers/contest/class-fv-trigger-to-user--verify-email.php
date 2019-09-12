<?php

use BracketSpace\Notification;

/**
 * Class FV_Trigger_To_User__Verify_Email
 *  * @since 2.3.00
 */
class FV_Trigger_To_User__Verify_Email extends Notification\Abstracts\Trigger {

    /** @var array $data */
    public $data;

    public function __construct() {

        // 1. Slug, can be prefixed with your plugin name.
        // 2. Title, should be translatable.
        parent::__construct(
            'fv/contest/to-user/verify-email',
            __( '*Voting* - please verify your email', 'fv' )
        );

        // 1. Action hook.
        // 2. (optional) Action priority, default: 10.
        // 3. (optional) Action args, default: 1.
        // It's the same as add_action( 'my_action_hook', 'callback', 10, 2 ) with
        // only difference - the callback is always action() method (see below).
        $this->add_action( 'fv/notification/contest/to-user/verify-email', 10, 1 );

        // 1. Trigger group, should be translatable.
        // This is optional, Group is displayed in the Trigger select.
        $this->set_group( 'WP Foto Vote (to user)' );

        // 1. Trigger description, should be translatable.
        // This is optional, Description is displayed in the Trigger select.
        $this->set_description(
            'Fires when user fill data in Subscribe modal'
        );

    }

    /**
     * Assigns action callback args to object
     * Return `false` if you want to abort the trigger execution
     *
     * @param array $data
     *
     * @return mixed void or false if no notifications should be sent
     */
    public function action( $data ) {

        // We can assign any property here, whole object will be accessible in Merge Tag resolver.
        $this->data = $data;
    }

    /**
     * Registers attached merge tags
     *
     * @return void
     */
    public function merge_tags() {
        $this->add_merge_tag( new Notification\Defaults\MergeTag\StringTag( array(
            'slug'        => 'user_name',
            'name'        => 'User name',
            'description' => 'Max',
            'example'     => true,
            'resolver'    => function( $trigger ) {
                return $trigger->data['user_name'];
            },
        ) ) );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\EmailTag( array(
            'slug'        => 'user_email',
            'name'        => 'User email',
            //'description' => '',
            'example'     => false,
            'resolver'    => function( $trigger ) {
                return $trigger->data['user_email'];
            },
        ) ) );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\UrlTag( array(
            'slug'        => 'verify_link',
            'name'        => 'Verify link',
            'description' => 'Link to verify vote',
            'example'     => false,
            'resolver'    => function( $trigger ) {
                return $trigger->data['verify_link'];
            },
        ) ) );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\StringTag( array(
            'slug'        => 'verify_hash',
            'name'        => 'Verify code',
            'description' => 'Code to verify vote',
            'example'     => false,
            'resolver'    => function( $trigger ) {
                return $trigger->data['verify_hash'];
            },
        ) ) );
    }
}
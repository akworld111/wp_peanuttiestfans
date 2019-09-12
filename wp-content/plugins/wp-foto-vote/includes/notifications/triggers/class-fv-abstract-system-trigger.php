<?php

use BracketSpace\Notification;

/**
 * Class FV_Abstract_Trigger_To_Admin__System
 * @since 2.3.00
 */
class FV_Abstract_Trigger_To_Admin__System extends Notification\Abstracts\Trigger {

    /** @var array $data */
    public $data;

    public function __construct( $slug, $title, $action, $description = '' ) {

        // 1. Slug, can be prefixed with your plugin name.
        // 2. Title, should be translatable.
        parent::__construct( $slug, $title );

        // 1. Action hook.
        // 2. (optional) Action priority, default: 10.
        // 3. (optional) Action args, default: 1.
        // It's the same as add_action( 'my_action_hook', 'callback', 10, 2 ) with
        // only difference - the callback is always action() method (see below).
        $this->add_action( $action, 10, 1 );

        // This is optional, Group is displayed in the Trigger select.
        $this->set_group( 'WP Foto Vote system notification (to admin)' );

        $this->set_description( $description );

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
            'slug'        => 'message',
            'name'        => 'Error or warning message',
            //'description' => 'Max',
            'example'     => false,
            'resolver'    => function( $trigger ) {
                return $trigger->data['message'];
            },
        ) ) );

        $this->add_merge_tag( new Notification\Defaults\MergeTag\EmailTag( array(
            'slug'        => 'file',
            'name'        => 'File, that generated message (can be empty)',
            //'description' => '',
            'example'     => false,
            'resolver'    => function( $trigger ) {
                return $trigger->data['file'];
            },
        ) ) );

    }
}
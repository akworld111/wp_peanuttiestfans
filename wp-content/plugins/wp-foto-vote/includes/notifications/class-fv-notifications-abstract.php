<?php

/**
 * Class FV_Notification_Integration__Abstract
 */
abstract class FV_Notification_Integration__Abstract {

    function __construct () {
        add_action( 'init', array($this, 'add_notification_triggers'), 11 );
    }

    /**
     * @throws Exception
     */
    public function add_notification_triggers() {
        global $pagenow;
        if ( !function_exists("register_trigger") ) {
            if ( 'plugins.php' != $pagenow ) {
                wp_add_notice("WP Foto Vote :: Notifications plugin not loaded!", "warning");
            }
            return;
        }

        $all_triggers = $this->_get_triggers();

        foreach ($all_triggers as $trigger) {
            // Fix for old PFA addon
            if ( empty($trigger['class']) ) {
                continue;
            }
            register_trigger( new $trigger['class'] );
        }
    }
    
    /**
     * @return array
     */
    abstract function _get_triggers();

    /**
     * @param string    $slug
     * @return string
     */
    abstract function _get_notification_template($slug);

    /**
     * @param \BracketSpace\Notification\Abstracts\Trigger $trigger
     * @param array $tags
     * @since 2.3.00
     */
    function _register_tags ( $trigger, $tags = [] ) {

        $class = '';
        foreach ( $tags as $tag_key => $tag_data ) {
            $class = 'BracketSpace\\Notification\\Defaults\\MergeTag\\' . $tag_data[0];
            $trigger->add_merge_tag( new $class( array(
                'slug'        => $tag_key,
                'name'        => $tag_data[2],
                'description' => isset($tag_data[3]) ? $tag_data[3] : '',
                'example'     => ! isset($tag_data[4]) ? true : $tag_data[4],
                'resolver'    => function( $trigger ) use ($tag_key, $tag_data) {
//                    if ( defined("TEST_NOTIFICATION") ) {
//                        return '{' . $tag_key . '}';
//                    }
                    if ( 'competitor' === $tag_data[1] ) {
                        return FV_Notifier::_get_competitor_notification_tags_value( $tag_key, $trigger->competitor );
                    } else {
                        return FV_Notifier::_get_contest_notification_tags_value( $tag_key, $trigger->contest );
                    }
                },
            ) ) );
        }
    }
}
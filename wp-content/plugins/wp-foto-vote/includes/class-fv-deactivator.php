<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      2.2.503
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Deactivator {

	/**
	 * Add tables to database when plugin activates
	 *
	 * @since    2.2.503
	 */
	public static function deactivate() {
		FV_Cron::deregister();
		add_option('fv-schedule-flush_rewrite_rules', true);

        /**
         * Delete all plugin data
         * @since 2.3.07
         */
		if ( get_option('fv-full-uninstall', false) ) {

		    try {
		        // Remove all contests
		        $contests = ModelContest::q()->find();

		        foreach ($contests as $contest) {
                    $contest->delete();
                }

                wp_delete_post( fv_setting('single-page') );

                foreach (FV_Admin::get_registered_settings(true) as $setting_key) {
                    delete_option($setting_key);
                }
                delete_option('fv');
                delete_option(FV::ADDONS_OPT_NAME);

                foreach ( FV_Skins::i()->getSkins() as $skin) {
                    foreach ( $skin->_getCustomizerSettings() as $option_key => $option ) {
                        delete_option($option_key);
                        fv_log( "deleted option", $option_key );
                    }
                }


                foreach ( FV_Image_Lightbox::i()->_getCustomizerSettings() as $option_key => $option ) {
                    delete_option($option_key);
                    fv_log( "deleted option", $option_key );
                }
                foreach ( FV_Customizer__Design::i()->_getCustomizerSettings() as $option_key => $option ) {
                    delete_option($option_key);
                    fv_log( "deleted option", $option_key );
                }

                // Remove an email notifications
                $all_notifications = get_posts(array('post_type' => 'notification', 'numberposts' => -1));
                foreach ($all_notifications as $notification_one) {
                    wp_delete_post($notification_one->ID, true);
                }

                // Delete tables from DB
                $my_db = new FV_DB;
                $my_db->clearAllData();

            } catch (Exception $e) {
		        echo "Could no remove plugin data!", $e->getMessage();
		        fv_log( "Could no remove plugin data!", $e->getMessage() );
            }
        }
	}

}

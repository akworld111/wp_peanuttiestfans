<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.2.073
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Activator {

	/**
	 * Add tables to database when plugin activates
	 *
	 * @since    2.2.073
	 */
	public static function activate() {
        FV::install();
		FV_Cron::register();
	}

}

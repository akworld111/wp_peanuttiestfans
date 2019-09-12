<?php
/**
 * Crn actions
 *
 * @since      2.2.503
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Cron {

	public static function execute () {
		FV_Admin_Winners::CRON_finish_contests();
		FV_GDPR_Tasks::run();
	}
	
	public static function register () {
		if (! wp_next_scheduled ( 'fv_cron' )) {
			fv_log( 'FV CRON activated!');
			wp_schedule_event(time(), '20min', 'fv_cron');
		}
	}		
	
	public static function deregister () {
		fv_log( 'FV CRON deactivated!');
		wp_clear_scheduled_hook('fv_cron');
	}
		
	public static function add_custom_schedules__filter( $schedules ) {
		$schedules['20min'] = array(
			'interval'  => HOUR_IN_SECONDS / 3,
			'display'   => 'Every 20 Minutes',
		);

		return $schedules;
	}

}

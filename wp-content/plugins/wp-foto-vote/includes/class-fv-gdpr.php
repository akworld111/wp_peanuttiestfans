<?php
/**
 * Cron tasks
 *
 * @since      2.3.00
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_GDPR_Tasks {

	/**
	 * Add tables to database when plugin activates
	 */
	public static function run() {
		self::erase_old_votes();
		self::erase_competitors_ip();
		self::reminder_to_erase_subscribers();
	}
	
	static function erase_old_votes() {
		$erase_in_days = (int) get_option('fv-erase-votes-log');

		if ( ! $erase_in_days ) {
			return;
		}

		$ended_contests = ModelContest::q()
			//->whereVotingDatesExpired()
			->where( 'status', FV_Contest::FINISHED )
			->where_early( 'date_finish', strtotime("-{$erase_in_days} day", current_time('timestamp', 0)) )
			->leftJoin( ModelMeta::query()->tableName(), "ct_meta", "`ct_meta`.`contest_id` = `t`.`id` and `ct_meta`.`meta_key` = 'votes_erased'", array(), '`ct_meta`.`meta_key` IS NULL' )
			->find();

		foreach ($ended_contests as $contest) {
			$deleted = ModelVotes::q()->deleteByContestID( $contest->id );
			$contest->meta()->create('votes_erased', 1);
			fv_log("FV_GDPR_Tasks :: removed {$deleted} votes rows for contest #{$contest->id}.");
		}
	}

	static function erase_competitors_ip() {
		$erase_in_days = (int) get_option('fv-erase-competitors-ip');

		if ( ! $erase_in_days ) {
			return;
		}

		$ended_contests = ModelContest::q()
			//->whereVotingDatesExpired()
			->where( 'status', FV_Contest::FINISHED )
			->where_early( 'date_finish', strtotime("-{$erase_in_days} day", current_time('timestamp', 0)) )
			->leftJoin( ModelMeta::query()->tableName(), "ct_meta", "`ct_meta`.`contest_id` = `t`.`id` and `ct_meta`.`meta_key` = 'competitors_ip_erased'", array(), '`ct_meta`.`meta_key` IS NULL' )
			->find();

		global $wpdb;
		$competitor_table_name = ModelCompetitors::q()->tableName();

		foreach ($ended_contests as $contest) {
			$deleted = $wpdb->query( "UPDATE `{$competitor_table_name}` SET `user_ip` = '' WHERE `contest_id` = '{$contest->id}';" );
			$contest->meta()->create('competitors_ip_erased', 1);
			fv_log("FV_GDPR_Tasks :: cleared {$deleted} IPs for contest #{$contest->id}.");
		}
	}

	static function reminder_to_erase_subscribers () {
		$erase_in_days = (int) get_option('fv-reminder-to-erase-subscribers');

		if ( ! $erase_in_days ) {
			return;
		}

		$ended_contests = ModelContest::q()
			//->whereVotingDatesExpired()
			->where( 'status', FV_Contest::FINISHED )
			->where_early( 'date_finish', strtotime("-{$erase_in_days} day", current_time('timestamp', 0)) )
			->leftJoin( ModelMeta::query()->tableName(), "ct_meta", "`ct_meta`.`contest_id` = `t`.`id` and `ct_meta`.`meta_key` = 'reminder_to_erase_subscribers'", array(), '`ct_meta`.`meta_key` IS NULL' )
			->find();

		$message = '';

		foreach ($ended_contests as $contest) {
			$contest->meta()->create('reminder_to_erase_subscribers', 1);

			$subscribers_count = ModelSubscribers::query()->where('contest_id', $contest->id)->total_count();

			if ( !$subscribers_count ) {
				continue;
			}
			$message .= sprintf('Contest "%s" (%s) has %d subscribers that should to be removed.' , $contest->name, $contest->getAdminUrl(), $subscribers_count ) ;

			fv_log("FV_GDPR_Tasks :: reminder_to_erase_subscribers triggered for contest #{$contest->id}.");
			echo("FV_GDPR_Tasks :: reminder_to_erase_subscribers triggered for contest #{$contest->id}.");
		}

		if ( $message ) {
			// Contest Name, Contest Link, Subscribers Link
			FV_Notifier::sendCustomNotification('system/to-admin/reminder-to-erase-subscribers', ['message'=>$message]);
		}
	}

}

<?php
/**
 * Process admin actions like "Add contest", "Add Form", etc
 *
 * @package    FV
 * @subpackage admin
 * @author     Maxim K <support@wp-vote.net>
 * @since      2.2.503 
 */
class FV_Admin_Winners
{
    
    public static function CRON_finish_contests()
    {
        fv_log( 'Run FinishContests >> ' . date('Y-m-d H:i:s') );

        // Прошедшие конкурсы не помеченные как законченные
        $expiredContests = ModelContest::q()->whereVotingDatesExpired()
            ->where('status', FV_Contest::PUBLISHED)
            ->find();

        /** @var FV_Contest $contest */
        foreach ($expiredContests as $contest) {
            $contest->finish();
            fv_log('Finish Contest >> ' . $contest->id);
        }
    }

    public static function AJAX_process_manual_pick()
    {
        if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv_winners_do_manual_pick_nonce', false, false) ) {
            fv_AJAX_response( false, 'no secure' );
        }
        
        $gump = new GUMP();

        GUMP::set_field_name('competitor_id', 'Competitor');

        $gump->validation_rules(array(
            'contest_id'        => 'required|integer',
            'place'             => 'required|integer',
            'place_caption'     => 'max_len,100',
            'competitor_id'     => 'required|integer',
        ));

        $validated_data = $gump->run($_REQUEST);

        if($validated_data === false) {
            fv_AJAX_response( false, '===== Form errors: =====', array('errors'=>$gump->get_errors_array(true)) );
        }

        $place = $validated_data['place'];

        $competitor = new FV_Competitor( $validated_data['competitor_id'] );

        if ( $competitor->contest_id != $validated_data['contest_id'] ) {
            fv_AJAX_response( false, 'invalid Contest!' );
        }

        $competitor->place = $place;
        if ( isset($validated_data['place_caption']) ) {
            $competitor->place_caption = trim($validated_data['place_caption']);
        }
        $competitor->save();

        do_action('fv/winners/manual_picked', $competitor);

        fv_AJAX_response(true, 'ready');
    }

    /**
     * @since 2.2.503
     */
    public function AJAX_winners_get_entries()
    {

        if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv-winners-get-entries', false, false) ) {
            fv_AJAX_response( false, 'no secure' );
        }

        if (!isset($_REQUEST['contest_id'])) {
            fv_AJAX_response( false, 'no contest_id' );
        }
        $contest = ModelContest::q()->findByPK( $_REQUEST['contest_id'] );

        if ( empty($contest) ) {
            fv_AJAX_response( false, 'invalid Contest!' );
        }

        $competitors = ModelCompetitors::q()->where('contest_id', $contest->ID)
            ->where_custom( 'place', 'IS NULL' )
            ->sort_by_votes( $contest->voting_type )
            ->what_fields( array('id', 'name', 'url', 'image_id', 'votes_average', 'votes_count') )
            ->find();

        $competitorsArr = array();

        foreach ($competitors as $competitor) {
            $competitorsArr[] = array(
                'id' => $competitor->ID,
                'text' => $competitor->name . ' [' . $competitor->getVotes(false, $contest->voting_type) . '♥]',
                'thumb' => $competitor->getThumbUrl(),
            );
        }

        fv_AJAX_response(true, 'ready', array('list' => $competitorsArr));
    }    
}
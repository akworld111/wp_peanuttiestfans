<?php

/**
 * Contests_List
 *
 * @since      2.2.500
 *
 * @package    FV
 * @subpackage public
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Public_Contests_List {
    /**
     * Class instance.
     *
     * @var object
     */
    protected static $instance;

    /**
     * Shortcode :: Show all contest items
     * [fv_contests_list skin="default" type="any,active,upload_opened,finished" count=""]
     * @since    2.2.082
     *
     * @param array $args {'skin', 'type', 'count', 'on_row'}
     *
     * @return void
     * @output html code
     */
    public function shortcode_show_contests_list($args)
    {
        //* Define the array of defaults
        $defaults = array(
            'skin'      => 'default',
            'type'      => 'active',     // active, upload_opened, finished
            //'contest_type' => '',     // photo,video
            'status'    => '*',
            'status_not'=>  1,
            'count'     => '6',
            'per_row_md'=> '3',
            'per_row_sm'=> '2',
            'per_row_xs'=> '1',
            'order'     => '',
            'sort'      => '',          // 'date_start', 'date_finish', 'name'
            'user_id'   => '',

            'thumb_width' => 400,
            'thumb_height' => 400,
            'thumb_crop' => 1,
        );
        //* merge incoming $args with $defaults
        $args = wp_parse_args($args, $defaults);

        // Check - is that skin registered?
        if ( !FV_Contests_List_Skins::i()->isRegistered($args['skin']) ) {
            $args['skin'] = 'default';
            fv_log( 'shortcode_show_contests_list >> skin is not Registered!', $args['skin'] );
        }

        wp_enqueue_style( 'fv_list_css_tpl_' . $args['skin'],
            FV_Templater::locateUrl($args['skin'], 'contests_list.css', 'contests_list'),
            array('fv_grid_css', 'fv_fonts_css'),
            FV::VERSION,
            'all'
        );

        $query = ModelContest::query()
            ->limit( (int) $args['count'] )
            ->withCompetitorsCount()
            ->where_custom_sql( "`comp`.`status` = 0" )
            ->withCompetitorsVotesSummary()
            ->what_fields( array("`t`.*") );
        //->group_by( '`t`.`id`' )
        //->leftJoin( ModelCompetitors::query()->tableName(), "P", "`P`.`contest_id` = `t`.`id`", array('count'=>"COUNT(`P`.`id`)", 'votes_count'=>"SUM(`P`.`votes_count`)") );

        // Apply USER_ID
        if ( !empty($args['user_id']) ) {
            $query->where('user_id', (int)$args['user_id']);
        }
        // Apply STATUS
        if ( is_numeric($args['status']) ) {
            $query->where('status', (int)$args['status']);
        }

        // Apply STATUS
        if ( is_numeric($args['status_not']) ) {
            $query->where_not('status', (int)$args['status_not']);
        }

        switch ( $args['type'] ) {
            case 'active':
                $query->whereVotingDatesActive();
                break;
            case 'upload_opened':
                $query->where_later( 'upload_date_finish', current_time('timestamp', 0) );
                break;
            case 'finished':
                $query->where( 'status', FV_Contest::FINISHED );
                break;
            default:
                break;
        }

        $sort = 'name';
        if ( $args['sort'] && in_array($args['sort'], array('date_start', 'date_finish', 'name')) ) {
            $sort = sanitize_title($args['sort']);
        }

        if ( isset($args['order']) && $args['order'] == 'ASC' ) {
            $query->sort_by( $sort, FvQuery::ORDER_ASCENDING );
        } elseif( isset($args['order']) ) {
            $query->sort_by( $sort, FvQuery::ORDER_DESCENDING );
        }

        $contests = $query->find(false);

        if ( !is_array($contests) || count($contests) == 0 ) {
            return "No contests found!";
        }

        //fv_dump( $contests );
        $contests_arr = array();
        $public_messages = fv_get_public_translation_messages();

        $thumb_params = array(
            'width' => absint($args['thumb_width']),
            'height' => absint($args['thumb_height']),
            'crop' => $args['thumb_crop'],
            'size_name' => 'fv-thumb-list',
        );

        $thumb_params['size_name'] = 'fv-thumb-list-' . $thumb_params['width'] .'x'. $thumb_params['width'] . 'c' . absint($thumb_params['thumb_crop']);

        foreach($contests as $CONTEST) {

            $CONTEST->cover_image_url = $CONTEST->getCoverImageUrl( $thumb_params );

            if ( $CONTEST->isFinished() ) {
                $CONTEST->cover_text_voting = str_replace(
                    '{date_finish}', date('d-m-Y', strtotime($CONTEST->date_finish)), $public_messages['contest_list_is_finished']
                );
            } else {

                if ($CONTEST->isVotingDatesActive()) {
                    $CONTEST->cover_text_voting = str_replace(
                        '{date_finish}', date('d-m-Y', strtotime($CONTEST->date_finish)), $public_messages['contest_list_voting_active']
                    );
                } elseif ($CONTEST->isVotingDatesFutureActive()) {
                    $CONTEST->cover_text_voting = str_replace(
                        array(
                            '{date_start}' => date('d-m-Y', strtotime($CONTEST->date_start)),
                            '{date_finish}' => date('d-m-Y', strtotime($CONTEST->date_finish)),
                        ),
                        $public_messages['contest_list_voting_active_future']
                    );
                }

                if ($CONTEST->isUploadDatesActive()) {
                    $CONTEST->cover_text_upload = str_replace(
                        '{upload_date_finish}', date('d-m-Y', strtotime($CONTEST->upload_date_finish)), $public_messages['contest_list_upload_active']
                    );
                } elseif ( $CONTEST->isUploadDatesFutureActive() ) {
                    $CONTEST->cover_text_upload = str_replace(
                        array(
                            '{upload_date_start}' => date('d-m-Y', strtotime($CONTEST->upload_date_start)),
                            '{upload_date_finish}' => date('d-m-Y', strtotime($CONTEST->upload_date_finish)),
                        ),
                        $public_messages['contest_list_upload_active_future']
                    );
                } else {
                    $CONTEST->cover_text_upload = $public_messages['contest_list_upload_inactive'];
                }


            }

            $contests_arr[] = $CONTEST;
        }

        return FV_Templater::render(
            FV_Templater::locate('', 'contests_list.php', 'contests_list'),
            compact('args', 'contests_arr'),
            true,
            "contests_list"
        );
    }

    /**
     * @return FV_Public_Contests_List
     * @since 2.2.405
     */
    public static function instance()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Contests_List();

        return self::$instance;
    }
}
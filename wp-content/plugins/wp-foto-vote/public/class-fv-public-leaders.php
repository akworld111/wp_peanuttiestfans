<?php

class FV_Public_Leaders {
    /**
     * Class instance.
     *
     * @since 2.2.405
     *
     * @var object
     */
    protected static $instance;

    /**
     * Show shortcode countdown by Contest
     * @since    2.2.084
     *
     * @param array         $args
     *
     * @return string       Html code
     */
    public function shortcode_leaders($args)
    {
        $args = wp_parse_args($args, array(
            'contest' => false,
            'contest_id' => false,
            'count' => get_option('fotov-leaders-count', 3),
            'type' => get_option('fotov-leaders-type', 'block'),
        ));

        if ( $args['contest'] && is_object($args['contest']) ) {
            $contest = $args['contest'];
        } elseif ( $args['contest_id'] && $args['contest_id'] > 0 ) {
            $contest = ModelContest::query()->findByPK((int)$args['contest_id'], true);
        } else {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }

        if ( !is_object($contest) ) {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }
        wp_enqueue_style('fv_main_css');

        if ( !FV_Leaders_Skins::i()->isRegistered($args['type']) ) {
            $args['type'] = 'block';
            fv_log( 'shortcode_leaders >> skin is not Registered!', $args['type'] );
        }

        //** Hide 'Leaders vote' text
        if ( isset($args['hide_title']) && $args['hide_title'] == true ) {
            $args["hide_title"] = true;
            $args["title"] = '';
        } else {
            $args["hide_title"] = false;
            $args["title"] = apply_filters('fv/public/leaders/title', fv_get_transl_msg('leaders_title'), $contest, $args);
        }

        if ( isset($args['leaders_width']) && (int)$args['leaders_width'] > 30 && (int)$args['leaders_width'] < 150 ) {
            $args["leaders_width"] = (int)$args['leaders_width'];
        }

        //** Link to contest page

        $args["contest_id"] = $contest->id;
        $args["contest"] = $contest;

        $args["thumb_size"] = array(
            'width'=>fv_setting('lead-thumb-width', 280),
            'height'=>fv_setting('lead-thumb-height', 350),
            'crop'=>fv_setting('lead-thumb-crop'),
            'size_name'=>'fv-leaders-thumb',
        );

        //$template_data["thumb_size"] = fv_get_image_sizes(get_option('fotov-image-size', 'thumbnail'));
        //$template_data["public_translated_messages"] = fv_get_public_translation_messages();

        // Show voting leaders
        // TODO - remove settings check here

        $leaders_query = ModelCompetitors::query()
            ->where_all(array('contest_id'=> $contest->id, 'status'=> ST_PUBLISHED))
            ->limit( $args['count'] );

        $leaders_query->sort_by($leaders_query->getVotesFieldName($contest->voting_type), 'DESC');

        $leaders_query = apply_filters( 'fv/public/leaders/query', $leaders_query, $contest, $args);

        $args["most_voted"] = apply_filters( 'fv_most_voted_data',
            $leaders_query->find(false, false, false),
            $args['type'],
            $args
        );

        $args = FV_Leaders_Skins::i()->call($args['type'], 'filterArgs', $args);

        FV_Leaders_Skins::i()->call($args['type'], 'assets', $args);

        //FvFunctions::dump($template_data);
        //FvFunctions::dump( FvFunctions::render_template(FV::$THEMES_ROOT . '/leaders/' . $type . '.php', $template_data, true, 'most_voted') );

        return FV_Templater::render( FV_Templater::locate($args['type'], 'leaders_tpl.php', 'leaders'), $args, true, 'most_voted' );
    }

    /**
     * @return FV_Public_Leaders
     * @since 2.2.405
     */
    public static function instance()
    {
        if ( ! isset( self::$instance ) )
            return self::$instance = new FV_Public_Leaders();

        return self::$instance;
    }
}
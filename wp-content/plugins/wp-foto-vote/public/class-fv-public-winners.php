<?php
defined('ABSPATH') or die("No script kiddies please!");

class FV_Public_Winners {

    /**
     * @since    2.2.503
     * @param array         $args
     *
     * @return string       Html code
     */
    public static function shortcode_winners($args)
    {
        $args = wp_parse_args($args, array(
            'display_winners'=> true,
            'contest_id'    => '',
            'contest'       => '',
            'winners_skin'  => '',
        ));

        if ( !$args['display_winners'] ) {
            return;
        }
        
        if ( is_object($args['contest']) ) {
            $contest = $args['contest'];
        } elseif ( $args['contest_id'] > 0 ) {
            $contest = new FV_Contest( (int)$args['contest_id'], true );
        } else {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }

        if ( !is_object($contest) ) {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }

        $skin = get_option('fv-winners-skin', 'red');
        if ( $args['winners_skin'] && FV_Winners_Skins::i()->isRegistered($args['winners_skin']) ) {
            $skin = $args['winners_skin'];    
        }

        /**
         * Fix for OptimizePress
         * @since 2.2.601
         * @added 17.07.2017
         */
        if ( !wp_style_is('fv_main_css') ) {
            FV_Public_Assets::register_assets();
        }

        wp_enqueue_style('fv_main_css');

        wp_enqueue_style('fv_winners_css_tpl', FV_Templater::locateUrl($skin, 'winners.css', 'winners'), array('fv_main_css'), FV::VERSION, 'all');

        FV_Winners_Skins::i()->get( $skin )->assets($args);

        //** Hide 'Leaders vote' text
        if ( isset($args['hide_title']) && $args['hide_title'] == true ) {
            $args["hide_title"] = true;
            $args["heading"] = '';
        } else {
            $args["hide_title"] = false;
            $args["heading"] = apply_filters('fv/public/leaders/title', fv_get_transl_msg('winners_heading'), $contest->id);
        }

        if ( isset($args['winners_width']) && (int)$args['winners_width'] > 30 && (int)$args['winners_width'] < 150 ) {
            $args["winners_width"] = (int)$args['winners_width'];
        }

        //** Link to contest page

        if ( empty($args["contest"]) ) {
            $args["contest"] = $contest;
        }
        $args["skin"] = $skin;

        $args["thumb_size"] = array(
            'width'     =>get_option('fv-winners-thumb-width', 300),
            'height'    =>get_option('fv-winners-thumb-height', 300),
            'crop'      =>get_option('fv-winners-thumb-crop', true),
            'size_name' =>'fv-winners-thumb',
        );

        // Show voting leaders
        // TODO - remove settings check here

        $winners_query = ModelCompetitors::query()
            ->where_all( array('contest_id'=> $contest->id, 'status'=> ST_PUBLISHED) )
            ->where_custom('place', 'IS NOT NULL')
            //->limit( get_option('fotov-leaders-count', 3) )
            ->sort_by('place', 'ASC');

        $winners_query = apply_filters( 'fv/public/winners/query', $winners_query, $skin, $args);

        $winners = apply_filters( 'fv/public/winners/find', $winners_query->find(false, false, false), $skin, $args );

        $template_data = FV_Winners_Skins::i()->get( $skin )->filterArgs( $args );
        unset($args);

        //FvFunctions::dump($template_data);
        //FvFunctions::dump( FvFunctions::render_template(FV::$THEMES_ROOT . '/leaders/' . $type . '.php', $template_data, true, 'most_voted') );
        
        return FV_Templater::render( 
            FV_Templater::locate('', 'winners.php', 'winners'), 
            compact('template_data','winners'),
            true, 
            'winners_main' 
        );
    }

}
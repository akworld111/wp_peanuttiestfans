<?php
/**
 * @created 27.03.2017
 */

class FV_Leaders_Poll extends FV_Leaders_Base {

    public function __construct() {
        $this->slug = 'poll';
        $this->title = 'Poll (not compatible with "Rate (stars)" mode)';

        parent::__construct();
    }

    public function assets( $args = array() ){
        wp_enqueue_style('fv-leaders-poll', FV_Templater::locateUrl($this->slug, 'assets/poll.css', 'leaders'), array('fv_main_css'), FV::VERSION);

        if ( !empty($args['poll_animation']) && $args['poll_animation'] ) {
            wp_enqueue_script('fv-leaders-poll', FV_Templater::locateUrl($this->slug, 'assets/poll.js', 'leaders'), array('fv_lib_js'), FV::VERSION, true);
        }
        // Load here any Additional Assets
    }

    /**
     * Filter Shortcode Args before passing to Template
     *
     * @param array $args
     * @return array
     */
    public function filterArgs( $args = array() ){

        $args['colors'] = array (
            0 => '#39c10f', 1 => '#82a0f2', 2 => '#8774f2', 3 => '#c867fc', 4 => '#007034',
            5 => '#37c68d', 6 => '#7c70d1', 7 => '#0a8258', 8 => '#eda6be', 9 => '#06e581', 10 => '#7b70d3',
            11 => '#6cf7ad', 12 => '#f98b7a', 13 => '#38d182', 14 => '#e2d048', 15 => '#2ae504',
            16 => '#f4bb64', 17 => '#9de57b', 18 => '#80ce56', 19 => '#38036d', 20 => '#e56d54',
            21 => '#75dd25', 22 => '#02a6d8', 23 => '#269e1e', 24 => '#e2ea44', 25 => '#e028c4',
            26 => '#d4f9a9', 27 => '#1555a8', 28 => '#0c9b4c', 29 => '#8eba30', 30 => '#f279bf',
            31 => '#3cc962', 32 => '#ffd2bf', 33 => '#6cb4dd', 34 => '#a5e27c', 35 => '#f4bbb7',
            36 => '#05775d', 37 => '#824cd3', 38 => '#fcbfdc', 39 => '#f4beba', 40 => '#f2ae26',
            41 => '#da95ed', 42 => '#edbf76', 43 => '#d640ed', 44 => '#36bc46', 45 => '#f29659',
            46 => '#bdc43c', 47 => '#750fbf', 48 => '#f9c8bd', 49 => '#6fdb78', 50 => '#ffc1cd',
            51 => '#4cffb7', 52 => '#3919c6', 53 => '#5bf92f', 54 => '#ffcdbf', 55 => '#3a0f7a',
            56 => '#d60c24', 57 => '#a9cff9', 58 => '#8be4e8', 59 => '#d65eae', 60 => '#788c05',
            61 => '#eef46e', 62 => '#d241e2', 63 => '#fcb3e0', 64 => '#d18bdd', 65 => '#efa5ae',
            66 => '#f4dbb7', 67 => '#b7e266', 68 => '#2fbfae', 69 => '#ecef3b', 70 => '#6f62e0',
            71 => '#db8a08', 72 => '#cead08', 73 => '#ffccd8', 74 => '#e5be8e', 75 => '#ff6e07',
            76 => '#39d3c6', 77 => '#c10bea', 78 => '#8bed8f', 79 => '#9fc914', 80 => '#ef93f9',
            81 => '#40a013', 82 => '#07f8fc', 83 => '#f9c9b1', 84 => '#5ecc2e',
        );        
        
        $args['votes_total'] = ModelCompetitors::query()
            ->where('contest_id', $args['contest_id'])
            ->what_field('SUM(`t`.`votes_count`)')
            ->findVar();
        
        return array_merge( $args, array(
            'title_img'         => FV_Templater::locateUrl($this->slug, 'images/cup128.png', 'leaders'),
            'total_title'       => 'Total votes: ',
            'poll_animation'    => 1,
        ));
        
        
    }    
}

new FV_Leaders_Poll();
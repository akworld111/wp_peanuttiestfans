<?php


class FV_Pinterest extends FV_Skin_Base {

    public function __construct() {
        $this->slug = 'pinterest';
        $this->title = 'Pinterest (images + video)';
        $this->singleTitle = 'Pinterest';

        $this->supportsCustomizer = true;

        $this->customizerSectionTitle = '[Gallery skin] Pinterest';

        parent::__construct();
    }

    public function registerCustomizerSettings() {
        $this->_registerCustomizerSetting( "circle_color", array(
            'default' => '#02caff',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Circle (on hover) color',
            'type' => 'color',
        ), array(
            '.is-gallery .clg-head-view:before' => array('attribute' => 'background-color','type' => 'css',),
        ) );
    }
    
    public function init(){
        
    } 
    
    public function afterList(){
        add_action('wp_footer',  array($this, 'wp_footer'), 99);
    }

    function wp_footer() {
        if ( false == wp_cache_get('fv_pinterest_wp_footer_loaded', 'fv') ) {
            wp_cache_set('fv_pinterest_wp_footer_loaded', '1', 'fv');
            echo '<div id="fv-progress" class="waiting"><dt></dt><dd></dd></div>';
        }
    }
    
    public function assetsList()
    {
        wp_enqueue_script('fv_theme_pinterest', FV_Templater::locateUrl($this->slug, 'js/fv_theme_pinterest.js'), array( 'jquery', 'fv_lib_js' ) , FV::VERSION);
    }

    /**
     * beforeSingle
     */
    public function beforeSingle()
    {
        // ====================
        add_filter( 'fv_contest_item_template_data', array($this, 'singleTemplateDataFilter') );
    }

    function singleTemplateDataFilter($template_data) {
        $order = rand(1,10) > 5 ? FvQuery::ORDER_ASCENDING : FvQuery::ORDER_DESCENDING;
        $template_data['most_voted'] = ModelCompetitors::query()
            ->limit(8)
            ->where_not( 'id', $template_data["contestant"]->id )
            ->where_all( array('contest_id' => $template_data["contest_id"], 'status'=>ST_PUBLISHED) )
            ->sort_by( 'id', $order )
            ->find(false, false, true, false, true);

        return $template_data;
    }
/*
    function beforeList()
    {
        $competitors = ModelContest::query()
            ->leftJoin( ModelCompetitors::query()->tableName(), "C", "`C`.`contest_id` = `t`.`id`" )
            ->what_fields( array("t.*", "`C`.`id` as c_id", "`C`.`name` as c_name", "`C`.`votes_count` as c_votes_count") )
            //->group_by( '`t`.`contest_id`' )
            ->sort_by('`C`.`votes_count`', 'DESC')
            //->sort_by('votes_count', 'DESC')
            ->limit( 10 )
            ->find();

        fv_dump( $competitors );
    }
*/
}

new FV_Pinterest();
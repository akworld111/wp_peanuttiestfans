<?php

class FV_Competitor_Categories {
    static $tax_slug = 'fv-category';
    static $post_type_slug = 'fv-competitor';

    static $contest_id;
    /**
     * Create taxonomy
     * @see register_post_type() for registering custom post types.
     */
    public static function register() {

        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name'              => _x( 'Categories', 'taxonomy general name', 'textdomain' ),
            'singular_name'     => _x( 'Category', 'taxonomy singular name', 'textdomain' ),
            'search_items'      => __( 'Search Categories', 'textdomain' ),
            'all_items'         => __( 'All Categories', 'textdomain' ),
            'parent_item'       => __( 'Parent Category', 'textdomain' ),
            'parent_item_colon' => __( 'Parent Category:', 'textdomain' ),
            'edit_item'         => __( 'Edit Category', 'textdomain' ),
            'update_item'       => __( 'Update Category', 'textdomain' ),
            'add_new_item'      => __( 'Add New Category', 'textdomain' ),
            'new_item_name'     => __( 'New Genre Category', 'textdomain' ),
            'menu_name'         => __( 'Category', 'textdomain' ),
        );

        $args = array(
            'publicly_queryable'=> false,
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => false,
            'rewrite'           => false,
        );

        register_taxonomy( FV_Competitor_Categories::$tax_slug, array( 'fv-competitor' ), $args );

        // includes/list-tables/class_terms-list-table.php
        // 'edit_' . $this->screen->taxonomy . '_per_page'
        add_filter('edit_' . self::$tax_slug . '_per_page', array('FV_Competitor_Categories', '_admin_items_per_page__filter'));

        //add_action('wp_ajax_add_tag' , array('FV_Competitor_Categories', '_ajax_hook_on_created_term__action'));

        add_action('created_' . self::$tax_slug , array('FV_Competitor_Categories', '_ajax_created_term__action2'), 10, 2);
    }

    public static function get_by_category() {
        get_posts();
        get_categories();
    }

    public static function _admin_items_per_page__filter() {
        return 200;
    }
    
    public static function admin_add_filter_get_terms_by_contest( $contest_ID = false ) {
        FV_Competitor_Categories::$contest_id = $contest_ID;
        // return apply_filters( 'get_terms', $terms, $term_query->query_vars['taxonomy'], $term_query->query_vars, $term_query );
        add_filter('get_terms', array('FV_Competitor_Categories', 'admin_get_terms_by_contest__filter2'), 11, 2);
    }
    public static function admin_get_terms_by_contest__filter2($terms, $taxonomy) {

        if ( !self::$contest_id ) {
            return $terms;
        }

        if ( !$taxonomy || $taxonomy[0] != self::$tax_slug ) {
            return $terms;
        }

        $terms = array_filter($terms, function ($term) {
            return isset($term->term_group) ? FV_Competitor_Categories::$contest_id == $term->term_group : true;
        });

        //remove_filter( 'get_terms', array('FV_Competitor_Categories', 'admin_get_terms_by_contest__filter2'), 11 );

        return $terms;
    }
    

    public static function _ajax_created_term__action2($term_id, $tt_id) {
        if ( !defined('DOING_AJAX') || !DOING_AJAX ) {
            return;
        }

        if ( empty($_REQUEST['action']) || $_REQUEST['action'] != 'add-tag' || empty($_REQUEST['contest_id']) ) {
            return;
        }
        
        wp_update_term( $term_id, self::$tax_slug, array(
            'term_group' => absint($_REQUEST['contest_id']),
        ) );
    }
}
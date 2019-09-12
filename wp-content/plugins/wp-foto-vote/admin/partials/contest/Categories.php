<?php
/**
 * @since 2.2.707
 */
?>

<div class="b-wrap">


    <div class="postbox fv-categories-status">
        <div class="inside">

            <form class="form-inline text-center" method="POST">
                <div class="form-group">
                    <label for="categories_on">Categories status:</label>
                </div>
                <div class="form-group">
                    <select id="categories_on" name="categories_on" class="form-control">
                        <option value="" <?php selected("", $contest->categories_on); ?>>Disabled</option>
                        <option value="single" <?php selected("single", $contest->categories_on); ?>>Enabled, Single per competitor</option>
                        <option value="multi" <?php selected("multi", $contest->categories_on); ?>>Enabled, Multi-select possible</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="button button-primary button-large" accesskey="s"><?php _e('Save', 'fv'); ?></button>
                </div>

                <input type="hidden" name="action" value="fv_contest_change_categories_state">
                <input type="hidden" name="contest_id" value="<?php echo $contest->id; ?>">
                <?php wp_nonce_field('fv_contest_change_categories_state_nonce'); ?>
            </form>

        </div>
    </div>

</div>

<?php

/** @var FV_Contest $contest */

if ( ! $contest->isCategoriesEnabled() ) {
    return;
}


/**
 * $post_type is set when the WP_Terms_List_Table instance is created
 *
 * @global string $post_type
 */
global $post_type;
$post_type = 'fv-competitor';


//fv_dump( $contest->getCategories() );
/*
$competitor = new FV_Competitor(3761);

$competitor->addCategories( 28 );

fv_dump( $competitor->getCategories('string') );
*/

//$wp_list_table = new FV_Terms_List_Table( array('screen' => 'edit-fv-category') );
$wp_list_table = _get_list_table('WP_Terms_List_Table', array('screen' => 'edit-fv-category'));


if ( ! FvFunctions::curr_user_can('general') ) {
    wp_die(
        '<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
        '<p>' . __( 'Sorry, you are not allowed to manage terms in this taxonomy.' ) . '</p>',
        403
    );
}

$tax = get_taxonomy( FV_Competitor_Categories::$tax_slug );
$taxonomy = FV_Competitor_Categories::$tax_slug;

$pagenum = $wp_list_table->get_pagenum();

$title = $tax->labels->name;


$location = false;
$referer = wp_get_referer();
if ( ! $referer ) { // For POST requests.
    $referer = wp_unslash( $_SERVER['REQUEST_URI'] );
}
$referer = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'error', 'message', 'paged' ), $referer );

if ( ! $location && ! empty( $_REQUEST['_wp_http_referer'] ) ) {
    $location = remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
}

if ( $location ) {
    if ( $pagenum > 1 ) {
        $location = add_query_arg( 'paged', $pagenum, $location ); // $pagenum takes care of $total_pages.
    }

    /**
     * Filters the taxonomy redirect destination URL.
     *
     * @since 4.6.0
     *
     * @param string $location The destination URL.
     * @param object $tax      The taxonomy object.
     */
    wp_redirect( apply_filters( 'redirect_term_location', $location, $tax ) );
    exit;
}

$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
    wp_redirect( add_query_arg( 'paged', $total_pages ) );
    exit;
}

wp_enqueue_script('admin-tags');
if ( current_user_can($tax->cap->edit_terms) )
    wp_enqueue_script('inline-edit-tax');


$class = ( isset( $_REQUEST['error'] ) ) ? 'error' : 'updated';


?>


        <?php
        if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
            /* translators: %s: search keywords */
            printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( wp_unslash( $_REQUEST['s'] ) ) );
        }
        ?>

        <div id="ajax-response"></div>

        <div id="col-container" class="wp-clearfix">

            <div id="col-left">
                <div class="col-wrap">

                    <?php

                    if ( current_user_can($tax->cap->edit_terms) ) {
                       ?>

                        <div class="form-wrap">
                            <h2><?php echo $tax->labels->add_new_item; ?></h2>
                            <form id="addtag" method="post" action="edit-tags.php" class="validate"<?php
                            /**
                             * Fires inside the Add Tag form tag.
                             *
                             * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
                             *
                             * @since 3.7.0
                             */
                            do_action( "{$taxonomy}_term_new_form_tag" );
                            ?>>
                                <input type="hidden" name="action" value="add-tag" />
                                <input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
                                <input type="hidden" name="post_type" value="<?php echo esc_attr('fv-competitor'); ?>" />
                                <input type="hidden" name="contest_id" value="<?php echo esc_attr($contest->ID); ?>" />
                                <?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>

                                <div class="form-field form-required term-name-wrap">
                                    <label for="tag-name"><?php _ex( 'Name', 'term name' ); ?></label>
                                    <input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" />
                                    <p><?php _e('The name is how it appears on your site.'); ?></p>
                                </div>
                                <?php if ( ! global_terms_enabled() ) : ?>
                                    <div class="form-field term-slug-wrap">
                                        <label for="tag-slug"><?php _e( 'Slug' ); ?></label>
                                        <input name="slug" id="tag-slug" type="text" value="" size="40" />
                                        <p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p>
                                    </div>
                                <?php endif; // global_terms_enabled() ?>

                                <div class="form-field term-description-wrap" style="display: none">
                                    <label for="tag-description"><?php _e( 'Description' ); ?></label>
                                    <textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
                                    <p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
                                </div>

                                <?php
                                    submit_button( $tax->labels->add_new_item );
                                ?>

                            </form></div>
                    <?php } ?>

                </div>
            </div><!-- /col-left -->

            <div id="col-right">
                <div class="col-wrap">

                    <?php $wp_list_table->views(); ?>

                    <form id="posts-filter" method="post">
                        <input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
                        <input type="hidden" name="post_type" value="<?php echo esc_attr( 'fv-competitor' ); ?>" />

                        <?php $wp_list_table->display(); ?>

                    </form>

                    <?php
                    /**
                     * Fires after the taxonomy list table.
                     *
                     * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
                     *
                     * @since 3.0.0
                     *
                     * @param string $taxonomy The taxonomy name.
                     */
                    do_action( "after-{$taxonomy}-table", $taxonomy );
                    ?>

                </div>
            </div><!-- /col-right -->

        </div><!-- /col-container -->


<?php if ( ! wp_is_mobile() ) : ?>
    <script type="text/javascript">
        try{document.forms.addtag['tag-name'].focus();}catch(e){}

        jQuery(document).on("click", "table.tags .column-posts > a", function() {
            return false;
        });
    </script>
    <style>
        table.tags .column-description {
        /*table.tags .column-posts {*/
            display: none;
        }

        table.tags .row-actions .edit,
        table.tags .row-actions .view {
            display: none;
        }
    </style>
    <?php
endif;

$wp_list_table->inline_edit();


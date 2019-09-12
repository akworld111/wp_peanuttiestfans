<?php

require_once FV::$INCLUDES_ROOT . 'libs/class-fv-wp-list-table.php';

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class FV_List_Votes_Log extends FV_WP_List_Table
{

    function extra_tablenav($which)
    {
        if ($which == "top") {
            $contestsQ = ModelContest::query()
                ->find(false, false, OBJECT_K);

            foreach ($contestsQ as $item) {
                $contests[$item->id] = $item->id . ' / ' . $item->name;
            }

            $selected_id = false;
            $selected_photo_id = 0;
            if (isset($_GET['contest_id']) && $_GET['contest_id'] > 0) {
                $selected_id = (int)$_GET['contest_id'];

                $photos = ModelCompetitors::query()
                    ->where('contest_id', $selected_id)
                    ->what_fields( array('id', 'name', 'votes_count') )
                    ->sort_by_votes( $contestsQ[$selected_id]->voting_type )
                    ->find();
                if (isset($_GET['photo_id']) && $_GET['photo_id'] > 0) {
                    $selected_photo_id = (int)$_GET['photo_id'];
                }
            }

            ?>
            <div class="alignleft actions bulkactions ml50">
                <label for="fv-filter-contest"><?php _e('Contest', 'fv') ?>:</label>
                <select name="contest_id" id="fv-filter-contest" class="select2">
                    <option value=""><?php _e('Filter by Contest', 'fv') ?></option>
                    <?php foreach ($contests as $c_id => $c_name): ?>
                        <option value="<?php echo $c_id ?>" <?php echo ($selected_id == $c_id) ? 'selected' : '' ?> ><?php echo $c_name ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="fv-filter-contest-photo"><?php _e('Photo', 'fv') ?>:</label>
                <select name="photo_id" id="fv-filter-contest-photo" <?php echo (!$selected_id) ? "disabled" : ""; ?> class="select2">
                    <?php if (!$selected_id) : ?>
                        <option value=""><?php _e('Select contest', 'fv') ?></option>
                    <?php else: ?>
                        <option value=""><?php _e('Filter by photo', 'fv') ?></option>
                        <?php foreach ($photos as $P): ?>
                            <option value="<?php echo $P->id ?>" <?php echo selected($P->id, $selected_photo_id) ?> ><?php echo '#', $P->id, ' / ', $P->name, ' [', $P->votes_count , ' ♥]' ?></option>
                        <?php endforeach; ?>
                    <?php endif ?>
                </select>
            </div>
        <?php
        }
        if ($which == "bottom") {
            //The code that goes after the table is there

        }
    }

    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     *
     * @var array
     **************************************************************************/


    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'votes_row', //singular name of the listed records
            'plural' => 'votes_rows', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));

    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name)
    {
        /**
         * @since 2.2.12
         */
        do_action('fv/admin/votes_log_table/column_default', $column_name, $item);

        switch ($column_name) {
            case 'referer':
            case 'browser':
                return $item->$column_name;
            case 'changed':
                $res = $item->$column_name . ' / ';
                if ( !$item->type ) {
                    $res .= ' +1';
                } elseif ( ModelVotes::$TYPE_RATE == $item->type ) {
                    $res .= ' rate +' . $item->rating;
                }
                return $res;
            case 'user_id':
                return "<a href=" . admin_url('user-edit.php?user_id=' . $item->$column_name) . ">" . $item->$column_name . "</a>";
            case 'soc_network':
                return "<a href='{$item->soc_profile}' target='_blank' title='see profile'>" . $item->$column_name . ' / ' . $item->soc_uid . "</a>";
            case 'ip':
                if (!$item->uid) {
                    $item->uid = 'empty EUID';
                }
                return "<a href='http://whatismyipaddress.com/ip/{$item->$column_name}' title='more info' target='_blank'>{$item->$column_name}</a>"
                        . "/ {$item->country}"
                        . "/<br/>{$item->uid}" . fv_tooltip_code('Persistent cookie value (evercookie), can be empty if user fast click vote after page opening, on Mobile or in case cheating.')
                        . "/<br/>$item->b_plugins" . fv_tooltip_code('Unique browser plugins hash (can be empty in Mobile or TOR browser)')
                        . "/<br/>{$item->display_size}px" . fv_tooltip_code('Browser size')
                        . "/<br/>{$item->mouse_pos}" . fv_tooltip_code('PosX x PosY - mouse position when user click Vote; Can be empty in Firefox or old browser.');
            case 'vote_id':
                return '<a href="' . add_query_arg(array('contest'=>$item->contest_id,'s'=>$item->vote_id), admin_url('admin.php?page=fv&show=competitors&search_where=id')) . '">'  . $item->$column_name . '</a>' .
                    ' / <a href="' . fv_single_photo_link($item->vote_id, false, $item->contest_id) . '">' . $item->competitor_name . '</a>';
            case 'score':
                if ($item->score_detail) :
                    $res = $item->$column_name . '%' . fv_tooltip_code($item->score_detail);
                else:
                    $res = $item->$column_name . '%';
                endif;
                $res .= ' / ';
                $res .= ($item->is_tor) ? 'TOR' : '-';
                return $res;
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/

    function column_contest_id($item)
    {

        //Build row actions
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&votes_row[]=%s&contest_id=%d&photo_id=%s&paged=%s">Just delete record</a>',
                $_REQUEST['page'],
                'delete',
                $item->id,
                isset($_REQUEST['contest_id']) ? $_REQUEST['contest_id'] : '',
                isset($_REQUEST['photo_id']) ? $_REQUEST['photo_id'] : '',
                isset($_REQUEST['paged']) ? $_REQUEST['paged'] : ''
            ),
            'delete_with_decrease' => sprintf('<a href="?page=%s&action=%s&votes_row[]=%s&contest_id=%d&photo_id=%s&paged=%s">Delete and decrease entry votes</a>',
                $_REQUEST['page'],
                'delete_with_decrease',
                $item->id,
                isset($_REQUEST['contest_id']) ? $_REQUEST['contest_id'] : '',
                isset($_REQUEST['photo_id']) ? $_REQUEST['photo_id'] : '',
                isset($_REQUEST['paged']) ? $_REQUEST['paged'] : ''
            ),
        );

        //Return the title contents
        return sprintf('%1$s / %2$s<br/> %3$s',
            $item->contest_id, //$1%s
            $item->contest_name, //$1%s
            $this->row_actions($actions) //$3%s
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item->id //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'contest_id' => __('Contest', 'fv'),
            'vote_id' => __('competitor', 'fv'),
            'ip' => __('IP address / Country / Evercookie UID / Browser plugins / Display size[w/h] / Mouse position', 'fv'),
            'score' => __('Fraud score / TOR', 'fv'),
            'soc_network' => __('Soc. network/ Soc. user id', 'fv'),
            'user_id' => __('User id', 'fv'),
            'changed' => __('Date / Type', 'fv'),
            'browser' => __('browser', 'fv'),
            'referer' => __('refer', 'fv'),
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'contest_id' => array('name', false), //true means it's already sorted
            'vote_id' => array('vote_id', false),
            'changed' => array('changed', false),
            'user_id' => array('changed', false),
            'ip' => array('ip', false),
            'score' => array('score', false),
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Just delete record (user can vote again)',
            'delete_with_decrease' => 'Delete and decrease votes (user can vote again)',
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action()
    {
        if ( !current_user_can( get_option('fv-needed-capability', 'manage_options') ) ) {
            return false;
        }

        //Detect when a bulk action is being triggered...
        if ( empty($_GET['votes_row']) || !is_array($_GET['votes_row'])) {
            return false;
        }

        array_map('absint', $_GET['votes_row']);

        $delete_message = __('Vote row(s) deleted!', 'fv');

        if ( 'delete' === $this->current_action() ) {

            foreach ($_GET['votes_row'] as $row_id) {
                // If not valid int or false - skip
                if (!$row_id) {
                    continue;
                }
                ModelVotes::query()->delete($row_id);
            }
        }

        if ( 'delete_with_decrease' === $this->current_action() ) {
            $contest = null;

            $votes_to_delete = ModelVotes::q()
                ->where_in('id', $_GET['votes_row'])
                ->find();

            foreach ($votes_to_delete as $vote_row) {
                $contest = new FV_Contest($vote_row->contest_id, true);

                ModelCompetitors::q()->decreaseVotesCount($vote_row->vote_id, $contest->voting_type, $vote_row->rating);
                ModelVotes::query()->delete($vote_row->id);

                $contest = null;

                $delete_message .= __(' Also votes count decreased.', 'fv');
            }
        }

        wp_add_notice( $delete_message, "success" );

        /*elseif (isset($_GET['votes_log']) && is_numeric($_GET['votes_log'])) {
            ModelVotes::query()->delete((int)$_GET['votes_log']);
            wp_add_notice(__('Vote row(s) deleted (this don`t change photo votes count).', 'fv'), "success");
        }*/

    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items()
    {
        //$my_db = new FV_DB;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /***********************************************************************
         * ---------------------------------------------------------------------
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $contest_id = (!empty($_REQUEST['contest_id'])) ? (int)$_REQUEST['contest_id'] : false;
        $photo_id = (!empty($_REQUEST['photo_id'])) ? (int)$_REQUEST['photo_id'] : 0; //If no sort, default to false
        $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : false; //If no sort, default to false
        $order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'ASC'; //If no order, default to asc

        $query = ModelVotes::query()
            ->what_fields( '`t`.*' )
            ->sort_by( $orderby, $order )
            ->limit(FV_RES_OP_PAGE)
            ->offset( ( $current_page - 1 ) * FV_RES_OP_PAGE )
            ->leftJoin( ModelContest::query()->tableName(), "contest", "`t`.`contest_id` = `contest`.`id`", array("name", "id") )
            ->leftJoin( ModelCompetitors::query()->tableName(), "competitor", "`t`.`vote_id` = `competitor`.`id`", array("name") );

        if ( !empty($contest_id) ) {
            $query->where('contest_id', $contest_id);
        }
        if ( !empty($photo_id) ) {
            $query->where('vote_id', $photo_id);
        }

        if ( isset($_REQUEST['s']) && strlen($_REQUEST['s']) > 1 ) {
            $query->set_searchable_fields( array("ip", "uid", "referer", "browser", "country", "user_id", "fb_pid") )
                ->search( sanitize_text_field($_REQUEST['s']) );
        }

        $this->items = $query->find();

        $total_items = $query->find(true);

        /*
        $stats = $my_db->getVoteStats($current_page, 0, $contest_id, $orderby, $order, $photo_id);
        $this->items = $stats['r'];
        $total_items = $stats['count_rows'];
        */

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => FV_RES_OP_PAGE, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / FV_RES_OP_PAGE) //WE have to calculate the total number of pages
        ));
    }


}

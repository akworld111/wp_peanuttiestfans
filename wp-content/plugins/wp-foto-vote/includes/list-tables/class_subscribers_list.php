<?php

require_once FV::$INCLUDES_ROOT . 'libs/class-fv-wp-list-table.php';

class FV_Subscribers_Log extends FV_WP_List_Table
{
    protected $sync_codes;

    function __construct()
    {
        //Set parent defaults
        parent::__construct(array(
            'singular' => 'subscriber', //singular name of the listed records
            'plural' => 'subscribers', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));

        $this->sync_codes = apply_filters( 'fv/subscribers/table/sync_codes', array() );
    }

    function extra_tablenav($which)
    {
        if ($which == "top") {

            $contests = FV_Admin_Contest::get_contests_list_flat();
            
            $selected_id = false;
            if (isset($_GET['contest_id']) && $_GET['contest_id'] > 0) {
                $selected_id = (int)$_GET['contest_id'];
            }

            ?>
            <div class="alignleft actions bulkactions ml50">
                <select name="contest_id" id="fv-filter-contest" class="select2">
                    <option value="">Filter by contest</option>
                    <?php foreach ($contests as $c_id => $c_name): ?>
                        <option value="<?php echo $c_id ?>" <?php echo ($selected_id == $c_id) ? 'selected' : '' ?> ><?php echo $c_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php
        }
    }


    /** ************************************************************************
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
        switch ($column_name) {
            case 'added':
            case 'name':
            case 'email':
            return $item->$column_name;
            case 'type':
                return isset($item->soc_network) ? $item->$column_name . ' / ' . $item->soc_network : $item->$column_name  ;
            case 'verified':
                return ($item->$column_name) ? 'yes' : 'no';
            case 'newsletter':
                $return = ($item->$column_name) ? 'yes' : 'no';
                if ($item->sync) {
                    $return .= ' » ';
                    $return .= isset($this->sync_codes[$item->sync]) ? $this->sync_codes[$item->sync] : $item->sync ;
                }
                return $return;
            case 'user_id':
                return "<a href=" . admin_url('user-edit.php?user_id=' . $item->$column_name) . ">" . $item->$column_name . "</a>";
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /** ************************************************************************
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/

    function column_contest_id($item)
    {

        //Build row actions
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&subscriber=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->id),
            'verify' => sprintf('<a href="?page=%s&action=%s&subscriber=%s">Verify</a>', $_REQUEST['page'], 'verify', $item->id),
        );

        //Return the title contents
        return sprintf('%1$s / %2$s<br/> %3$s',
            $item->contest_id, //$1%s
            $item->contest_name, //$1%s
            $this->row_actions($actions) //$3%s
        );
    }

    /** ************************************************************************
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
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'contest_id' => __('Contest', 'fv'),
            'added' => __('Added', 'fv'),
            'name' => __('Name', 'fv'),
            'email' => __('Email', 'fv'),
            'type' => __('Type', 'fv'),
            'user_id' => __('User id', 'fv'),
            'newsletter' => __('Newsletter', 'fv'),
            'verified' => __('Verified', 'fv'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'contest_id' => array('contest_id', false), //true means it's already sorted
            'added' => array('added', true),
            'user_id' => array('user_id', false),
            'email' => array('email', false),
            'type' => array('type', false),
            'verified' => array('verified', false),
        );
        return $sortable_columns;
    }

    /** ************************************************************************
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /** ************************************************************************
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action()
    {
        if ( empty($_GET['subscriber']) ) {
            return false;
        }

        if ( !current_user_can( get_option('fv-needed-capability', 'manage_options') ) ) {
            return false;
        }

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            if ( is_array($_GET['subscriber']) ) {

                foreach ($_GET['subscriber'] as $row_id) {
                    ModelSubscribers::query()->delete((int)$row_id);
                }
                wp_add_notice( count($_GET['subscriber']) . __(' Subscribers deleted.', 'fv'), "success");

            } elseif ( is_numeric($_GET['subscriber']) ) {

                ModelSubscribers::query()->delete((int)$_GET['subscriber']);
                wp_add_notice(__('1 Subscriber deleted.', 'fv'), "success");

            }
        }

        if ( 'verify' === $this->current_action() && is_numeric($_GET['subscriber']) ) {
            $subscriber = ModelSubscribers::query()->findByPK( (int)$_GET['subscriber'] );

            if ( !empty($subscriber) ) {
                ModelSubscribers::query()->updateByPK( array('verified'=>1), $subscriber->id );
                wp_add_notice( '1 Subscriber set as verified (' . $subscriber->email . ').', 'success' );
            } else {
                wp_add_notice( 'Can find Subscriber with ID: ' . (int)$_GET['subscriber'] , 'warning' );
            }

        }
    }

    /** ************************************************************************
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

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


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

        $current_page = $this->get_pagenum();

        $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'added'; //If no sort, default to false
        $order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'DESC'; //If no order, default to asc

        $per_page = 25;

        $query = ModelSubscribers::query()
            ->what_fields( '`t`.*' )
            ->sort_by( $orderby, $order )
            ->limit( $per_page )
            ->offset( ( $current_page - 1 ) * $per_page )
            ->leftJoin( ModelContest::query()->tableName(), "contest", "`t`.`contest_id` = `contest`.`id`", array("name") );

        if ( isset($_REQUEST['s']) && strlen($_REQUEST['s']) > 1 ) {
            $query->set_searchable_fields( array("name", "email", "type") )
                ->search( sanitize_text_field($_REQUEST['s']) );
        }

        if ( !empty($_REQUEST['contest_id']) ) {
            $query->where('contest_id', absint($_REQUEST['contest_id']));
        }

        $this->items = $query->find();

        $total_items = $query->find(true);

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page) //WE have to calculate the total number of pages
        ));
    }
}
<?php

// https://github.com/CaerCam/Custom-AJAX-List-Table-Example/blob/master/list-table-example.php

require_once FV::$INCLUDES_ROOT . 'libs/class-fv-wp-list-table.php';

/**
 * Class FV_List_Competitors
 *
 * @since 2.2.512
 */
class FV_List_Competitors extends FV_WP_List_Table {

    public $js_vars;

    /**
     * @var ModelCompetitors
     */
    protected $query;
    /**
     * @var bool|FV_Contest
     */
    public $contest = false;
    /**
     * @var bool
     */
    protected $is_moderation_page = false;

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct( $contest_id = false, $moderation_list = false ){
        global $status, $page;

        if ( $contest_id ) {
            $this->contest = new FV_Contest( $contest_id, true );
        }

        if ( $moderation_list ) {
            $this->is_moderation_page = $moderation_list;
        }

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'competitor',     //singular name of the listed records
            'plural'    => 'competitors',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );

    }


    function extra_tablenav($which) {
        if ( !$this->contest ) {
            return;
        }

        ?>
            <div class="actions alignleft">
                <button type="button" data-call="Competitor.singleForm" data-contest="<?php echo $this->contest->id; ?>" data-competitor="" data-nonce="<?php echo wp_create_nonce('fv_nonce') ?>" class="button">
                    <span class="dashicons dashicons-welcome-add-page"></span><?php echo __('Add one photo', 'fv'); ?>
                </button>
                <button type="button"  data-call="Competitor.addMany" data-contest="<?php echo $this->contest->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_multi_add_nonce') ?>" class="button">
                    <span class="dashicons dashicons-welcome-add-page"></span><?php echo __('Add many photos', 'fv'); ?>
                </button>
            </div>

            <?php if ( $this->contest->isCategoriesEnabled() ): ?>
            <div class="actions alignleft clear hidden action__add_category">
                <select name="add_category" id="bulk-add-category-selector-<?php echo $which; ?>">
                    <option value="">Category to add</option>
                    <?php foreach ( $this->contest->getCategories() as $category ): ?>
                        <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        <?php
    }

    /** ************************************************************************
     * @param FV_Competitor    $item A singular item (one full row's worth of data)
     * @param array         $column_name The name/slug of the column to be processed
     * @return string       Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name) {
            case 'name':
                return $item->$column_name;
            case 'description':
                return '<div class="column-description-data">' .$item->$column_name . '</div>';
            case 'contest_name':
                return '<a href="' . admin_url('admin.php?page=fv&show=config&contest=' . $item->contest_id) . '">' . $item->$column_name . '</a>';
                break;
            case 'details':
                $meta = $item->meta()->get_custom_all_as_string();
                if ($meta) {
                    $meta = '<strong>Meta:</strong> ' . $meta;
                }

                $categories = '';
                if ( (isset($item->contest_categories_on) && $item->contest_categories_on) || ($this->contest && $this->contest->isCategoriesEnabled()) ) {
                    $categories = $item->getCategories('string');
                    if ($categories) {
                        $categories = ' <strong>Categories:</strong> ' . $categories;
                    }
                }

                return $meta . $categories;
                break;
            case 'votes_count':
                return '<span title="fail votes : ' . $item->votes_count_fail. '">' . $item->getVotes(false, false, 'total_count') . '</span> <i class="fvicon-heart"></i>';
                break;
            case 'views':
                return $item->views . ' <i class="fvicon-eye"></i>';
                break;
            case 'user_email':
                return
                    '<a href="#0" class="list-search" data-search="' . esc_attr($item->getAuthorEmail()) . '" data-where="user_email">' .  $item->getAuthorEmail()  . '</a>' .
                    '<br/>IP: <a href="#0"  class="list-search" data-search="' . esc_attr($item->user_ip) . '" data-where="user_ip">' . $item->user_ip . '</a>' .
                    '<br/> User ID: <a href="' . admin_url('user-edit.php?user_id='.$item->user_id) . '" target="_blank">' . $item->user_id . '</a>';
                break;
            case 'status_text':
                return __(fv_get_status_name($item->status), 'fv');
                break;
            case 'added_date':
                return date('d/m/Y',$item->added_date);
                break;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
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
     * @param FV_Contest    $item  A singular item (one full row's worth of data)
     * @return string           Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_name2($item){

        //Build row actions
        $actions = array(
            'config'      => sprintf('<a href="?page=%s&show=%s&contest=%s">%s</a>', $_REQUEST['page'], 'config', $item->id, __('Config', 'fv')),
            'competitors'      => sprintf('<a href="?page=%s&show=%s&contest=%s">%s</a>', $_REQUEST['page'], 'competitors', $item->id, __('Competitors', 'fv')),
            //'delete'    => sprintf('<a href="?page=%s&action=%s&contest=%s" onclick="return confirm(\'%s\');">%s</a>', $_REQUEST['page'], 'delete', $item->id, __('Are you really want to delete contest and all contestants & votes log records?', 'fv'), __('Delete', 'fv')),
            'vote_log'    => sprintf('<a href="?page=%s&contest_id=%s">%s</a>', 'fv-vote-log', $item->id, __('Vote log', 'fv')),
            //'clone'    => '<a href="#0" onclick="alert(\'To clone contest click *Edit => Clone*\')">Clone</a>',
        );

        //Return the title contents
        return sprintf('%1$s %2$s<br/> <span style="color:silver">[fv id="%3$s"]</span>%4$s',
            /*$1%s*/ $item->name,
            /*$2%s*/ $item->isFinished() ? '<span class="dashicons dashicons-awards" title="Finished"></span>' : '',
            /*$3%s*/ $item->id,
            /*$4%s*/ $this->row_actions($actions)
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
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" class="checkbox-%1$s" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param FV_Competitor $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_img($item){

        ob_start();

        $image_src = $item->getThumbArr( '', true );

        $img_class = array();
        if ( isset($image_src[4]) ) {
            $img_class[] = $image_src[4];
        }
        if ( isset($item->options['provider']) ) {
            $img_class[] = $item->options['provider'];
        }
        if ( $item->storage ) {
            $img_class[] = $item->storage;
        }
        $img_class  = array_unique ($img_class);

        ?>
            <a href="<?php echo $item->getImageUrl(); ?>" target="_blank" class="column-img__a type--<?php echo esc_attr( implode(' type--', $img_class) ); ?>" title="<?php echo esc_attr( implode(' ', $img_class) ); ?>">
                <img src="<?php echo ( is_array($image_src) )? $image_src[0] : FV::$ASSETS_URL . 'img/no-photo.png'; ?>" width="55" class="fv-table-thumb" />
            </a>
            <?php if ($item->place): ?>
                <div class="is-winner"><i class="fvicon-trophy3"></i> <strong><?php echo $item->getPlaceCaption(); ?></strong></div>
            <?php endif; ?>
            <?php do_action('fv/admin/contestant/extra', $item); ?>
            <button type="button" class="toggle-row"><span class="screen-reader-text"> <?php __( 'Show more details' ) ?> </span></button>
        <?php
        //$this->row_actions( array() );

        return ob_get_clean();
    }

    /**
     * @param FV_Competitor $item
     * @return string
     */
    function column_actions($item){
        ob_start();
        ?>
            <?php if( $this->_is_moderation_page() ): ?>
                <a href="#<?php echo $item->id; ?>" class="a-underline" data-action="approve" data-call="Competitor.approve" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_competitor_approve_nonce') ?>"><?php _e('Approve', 'fv') ?></a><br/>
                <a href="#<?php echo $item->id; ?>" class="a-underline" data-call="Competitor.approve" data-need-comment="yes" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_competitor_approve_nonce') ?>"><?php _e('Approve with Comment', 'fv') ?></a><br/>
                <a href="#<?php echo $item->id; ?>" class="a-underline" data-action="delete" data-confirmation="yes" data-call="Competitor.delete" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_competitor_delete_nonce') ?>"><?php _e('Delete', 'fv') ?></a><br/>
                <a href="#<?php echo $item->id; ?>" class="a-underline" data-confirmation="yes" data-call="Competitor.delete" data-need-comment="yes" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_competitor_delete_nonce') ?>"><?php _e('Delete with Comment', 'fv') ?></a>
            <?php else: ?>
                <a href="#<?php echo $item->id; ?>" data-call="Competitor.singleForm" data-contest="<?php echo $item->contest_id; ?>" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_nonce') ?>"><?php _e('Edit', 'fv') ?></a>
                / <a href="#<?php echo $item->id; ?>" data-action="delete" data-confirmation="yes" data-call="Competitor.delete" data-competitor="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_competitor_delete_nonce') ?>"><?php _e('Delete', 'fv') ?></a>
                /
                <a href="#<?php echo $item->id; ?>" data-call="Competitor.moveModal" data-contestant="<?php echo $item->id; ?>" data-nonce="<?php echo wp_create_nonce('fv_nonce') ?>" title="Move to another contest">
                    <span class="dashicons dashicons-migrate"></span>
                </a>
            <?php endif; ?>
            <?php if ( $item->image_id ): ?>
                <a href="<?php echo admin_url("post.php?post={$item->image_id}&action=edit") ; ?>" target="_blank" title="edit attachment"><span class="dashicons dashicons-format-image"></span></a>
            <?php endif; ?>
            <br/>
            <a href="#<?php echo $item->id; ?>" data-action="rotate-right" data-confirmation="yes" title="<?php _e("rotate right", 'fv') ?>" onclick="fv_rotate_image(this, 270, <?php echo $item->contest_id ?>, <?php echo $item->id; ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><span class="dashicons dashicons-imgedit-rright rotate_img"></span></a>

            <a href="#<?php echo $item->id; ?>" data-action="rotate-left" data-confirmation="yes" title="<?php _e("rotate left", 'fv') ?>" onclick="fv_rotate_image(this, 90, <?php echo $item->contest_id ?>, <?php echo $item->id; ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><span class="dashicons dashicons-imgedit-rleft rotate_img"></span></a>

            <?php if( $this->_is_moderation_page() ): ?>
                &nbsp;&nbsp;
                <a href="<?php echo $item->getAdminLink(); ?>" class="a-underline" target="_blank" title="edit"><span class="dashicons dashicons-edit"></span>edit</a>
            <?php endif; ?>

            <?php if( !$this->_is_moderation_page() ): ?>
                &nbsp;&nbsp;
                <a href="<?php echo $item->getSingleViewLink(); ?>" target="_blank" title="view public page"><span class="dashicons dashicons-admin-links"></span></a>
                &nbsp;&nbsp;
                <a href="<?php echo add_query_arg( array('contest_id' => $item->contest_id, 'photo_id' => $item->id), admin_url('admin.php?page=fv-vote-analytic') ) ?>" title="Votes analytic" target="_blank"><span class="dashicons dashicons-heart"></span></a>
            <?php endif; ?>




        <?php
        return ob_get_clean();
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
    function get_columns(){
        $columns = array(
            'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
            'img'               => __('img', 'fv'),
            'contest_name'      => __('Contest', 'fv'),
            'name'              => __('Caption', 'fv'),
            'description'       => __('description', 'fv'),
            'votes_count'       => __('&nbsp;&nbsp;&nbsp;&nbsp;<i class="fvicon-heart"></i>', 'fv'),
            'details'           => __('details', 'fv'),
            'user_email'        => __('user email', 'fv'),
            'status_text'       => __('status', 'fv'),
            'added_date'        => __('added', 'fv'),
            'actions'           => __('actions', 'fv'),
        );

        if ( $this->_is_moderation_page() ) {
            unset( $columns['status_text'] );
            unset( $columns['votes_count'] );
        } else {
            unset( $columns['contest_name'] );
        }

        return apply_filters('fv/admin/competitors_table/get_columns', $columns, $this);
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
    function get_sortable_columns() {
        $sortable_columns = array(
            'added_date'    => array('id',true),     //true means it's already sorted
            'name'          => array('name',false),     //true means it's already sorted
            'votes_count'   => array('votes_count',false),     //true means it's already sorted
            'user_email'    => array('user_email',false),     //true means it's already sorted
            'status_text'        => array('status',false),     //true means it's already sorted
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
    function get_bulk_actions() {

        fv_get_tooltip_code('\'Published\' - entry public visible' .
            '<br/>\'On Moderation - visible only in admin (Moderation & Entries list pages)' .
            '<br/>\'Draft\' - visible only in admin');

        $actions = array(
            'delete__with_mail' => 'Delete and send mail to user',
            'delete'            => 'Delete',
        );

        if ( $this->_is_moderation_page() ) {
            $actions['set__approved__with_mail'] = 'Approve and send mail to user';
        } else {
            $actions['set__approved'] = 'Set as "Published"';
            $actions['set__approved__with_mail'] = 'Set as "Published" and send mail to user that entry "Approved"';
            $actions['set__on_moderation'] = 'Set as "On Moderation"';
            $actions['set__draft'] = 'Set as "Draft"';
            if ( $this->contest->isCategoriesEnabled() ) {
                $actions['add_category'] = 'Add category';
                $actions['reset_categories'] = 'Reset categories';
            }
        }

        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        if ( !FvFunctions::curr_user_can() ) {
            return false;
        }

        if ( $this->current_action() && !empty($_REQUEST[ $this->_args['singular'] ]) && is_array($_REQUEST[ $this->_args['singular'] ]) ) {
            $ids = $_REQUEST[ $this->_args['singular'] ];
            array_walk($ids, 'absint');

            $entries = ModelCompetitors::q()
                ->where_in('id', $ids)
                ->find();

            foreach ($entries as $entry){
                if ( 'delete' === $this->current_action() ) {
                    $entry->delete( false );
                } else if ( 'delete__with_mail' === $this->current_action() ) {
                    $admin_comment = !empty($_POST['admin_comment']) ? sanitize_text_field($_POST['admin_comment']) : '';
                    $entry->delete( true, $admin_comment );
                } else if ( 'set__approved__with_mail' === $this->current_action() ) {
                    $admin_comment = !empty($_POST['admin_comment']) ? sanitize_text_field($_POST['admin_comment']) : '';
                    $entry->approve( true, $admin_comment );
                } else if ( 'set__approved' === $this->current_action() ) {
                    $entry->approve( false );
                } else if ( 'set__on_moderation' === $this->current_action() ) {
                    $entry->status = FV_Competitor::MODERATION;
                    $entry->save();
                } else if ( 'set__draft' === $this->current_action() ) {
                    $entry->status = FV_Competitor::DRAFT;
                    $entry->save();
                } else if ( 'reset_categories' === $this->current_action() ) {
                    $entry->resetCategories();
                }else if ( 'add_category' === $this->current_action() && !empty($_REQUEST['category_to_add']) ) {
                    $entry->setCategories( absint($_REQUEST['category_to_add']) );
                }

            }
        }
    }


    /**
     * Generate the table rows
     *
     * @since 3.1.0
     * @access public
     */
    public function display_rows() {
        printf(
            '<tr><td colspan="%d"><span class="spinner is-active"></span></td></tr>',
            count($this->_column_headers)
        );
    }

    /**
     * Generates the columns template for a single row of the table
     *
     * @since 2.2.512
     * @access protected
     *
     */
    protected function single_row_columns_template() {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        echo '<tr class="id{{data.id}} status{{data.status}}" data-id="{{data.id}}">';
        foreach ( $columns as $column_name => $column_display_name ) {
            $classes = "$column_name column-$column_name";
            if ( $primary === $column_name ) {
                $classes .= ' has-row-actions column-primary';
            }

            if ( in_array( $column_name, $hidden ) ) {
                $classes .= ' hidden';
            }

            // Comments column uses HTML in the display name with screen reader text.
            // Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
            $data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

            $attributes = "class='$classes' $data";


            if ( 'cb' == $column_name ) {
                echo '<th scope="row" class="check-column">';
                echo sprintf(
                    '<input type="checkbox" name="%1$s[]" class="checkbox-%1$s" value="{{data.id}}" />',
                    /*$1%s*/ $this->_args['singular']  //Let's simply repurpose the table's singular label ("movie")
                );
                echo '</th>';
            } else {
                echo "<td $attributes>";
                echo '{{{data.' , $column_name. '}}}';
                echo "</td>";
            }
        }
        echo '</tr>';
    }

    /**
     * Display the search box.
     *
     * @since 3.1.0
     * @access public
     *
     * @param string $text The search button text
     * @param string $input_id The search input id
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;

        $input_id = $input_id . '-search-input';
        $search_where_val = isset($_REQUEST['search_where']) ? sanitize_text_field( $_REQUEST['search_where'] ) : '';
        $search_by_category_ID = isset($_REQUEST['search_by_category']) ? absint( $_REQUEST['search_by_category'] ) : '';

        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" pattern=".{1,50}"/>
            <?php submit_button( $text, 'button', '', false, array('id' => 'search-submit') ); ?>
            <select name="search_where" id="<?php echo $input_id ?>-where">
                <?php foreach ( $this->get_search_where_arr() as $s_key => $s_data ): ?>
                    <option value="<?php echo $s_key; ?>" <?php selected($s_key, $search_where_val); ?>><?php echo $s_data['text']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ( $this->contest && $this->contest->isCategoriesEnabled() ): ?>
                <select name="search_by_category" id="<?php echo $input_id ?>-by_category">
                    <option>Do not filter by category</option>
                    <?php foreach ( $this->contest->getCategories() as $category ): ?>
                        <option value="<?php echo $category->term_id; ?>" <?php selected($category->term_id, $search_by_category_ID); ?>><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </p>
        <?php
    }

    public function get_search_where_arr(  ) {
        return array(
            "details"   => array(
                'text'  => 'Name, Description, Full description',
                'fields'  => array('name', 'description', 'full_description'),
            ),
            "user_email"   => array(
                'text'  => 'user Email',
                'fields'  => array('user_email'),
            ),
            "user_ip"   => array(
                'text'  => 'user IP',
                'fields'  => array('user_ip'),
            ),
            "user_id"   => array(
                'text'  => 'user ID',
                'fields'  => array('user_id'),
            ),
            "meta"   => array(
                'text'  => 'Meta values',
                'fields'  => array(),
            ),
            "url"   => array(
                'text'  => 'Image url',
                'fields'  => array('url'),
            ),
            "votes_count"   => array(
                'text'  => 'Votes count',
                'fields'  => array('votes_count'),
            ),
            "id"   => array(
                'text'  => 'Competitor ID',
                'fields'  => array('id'),
            ),
        );
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
    function prepare_items() {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = FV_RES_OP_PAGE;

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
        //$total_items = ModelContest::query()->find(true);

        $orderBy = (!empty($_REQUEST['orderby'])) ? sanitize_title($_REQUEST['orderby']) : 'id'; //If no sort, default to false
        $order = (!empty($_REQUEST['order'])) ? strtoupper(sanitize_title($_REQUEST['order'])) : 'DESC'; //If no order, default to asc
        $contest_id = isset($_REQUEST['contest']) ? (int)$_REQUEST['contest'] : 0; //If no order, default to asc
/*
        if ( $orderBy == 'added_date' ) {
            $orderBy = 'UNIX_TIMESTAMP(added_date)';
        }*/

        $query = ModelCompetitors::query()
            ->what_fields(array(
                '`t`.*',
            ))
            ->limit( $per_page )
            ->offset( ( $current_page - 1 ) * $per_page )
            ->withContest()
            ->what_fields( array('`c`.categories_on as contest_categories_on') );

        // First add 'order_position', else it will not work correct
        if ( $orderBy == 'id' ) {
            $query->sort_by('IFNULL(order_position, 99999)', $order == 'DESC'? 'ASC' : 'DESC');
            //$query->sort_by('order_position', $order == 'DESC'? 'ASC' : 'DESC');
        }
        $query->sort_by( $orderBy, $order );

        $search = !empty($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        $search_where_arr = $this->get_search_where_arr();
        $search_where = !empty($_REQUEST['search_where']) && array_key_exists( $_REQUEST['search_where'], $search_where_arr ) ? $_REQUEST['search_where'] : 'details';

        if ( strlen($search) >= 1 ) {

            if ( $search_where !== 'meta' ) {
                // For numeric Fields
                if ( $search_where == 'votes_count' || $search_where == 'user_id'|| $search_where == 'id' ) {
                    $query->where( $search_where_arr[$search_where]['fields'][0], $search );
                } else {
                    $query
                        ->search( $search )
                        ->set_searchable_fields( $search_where_arr[$search_where]['fields'] );
                }
            } else {
                $query->leftJoin(
                    ModelMeta::q()->tableName(),
                    "M",
                    "`M`.`contestant_id` = `t`.`id`",
                    array(),
                    '`M`.`value` LIKE "%' . $search . '%" AND `M`.`custom`'
                );
            }

        }

        // !! Filter By Category !!
        $category_id = isset($_REQUEST['search_by_category']) ? (int)$_REQUEST['search_by_category'] : 0;

        if ( $category_id ) {
            $query->byCategory($category_id, $contest_id);
                //->byContest();
        }
        // !! Filter By Category :: END !!

        if ( $this->_is_moderation_page() ) {
            $query
                ->where( 'status' , ST_MODERATION )
                ->what_fields( array('`c`.name as contest_name') );
        } else {
            $query->where( 'contest_id', $contest_id );
        }

        if ( !FvFunctions::is_ajax() ) {
            $this->query = $query;
        }

        $this->items = $query->find(false, false, true, true, true);

        $total_items = $query->find(true);

        $this->js_vars = array(
            'contest'       => $contest_id,
            'order'         => $order,
            'orderby'       => $orderBy,
            's'             => $search,
            'search_where'  => $search_where,
            'search_by_category'  => $category_id,
        );

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    /**
     * @return bool
     */
    public function _is_moderation_page() {
        return $this->is_moderation_page || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'fv-moderation') ? true : false;
    }

    public function _get_entries_array() {
        $entries = array();
        $N = 0;

        list($columns, $hidden, $sortable, $primary) = $this->get_column_info();

        foreach ( $this->items as $key => $item ) :

            $entries[$N] = $this->_get_entry($item, $columns);

            $N++;
        ENDFOREACH;

        return $entries;
    }

    public function _get_entry( $item, $columns = false ) {
        if ( !$columns ) {
            $columns = $this->get_columns();
        }

        $entry['id'] = $item->id;
        $entry['status'] = $item->status;

        foreach ($columns as $column_name => $column_display_name) {
            $entry[$column_name] = $this->_get_entry_column($item, $column_name);
        }

        return $entry;
    }

    public function _get_entry_column( $item, $column_name ) {

        if ('cb' != $column_name) {
            if ( method_exists( $this, 'column_' . $column_name ) ) {
                return call_user_func( array( $this, 'column_' . $column_name ), $item );
            } else {
                return $this->column_default($item, $column_name);
            }
        }

    }

    public function ajax_response()
    {
        $page = (int)$_REQUEST['paged'];

        // To allow correct run "process_bulk_actions"
        unset($_REQUEST['action']);

        //Fetch, prepare, sort, and filter our data...
        $this->prepare_items();

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $data = array(
            'entries' => $this->_get_entries_array( $this->get_pagination_arg('page') ),
            'pagination' => array(
                'top' => $pagination_top,
                'bottom' => $pagination_bottom,
            ),
            'headers' => $headers,
        );

        fv_AJAX_response(
            true,
            'ready',
            array(
                'data'=> $data,
                'paged'=>$page,
                'total_pages'=> $this->get_pagination_arg('total_pages'),
            )
        );
    }

    /**
     * Send required variables to JavaScript land
     *
     * @access public
     */
    public function _js_vars() {
        $args = array_merge( array(
            'action'        => 'fv_competitors_list__get_page',
            '_ajax_nonce'   => wp_create_nonce('fv-competitors-list-nonce'),
            'paged'         => $this->get_pagination_arg('page'),
            'show'          => 'competitors',
            'page'          => isset($_REQUEST['page']) ? $_REQUEST['page'] : 'fv',
            'screen'        => array(
                'id'   => $this->screen->id,
                'base' => $this->screen->base,
            )
        ), (array)$this->js_vars );

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $data = array(
            $this->get_pagenum() => array(
                'entries' => $this->_get_entries_array(),
                'pagination' => array(
                    'top' => $pagination_top,
                    'bottom' => $pagination_bottom,
                ),
                'headers' => $headers,
            ),
            'total_pages' => $this->get_pagination_arg('total_pages'),
        );

        $data = $this->_prefetch_pages($data);

        printf( "<script type='text/javascript'>list_args = %s;</script>\n", wp_json_encode( $args ) );
        printf( "<script type='text/javascript'>list_data = %s;</script>\n", wp_json_encode( $data ) );

        echo '<script type="text/html" id="tmpl-contestant-row">';
        echo $this->single_row_columns_template();
        echo '</script>';
    }

    public function _prefetch_pages( $data ) {
        $total_pages = $this->get_pagination_arg('total_pages');
        $curr_page = $this->get_pagenum();

        if ( $total_pages == 1 ) {
            return $data;
        }

        if ( $total_pages !== $curr_page ) {
            // Fetch new pages
            $offset = $curr_page * FV_RES_OP_PAGE;      // 1*15 = 15 for example
            $next_page = $curr_page + 1;
        } else {
            $offset = ($curr_page - 1) * FV_RES_OP_PAGE;     // (8-1) * 15
            $next_page = $curr_page - 1;

            if ( $offset < 0 ) {
                $offset = 0;
            }
        }
        //$limit = FV_RES_OP_PAGE;

        $this->items = $this->query
            //->limit($limit)
            ->offset($offset)
            ->find(false, false, true, true, true);

        ob_start();
        $this->print_column_headers();
        $headers = ob_get_clean();

        // Fix to render next page pagination
        $_REQUEST['paged'] = $next_page;

        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();

        ob_start();
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();

        $data[$next_page] = array(
            'entries' => $this->_get_entries_array(),
            'pagination' => array(
                'top' => $pagination_top,
                'bottom' => $pagination_bottom,
            ),
            'headers' => $headers,
        );

        return $data;
    }

}
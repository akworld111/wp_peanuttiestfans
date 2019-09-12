<?php
/**
 * Variables:
 *      $listTable  object
 */
defined('ABSPATH') or die("No script kiddies please!");
?>

<div class="wrap fv-page">
    <h2><?php echo __('Photo contests', 'fv'); ?>
        <a href="?page=<?php echo $_REQUEST['page']; ?>&show=new-contest" class="add-new-h2"><?php echo __('Add new', 'fv'); ?> </a>
    </h2>

    <?php
        $is_delete = ( isset($_GET['action']) && 'delete' == $_GET['action'] )
                    || (isset($_GET['action2']) && 'delete' == $_GET['action2']) ? true : false;
        if ( $is_delete ) {
            echo '<div id="setting-error-settings_updated" class="updated settings-error">
                <p>
                    <strong>' . __('Contest deleted.', 'fv') . '</strong>
                </p>
             </div>';
        }
    ?>

    <div class="fv_content_wrapper">
        <div class="fv_content_cell" id="fv-content">
            <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
                <p><?php echo __('Create contest, copy shortcode and insert it into the page/post text.', 'fv') ?></p>
                <p><a href="https://wp-vote.net/shortcode/" target="_blank"><?php _e('All available Shortcodes >>', 'fv') ?></a></p>
            </div>

            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="contests-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $listTable->display() ?>
            </form>
        </div>  <!-- .fv_content_cell :: END -->

        <div id="fv-sidebar"><?php include('_sidebar.php') ?></div><!-- #fv-sidebar :: END -->
    </div>
</div>
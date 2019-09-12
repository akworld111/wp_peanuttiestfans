<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>

<div class="wrap fv-page">
    <?php do_action('fv_admin_notices'); ?>
    <hr />
    <h2><?php _e('Subscribers list', 'fv') ?></h2>
    <p><?php _e('New records added when selected Additional security is "Subscribe form" or "Social login" (only with using Facebook, Vkontakte login and a few other).', 'fv') ?></p>
    <p><?php _e('To remove all records for contest - go to contest config page and click <strong>Clear "Subscribers" for this contest</strong> in sidebar.', 'fv') ?></p>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="log-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $Table->search_box( __('Search by `name, email, type` > 1 symbol', "fv"), 'search_field') ?>
        <!-- Now we can render the completed list table -->
        <?php $Table->display() ?>
    </form>

    <br/>
    
    <form class="fv-run-export-form">
        <button type="submit" class="button"><?php _e('Export to csv', 'fv') ?></button>

        <?php wp_nonce_field('fv_export_nonce'); ?>
        
        <input type="hidden" name="type" value="subscribers_list">
        <input type="hidden" name="action" value="fv_export">
        
        <?php
        $contests = FV_Admin_Contest::get_contests_list_flat();
    
        $selected_id = false;
        if (isset($_GET['contest_id']) && $_GET['contest_id'] > 0) {
        $selected_id = (int)$_GET['contest_id'];
        }
    
        ?>
        from
    
        <select name="contest_id" class="select2">
            <option value="">All contests</option>
            <?php foreach ($contests as $c_id => $c_name): ?>
                <option value="<?php echo $c_id ?>" <?php echo ($selected_id == $c_id) ? 'selected' : '' ?> ><?php echo $c_name ?></option>
            <?php endforeach; ?>
        </select>
    
        up to
        <select name="max">
            <option value="5000">5000</option>
            <option value="3000">3000</option>
            <option value="10000">10 000</option>
            <option value="15000">15 000</option>
            <option value="0">all</option>
        </select>
        (to avoid "out of memory" errors)
    </form>

</div>

<script>
    jQuery(document).ready(function () {
        jQuery('select#fv-filter-contest').on('change', function () {
            var contestFilter = jQuery("select#fv-filter-contest").val();

            if (contestFilter != '') {
                document.location.href = 'admin.php?page=fv-subscribers-list&contest_id=' + contestFilter;
            } else {
                document.location.href = 'admin.php?page=fv-subscribers-list';
            }
        });
    });
</script>

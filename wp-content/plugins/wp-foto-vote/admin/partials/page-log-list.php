<?php
defined('ABSPATH') or die("No script kiddies please!");
?>

<script>
    jQuery(document).ready(function () {
        jQuery('select#fv-filter-contest').on('change', function () {
            var contestFilter = jQuery("select#fv-filter-contest").val();

            if (contestFilter != '') {
                document.location.href = 'admin.php?page=fv-vote-log&contest_id=' + contestFilter;
            } else {
                document.location.href = 'admin.php?page=fv-vote-log';
            }
        });

        jQuery('select#fv-filter-contest-photo').on('change', function () {
            var contestFilter = jQuery("select#fv-filter-contest").val();
            var contestFilterPhoto = jQuery("select#fv-filter-contest-photo").val();

            if (contestFilter != '') {
                document.location.href = 'admin.php?page=fv-vote-log&contest_id=' + contestFilter + '&photo_id=' + contestFilterPhoto;
            } else {
                document.location.href = 'admin.php?page=fv-vote-log';
            }
        });
    });
</script>

<div class="wrap fv-page">
    <?php do_action('fv_admin_notices'); ?>
    <style type="text/css">
        .ml50 {margin-left: 30px;}
        .tablenav .actions label {display: inline-block;}
        .tablenav .actions select[name*="contest-filter"] {float: none;}
    </style>

    <h2><?php _e('Voting log / can be cleared on the page, where you can edit contest', 'fv') ?></h2>

    <p>
        <?php _e('There your can see votes log. It can help you to check voting for fraud.
            As example if your see a lot similar `browsers` voted for one person or many empty `refer`.<br/>
            Also you can compare `change` field for check voting activity almost at the same time by one photos.', 'fv') ?>
        <span style="color:red;"><?php _e('!Important - removing records from log not decreases photo votes.', 'fv') ?></span>
    </p>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="log-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $Table->search_box(__('Search by `ip, browser, referer, country, user_id` > 1 symbol', "fv"), 'search_field') ?>
        <!-- Now we can render the completed list table -->
        <?php $Table->display() ?>
    </form>

    <form class="fv-run-export-form">
        <h3><?php _e('Export data as csv', 'fv') ?></h3>

        <button type="submit" class="button"><?php _e('Export to csv', 'fv') ?></button>

        <?php wp_nonce_field('fv_export_nonce'); ?>

        <input type="hidden" name="type" value="log_list">
        <input type="hidden" name="action" value="fv_export">

        <input type="hidden" name="contest_id" value="<?php echo esc_attr($contest_id); ?>">
        <input type="hidden" name="competitor_id" value="<?php echo esc_attr($competitor_id); ?>">

        records for
        <select name="period">
            <option value="this_month"><?php _e('this month', 'fv') ?></option>
            <option value="30"><?php _e('last 30 days', 'fv') ?></option>
            <option value="prev_month"><?php _e('previous month', 'fv') ?></option>
            <option value="90"><?php _e('last 90 days', 'fv') ?></option>
            <option value="180"><?php _e('last 180 days', 'fv') ?></option>
            <option value="all_time"><?php _e('all', 'fv') ?></option>
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
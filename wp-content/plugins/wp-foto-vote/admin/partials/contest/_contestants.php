<div class="metabox-holder columns-1 b-wrap">
    <div class="postbox ">
        <h3 class="hndle"><span>
            <?php if ( $contest->isVotingDatesActive() ): ?>
                <span class="label label-info"><?php _e('Voting dates active', 'fv'); ?></span>
            <?php else: ?>
                <span class="label label-warning"><?php _e('Voting dates inactive', 'fv'); ?></span>
            <?php endif; ?>
            &nbsp;
            <?php if ( $contest->isUploadDatesActive() ): ?>
                <span class="label label-info"><?php _e('Upload dates active', 'fv'); ?></span>
            <?php else: ?>
                <span class="label label-warning"><?php _e('Upload dates inactive', 'fv'); ?></span>
            <?php endif; ?>

        </span></h3>
        <div class="inside b-wrap">
            <div id="sv_table">
                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="competitors-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

                    <!-- Now we can render the completed list table -->
                    <?php $listTable->search_box(__('Search data > 1 symbol by', "fv"), 'fv') ?>

                    <!-- Now we can render the completed list table -->
                    <?php $listTable->display() ?>
                </form>

                <?php
                //include FV::$ADMIN_PARTIALS_ROOT . '_table_units.php';
                ?>
            </div>

            <div class="competitors-global-actions">
                <br>
                <form class="fv-run-export-form">
                    <?php wp_nonce_field('fv_export_nonce'); ?>

                    <input type="hidden" name="type" value="contest_data">
                    <input type="hidden" name="action" value="fv_export">

                    <input type="hidden" name="contest_id" value="<?php echo esc_attr($contest->id); ?>">

                    <button class="button" type="submit">
                        <span class="dashicons dashicons-upload"></span> <?php _e('Export all photos to csv', 'fv') ?>
                    </button>
                </form>

                <button type="button" class="button" data-call="Contest.resetVotes" data-contest="<?php echo $contest->id ?>">
                    <span class="dashicons dashicons-no-alt"></span> <?php echo __('Reset all votes in this contest', 'fv') ?>
                    <?php fv_get_tooltip_code(__('This will set votes count to 0 for all photos', 'fv')) ?>
                </button>

            </div>

        </div>
    </div>
</div>

<!-- Move to contest Modal HTML template -->
<script type="text/html" id="tpl-clone-contestant-move">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Select contest where move contestant "*name*":</h4>
    </div>
    <div class="modal-body">
        <select class="select2 fv-move-to-contest-id">
            <?php
            if ( !empty($all_contests) ) :
                foreach ($all_contests as $single_contest) {
                    echo '<option value="', $single_contest->id, '"> ', $single_contest->name, '</option>';
                }
            endif;
            ?>
        </select>
        <button type="button" class="btn btn-primary btn-move-go" data-call="Competitor.moveGo" data-contestant="" data-nonce="">
            <span class="dashicons dashicons-migrate"></span> Move
        </button>
    </div>
    <div class="modal-footer">
        <p class="pull-left">
            <strong>Note:</strong> If you created contest, but can't found it in a list - try refresh page.
        </p>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>
</script>
<!-- Move to contest Modal HTML template :: END -->

<script type="text/html" id="tmpl-fv-contestant-meta">
    <div class="row">
        <div class="col-md-7">
            <input name="form[meta_key][{{ data }}]" class="form-control" type="text" value="" placeholder="required" required/>
        </div>
        <div class="col-md-16">
            <input name="form[meta_val][{{ data }}]" class="form-control" type="text" value=""/>
        </div>
        <div class="col-md-1">
            <a class="meta--remove" href="#0" data-call="Competitor.removeMetaRow"><span class="dashicons dashicons-trash"></span></a>
        </div>
        <input class="meta--type" name="form[meta_type][{{ data }}]" type="hidden" value="new"/>
    </div>
</script>



<script type="text/html" id="tmpl-fv-contestant-meta">
    <div class="row">
        <div class="col-md-7">
            <input name="form[meta_key][{{ data }}]" class="form-control" type="text" value="" placeholder="required" required/>
        </div>
        <div class="col-md-16">
            <input name="form[meta_val][{{ data }}]" class="form-control" type="text" value=""/>
        </div>
        <div class="col-md-1">
            <a class="meta--remove" href="#0" data-call="Competitor.removeMetaRow"><span class="dashicons dashicons-trash"></span></a>
        </div>
        <input class="meta--type" name="form[meta_type][{{ data }}]" type="hidden" value="new"/>
    </div>
</script>

<script type="text/html" id="tmpl-fv-competitors-multi-form">
    <?php include '_photos_list_form.php'; ?>
</script>

<script type="text/html" id="tmpl-fv-competitors-multi-form-one">
    <?php include '_photos_list_form_item.php'; ?>
</script>

<?php
do_action('fv/admin/page/contest_single/competitors/tpls');
?>
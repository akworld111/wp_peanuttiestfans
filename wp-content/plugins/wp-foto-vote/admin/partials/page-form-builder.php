<div class="wrap">
    <h2>
        <?php _e('Upload form builder', 'fv') ?> #<?php echo $form->ID; ?>
        <a href="<?php echo admin_url("admin.php?page=fv-formbuilder"); ?>" class="add-new-h2">< Back to all Forms</a>
    </h2>
    <?php if ( empty($form) ) : ?>
        Sorry, but no Form found. <a href="<?php echo admin_url("admin.php?page=fv-formbuilder"); ?>"><span class="dashicons dashicons-undo"></span> Back to Forms >></a>
    <?php else: ?>
        <p>
            <?php _e('<strong>Note:</strong> When you will used multiupload, please be sure, now limit by image size works just for 1 field and Upload limit count +1 by upload step now (example - you set up limit as 2 and shows 5 file fields, than user can upload 2*5 images max).' , 'fv') ?>
        </p>
        <div class='fb-main'></div>

        <script>
            var $ = jQuery;
            FvLib.addHook('doc_ready', function() {
                var fb = new Formbuilder({
                    selector: '.fb-main',
                    bootstrapData: jQuery.parseJSON('<?php echo $form->fields; ?>'),
                    bootstrapTitle: '<?php echo $form->title; ?>',
                });

                Formbuilder.options.HTTP_ENDPOINT = "<?php echo add_query_arg('form_id',$form->ID, wp_nonce_url( admin_url('admin-ajax.php') )); ?>";
                Formbuilder.options.HTTP_SAVE_ACTION = "fv_save_form_structure";
                Formbuilder.options.HTTP_RESET_ACTION = "fv_reset_form_structure";
                Formbuilder.options.HTTP_METHOD = "POST";
                Formbuilder.options.FORM_ID = <?php echo $form->ID; ?>;
            });
        </script>
    <?php endif; ?>
</div>
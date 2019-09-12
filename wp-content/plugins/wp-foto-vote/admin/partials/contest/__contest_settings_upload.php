<div class="meta-box-sortables ">
    <div class="postbox ">
        <h3 class="hndle"><span>
            <?php echo __('Upload settings', 'fv') ?>
            <?php if ( $contest->isUploadDatesActive() ): ?>
                <span class="label label-info"><?php _e('Upload dates active', 'fv'); ?></span>
            <?php else: ?>
                <span class="label label-warning"><?php _e('Upload dates inactive', 'fv'); ?></span>
            <?php endif; ?>
        </span></h3>
        <div class="inside">
                <!-- ============= Upload ============= -->
            <div class="row">
                <div class="form-group col-sm-12">

                    <label><i class="fvicon fvicon-calendar"></i> <?php echo __('Upload date start', 'fv'); ?></label>
                    <input type="text" class="datetime form-control" id="upload_date_start" name="upload_date_start" value="<?php echo $contest->upload_date_start; ?>">
                    <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>

                </div>

                <div class="form-group col-sm-12">
                    <label><i class="fvicon fvicon-calendar"></i> <?php echo __('Upload date finish', 'fv'); ?></label>
                    <?php fv_get_tooltip_code(__('When time end, upload form will be hidden', 'fv')) ?>
                    <input type="text" class="datetime form-control" id="upload_date_finish" name="upload_date_finish" value="<?php echo $contest->upload_date_finish; ?>">
                    <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-12">
                    <label style="color: #0073aa;" for="upload_enable"><i class="fvicon fvicon-download"></i>
                        <?php echo __('Display upload form before contest?', 'fv'); ?>
                    </label>
                    <select id="upload_enable" name="upload_enable" class="form-control">
                        <option value="1" <?php selected('1', $contest->upload_enable); ?>> <?php _e('Yes', 'fv') ?> <?php _e('["Upload dates" must be active]', 'fv') ?></option>
                        <option value="0" <?php selected('0', $contest->upload_enable); ?>> <?php _e('No', 'fv') ?> <?php _e('[You can still use "Upload shortcode"]', 'fv') ?></option>
                    </select>
                    <small><?php _e('Don\'t applies to "Upload shortcode".', 'fv'); ?></small>
                </div>


                <div class="form-group col-sm-12">
                    <label for="moderation_type" style="color: #0073aa;"><i class="fvicon fvicon-signup"></i>
                        <?php echo __('Entries Moderation?', 'fv') ?> <?php fv_get_tooltip_code(__('Admin must moderate photos before publishing on site?', 'fv')) ?>
                    </label>
                    <select id="moderation_type" name="moderation_type" class="form-control">
                        <option value="pre" <?php /** @noinspection PhpExpressionResultUnusedInspection */
                        ( isset($contest->id) )? selected('pre', $contest->moderation_type) : ''?>> <?php _e('Need moderation', 'fv') ?></option>
                        <option value="after" <?php /** @noinspection PhpExpressionResultUnusedInspection */
                        ( isset($contest->id) )? selected('after', $contest->moderation_type) : ''?>> <?php _e('No, thanks', 'fv') ?></option>
                    </select>
                </div>
            </div>

            <div class="row">

                <div class="form-group col-sm-12">
                    <label for="upload_limit_by_user">
                        <i class="fvicon fvicon-user-circle-o"></i> <?php echo __('User must be logged for upload?', 'fv') ?>
                    </label>
                    <select id="upload_limit_by_user" name="upload_limit_by_user" class="form-control fv-js-relation" data-r-el=".field-upload_limit_by_role" data-r-show-on="role">
                        <option value="global" <?php selected('global', $contest->upload_limit_by_user); ?>> <?php _e('Use global settings', 'fv') ?></option>
                        <option value="yes" <?php selected('yes', $contest->upload_limit_by_user); ?>> <?php _e('Yes', 'fv') ?></option>
                        <option value="role" <?php selected('role', $contest->upload_limit_by_user); ?>> <?php _e('Yes, with specified Role', 'fv') ?></option>
                        <option value="no" <?php selected('no', $contest->upload_limit_by_user); ?>> <?php _e('No', 'fv') ?></option>
                    </select>
                </div>

                <div class="form-group col-sm-12 field-upload_limit_by_role <?php echo $contest->upload_limit_by_user !== 'role' ? 'hidden' : ''; ?>">
                    <label for="limit_by_role">
                        <?php _e('Required user role(s):', 'fv') ?>
                        <?php fv_get_tooltip_code(__('Use Shift or Ctrl key for multi-select', 'fv')) ?>
                    </label>
                    <select id="upload_limit_by_role" name="upload_limit_by_role[]" class="form-control" multiple="" size="6">
                        <?php fv_dropdown_roles( $contest->upload_limit_by_role ); ?>
                    </select>
                </div>

            </div>
            <div class="row">

                <div class="form-group col-sm-12">
                    <label for="upload_limit_size">
                        <?php echo __('Use local image size limit?', 'fv') ?>
                    </label>
                    <select id="upload_limit_size" name="upload_limit_size" class="form-control fv-js-relation" data-r-el=".field-upload_max_size" data-r-show-on="yes">
                        <option value="global" <?php selected('global', $contest->upload_limit_size); ?>> <?php _e('Use global settings', 'fv') ?></option>
                        <option value="yes" <?php selected('yes', $contest->upload_limit_size); ?>> <?php _e('Yes', 'fv') ?></option>
                        <option value="no" <?php selected('no', $contest->upload_limit_size); ?>> <?php _e('No', 'fv') ?></option>
                    </select>
                </div>

                <div class="form-group col-sm-12 field-upload_max_size <?php echo $contest->upload_limit_size !== 'yes' ? 'hidden' : ''; ?>">
                    <label for="upload_limit_size">
                        <?php echo __('Max image size in KB:', 'fv') ?>
                    </label>

                    <input type="number" class="form-control" name="upload_max_size" value="<?php echo $contest->upload_max_size; ?>" min="0" max="50000" step="1">
                    <small>Max size in kilobytes, 1024 KB = 1 MB.</small>

                </div>
                
            </div>

            <div class="row">

                <!-- ============= Upload ============= -->
                <div class="form-group col-sm-12">
                    <label for="form_id">
                        <?php _e('Form', 'fv') ?>
                    </label>
                    <br/>
                    <select id="form_id" name="form_id" class="form-control">
                        <?php if (!empty($all_forms)): foreach($all_forms as $one_form):
                            echo '<option value="' , $one_form->ID , '"' , selected( $one_form->ID, $contest->form_id ) , '>', $one_form->title, ' [id:',$one_form->ID, ']</option>';
                        endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group col-sm-12">
                    <label for="upload_theme"><i class="fvicon fvicon-box-add"></i>
                        <?php echo __('Upload form skin', 'fv'); ?>

                    </label>
                    <br/>
                    <select id="upload_theme" name="upload_theme" class="form-control">
                        <option value="default" <?php selected('default', $contest->upload_theme); ?>>default</option>
                        <?php do_action('fv/admin/contest_settings/upload_theme', $contest); ?>
                    </select>
                </div>


            </div>

                <div class="form-group">
                    <label for="redirect_after_upload_to">
                        <?php _e('Redirect to page after success upload (3,5 sec delay):', 'fv') ?>
                    </label>
                    <select name="redirect_after_upload_to" id="redirect_after_upload_to" class="fv-posts-dropdown" disabled="disabled">
                        <option value="0" class="do-not-remove-option"><?php echo esc_attr( __( 'No redirect' ) ); ?></option>
                        <?php if ( $contest_redirect_after_upload_to_page ) : ?>
                            <option value="<?php echo $contest_redirect_after_upload_to_page->ID; ?>" selected><?php echo $contest_redirect_after_upload_to_page->post_title, '[', $contest_redirect_after_upload_to_page->post_type, ']'; ?></option>
                        <?php endif; ?>
                    </select>
                    <a href="#0" class="fv-init-posts-dropdown" data-what-get="all" data-what-get="all" data-post-id="<?php echo ($contest->redirect_after_upload_to)? $contest->redirect_after_upload_to : ''; ?>">edit</a>

                </div>
                <div class="form-group">
                        <label for="max_uploads_per_user">
                            <?php echo __('The maximum number of one user uploaded photo', 'fv') ?> <?php fv_get_tooltip_code(__('Maximum number of pictures uploaded by each user in this competition. 0 = no limits', 'fv')) ?>
                        </label>
                        <input type="number" id="max_uploads_per_user" name="max_uploads_per_user" value="<?php echo $contest->max_uploads_per_user; ?>" style="width: 60px;">
                        <small><?php _e('please setup by what params limit upload [ip, email, user id] for make it working (in Settings => Upload)', 'fv'); ?></small>
                </div>

                <div class="clearfix"></div>
        </div>
    </div>
</div>

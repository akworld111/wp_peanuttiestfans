<?php
/** @var FV_Contest $contest */
/** @var FV_Competitor $unit */
$attachmentDetails = $unit->getAttachmentDetails();

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="fv_popup_label">
        <?php echo ($unit->id)? __('Editing contestant:', 'fv') : __('Adding contestant:', 'fv')  ?>
        <small><?php echo __('Please don\'t use " (double quotes) in fields', 'fv') ?></small>
    </h4>
</div>

<div class="modal-body status<?php echo esc_attr($unit->status); ?>"">

    <form class="clear clearfix">
        <div class="col-md-11">

            <div class="form-group-image clearfix clear">
                <?php if( $unit->image_id ): ?>
                    <?php FV_Public_Single::get_instance()->render_main_image( $unit->getImageUrl(), $unit, 'img-responsive competitor-attachment' ); ?>
                <?php else: ?>
                    <img src="<?php echo FV::$ASSETS_URL ?>img/no-photo.png" class="img-responsive competitor-attachment">
                <?php endif; ?>

                <div class="competitor-attachment__details <?php echo !$attachmentDetails ? 'hidden' : ''; ?>">
                    <p>
                        <strong><?php echo __('File size', 'fv') ?>:</strong>
                        <span class="competitor-attachment__details__filesize"><?php echo $attachmentDetails['file_size']; ?></span>
                        <br>
                        <strong><?php echo __('Dimensions (W x H)', 'fv') ?>:</strong>
                        <span class="competitor-attachment__details__dimensions"><?php echo $attachmentDetails['width'], 'x', $attachmentDetails['height']; ?></span> px.
                        <?php if( $unit->image_id ): ?>
                            <br/>
                            <a class="competitor-attachment__details__edit_link" href="<?php echo admin_url("post.php?action=edit&post=".$unit->image_id) ; ?>" target="_blank" title="edit attachment">
                                Edit media
                            </a>
                        <?php endif; ?>

                    </p>
                </div>

                <?php do_action('fv/admin/form_edit_photo/attachment_details/extra', $unit); ?>

                <div class="row">
                    <div class="col-sm-19">
                        <div class="input-group">
                            <div class="input-group-addon">url</div>
                            <input type="text" class="form-control" name="form[image]" id="image-src" value="<?php echo $unit->url; ?>" placeholder="image url">
                        </div>
                        <input type="hidden" name="form[image_id]" id="image-id" value="<?php echo $unit->image_id; ?>">
                        <input type="hidden" name="form[mime_type]" id="mime-type" value="">
                    </div>
                    <div class="col-sm-5">
                        <button type="button" class="btn" data-call="Competitor.selectMedia">Select</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-13">

            <?php if( $contest->isFinished() ): ?>
                <div class="row">

                    <div class="form-group col-sm-5 form-group-place">
                        <label><?php echo __('Place', 'fv') ?></label>
                        <select class="form-control" name="form[place]" type="number">
                            <option value="0">none</option>
                            <?php for( $N = 1; $N <= $contest->winners_count; $N++ ): ?>
                                <option value="<?php echo $N; ?>" <?php selected($N, $unit->place); ?>><?php echo $N; ?>th</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-16 form-group-place_caption">
                        <label><?php echo __('Place Caption', 'fv') ?></label> <small>max length - 100 chars, some html allowed</small>
                        <div class="row">
                            <div class="col-sm-20">
                                <input class="form-control" id="place_caption" name="form[place_caption]" type="text" value="<?php echo stripslashes($unit->getPlaceCaption()); ?>" onkeyup="fv_count_chars(this);" disabled/>
                                <span class="need-count-chars"><?php echo mb_strlen(stripslashes($unit->getPlaceCaption())); ?></span>
                            </div>
                            <div class="col-sm-4">
                                <a href="#edit" class="allow-edit-input-a" data-call="Core.unlockInput" data-target="#place_caption"><i class="fvicon-pencil"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

            <div class="row">
                <div class="form-group <?php echo $contest->voting_type == 'rate' ? 'col-sm-13' : 'col-sm-18'; ?> form-group-name">
                    <label><?php echo __('Name', 'fv') ?></label> <small>max length - 255 chars, no html allowed</small>
                    <input class="form-control" name="form[name]" type="text" value="<?php echo esc_attr($unit->name); ?>" onkeyup="fv_count_chars(this);"/>
                    <span class="need-count-chars"><?php echo mb_strlen(esc_attr($unit->name)); ?></span>
                </div>

                <div class="form-group col-sm-3 form-group-order_position">
                    <label><?php echo __('List pos.', 'fv') ?></label>
                    <input class="form-control" name="form[order_position]" type="number" size="5" value="<?php echo $unit->order_position; ?>" />
                </div>

                <div class="form-group col-sm-3 form-group-votes">
                    <label><?php echo __('Votes', 'fv') ?></label>
                    <input class="form-control" name="form[votes]" type="number" size="4" value="<?php echo $unit->votes_count; ?>" />
                </div>

            <?php if ( $contest->voting_type == 'rate' ) :?>
                <div class="form-group col-sm-5 form-group-votes_average">
                    <label><?php echo __('Rating', 'fv') ?></label>
                    <div class="clearfix">
                        <input class="form-control w07 pull-left" name="form[votes_average]" type="number" size="1" value="<?php echo $unit->votes_average; ?>" min="0" step="any"/>
                        of <?php echo fv_setting('rate-stars-count', 5); ?>
                    </div>
                </div>
            <?php elseif ( $contest->voting_type == 'rate_summary' ) :?>
                <div class="form-group col-sm-5 form-group-rating_summary">
                    <label><?php echo __('Rating', 'fv') ?></label>
                    <div class="clearfix">
                        <input class="form-control w07 pull-left" name="form[rating_summary]" type="number" size="2" value="<?php echo $unit->rating_summary; ?>" min="0" step="0.01"/>
                        total
                    </div>
                </div>
            <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <div class="form-group form-group-description">
                <label> <?php echo __('Short description', 'fv') ?> </label> <small>max - 500 chars,
                    html allowed like
                    <a href="https://core.trac.wordpress.org/browser/trunk/src/wp-includes/kses.php#L60" target="_blank">in post</a></small>
                <input name="form[description]" class="form-control" type="text" value="<?php echo esc_attr( $unit->description ) ?>" onkeyup="fv_count_chars(this);"/>
                <span class="need-count-chars"><?php echo mb_strlen(esc_attr($unit->description)); ?></span>
            </div>
            <div class="form-group form-group-full_description">
                <label> <?php echo __('Full description', 'fv') ?> </label> <small>max - 1255 chars,
                    html allowed like
                    <a href="https://core.trac.wordpress.org/browser/trunk/src/wp-includes/kses.php#L60" target="_blank">in post</a></small>
                <textarea name="form[full_description]" class="form-control" rows="4" onkeyup="fv_count_chars(this);"><?php echo  esc_attr( $unit->full_description ) ?></textarea>
                <span class="need-count-chars"><?php echo mb_strlen(esc_attr($unit->full_description)); ?></span>
            </div>

            <div class="form-group form-group-social_description">
                <label> <?php echo __('Social description', 'fv') ?> </label> <small>max - 150 chars, no html allowed
                    <?php echo __('(used on sharing image into Social networks)', 'fv') ?></small>
                <input name="form[social_description]" class="form-control" type="text" value="<?php echo esc_attr($unit->social_description) ?>" onkeyup="fv_count_chars(this);"/>
                <span class="need-count-chars"><?php echo mb_strlen(esc_attr($unit->social_description)); ?></span>
            </div>

            <?php if( $contest->isCategoriesEnabled() ): ?>
                <div class="form-group form-group-category">
                    <label><?php echo __('Category', 'fv') ?></label>
                    <?php
                    $categories = $contest->getCategories();
                    $comp_categories = $unit->getCategories('IDs');
                    ?>
                    <select class="form-control" <?php echo $contest->isMultiCategories() ? 'multiple="multiple" size="6"' : ''; ?> name="form[categories][]">
                        <?php FOREACH($categories as $category): ?>
                            <option value="<?php echo $category->term_id; ?>" <?php selected( in_array($category->term_id, $comp_categories) ); ?>><?php echo $category->name; ?></option>
                        <?php ENDFOREACH; ?>
                    </select>
                    <?php if( $contest->isMultiCategories() ): ?>
                        <small>Use Ctrl to multi-select</small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                    <strong>User info:</strong><br/>
                    Email:
                    <input text="email" name="form[user_email]" value="<?php echo ( !empty($unit->user_email) ) ? $unit->user_email : ''; ?>" class="text-input-inline"/>
                    <?php if( $unit->user_id ): ?>
                        / <a href="<?php echo admin_url('user-edit.php?user_id=' . $unit->user_id); ?>" target="_blank">User id: <?php echo $unit->user_id; ?></a>
                    <?php else: ?>
                        / Uploaded by not logged in user
                    <?php endif; ?>
                    <?php if($unit->user_ip): ?>
                        / User IP: <?php echo $unit->user_ip; ?>
                    <?php endif; ?>
                    <?php if($unit->upload_info): ?>
                        <br/>Upload form data: <?php echo FvFunctions::showUploadInfo($unit->upload_info); ?>
                    <?php endif; ?>
            </div>

            <div class="form-group form-group-meta">
                <div class="row">
                    <div class="col-md-8">
                        <strong>Meta key <small>max - 100</small>:</strong>
                    </div>
                    <div class="col-md-16">
                        <strong><span data-call="Competitor.showSystemMeta" class="clickable">Meta value</span> <small>max - 500</small>:</strong>

                        <button type="button" class="btn btn-default pull-right btn__add-meta-row" data-call="Competitor.addMetaRow">Add meta field</button>
                    </div>
                </div>
                <div class="meta-rows">
                    <?php
                    // If not new contestant
                    if ( $unit->id ) {
                        $all_meta = $unit->meta()->get_custom_all();
                    }
                    if ( !empty($all_meta) ) :
                        foreach($all_meta as $meta_row) :
                        ?>
                            <div class="row">
                                <div class="col-md-7">
                                    <input name="form[meta_key][<?php echo $meta_row->ID; ?>]" class="form-control" type="text" value="<?php echo esc_attr( $meta_row->meta_key ) ?>"/>
                                </div>
                                <div class="col-md-16">
                                    <input name="form[meta_val][<?php echo $meta_row->ID; ?>]" class="form-control" type="text" value="<?php echo stripslashes(esc_attr( $meta_row->value )) ?>"/>
                                </div>
                                <div class="col-md-1">
                                    <a class="meta--remove" href="#0" data-call="Competitor.removeMetaRow"><span class="dashicons dashicons-trash"></span></a>
                                </div>
                                <input class="meta--type" name="form[meta_type][<?php echo $meta_row->ID; ?>]" type="hidden" value="exists"/>
                            </div>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <div class="system-meta hidden">
                    <?php
                    if ( $unit->id ):
                        fv_dump( $unit->meta()->get_all_flat() );
                        fv_dump( $unit->options );
                    endif;
                    ?>
                </div>

                <div class="text-right">

                </div>
            </div>

            <input name="form[id]" type="hidden" value="<?php echo $unit->id ?>" />
            <?php wp_nonce_field('save_contestant', 'fv_nonce'); ?>
            <input class="status" name="form[status]" type="hidden" value="<?php echo $unit->status ?>" />
            <?php echo __('Status', 'fv') ?>  <span class="foto_status"> <?php echo fv_get_status_name($unit->status) ?> </span> |
            <a href="#" data-call="Competitor.setFormStatus" data-status="0"><?php echo __('Publish', 'fv') ?></a> &nbsp;
            <a href="#" data-call="Competitor.setFormStatus" data-status="1" class="moderaion"><?php echo __('To moderation', 'fv') ?></a> &nbsp;
            <a href="#" data-call="Competitor.setFormStatus" data-status="2" class="draft" ><?php echo __('To draft', 'fv') ?></a> &nbsp;&nbsp;

            <br>

            <?php do_action('fv/admin/form_edit_photo/extra', $unit); ?>

        </div>
    </form>


</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel and close</button>
    <button type="button" class="btn btn-primary" data-call="Competitor.save" data-contest="<?php echo $unit->contest_id ?>">
        <?php echo __('Save', 'fv') ?>
    </button>
</div>

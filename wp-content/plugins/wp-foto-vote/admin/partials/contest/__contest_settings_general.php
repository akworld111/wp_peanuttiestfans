<div class="meta-box-sortables">
    <div class="postbox ">
        <h3 class="hndle">
            <span>
                <?php _e('General options', 'fv') ?>
            </span>
        </h3>
        <div class="inside">

            <fieldset>

                <div class="row">
                    <?php
                    $contest_types = apply_filters('fv/admin/contest/types_array', array(0 => 'Default'), $contest);
                    // Fix to avoid issues
                    if ( !$contest_types ) {
                        $contest_types = array(
                            0 => 'Default',
                        );
                    }
                    ?>
                    <div class="form-group col-sm-10">
                        <label for="type"><i class="fvicon fvicon-pictures"></i> </span> <?php echo __('Contest type', 'fv'); ?></label>
                        <select id="type" name="type" class="form-control">
                            <?php foreach ( $contest_types as $contest_type_key => $contest_type_title ) : ?>
                                <option value="<?php echo $contest_type_key; ?>" <?php selected($contest_type_key, $contest->type) ?>> <?php echo $contest_type_title; ?></option>
                            <?php endforeach; ?>
                            <?php if( !isset($contest_types[1]) ): ?>
                                <option value="0" disabled>Video (please install Video addon)</option>
                            <?php endif; ?>
                            <?php if( !isset($contest_types[2]) ): ?>
                                <option value="0" disabled>Instagram (please install Instagram addon)</option>
                            <?php endif; ?>
                        </select>
                    </div>


                    <div class="form-group col-sm-14">
                        <label style="color: #0073aa;" for="page_id">
                            <?php _e('Page, where contest are placed', 'fv') ?>
                            <?php fv_get_tooltip_code(__('Used for: <br/>- Generating link \'Back to contest\' from Single View'
                                .' <br/>- Link to Single Contest from \'Contests list\''
                                .' <br/>- Link to confirm Email (\'Subscribe Form\' Voting security)'
                                , 'fv')); ?>
                        </label>
                        <div class="row">
                            <div class="col-sm-21">
                                <select name="page_id" id="page_id" class="fv-posts-dropdown form-control" disabled="disabled">
                                    <option value=""><?php echo esc_attr( __( 'None selected' ) ); ?></option>
                                    <?php if ( !empty($contest->page_id) && $contest_att_page ) : ?>
                                        <option value="<?php echo $contest_att_page->ID; ?>" selected><?php echo $contest_att_page->post_title, '[', $contest_att_page->post_type, ']'; ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <a href="#0" class="fv-init-posts-dropdown" data-what-get="all" data-post-id="<?php echo $contest->page_id; ?>">edit</a>
                            </div>
                        </div>


                        <br/>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-8">
                        <label for="fv_timer">
                            <i class="fvicon fvicon-stopwatch"></i> </span><?php echo __('Countdown', 'fv') ?>
                        </label>
                        <select id="fv_timer" name="fv_timer" class="form-control">
                            <option value="no" <?php selected('no', $contest->timer); ?>> <?php _e('Do not display', 'fv') ?></option>
                            <?php foreach ($countdowns as $key => $countdown_title): ?>
                                <option value="<?php echo $key ?>" <?php selected($key, $contest->timer); ?>><?php echo $countdown_title ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-8">
                        <label for="lightbox_theme"><i class="fvicon fvicon-zoomin"></i> <?php echo __('Lightbox', 'fv'); ?></label>
                        <select id="lightbox_theme" name="lightbox_theme" class="form-control">
                            <?php foreach (FvFunctions::getLightboxArr() as $lightbox => $theme_title): ?>
                                <option value="<?php echo $lightbox ?>" <?php selected($lightbox, $contest->lightbox_theme); ?>><?php echo $theme_title ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-8">
                        <label for="sorting"><span class="typcn typcn-arrow-unsorted"></span> <?php echo __('Photos order', 'fv'); ?> <?php fv_get_tooltip_code(__('Output order of pictures on the page', 'fv')) ?></label>
                        <select id="sorting" name="sorting" class="form-control">
                            <?php foreach (fv_get_sotring_types_arr() as $key => $sort_title): ?>
                                <option value="<?php echo $key ?>" <?php selected($key, $contest->sorting); ?>><?php echo $sort_title ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-8">
                        <label for="hide_votes">
                            <i class="fvicon fvicon-eye"></i> </span><?php echo __('Hide votes count?', 'fv') ?>
                        </label>
                        <select id="hide_votes" name="hide_votes" class="form-control">
                            <option value="global" <?php selected('global', $contest->hide_votes); ?>> <?php _e('Use global settings', 'fv') ?></option>
                            <option value="yes" <?php selected('yes', $contest->hide_votes); ?>> <?php _e('Yes', 'fv') ?></option>
                            <option value="no" <?php selected('no', $contest->hide_votes); ?>> <?php _e('No', 'fv') ?></option>
                        </select>
                    </div>

                </div>

                <div class="clear"></div>


                <?php

                $cover_image_src = null;

                if ( $contest->cover_image ) {
                    $cover_image_src = wp_get_attachment_image_src($contest->cover_image);
                }
                ?>

                <div><strong><legend><?php _e('Contest list settings', 'fv') ?></legend></strong></div>
                <div>
                    <label for="fv_cover_image"><?php echo __('Cover image ID for contest list', 'fv') ?> <?php fv_get_tooltip_code(__('Don`t shows in photos list, only as cover image.', 'fv')) ?></label>
                    <input type="number" id="fv_cover_image" name="fv_cover_image" value="<?php echo $contest->cover_image ?>" min="0" max="99999" size="5">
                    <input type="hidden" id="fv_cover_image_url">
                    <input type="button" class="button" value="Upload Image" onclick="fv_wp_media_upload('#fv_cover_image_url', '#fv_cover_image', '#cover-image-thumb')"/>
                    <img src="<?php echo $cover_image_src ? $cover_image_src[0] : ''; ?>" alt="" id="cover-image-thumb" height="28">
                    <br/><small><?php _e('(need, only if you uses contest_list shortcode)', 'fv') ?></small>
                </div>

                <?php do_action('fv/admin/contest_settings_form', $contest); ?>



            </fieldset>

        </div>
    </div>
</div>
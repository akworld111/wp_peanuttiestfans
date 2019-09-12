    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">
                <div class="b-wrap side-labels">
                    <?php if ( $contest->isVotingDatesActive() ): ?>
                        <span class="label label-info"><?php _e('Voting dates active', 'fv'); ?></span>
                    <?php else: ?>
                        <span class="label label-warning"><?php _e('Voting dates inactive', 'fv'); ?></span>
                    <?php endif; ?>

                    <?php if ( $contest->isUploadDatesActive() ): ?>
                        <span class="label label-info"><?php _e('Upload dates active', 'fv'); ?></span>
                    <?php else: ?>
                        <span class="label label-warning"><?php _e('Upload dates inactive', 'fv'); ?></span>
                    <?php endif; ?>
                </div>

                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div id="submitdiv" class="postbox ">
                        <h3 class="hndle"><span><?php echo __('Actions', 'fv') ?></span></h3>
                        <div class="inside">
                            <div class="submitbox" id="submitpost">
                                <div id="major-publishing-actions">
                                    <div class="contest-author">
                                        <strong>Website time:</strong> <code><?php echo current_time("mysql"); ?></code>
                                        <?php fv_get_tooltip_code(__('Make sure that website time is correct, else your contest start/end dates will work incorrect. <br/> Time can be changed in Wordpress Settings - General - Timezone option', 'fv')) ?>
                                        <br/><br/>
                                    </div>

                                    <?php if ( $contest->user_id ) : $author = get_userdata($contest->user_id); ?>
                                        <div class="contest-author">
                                            <strong>Author:</strong> <a href="<?php echo admin_url('user-edit.php?user_id='.$author->ID); ?>"><?php echo $author->display_name; ?></a><br/><br/>
                                        </div>
                                    <?php endif; ?>

                                    <div class="contest-extra-actions"><?php do_action('fv/admin/edit-contest/actions', $contest); ?></div>

                                    <form class="fv-run-export-form">
                                        <button class="button" type="submit">
                                            <span class="dashicons dashicons-upload"></span> <?php _e('Export all photos to csv', 'fv') ?>
                                        </button>
                                        <?php wp_nonce_field('fv_export_nonce'); ?>
                                        <input type="hidden" name="type" value="contest_data">
                                        <input type="hidden" name="action" value="fv_export">
                                        <input type="hidden" name="contest_id" value="<?php echo $contest->id ?>">
                                    </form>

                                    <br/><small><a href="http://youtu.be/JNp15MjZwUs" target="_blank"><?php _e('How to Import CSV to Google Drive', 'fv') ?></a></small>
                                    <br/><br/>

                                    <div class="clear_ip">
                                        <button type="button" class="button" data-call="Contest.clearStats" data-contest="<?php echo $contest->id ?>">
                                            <span class="dashicons dashicons-trash"></span> <?php echo __('Clear "Votes log" for this contest', 'fv') ?>
                                            <?php fv_get_tooltip_code(__('After users can vote again, so may be you need also reset votes count?', 'fv')) ?>
                                        </button>
                                    </div>
                                    <div class="clear_votes mb15">
                                        <button type="button" class="button" data-call="Contest.resetVotes" data-contest="<?php echo $contest->id ?>">
                                            <span class="dashicons dashicons-no-alt"></span> <?php echo __('Reset all votes in this contest', 'fv') ?>
                                            <?php fv_get_tooltip_code(__('This will set votes count to 0 for all photos', 'fv')) ?>
                                        </button>
                                    </div>
                                    <div class="clear_subscribers mb15">
                                        <button type="button" class="button" data-call="Contest.clearSubscribers" data-contest="<?php echo $contest->id ?>">
                                            <span class="dashicons dashicons-trash"></span> <?php echo __('Clear "Subscribers" for this contest', 'fv') ?>
                                            <?php fv_get_tooltip_code(__('Remove all subscribers list', 'fv')) ?>
                                        </button>
                                    </div>
                                    
                                    <div>
                                        <button type="button" class="button" data-call="Contest.showCloneContestWnd">
                                            <span class="dashicons dashicons-admin-appearance"></span> <?php echo __('Clone contest', 'fv') ?>
                                        </button>
                                    </div>
                                    <br/>
                                    <div id="delete-action">
                                        <a class="submitdelete deletion" onclick="return confirm('<?php _e('Are you sure', 'fv') ?>');" href="<?php echo admin_url( 'admin.php?page=fv&action=delete&contest[]=' ); ?><?php echo $contest->id; ?>">
                                            <?php _e('Delete', 'fv') ?>
                                        </a>
                                    </div>


                                    <div id="publishing-action">
                                        <button type="submit" name="publish" data-call="Contest.submitContestForm" class="button button-primary button-large" accesskey="s">
                                            <?php _e('Save contest settings', 'fv'); ?>
                                        </button>
                                    </div>

                                    <div class="clear"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--
                    <div id="formatdiv" class="postbox " style="display: none;">
                        <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                        <h3 class="hndle"><span>Формат</span></h3>
                        <div class="inside">
                            <div id="post-formats-select">
                                <input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" checked="checked"> <label for="post-format-0" class="post-format-icon post-format-standard">Стандартный</label>
                                <br><input type="radio" name="post_format" class="post-format" id="post-format-video" value="video"> <label for="post-format-video" class="post-format-icon post-format-video">Видео</label>
                                <br><input type="radio" name="post_format" class="post-format" id="post-format-aside" value="aside"> <label for="post-format-aside" class="post-format-icon post-format-aside">Заметка</label>
                                <br><input type="radio" name="post_format" class="post-format" id="post-format-quote" value="quote"> <label for="post-format-quote" class="post-format-icon post-format-quote">Цитата</label>
                                <br>
                            </div>
                        </div>
                    </div>
                    -->

                </div></div>
            <div id="postbox-container-2" class="postbox-container b-wrap">

                <form name="post" action="<?php echo admin_url( 'admin.php?page=fv&action=save&contest=' ); ?><?php echo $contest->id; ?>" method="post" id="contest-form">
                    <?php wp_nonce_field( 'fv_edit_contest_action','fv_edit_contest_nonce' ); ?>
                    <input type="hidden" name="contest_id" value="<?php echo $contest->id; ?>">


                    <div id="titlediv">
                        <div id="titlewrap">
                            <input type="text" name="contest_title" size="30" placeholder="<?php echo __('Enter contest name', 'fv') ?>" value="<?php echo esc_attr($contest->name); ?>" id="title" required="true">
                        </div>
                    </div>
                    <div class="row"><?php

                        if ( $contest->isFinished() ):
                            include '__contest_finished.php';
                        endif;

                        $settings_tabs = array(
                            'general'  => 'General',
                            'voting'  => 'Voting',
                            'upload'  => 'Public Submission',
                            'winners'  => 'Winners / Leaders',
                            'social'  => 'Social sharing',
                            'addons'  => 'Addons',
                        );

    //                    include '__contest_settings_vote.php';
    //
    //
    //
    //                    include '__contest_settings_upload.php';
    //
    //                    include '__contest_settings_design.php';
    //                    include '__contest_settings_other.php';



                        ?></div>

                    <div class="fv-tabs" id="" data-save-tab="on" data-save-tab-key="contest_config_">

                        <!-- Tabs -->
                        <nav class="fv-tabs-nav">
                            <ul class="fv-tabs-navigation">
                                <?php foreach ($settings_tabs as $group_slug => $group_name) : ?>
                                    <li><a href="#<?php echo $group_slug; ?>" data-content="<?php echo $group_slug; ?>" class="<?php echo ($group_slug == 'general') ? 'selected' : ''; ?>">
                                            <?php echo $group_name; ?>
                                        </a></li>
                                <?php endforeach; ?>
                            </ul> <!-- fv-tabs-navigation -->

                        </nav>

                        <!-- Tabs content / Generate ul list with tables for tabbed navigation -->
                        <ul class="fv-tabs-content">
                            <?php foreach ($settings_tabs as $group_slug => $group_name) : ?>
                                <li data-content="<?php echo $group_slug; ?>" class="<?php echo ($group_slug == 'general') ? 'selected' : ''; ?>">
                                    <?php include_once "__contest_settings_{$group_slug}.php"; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    </div>

                </form>
            </div>
        </div><!-- /post-body -->

        <br class="clear">

        <div class="fv-integration">
            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Gallery shortcode + upload form (if enabled in settings)</h3>
                    <input type="text" class="widefat readonly-style" value='[fv id=<?php echo $contest->id; ?> show_opened=true count_to=upload]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>The most simple way to integrate a contest.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>theme</code> – you can override the global settings of the <strong>list skin</strong>&nbsp;(enter skin slug, like “beauty”)</li>
                        <li><code>sorting</code> – you can&nbsp;override contest “order by” setting (entrer valid order type, like “newest”)</li>
                        <li><code>upload_theme</code> – will be passed to Upload form, if it was enabled</li>
                        <li><code>show_opened</code> – will be passed to Upload form, if it was enabled</li>
                        <li><code>apply_upload_dates</code> – will be passed to Upload form, if it was enabled</li>
                        <li><code>upload_not_active_msg</code> – will be passed to Upload form, if it was enabled</li>
                        <li><code>display_winners</code> [1 or 0] – allows hide Winners block</li>
                        <li><code>winners_skin</code>&nbsp;– winners block design<br>
                            Allowed values: [“red”, “simple”]</li>
                        <li><code>winners_width</code>&nbsp;– width of winners block<br>
                            Allowed values: [100-999]</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>

            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Upload form shortcode</h3>
                    <input type="text" class="widefat readonly-style" value='[fv_upload_form contest_id=<?php echo $contest->id; ?> show_opened=true]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>Display only the upload form.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>show_opened</code> [0 or 1]&nbsp;–&nbsp;is form files must be visible or just&nbsp;Form title (so user need click into title for toogle form fields)</li>
                        <li><code>apply_upload_dates</code> [0 or 1] – is need check upload dates and do not display upload form if dates expired or not started yes (or contest was finished)?</li>
                        <li><code>upload_not_active_msg</code> [string] – work with param “apply_upload_dates” and allows override global message about inactive upload</li>
                        <li><code>upload_theme</code> [“default”, etc] – you can&nbsp;override contest “upload theme” paramater</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>

        </div>

        <div class="fv-integration">


            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Countdown shortcode</h3>
                    <input type="text" class=" widefat readonly-style" value='[fv_countdown contest_id=<?php echo $contest->id; ?> count_to=upload]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>Display time left to voting/upload start or end.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>count_to</code> 'upload' or 'voting' &nbsp;–&nbsp; count to Voting or Upload dates star/end?</li>
                        <li><code>type</code> ['default' or 'final', etc] – countdown skin</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>

            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Contest Description</h3>
                    <input type="text" class="widefat readonly-style" value='[fv_contest_description contest_id=<?php echo $contest->id; ?>]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>Display only the contest Description.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>contest_id</code> – Contest ID</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>



        </div>

        <div class="fv-integration">

            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Leaders shortcode</h3>
                    <input type="text" class="widefat readonly-style" value='[fv_leaders contest_id=<?php echo $contest->id; ?>]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>Display only leaders block.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>hide_title</code>&nbsp;– is it need hide block title?<br>
                            Allowed values: [0 or 1] or [true / false]</li>
                        <li><code>count</code>&nbsp;– how many leaders display?<br>
                            Allowed values: [1-10]<br>
                            <span style="text-decoration: underline;">For&nbsp;“block”, “block_2” <em>type</em> recommended value is&nbsp;1-4</span></li>
                        <li><code>type</code>&nbsp;– block design<br>
                            Allowed values: [“poll”, “text”, “block”, “block_2”, “table_1”, “table_2”]</li>
                        <li><code>leaders_width</code>&nbsp;– width of leaders block (applicable for “block”, “block_2”)<br>
                            Allowed values: [100-999]</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>

            <div class="fv-integration-side">
                <div class="fv-integration-info">
                    <h3>Winners shortcode</h3>
                    <input type="text" class="widefat readonly-style" value='[fv_winners contest_id=<?php echo $contest->id; ?> winners_skin="red"]' onclick="this.setSelectionRange(0, this.value.length)">
                    <p>Display only winners block.</p>
                </div>

                <div class="fv-integration-params">
                    <h4>Allowed params:</h4>
                    <ul class="text-left">
                        <li><code>winners_skin</code>&nbsp;– winners block design<br>
                            Allowed values: [“red”, “simple”]</li>
                        <li><code>winners_width</code>&nbsp;– width of winners block, not set by default<br>
                            Allowed values: [100-999]</li>
                    </ul>
                </div> <!-- /.fv-integration-params -->
            </div>
        </div>

        <a href="http://wp-vote.net/shortcode/" target="_blank"><?php _e('All shortcodes >>', 'fv') ?></a>
    </div><!-- /poststuff -->


<!-- Clone contest Modal HTML template -->
<script type="text/html" id="clone-contest-modal-template">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Select clone type:</h4>
    </div>
    <div class="modal-body">
        <button type="button" class="btn btn-primary" data-call="Contest.clone" data-contest="<?php echo $contest->id; ?>"><span class="dashicons dashicons-admin-appearance"></span> Clone contest</button>
        <button type="button" class="btn btn-secondary" data-call="Contest.clone" data-contest="<?php echo $contest->id; ?>" data-with-content="yes"><span class="dashicons dashicons-admin-appearance"></span> Clone contest & categories & <span class="dashicons dashicons-format-gallery"></span> photos</button>
        <br/><br/><strong>Note:</strong> On cloning with photos no images duplicates will be created - contest items just contains reference to Media library photo ID.
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>
</script>
<!-- Clone contest Modal HTML template :: END -->
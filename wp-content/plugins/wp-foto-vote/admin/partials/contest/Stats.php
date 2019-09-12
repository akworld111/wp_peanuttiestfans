<div class="metabox-holder">
    <div class="postbox">
            <h3 class="hndle"><span><?php echo __('Stats', 'fv') ?></span></h3>
            <div class="inside b-wrap">
                <div class="row tile_count">
                    <div class="col-sm-5 col-md-4">
                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-user"></span> Participants </span>
                            <div class="count"><?php echo $competitors_count_by_user_ID; ?></div>
                            <span class="count_bottom">(grouped by user ID)</span>
                        </div>

                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-user"></span> Participants </span>
                            <div class="count"><?php echo $competitors_count_by_email; ?></div>
                            <span class="count_bottom">(grouped by Email)</span>
                        </div>

                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-user"></span> Participants </span>
                            <div class="count"><?php echo $competitors_count_by_IP; ?></div>
                            <span class="count_bottom">(grouped by IP)</span>
                        </div>
                    </div><!-- /.col-sm-4 -->

                    <div class="col-sm-5 col-md-4">
                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-heart-full-outline"></span> Summary votes</span>
                            <div class="count"><?php echo $votes_count->votes_count_summary; ?></div>
                            <span class="count_bottom">(by all competitors)</span>
                        </div>

                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-heart-outline"></span> Summary fail votes</span>
                            <div class="count"><?php echo $votes_count->votes_count_fail_summary; ?></div>
                            <span class="count_bottom">(by all competitors)</span>
                        </div>

                        <div class="tile_stats_count">
                            <span class="count_top"><span class="typcn typcn-heart-half-outline"></span> Avg per competitor</span>
                            <div class="count"><?php echo round($votes_count->votes_count_summary / $competitors_count, 2); ?></div>
                            <span class="count_bottom">&nbsp;</span>
                        </div>
                    </div><!-- /.col-sm-4 -->

                    <div class="col-sm-12 col-md-16">
                        <h4>Top 5 Most voted competitors</h4>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#id</th>
                                <th>Thumb</th>
                                <th>Votes</th>
                                <th>Name</th>
                                <th>Added date</th>
                                <th>User ID</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top5 as $top5_row):
                                    $image_src = FvFunctions::getPhotoThumbnailArr($top5_row);
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo '<a href="', fv_single_photo_link($top5_row->id), '" target="_blank">', $top5_row->id, '</a>'; ?></th>
                                        <th><img src="<?php echo ( is_array($image_src) )? $image_src[0] : $top5_row->url; ?>" width="55" class="fv-table-thumb" /></th>
                                        <td>
                                            <?php echo '<a href="', admin_url("admin.php?page=fv-vote-analytic&contest_id={$contest_id}&photo_id={$top5_row->id}"), '" target="_blank" title="View analytics">' , $top5_row->votes_count, ' <span class="typcn typcn-heart-full-outline"></span></a>'; ?>
                                        </td>
                                        <td><?php echo $top5_row->name; ?></td>
                                        <td><?php echo date('d/m/Y',$top5_row->added_date) ?></td>
                                        <td class="user_id"><a href="<?php echo admin_url('user-edit.php?user_id='.$top5_row->user_id) ?>" target="_blank"><?php echo $top5_row->user_id ?></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.col-sm-12 -->

                    <div class="clearfix">
                        <h4><?php _e('Top 10 Most cheating competitors', 'fv'); ?></h4>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#id</th>
                                <th>TOR* records summary</th>
                                <th>Fraud score avg</th>
                                <th>Most display sizes</th>
                                <th>Votes</th>
                                <th>Name</th>
                                <th>Added date</th>
                                <th>User ID</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top5cheat as $top5_row): ?>
                                    <tr>
                                        <th scope="row">
                                            <?php echo '<a href="', fv_single_photo_link($top5_row->C_id), '" target="_blank">', $top5_row->C_id, '</a>'; ?>
                                            / <?php echo '<a href="', admin_url("admin.php?page=fv-vote-log&contest_id={$contest_id}&photo_id={$top5_row->C_id}"), '" target="_blank">view ' , $top5_row->votes_records_summary, ' votes</a>'; ?>
                                        </th>
                                        <td><?php echo $top5_row->is_tor_summary; ?></td>
                                        <td><?php echo round($top5_row->score_avg, 2); ?></td>
                                        <td><?php echo isset( $top5cheatScreens[$top5_row->C_id] ) ? $top5cheatScreens[$top5_row->C_id]->display_size . ' [' . $top5cheatScreens[$top5_row->C_id]->display_size_summary . ']' : ''; ?></td>
                                        <td><?php echo $top5_row->C_votes_count; ?></td>
                                        <td><?php echo $top5_row->C_name; ?></td>
                                        <td><?php echo date('d/m/Y',$top5_row->C_added_date) ?></td>
                                        <td class="user_id"><a href="<?php echo admin_url('user-edit.php?user_id='.$top5_row->C_user_id) ?>" target="_blank"><?php echo $top5_row->C_user_id ?></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.col-sm-12 -->
                    <small>
                        *TOR browser often used for change IP and doing some cheating.
                    </small>

                </div>

            </div>
    </div>
</div>

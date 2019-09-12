<div class="meta-box-sortables">
    <div class="postbox ">
        <h3 class="hndle">
            <span>
                <?php _e('Voting settings', 'fv') ?>
                <?php if ( $contest->isVotingDatesActive() ): ?>
                    <span class="label label-info"><?php _e('Voting dates active', 'fv'); ?></span>
                <?php else: ?>
                    <span class="label label-warning"><?php _e('Voting dates inactive', 'fv'); ?></span>
                <?php endif; ?>
            </span>
        </h3>
        <div class="inside">

            <div class="row">
                 <div class="form-group col-sm-12 col-xs-12">
                     <label><i class="fvicon fvicon-calendar"></i>
                        <?php echo __('Date start', 'fv'); ?>
                     </label>
                    <input type="text" class="datetime form-control" id="date_start" name="date_start" value="<?php echo $contest->date_start ?>">
                    <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>

                </div>

                <div class="form-group col-sm-12 col-xs-12">
                    <label><i class="fvicon fvicon-calendar"></i>
                        <?php echo __('Date finish', 'fv'); ?>
                        <?php fv_get_tooltip_code(__('When time ends, vote buttons will be hidden,<br/> and user can only see results', 'fv')) ?>
                    </label>
                    <input type="text" class="datetime form-control" id="date_finish" name="date_finish" value="<?php echo $contest->date_finish ?>">
                    <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>

                </div>

            </div> <!-- .row -->

            <div class="row">

                <div class="form-group col-sm-24">
                    <label for="voting_frequency">
                        <i class="fvicon fvicon-history"></i> <?php _e('Frequency of voting', 'fv') ?> <?php fv_get_tooltip_code(__('how many times user can vote in contest', 'fv')) ?>
                    </label>
                    <br/>
                    <input type="number" id="voting_max_count" name="voting_max_count" value="<?php echo $contest->voting_max_count; ?>" style="width: 50px;" min="0" max="999">
                    <?php fv_get_tooltip_code(__('0 mean 1 vote for each photo in contest', 'fv')) ?>
                    vote(s) during
                    <select id="voting_frequency" name="voting_frequency" class="">
                        <option value="once" <?php selected('once', $contest->voting_frequency) ?>>all contest [once]</option>
                        <option value="day" <?php selected('day', $contest->voting_frequency) ?>>day [reset at midnight]</option>
                        <option value="24h" <?php selected('24h', $contest->voting_frequency) ?>>24 hours</option>
                        <option value="12h" <?php selected('12h', $contest->voting_frequency) ?>>12 hours</option>
                        <option value="6h" <?php selected('6h', $contest->voting_frequency) ?>>6 hours</option>
                    </select>

                    <?php _e('but not more than', 'fv'); ?>
                    <input type="number" id="voting_max_count_total" name="voting_max_count_total" value="<?php echo $contest->voting_max_count_total; ?>" style="width: 40px;" min="0" max="999">
                    <?php fv_get_tooltip_code(__('0 mean no limit', 'fv')) ?>
                    <?php _e('during all contest time', 'fv'); ?>

                    <br/>
                    <small>more here - <a href="http://wp-vote.net/doc/voting-settings/" target="_blank">http://wp-vote.net/doc/voting-settings/</a></small>

                </div>

            </div> <!-- .row -->

            <div class="row">
                <div class="form-group">
                    <div class="col-sm-16">
                        <span class="fvicon fvicon-heart"></span> <label for="voting_type"><?php echo __('Voting type', 'fv') ?></label>
                        <select id="voting_type" name="voting_type" class="form-control">
                            <option value="like" <?php selected($contest->voting_type, 'like'); ?>>Like (+1)</option>
                            <option value="rate" <?php selected($contest->voting_type, 'rate'); ?>>Rating (8 of 10[10 votes])</option>
                            <option value="rate_summary" <?php selected($contest->voting_type, 'rate_summary'); ?>>Rating summary (88[10 votes])</option>
                        </select>
                    </div>
                    <div class="col-sm-24">
                        <small><?php echo __('"Rating" doesn\'t compatible with "Additional security" FB Share and Subscribe form. Don\'t change voting type when voting starts!', 'fv') ?></small>
                        <br/>
                        <small><?php echo __('Change stars count for "Rating" modes possible in Settings => Voting tab', 'fv') ?></small>
                    </div>
                </div>
            </div> <!-- .row -->

            <br/>
                
            <div class="row">

                <div class="form-group col-sm-12">
                    <span class="dashicons dashicons-shield-alt"></span> <label for="voting_security"><?php echo __('Block votes by', 'fv') ?>
                        <?php fv_get_tooltip_code(__('Select - how secure contest voting process?', 'fv')) ?></label>
                    <?php //defaultArecapcha  defaultAregistered ?>
                    <select id="voting_security" name="voting_security" class="form-control">
                        <option value="cookiesAip" <?php selected('cookiesAip', $contest->voting_security); ?>>IP + cookies + user ID (if logged in)</option>
                        <option value="cookies" <?php selected('cookies', $contest->voting_security); ?>>cookies + user ID (if logged in) - lead to a massive cheating without require to login</option>
                        <?php do_action('fv/admin/contest_settings/security_type', $contest); ?>
                    </select>
                    <small>more here - <a href="http://wp-vote.net/doc/voting-settings/" target="_blank">http://wp-vote.net/doc/voting-settings/</a></small>
                </div>

                <div class="form-group col-sm-12">
                    <span class="dashicons dashicons-shield-alt"></span> <label for="voting_security_ext"><?php echo __('Additional security', 'fv') ?>
                        <?php fv_get_tooltip_code(__('Select - how secure contest voting process?', 'fv')) ?></label>
                    <?php //defaultArecapcha  defaultAregistered ?>
                    <select id="voting_security_ext" name="voting_security_ext" class="form-control">
                        <option value="none" <?php selected('none', $contest->voting_security_ext); ?>>None</option>
                        <option value="reCaptcha" <?php selected('reCaptcha', $contest->voting_security_ext); ?>>reCaptcha (require Recaptcha KEY)</option>
                        <option value="mathCaptcha" <?php selected('mathCaptcha', $contest->voting_security_ext); ?>>mathCaptcha (https://wordpress.org/plugins/wp-math-captcha/)</option>
                        <option value="subscribe" <?php selected('subscribe', $contest->voting_security_ext); ?>>Subscribe form (require selected "Page, where contest are placed")</option>
                        <option value="subscribeForNonUsers" <?php selected('subscribeForNonUsers', $contest->voting_security_ext); ?>>Subscribe form for not Authorized (require selected "Page, where contest are placed")</option>
                        <option value="fbShare" <?php selected('fbShare', $contest->voting_security_ext); ?>>Facebook Share (require FB APP ID and FB dialog "Feed")</option>
                        <option value="social" <?php selected('social', $contest->voting_security_ext); ?>>Simple Social login</option>
                        <option value="wp_social_login" <?php selected('wp_social_login', $contest->voting_security_ext); ?>>Social login with "WordPress Social Login" plugin (for not Authorized only)</option>
                        <?php do_action('fv/admin/contest_settings/voting_security_ext', $contest); ?>
                    </select>
                </div>


            </div> <!-- .row -->

            <div class="row">

                <div class="form-group col-sm-12">
                    <label for="required_role"><?php _e('Require user to be logged in for vote?', 'fv') ?></label>

                    <select id="limit_by_user" name="limit_by_user" class="form-control fv-js-relation" data-r-el=".field-limit_by_role" data-r-show-on="role">
                        <option value="no" <?php selected('no', $contest->limit_by_user); ?>><?php _e('No', 'fv') ?></option>
                        <option value="yes" <?php selected('yes', $contest->limit_by_user); ?>><?php _e('Yes', 'fv') ?></option>
                        <option value="role" <?php selected('role', $contest->limit_by_user); ?>><?php _e('Yes, with specified Role', 'fv') ?></option>
                    </select>
                </div>

                <div class="form-group col-sm-12 field-limit_by_role <?php echo $contest->limit_by_user !== 'role' ? 'hidden' : ''; ?>">
                    <label for="limit_by_role">
                        <?php _e('Required user role(s):', 'fv') ?>
                        <?php fv_get_tooltip_code(__('Use Shift or Ctrl key for multi-select', 'fv')) ?>
                    </label>
                    <select id="limit_by_role" name="limit_by_role[]" class="form-control" multiple="" size="6">
                        <?php fv_dropdown_roles( $contest->limit_by_role ); ?>
                    </select>
                </div>
            </div> <!-- .row -->


        </div>
    </div>
</div>
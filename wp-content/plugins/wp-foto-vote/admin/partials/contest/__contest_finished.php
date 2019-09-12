<div class="meta-box-sortables col-lg-24">
    <div class="postbox ">
        <div class="inside">
            <h3><?php _e('Contest has been finished', 'fv') ?></h3>

            <br/>

            <p><?php _e( sprintf('At <strong><a href="">Winners tab</a></strong> you can find list of contest Winners.', $contest->getAdminUrl('winners')), 'fv') ?></p>

            <br/>
            <p>
                <a type="submit" class="button button-primary button-large" href="<?php echo wp_nonce_url( add_query_arg(array(
                    'contest_id'    => $contest->id,
                    'action'    => 'fv_reactivate_contest',
                )), 'fv_reactivate_contest_nonce'); ?>">
                    <?php _e('Make contest active and set "Voting end date" to NOW + 3 days*', 'fv'); ?>
                </a>
            </p>
            <p><strong><?php _e('*Please note:', 'fv') ?></strong></p>
            <ul>
                <li><?php _e('1. After 3 days contest again will be set as Finished', 'fv') ?></li>
                <?php if ( !$contest->isManualWinnersPick() ) : ?>
                    <li><?php _e('2. All Winners will be reset', 'fv') ?></li>
                <?php endif; ?>
            </ul>

            <br/>
            <br/>
        </div>
    </div>
</div>
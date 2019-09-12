<?php
/**
 * @var FV_Competitor   $contestant
 * @var FV_Contest      $contest
 */
$theme_imgs_url = str_replace('soc-fb.png', '', FV_Templater::locateUrl($theme, 'img/soc-fb.png'));
?>

<div id="fv_constest_item" class="photo-single-item">
    <div class="fv_name">
        <span class="name"><?php echo $contestant->getHeadingForTpl('single', $contest); ?></span>

        <div class="back">
            <a href="<?php echo $contest_link; ?>">
                <i class="fvicon fvicon-login"></i> <?php echo $public_translated_messages['back_to_contest'] ?>
            </a>
        </div>
    </div>
    <div class="fv_photo">
        <div class="fv_photo_votes">
            <a href="#" onclick="sv_vote(<?php echo $contestant->id ?>); return false;">
                <?php if ( ! $contest->isNeedHideVotes() ): ?>
                    <i class="fvicon- fv-vote-icon"></i> <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->getVotes($contest); ?></span>
                <?php else: ?>
                    <i class="fvicon- fv-vote-icon"></i>
                <?php endif; ?>
            </a>
        </div>
        <?php if (fv_setting('soc-counter', false)): ?>
            <div class="fv-svotes-container">
                <a href="#0" class="fv-svotes-a" onclick="FvModal.goShare(<?php echo $contestant->id ?>); return false;"
                   title="<?php echo $public_translated_messages['shares_count_text']; ?>">
                    <i class="fvicon-share"></i> <span class="fv-soc-votes fv_svotes_<?php echo $contestant->id; ?>">0</span>
                </a>
            </div>
        <?php endif; ?>

        <?php FV_Public_Single::render_main_image($image, $contestant, 'mainImage img-thumbnail'); ?>

        <?php if (!empty($next_id)): ?>
            <div class="fv_next fv_nav">
                <a href="<?php echo fv_single_photo_link($next_id, false, $contestant->contest_id); ?>" title="<?php _e('Next', 'fv') ?>">
                    <span class="fvicon-arrow-right"></span></a>
            </div>
        <?php endif; ?>

        <?php if (!empty($prev_id)): ?>
            <div class="fv_prev fv_nav">
                <a href="<?php echo fv_single_photo_link($prev_id, false, $contestant->contest_id); ?>" title="<?php _e('Previous', 'fv') ?>">
                    <span class="fvicon-arrow-left"></span></a>
            </div>
        <?php endif; ?>

    </div>
    <div class="fv_social">
        <span><?php echo $public_translated_messages['single_share_heading']; ?></span>

        <div class="fv_social_icons">
            <?php if (!fv_setting('voting-noshow-vk', false)): ?>
                <a href="#0" onclick="return sv_vote_send('vk', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-vk.png"/></a>
            <?php endif; ?>
            <?php if (!fv_setting('voting-noshow-fb', false)): ?>
                <a href="#0" onclick="return sv_vote_send('fb', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-fb.png"/></a>
            <?php endif; ?>
            <?php if (!fv_setting('voting-noshow-tw', false)): ?>
                <a href="#0" onclick="return sv_vote_send('tw', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-tw.png"/></a>
            <?php endif; ?>
            <?php if (!fv_setting('voting-noshow-ok', false)): ?>
                <a href="#0" onclick="return sv_vote_send('ok', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-ok.png"/></a>
            <?php endif; ?>
            <?php if (!fv_setting('voting-noshow-gp', false)): ?>
                <a href="#0" onclick="return sv_vote_send('gp', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-gp.png"/></a>
            <?php endif; ?>
            <?php if (!fv_setting('voting-noshow-pi', false)): ?>
                <a href="#0" onclick="return sv_vote_send('pi', this,<?php echo $contestant->id ?>)"
                   target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-pi.png"/></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="fv_description"><?php echo $contestant->getDescForTpl('single'); ?></div>

    <div class="clearfix">
        <div class="comments-block"><?php
            FV_Public_Single::get_instance()->render_comments($contestant);
            ?>
        </div>
    </div>

    <br/>

    <div id="fv_most_voted" class="theme">
        <span class="title"><span><?php echo $public_translated_messages['other_title'] ?></span></span>
        <?php
        include 'related.php';
        ?>
    </div>

    <div style="clear: both;"></div>
    <br/>

</div>
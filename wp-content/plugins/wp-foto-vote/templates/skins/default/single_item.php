<?php
$theme_imgs_url = FV_Templater::locateUrl($theme, 'img/');

/** @var FV_Competitor 	$contestant 				*/
/** @var FV_Contest 	$contest 					*/
/** @var string 		$theme 						Skin slug */
/** @var string 		$contest_link 				Link to Contest page */
/** @var array 			$public_translated_messages 	Translations */

?>
<div id="fv_constest_item" class="contest-block photo-single-item">
	<div class="fv_name_wrap fv-single-item__heading-wrap">
        <div class="fv-single-item__nav-back">
            <a href="<?php echo $contest_link; ?>" class="btn-back fv-single-item__back">
                <i class="fvicon fvicon-login"></i> <?php echo $public_translated_messages['back_to_contest'] ?>
            </a>
        </div>

		<div class="fv_name fv-single-item__heading"><?php echo $contestant->getHeadingForTpl('single'); ?></div>
	</div>
	<div class="fv_photo">
		<?php FV_Public_Single::render_main_image($image, $contestant, 'photo-single--main-image mainImage img-thumbnail'); ?>

		<?php if (!empty($next_id)): ?>
			<div class="fv_next fv_nav">
				<a href="<?php echo fv_single_photo_link($next_id, false, $contestant->contest_id); ?>" title="<?php _e('Next', 'fv') ?>"><span class="fvicon-arrow-right"></span></a>
			</div>
		<?php endif; ?>

		<?php if (!empty($prev_id)): ?>
			<div class="fv_prev fv_nav">
				<a href="<?php echo fv_single_photo_link($prev_id, false, $contestant->contest_id); ?>" title="<?php _e('Previous', 'fv') ?>"><span class="fvicon-arrow-left"></span></a>
			</div>
		<?php endif; ?>
		
		<div style="clear: both;"></div>
		<?php if( ! $contest->isNeedHideVotes() ): ?>
            <div class="fv_photo_votes">
                <?php echo $public_translated_messages['vote_count_text']; ?>:
                <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->getVotes($contest); ?></span>
            </div>
        <?php endif; ?>

		<?php if ($konurs_enabled): ?>
			<div class="fv_button"><input type="button" class="fv_vote" value="<?php echo $public_translated_messages['vote_button_text']; ?>" onclick="sv_vote(<?php echo $contestant->id?>)" /></div>
		<?php endif; ?>

        <?php if( fv_setting('soc-counter', false) ): ?>
            <a href="#0" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $contestant->id ?>); return false;" >
                <span class="fv-soc-votes fv_svotes_<?php echo $contestant->id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            </a>
        <?php endif; ?>
		
	</div>

	<?php do_action('fv/public/single_item/extra', $contestant); ?>
		
	<div class="fv_social">
		<span><?php _e('Share to friends', 'fv') ?></span>
		<div class="fv_social_icons">
			<?php if (!fv_setting('voting-noshow-vk', false)): ?>
				<a href="#0" onclick="return sv_vote_send('vk', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-vk.png" /></a>
			<?php endif; ?>
			<?php if (!fv_setting('voting-noshow-fb', false)): ?>
				<a href="#0" onclick="return sv_vote_send('fb', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-fb.png" /></a>
			<?php endif; ?>
			<?php if (!fv_setting('voting-noshow-tw', false)): ?>
				<a href="#0" onclick="return sv_vote_send('tw', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-tw.png" /></a>
			<?php endif; ?>
			<?php if (!fv_setting('voting-noshow-ok', false)): ?>
				<a href="#0" onclick="return sv_vote_send('ok', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-ok.png" /></a>
			<?php endif; ?>
			<?php if (!fv_setting('voting-noshow-gp', false)): ?>
				<a href="#0" onclick="return sv_vote_send('gp', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-gp.png" /></a>
			<?php endif; ?>
			<?php if (!fv_setting('voting-noshow-pi', false)): ?>
				<a href="#0" onclick="return sv_vote_send('pi', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-pi.png" /></a>
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

</div>
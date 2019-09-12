<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $most_voted - array() with photos
 * $variables -
 *
 * @var FV_Contest      $contest
 * $contest_id

 * $leaders_width
 *
 * thumb_size
 * */

/** @var FV_Competitor $photo */

if ( isset($thumb_size['width']) ) {
    $block_leaders_width = (int)$thumb_size['width'] . 'px';
} else {
    $block_leaders_width = round(95/count($most_voted)) . '%';
}

?>
<style>
    img.fv-leaders--image{border-radius:<?php echo fv_setting('lead-thumb-round', 0); ?>%!important;}
    .fv-leaders--details{
        background-color:<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>!important;
        color:<?php echo fv_setting('lead-primary-color', '#e6e6e6', false, 7); ?>!important;
    }
</style>

<div class="fv-leaders block">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo esc_html($title); ?></span>
        </span>
    <?php endif; ?>

    <div class="fv-leaders--container">
        <?php $i = 1; foreach ($most_voted as $key => $photo): ?>
            <div class="fv-leaders--item" style="width:<?php echo $block_leaders_width; ?>;">
                <div class="fv-leaders--image-wrap">
                    <a href="<?php echo $photo->getSingleViewLink(); ?>">
                        <?php FV_Public_Gallery::render_image_html($photo->getThumbArr($thumb_size), $photo, 'fv-leaders--image', 'other'); ?>
<!--                        <img class="fv-leaders--image" src="--><?php //echo esc_url($photo->getThumbUrl($thumb_size)); ?><!--" alt="--><?php //echo esc_attr($photo->name); ?><!--"/>-->
                    </a>
                </div>

                <div class="fv-leaders--details">
                    <?php if ( ! $contest->isNeedHideVotes() ): ?>
                        <div class="fv-leaders--votes"><i class="fvicon fv-vote-icon"></i> <?php echo $photo->getVotes($contest); ?></div>
                    <?php endif; ?>
                    <div class="fv-leaders--name"><?php echo $photo->getHeadingForTpl('list'); ?></div>
                </div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>

<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $variables -
 *
 * $contest_id
 
 * $leaders_width
 *
 * $thumb_size
 * */

/** @var FV_Competitor[] $most_voted */
/** @var FV_Contest $contest */

$block_leaders_width = round(100/count($most_voted), 2) . '%';

$active = 0;
// Move most voted item to 2 place, at it will be it middle
//if (count($most_voted) > 2) {
//    $most_voted[999] = $most_voted[0];
//    $most_voted[0] = $most_voted[1];
//    $most_voted[1] = $most_voted[999];
//    unset($most_voted[999]);
//    $active = 1;
//}

$active = 0;

$title_bg = fv_setting('lead-primary-bg', '#e6e6e6', false, 7);
$img_border_radius = fv_setting('lead-thumb-round', false, 0);

?>
<style>
    .fv-leaders-block2--item{
        border-color:<?php echo fv_setting('lead-primary-color', '#f7941d', false, 7); ?>!important;
        background:<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>!important;
    }
    .fv-leaders-block2--item-title{color:<?php echo fv_setting('lead-primary-color', '#f7941d', false, 7); ?>!important;}
    <?php if ( $img_border_radius > 0 && $img_border_radius <= 50 ): ?>
        img.fv-leaders--image{border-radius:<?php echo $img_border_radius; ?>%!important;}
    <?php endif; ?>
</style>

<div class="fv-leaders fv-leaders-block2">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo $title; ?></span>
        </span>
    <?php endif; ?>
    <div class="fv-leaders-block2--container fv-leaders-block2--container-<?php echo count($most_voted); ?>">
        <?php $i = 0; foreach ($most_voted as $key => $photo):
            $thumb = $photo->getThumbArr($thumb_size);
            ?>
            <div class="fv-leaders-block2--item <?php echo ($i == $active)? 'fv-leaders-block2--item-tall fv-leaders-block2--item-right':''; ?> <?php echo ($i > $active)? 'fv-leaders-block2--item-right':''; ?>"  style="width:<?php echo $block_leaders_width; ?>;">
                <div class="fv-leaders-block2--item-title"><?php echo $photo->getHeadingForTpl('list'); ?></div>
                <div class="fv-leaders-block2--image-wrap">
                    <a href="<?php echo $photo->getSingleViewLink(); ?>">
                        <?php FV_Public_Gallery::render_image_html($photo->getThumbArr($thumb_size), $photo, 'fv-leaders--image', 'other'); ?>
<!--                        <img class="fv-leaders--image" src="--><?php //echo $thumb[0]; ?><!--" alt="--><?php //echo esc_attr($photo->getHeadingForTpl('list')); ?><!--" width="--><?php //echo $thumb[1]; ?><!--"/>-->
                    </a>
                </div>
                <?php if ( ! $contest->isNeedHideVotes() ): ?>
                    <div class="fv-leaders-block2--item-votes"><i class="fvicon fv-vote-icon"></i> <?php echo $photo->getVotes($contest); ?></div>
                <?php endif; ?>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>

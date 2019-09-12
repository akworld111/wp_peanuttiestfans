<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
*/
/** @var array $thumb_size */
/** @var FV_Competitor $competitor */
/** @var FV_Contest $contest */
/** @var string $skin */

$thumbnail = $competitor->getThumbArr($thumb_size);

$hide_votes = false;
?>

<div class="FV_Winner ContestEntry" style="width: <?php echo get_option('fv-winners-block-width', FV_CONTEST_BLOCK_WIDTH); ?>px;">
    <div class="FV_Winner__name">
        <span><i class="fvicon-trophy2"></i> <?php echo $competitor->getPlaceCaption(); ?></span>
    </div>

    <div class="FV_Winner__photo">
        <a data-id="<?php echo $competitor->id; ?>" href="<?php echo $competitor->getSingleViewLink(); ?>" title="<?php echo esc_attr($competitor->getHeadingForTpl('winner')); ?>">
            <?php FV_Public_Gallery::render_image_html($thumbnail, $competitor, 'FV_Winner__photo_img', 'other'); ?>
        </a>
    </div>

    <div class="FV_Winner__place_caption"><?php echo $competitor->getHeadingForTpl('winner'); ?></div>

    <div class="FV_Winner__description"><?php echo $competitor->getDescForTpl('winner'); ?></div>

    <?php do_action('fv/public/winner/extra', $competitor); ?>

    <?php if ($hide_votes == false): ?>
        <div class="FV_Winner__votes">
            <i class="fvicon fv-vote-icon"></i>
            <span class="sv_votes_<?php echo $competitor->id; ?>"><?php echo $competitor->getVotes(); ?></span>
        </div>
    <?php endif; ?>
</div>
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

$bg_height = apply_filters( 'fv/public/winners/skin_' . $skin . '/bg_height',
    get_option('fv-winners-block-width', FV_CONTEST_BLOCK_WIDTH)*0.7,
    $competitor,
    $contest
);

?>

<div class="FV_Winner text-center" data-id="<?php echo $competitor->id; ?>" style="width: <?php echo get_option('fv-winners-block-width', FV_CONTEST_BLOCK_WIDTH); ?>px;">
    <div class="FV_Winner__place_caption"><i class="fvicon fvicon-trophy2"></i> <?php echo $competitor->getPlaceCaption(); ?></div>

    <div class="FV_Winner__bg" style="background-image: url(<?php echo esc_attr($thumbnail[0]); ?>); height: <?php echo $bg_height; ?>px;"></div>
    <div class="FV_Winner__name">
        <?php echo $competitor->getHeadingForTpl('winner'); ?> [<span class="FV_Winner__likes"><?php echo $competitor->getVotes($contest); ?> <i class="fvicon fv-vote-icon"></i></span>]
    </div>

    <div class="FV_Winner__description"><?php echo $competitor->getDescForTpl('winner'); ?></div>
</div> <!-- /.FV_Winner -->
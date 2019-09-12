<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
*/
/** @var array $thumb_size */
/** @var FV_Competitor $competitor */
/** @var FV_Contest $contest */
/** @var string $skin */

$metateam = $competitor->meta()->get_value('meta_team');

$thumbnail = $competitor->getThumbArr(array('width'=>575, 'height'=>452, 'crop'=>1, 'size_name'=>'winner_thumb'));

$hide_votes = false;

$bg_height = apply_filters( 'fv/public/winners/skin_' . $skin . '/bg_height',
    get_option('fv-winners-block-width', FV_CONTEST_BLOCK_WIDTH)*0.7,
    $competitor,
    $contest
);

?>



<div class="FV_Winner text-center" data-id="<?php echo $competitor->id; ?>">
    <div class="FV_winner__wrap">
        <div class="FV_Winner__bg">
            <img src="<?php echo esc_attr($thumbnail[0]); ?>" alt="">
        </div>
        <div class="FV_Winner__name">
            <?php echo $competitor->getHeadingForTpl('winner'); ?> <span class="FV_Winner__likes"><?php echo $competitor->getVotes($contest); ?></span>
        </div>
        <div class="FV_winner_meta">
            <?php echo esc_html($competitor->getAuthorName()); ?>
            <div class="contest-block-description"><?php echo $metateam; ?></div>  
        </div>
    </div> 

</div> <!-- /.FV_Winner -->
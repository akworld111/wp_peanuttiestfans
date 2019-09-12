<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 *
 * $contest_id
 *
 * thumb_size
 * */

/** @var FV_Competitor[] $most_voted */
/** @var FV_Contest $contest */

$img_border_radius = fv_setting('lead-thumb-round', 0);
?>

<style>
    .LeaderBoard__item_counter{background:<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>!important;}
    .LeaderBoard__item_link{color:<?php echo fv_setting('lead-primary-color', '#f7941d', false, 7); ?>!important;}
</style>

<div id="LeaderBoard" class="fv-leaders">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <div class="LeaderBoard__title_container">
            <?php if ( $title_img ) : ?>
                <img src="<?php echo esc_url($title_img); ?>" class="LeaderBoard__title_image">
            <?php endif; ?>
            <div class="LeaderBoard__title"><?php echo esc_html($title); ?></div>
        </div>
    <?php endif; ?>

    <ul class="LeaderBoard__list">
        <?php
        if ( $most_voted ):
            $votes_percent = 0;
            $counter = 1;
            foreach ($most_voted as $photo) :
                $color = array_shift ( $colors );
                $votes_percent = 0;
                if ( $votes_total && $photo->votes_count ) {
                    $votes_percent = $photo->votes_count / ($votes_total / 100);
                }
                ?>
                <li class="LeaderBoard__item">
                    <div class="LeaderBoard__item_counter">
                        <?php echo $counter++; ?>
                    </div>
                    <div class="LeaderBoard__item_inner">
                        <div class="LeaderBoard__item_title">
                            <a href="<?php echo $photo->getSingleViewLink(); ?>" class="LeaderBoard__item_link" target="_blank">
                                <i class="fvicon fvicon-image"></i> <?php echo esc_attr($photo->getHeadingForTpl()); ?>
                            </a>
                        </div>
                        <?php if ( ! $contest->isNeedHideVotes() ): ?>
                            <div class="LeaderBoard__item_votes">
                                <?php echo $photo->getVotes($contest), ' (', round($votes_percent, 2), '%)'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="LeaderBoard__item_progress"
                            <?php if ( !empty($poll_animation) && $poll_animation ): ?>
                             data-w="<?php echo $votes_percent; ?>" style="background: <?php echo $color; ?>;">
                            <?php else: ?>
                                style="width: <?php echo $votes_percent; ?>%; background: <?php echo $color; ?>;">
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php
            endforeach;
        endif;
        ?>
        <li class="LeaderBoard__item LeaderBoard__item_total">
            <div class="LeaderBoard__item_votes LeaderBoard__item_votes_total">
                <?php echo esc_html($total_title), $votes_total; ?>
            </div>
        </li>
    </ul>
</div>
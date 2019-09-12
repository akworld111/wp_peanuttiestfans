<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $most_voted - array() with photos
 * $variables -
 *
 * $contest_id
 
 * $leaders_width
 *
 * thumb_size
 * */

/** @var FV_Competitor[] $most_voted */
/** @var FV_Contest $contest */
?>

<style>
    .fv-most-voted-table thead{background:<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>!important;}
    .fv-most-voted-table .fv-most-voted-table--a{color:<?php echo fv_setting('lead-primary-text', '#f7941d', false, 7); ?>!important;}
    img.fv-leaders--image{border-radius:<?php echo fv_setting('lead-thumb-round', 0); ?>%!important;}
</style>

<div class="fv-leaders">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
                <?php echo $title; ?></span>
            </span>
    <?php endif; ?>
    <table class="table table-striped table-hover fv-most-voted-table">
        <thead>
        <tr>
            <th><i class="fvicon-star"></i> <?php echo fv_get_transl_msg('lead_table_rank'); ?></th>
            <th><i class="fvicon-image"></i> <?php echo fv_get_transl_msg('lead_table_photo'); ?></th>
            <?php if ( ! $contest->isNeedHideVotes() ): ?>
                <th><i class="fvicon fv-vote-icon"></i> <?php echo fv_get_transl_msg('lead_table_votes'); ?></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>

        <?php
        $i = 1;
        foreach ($most_voted as $key => $photo):
            $thumb = $photo->getThumbArr($thumb_size);
            ?>
            <tr>
                <td class="fv-most-voted-table--rank"><span><?php echo $i; ?></span></td>
                <td class="fv-most-voted-table--contestant">
                    <div class="fv-most-voted-table--info">
                        <a href="<?php echo $photo->getSingleViewLink(); ?>" class="trans"></a>
<!--                        <img class="fv-leaders--image" src="--><?php //echo $thumb[0]; ?><!--" alt="--><?php //echo esc_attr($photo->getHeadingForTpl('list')); ?><!--" width="--><?php //echo $thumb[1]; ?><!--">-->
                        <?php FV_Public_Gallery::render_image_html($photo->getThumbArr($thumb_size), $photo, 'fv-leaders--image', 'other'); ?>
                        <strong class="fv-most-voted-table--info-title"><?php echo $photo->getHeadingForTpl('list'); ?></strong>
                    </div>

                </td>
                <?php if ( ! $contest->isNeedHideVotes() ): ?>
                    <td class="fv-most-voted-table--votes-td"><span><?php echo $photo->getVotes($contest); ?></span></td>
                <?php endif; ?>
            </tr>
        <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>
</div>
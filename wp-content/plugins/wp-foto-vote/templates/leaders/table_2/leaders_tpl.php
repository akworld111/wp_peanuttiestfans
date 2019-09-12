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
 
 * $page_url
 * $leaders_width
 *
 * thumb_size
 * */

/** @var FV_Competitor[] $most_voted */
/** @var FV_Contest $contest */

$img_border_radius = fv_setting('lead-thumb-round', 0);
?>

<style>
    .fv-most-voted-table2 thead{background:<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>!important;}
    .fv-most-voted-table2{color:<?php echo fv_setting('lead-primary-color', '#f7941d', false, 7); ?>!important;}
    <?php if ( $img_border_radius > 0 && $img_border_radius <= 50 ): ?>
        img.fv-leaders--image{border-radius:<?php echo $img_border_radius; ?>%!important;}
    <?php endif; ?>
</style>

<div class="fv-leaders">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
                <?php echo $title; ?></span>
            </span>
    <?php endif; ?>

    <table class="fv-most-voted-table2">
        <thead>
        <tr>
            <th><?php echo fv_get_transl_msg('lead_table_rank'); ?></th>
            <th><?php echo fv_get_transl_msg('lead_table_photo'); ?></th>
            <?php if ( ! $contest->isNeedHideVotes() ): ?>
                <th><?php echo fv_get_transl_msg('lead_table_votes'); ?></th>
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
                <td class="fv-most-voted-table2--img-td">
                    <a href="<?php echo $photo->getSingleViewLink(); ?>" class="fv-most-voted-table2--a">
<!--                        <img class="fv-leaders--image" src="--><?php //echo $thumb[0]; ?><!--" alt="--><?php //echo esc_attr($photo->getHeadingForTpl('list')); ?><!--" width="--><?php //echo $thumb[1]; ?><!--">-->
                        <?php FV_Public_Gallery::render_image_html($photo->getThumbArr($thumb_size), $photo, 'fv-leaders--image', 'other'); ?>
                    </a>
                </td>
                <td  class="fv-most-voted-table2--title-td"><?php echo $photo->getHeadingForTpl('list'); ?></td>
                <?php if ( ! $contest->isNeedHideVotes() ): ?>
                    <td><?php echo $photo->getVotes($contest); ?></td>
                <?php endif; ?>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>
</div>
<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $title - Leaders title
 *
 * $most_voted - array() with photos
 * $variables -
 *
 * $contest_id
 
 * $leaders_width
 *
 */
?>

<div class="fv-most-voted text">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo $title; ?></span>
        </span>
    <?php endif; ?>

    <?php $i = 1; foreach ($most_voted as $key => $photo): ?>
        <a href="<?php echo fv_single_photo_link($photo->id); ?>"><strong><?php echo $photo->name ?></strong></a>
        <span id="fv-most-voted-<?php echo $key ?>"> <?php echo $photo->votes_count ?></span>
        <?php if (($i != count($most_voted))) echo ', ' ?>
    <?php $i++; endforeach; ?>
</div>

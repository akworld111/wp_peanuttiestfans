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
 * $thumb_size
 * */

/* @var FV_Competitor[] $most_voted */

$block_leaders_width = round(100/count($most_voted), 2) . '%';

$active = 0;
// Move most voted item to 2 place, at it will be it middle
if (count($most_voted) > 2) {
    $most_voted[999] = $most_voted[0];
    $most_voted[0] = $most_voted[1];
    $most_voted[1] = $most_voted[999];
    unset($most_voted[999]);
    $active = 1;
}

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
            $thumb = FvFunctions::getPhotoThumbnailArr($photo, $thumb_size);
            ?>


            <div class="contest-block ContestEntry">
                <div class="ContestEntry__name">
                    <span>Pupina</span>
                </div>

                <div class="ContestEntry__photo">
                    <a name="photo-154" data-id="154" class="" rel="fw" href="http://www.latuaestateaddosso.it/gallery/?fv-sorting=popular&amp;contest_id=1&amp;photo=154" title="Pupina" data-title="Pupina <br/>Vote count: <span class='sv_votes_154'>11854</span>">
                        <img src="http://www.latuaestateaddosso.it/wp-content/uploads/IMG_1772-440x480.jpg">
                    </a>
                </div>

                <div class="ContestEntry__footer">
                    <div class="row">
                        <div class="col-xs-12">
                            <img src="http://www.latuaestateaddosso.it/wp-content/uploads/2016/08/contest_footer.png" alt="">
                        </div>
                    </div>
                </div>


                <?php if ($hide_votes == false): ?>
                    <div class="ContestEntry__votes">
                        <span class="sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    </div>

                    <div class="contest-block-votes">
                        <?php echo $public_translated_messages['vote_count_text']; ?>:
                        &nbsp;<span class="contest-block-votes-count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                        <?php if( fv_setting('soc-counter', false) ): ?>
                            <br/><?php echo $public_translated_messages['shares_count_text']; ?>: <span class="contest-block-votes-count fv_svotes_<?php echo $id ?>">0</span>
                        <?php endif; ?>
                        <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
                    </div>
                <?php endif; ?>

                <p class="contest-description">
                    Il nostro primo viaggio fuori dall'Italia, nella città che ho sognato di visitare sin da quando ero piccola. Sempre insieme.    </p>
            </div>


            <div class="fv-leaders-block2--item <?php echo ($i == $active)? 'fv-leaders-block2--item-tall':''; ?>"  style="width:<?php echo $block_leaders_width; ?>;">
                <div class="fv-leaders-block2--item-title"><?php echo $photo->name; ?></div>
                <div class="fv-leaders-block2--image-wrap">
                    <a href="<?php echo fv_single_photo_link($photo->id); ?>"><img class="fv-leaders--image" src="<?php echo $thumb[0]; ?>" alt="<?php echo $photo->name; ?>"  width="<?php echo $thumb[1]; ?>"/></a>
                </div>
                <div class="fv-leaders-block2--item-votes"><i class="fvicon-heart3"></i> <?php echo $photo->getVotes(); ?></div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>

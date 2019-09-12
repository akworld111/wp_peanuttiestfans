<?php
defined('ABSPATH') or die("No script kiddies please!");
?>
<div class="fv_leaders photo-display-container ju"><?php
        $i = 1; foreach ($most_voted as $key => $unit):
        $thumbnail = FvFunctions::getPhotoThumbnailArr($unit, "medium");
        //wp_get_attachment_image_src($unit->image_id, "medium");
        if ( !get_option('fotov-photo-in-new-page', false) ) {
            $image_full = $unit->url;
        } else {
            $image_full = fv_single_photo_link($unit->id);
        }
        $style = '';
        if ( get_option('fotov-leaders-count', 3) == 4 ) {
            $style ='style="width: 18%;"';
        }

        ?>
        <div class="photo-display-item contest-block" <?php echo $style; ?>>

            <div class="thumb">
                <div class="photo_container pc_ju">
                    <a data-id="<?php echo $unit->id; ?>"
                       class="<?php if (!fv_photo_in_new_page($theme)): ?> fv_lightbox nolightbox <?php endif; ?>" rel="fw"
                       title="<?php echo htmlspecialchars(stripslashes($unit->name)) . ' <br/>' . $public_translated_messages['vote_count_text'] . ': ' . $unit->votes_count ?>"
                       href="<?php echo $image_full ?>">
                        <img src="<?php echo $thumbnail[0]; ?>" alt="" class="pc_img" border="0">
                    </a>
                </div>

                <div class="meta">
                    <div class="attribution-block">
                        <span class="attribution">
                            <span><?php echo mb_substr($unit->name, 0, 30, 'UTF-8') ?> </span>
                        </span>
                    </div>

                    <span class="inline-icons">
                        <a data-track="favorite" href="#" class="fave-star-inline canfave fv_vote" onclick="sv_vote(<?php echo $unit->id ?>); return false;">
                            <snap class="fvicon fvicon-star3"></snap> <span class="fave-count count sv_votes_<?php echo $unit->id ?>"><?php echo $unit->votes_count ?></span>
                        </a>
                        <a href="#0" onclick="jQuery('a[name=photo-<?php echo $unit->id ?>]')[0].click(); return false;" class="lightbox-inline">
                            <span class="fvicon-expand2"></span>
                        </a>
                    </span>
                </div>
            </div>
        </div>

        <?php $i++; endforeach; ?>
</div>
<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * $photo - PHOTO (object)
 * $id - PHOTO ID (int)
 * $thumbnail - PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $image_full - PHOTO FULL SRC (string)
 * $name - PHOTO NAME (string - max 255)
 * $description - PHOTO DESCRIPTION (string - max 255)
 * $photo->full_description - PHOTO FULL DESCRIPTION (string - max 500)
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code> * $votes - PHOTO VOTES COUNT (int)
* $upload_info - json decoded Upload form fields*
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*
*** OTHER ***
 * $leaders - is this leaders block? (bool)
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $page_url - PAGE URL (string)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $hide_votes - NEED HIDE VOTES? (bool)
 * $author_link
 * $author_name
 */

/** @var FV_Competitor $photo */
?>

<div class="photo-display-item item contest-block" data-h="<?php echo $thumbnail[2]; ?>" data-w="<?php echo $thumbnail[1]; ?>" data-id="<?php echo $id; ?>">

    <div class="thumb">
        <div class="photo_container pc_ju">
            <a name="<?php echo 'photo-' . $id; ?>" data-id="<?php echo $id; ?>"
               class="<?php if (!fv_photo_in_new_page($theme)): ?>fv_lightbox nolightbox no-lightbox noLightbox<?php endif; ?>" rel="fw"
               title="<?php echo htmlspecialchars(stripslashes($name)); ?>" data-title="<?php echo $data_title ?>"
               href="<?php echo $image_full ?>">
                <?php
                    if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                        printf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                 data-lazy-src="%s" height="%s" class="pc_img fv-lazy" alt="%s"/>', $thumbnail[0], $thumbnail[2], htmlspecialchars($name));
                    }
                    else {
                        printf('<img src="%s" height="%s" class="pc_img" alt="%s"/>', $thumbnail[0], $thumbnail[2], htmlspecialchars($name));
                    }
                ?>
            </a>
        </div>

        <div class="meta">
            <div class="title">
                <a href="<?php echo $photo->getSingleViewLink(); ?>" onclick="jQuery('a[name=photo-<?php echo $id ?>]')[0].click(); return false;" class="title">
                    <?php echo substr($description, 0, 80) ?>
                </a>

                <?php if ( get_option('fv-display-author') && $photo->getAuthorName() ): ?>
                    <div class="contest-block--author">
                        <?php echo $photo->getAuthorAvatarHtml(25); ?>

                        <?php if ( get_option('fv-display-author') == 'link' && $photo->getAuthorLink() ): ?>
                            <a href="<?php echo esc_url($photo->getAuthorLink()); ?>" class="contest-block--author-link" target="_blank">
                                <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                            </a>
                        <?php else: ?>
                            <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>

            <div class="attribution-block">
                <span class="attribution">
                    <span><?php echo $name; ?> </span>
                </span>
            </div>

            <span class="inline-icons">
                <?php if( fv_setting('soc-counter', false) ): ?>
                    <i class="fvicon-share"></i> <span class="fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                <?php endif; ?>

                <a data-track="favorite" href="#0" class="fave-star-inline canfave fv_vote" <?php if ($konurs_enabled): ?> onclick="sv_vote(<?php echo $id ?>); return false;" <?php endif; ?>>
                    <i class="fvicon- fv-vote-icon"></i>
                    <?php if( $hide_votes == false ): ?>
                        <span class="fave-count count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    <?php endif; ?>
                </a>
                <a href="#0" onclick="jQuery('a[name=photo-<?php echo $id ?>]')[0].click(); return false;" class="lightbox-inline">
                    <span class="fvicon-expand2"></span>
                </a>
                <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
            </span>
        </div>
    </div>
</div>
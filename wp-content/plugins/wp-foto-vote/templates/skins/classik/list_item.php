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
 */
?>

<div class="sv_unit contest-block" style="width: <?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH); ?>px;" data-id="<?php echo $id; ?>">
    <div align="center">
        <a name="photo-<?php echo $id ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" href="<?php echo $image_full; ?>" title="<?php echo htmlspecialchars($name); ?>" data-title="<?php echo esc_html($data_title); ?>">
            <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, '', $theme); ?>
        </a>
    </div>
    <?php do_action('fv/public/list_item/extra', $photo); ?>
    <div class="contest-block-title"><strong><?php echo $name; ?></strong></div>
    <div class="contest-block-description"><em><?php echo $description; ?></em></div>

    <?php if ($hide_votes == false): ?>
        <div class="contest-block-votes">
            <?php echo $public_translated_messages['vote_count_text']; ?>:
            &nbsp;<span class="contest-block-votes-count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
            <?php if( fv_setting('soc-counter', false) ): ?>
                <br/><?php echo $public_translated_messages['shares_count_text']; ?>: <span class="contest-block-votes-count fv_svotes_<?php echo $id ?>">0</span>
            <?php endif; ?>
            <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
        </div>
    <?php endif; ?>

    <?php if ($konurs_enabled): ?>
        <div class="fv_button">
            <button class="fv_vote fv-vote-icon" onclick="sv_vote(<?php echo $id ?>)">
                <?php echo $public_translated_messages['vote_button_text']; ?>
            </button>
        </div>
    <?php endif; ?>
</div>
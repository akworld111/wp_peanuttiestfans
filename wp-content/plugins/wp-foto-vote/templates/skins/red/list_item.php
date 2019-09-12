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
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*
*** OTHER ***
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $hide_votes - NEED HIDE VOTES? (bool)
 */
?>

<div class="contest-block ContestEntry" style="width: <?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH); ?>px;" data-id="<?php echo $id; ?>">
    <div class="ContestEntry__name">
        <span><?php echo $name; ?></span>
    </div>

    <div class="ContestEntry__photo">
        <a name="photo-<?php echo $id ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" href="<?php echo $image_full; ?>" title="<?php echo esc_attr($name) ?>" data-title="<?php echo esc_attr($data_title); ?>">
            <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, '', $theme); ?>        </a>
    </div>

    <?php do_action('fv/public/list_item/extra', $photo); ?>

    <div class="ContestEntry__description"><?php echo $description; ?></div>
    
    <?php if ($hide_votes == false): ?>
        <div class="ContestEntry__votes">
            <span class="sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
        </div>
    <?php endif; ?>

    <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>

    <?php do_action('fv/contest_list_item/before_vote_button', $photo, $contest, $theme); ?>

    <?php if ($konurs_enabled): ?>
        <div class="fv_button ContestEntry__vote_link ContestEntry__bottom_block">
            <span class="fv_vote" onclick="sv_vote(<?php echo $id ?>)" role="button">
                <i class="fvicon- fv-vote-icon"></i>
                <?php echo $public_translated_messages['vote_button_text']; ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="ContestEntry__share_block ContestEntry__bottom_block">
        <span class="ContestEntry__share_button" role="button" onclick="FvModal.goShare(<?php echo $id ?>);">
            <i class="fvicon-share"></i>
            <?php if( fv_setting('soc-counter', false) ): ?>
                <span class="ContestEntry__shares fv_svotes_<?php echo $id ?>" title="<?php echo esc_attr($public_translated_messages['shares_count_text']); ?>">0</span>
            <?php endif; ?>
        </span>
    </div>
</div>
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
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*** OTHER ***
 * $leaders - is this leaders block? (bool)
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $upload_info - json decoded Upload form fields
 * $hide_votes - NEED HIDE VOTES? (bool)
 */
?>

<div class="contest-block <?php echo (!$konurs_enabled)? 'ended': ''; ?>" style="width: <?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH) ; ?>px;" data-id="<?php echo $id; ?>">

    <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, '', $theme); ?>

    <div class="vote-heart fv_button fv_vote" onclick="sv_vote(<?php echo $id ?>, 'vote', this);">
        <span class="fvicon fv-vote-icon"></span>
        <?php if( $hide_votes == false ): ?>
            <span class="sv_votes sv_votes_<?php echo $id ?><?php echo (!$konurs_enabled)? ' ended': ''; ?>"><?php echo $votes; ?></span>
        <?php endif; ?>
    </div>

    <?php if( fv_setting('soc-counter', false) ): ?>
        <a href="#0" class="fv-share-counter" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
            <i class="fvicon-share"></i> <span class="fv_svotes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
        </a>
    <?php endif; ?>

    <a name="photo-<?php echo $id ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" href="<?php echo $image_full ?>" title="<?php echo esc_attr($name); ?>" data-title="<?php echo $data_title ?>">
        <span class="fvicon fvicon-zoomin"></span>
    </a>

</div>
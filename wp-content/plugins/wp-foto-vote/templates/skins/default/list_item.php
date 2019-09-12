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

<div class="contest-block" style="width: <?php echo ( !$leaders )? $fv_block_width . 'px' : $fv_block_width . '%' ; ?>;" data-id="<?php echo $id; ?>">
    <a name="photo-<?php echo $id?>" ></a>
    <div>
        <div class="contest-block--img-wrap">
            <a name="photo-<?php echo $id?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" href="<?php echo $image_full ?>" title="<?php echo esc_attr($name); ?>" data-title="<?php echo $data_title ?>">
                <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, '', $theme); ?>
            </a>
        </div>
        <div class="clearfix"></div>
        <?php do_action('fv/public/list_item/extra', $photo); ?>
        <div class="clearfix"></div>
        <div class="contest-block--title">
            <div class="contest-block--name"><?php echo $name ?></div>
            <?php if( $hide_votes == false ): ?>
                <div class="contest-block--votes sv_votes_<?php echo $id?>" title="<?php echo $public_translated_messages['vote_count_text']; ?>: "><?php echo $votes ?></div>
            <?php endif; ?>
        </div>
        <div class="clearfix"></div>
        <div class="contest-block--description"><?php echo $description ?></div>
    </div>
    <div class="clearfix"></div>

	<div class="fv_button">
        <?php if ($konurs_enabled): ?>
            <button class="fv_vote" onclick="sv_vote(<?php echo $id?>)">
                <i class="fvicon- fv-vote-icon"></i> <?php echo $public_translated_messages['vote_button_text']; ?>
            </button>
        <?php endif; ?>
        <a href="#0" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
            <?php if( fv_setting('soc-counter', false) ): ?>
                <span class="fv-soc-votes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            <?php endif; ?>
        </a>
    </div>

</div>
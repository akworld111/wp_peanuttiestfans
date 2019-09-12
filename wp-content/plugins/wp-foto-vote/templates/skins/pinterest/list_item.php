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
 * $description - PHOTO DESCRIPTION (string - max 500)
 * $photo->full_description - PHOTO FULL DESCRIPTION (string - max 1255)
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $votes - PHOTO VOTES COUNT (int)
 * $upload_info - json decoded Upload form fields*
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*** OTHER ***
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $hide_votes - NEED HIDE VOTES? (bool)
 * $author_link
 * $author_name
 */
/** @var FV_Competitor $photo */

$rel = apply_filters('fv/public/theme/list_item/rel', 'fw', $photo);
// width: < ?php echo $thumbnail[1]; ? >px;

$display_author = get_option('fv-display-author');
if ( !empty($shortcode_args) && null !== $shortcode_args['display_author'] ) {
    $display_author = (bool) $shortcode_args['display_author'];
}

?>

<div class="contest-block clg-item is-gallery" style="width: <?php echo $fv_block_width . 'px'; ?>;" data-id="<?php echo $id; ?>">
    <div class="clg-item-head" style="height: <?php echo $thumbnail[2]; ?>px;">
        <a name="photo-<?php echo $id; ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="<?php echo $rel; ?>" href="<?php echo esc_url($image_full); ?>" title="<?php echo esc_attr($data_title); ?>" data-title="<?php echo esc_attr($data_title); ?>">
            <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, 'clg-cover-image', $theme); ?>        
        </a>

        <div class="clg-head-overlay" style="visibility: hidden;">
            <?php if (!fv_photo_in_new_page($contest)): ?>
                <a href="#" onclick="jQuery('a[name=photo-<?php echo $id ?>]').click(); return false;" class="fvicon clg-head-view"></a>
            <?php else: ?>
                <a href="<?php echo esc_url($image_full); ?>"  class="fvicon clg-head-view"></a>
            <?php endif; ?>

            <div class="clg-head-social">
                <?php if ($konurs_enabled): ?>
                    <span class="clg-like-button fv_vote fvicon- fv-vote-icon" onclick="sv_vote(<?php echo $id ?>); return false;" title="<?php echo $public_translated_messages['vote_button_text']; ?>"></span>
                <?php endif; ?>
                    <span class="clg-facebook-share fvicon-facebook" onclick="return sv_vote_send('fb', this ,<?php echo $id ?>);" ></span>
                    <span class="clg-share fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>);" ></span>
                    <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
            </div>
        </div>
    </div>
    <?php
        // Avoid 2 actions call
        if ( has_action('fv/public/list_item/extra') ) {
            do_action('fv/public/list_item/extra', $photo);
        } else {
            do_action('fv/public/punterest_theme/list_item/extra', $photo);
        }
    ?>
    <div class="clg-item-info">
        <div class="clg-body-social">
            <?php if ($konurs_enabled): ?>
                <span class="clg-like-button fvicon-heart2" onclick="sv_vote(<?php echo $id ?>); return false;" title="<?php echo $public_translated_messages['vote_button_text']; ?>"></span>
            <?php endif; ?>
            <span class="clg-facebook-share fvicon-facebook" onclick="return sv_vote_send('fb', this ,<?php echo $id ?>);" ></span>
            <span class="clg-share fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>);" ></span>
            <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
        </div>

        <div>

            <p class="clg-info-social clg-info-row">
                <?php if( fv_setting('soc-counter', false) ): ?>
                    <i class="fvicon-share"></i><span class="clg-info-row--text fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                <?php endif; ?>

                <?php if( $hide_votes == false ): ?>
                    <i class="clg-info-likes fvicon-" title="<?php echo $votes ?>"></i> 
                    <span class="clg-info-row--text sv_votes_<?php echo $id ?>" title="<?php echo $votes ?>"><?php echo $votes ?></span>
                <?php endif; ?>
            </p>

            <div class="clg-info-title sv_title"><?php echo $name; ?></div>
        </div>
        <div class="clg-info-row">
            <div class="clg-description">
                <?php echo $description; ?>
            </div>

            <?php if ( $display_author && $photo->getAuthorName() ): ?>
                <?php echo $photo->getAuthorAvatarHtml(25); ?>

                <?php if ( get_option('fv-display-author') == 'link' && $photo->getAuthorLink() ): ?>
                    <a href="<?php echo esc_url($photo->getAuthorLink()); ?>" class="clg-info--author-link" target="_blank">
                        <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                    </a>
                <?php else: ?>
                    <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
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
<div class="contest-block" data-id="<?php echo $id; ?>">

    <article class="hermes-entry" style="background-image: url( <?php echo esc_attr($thumbnail[0]); ?> );">
        <a name="photo-<?php echo $id ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?>" rel="fw" href="<?php echo esc_attr($image_full); ?>" title="<?php echo esc_attr($name) ?>" data-title="<?php echo esc_attr($data_title); ?>">
            <div class="hermes-overlay"></div>
        </a>

        <div class="hermes-actions">

            <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>

            <?php do_action('fv/contest_list_item/before_vote_button', $photo, $contest, $theme); ?>

            <?php if ($konurs_enabled): ?>
                <span class="fv_vote hermes-actions__one hermes-actions__vote" onclick="fv_vote(<?php echo $id ?>)" role="button">
                    <i class="fvicon- fv-vote-icon"></i><span class="hermes-actions__vote_text"><?php echo $public_translated_messages['vote_button_text']; ?></span>
                </span>
            <?php endif; ?>

            <span class="hermes-actions__one hermes-actions__share" role="button" onclick="FvModal.goShare(<?php echo $id ?>);">
                <i class="fvicon-share"></i>
                <?php if (fv_setting('soc-counter', false)): ?>
                    <span class="ContestEntry__shares fv_svotes_<?php echo $id ?>" title="<?php echo esc_attr($public_translated_messages['shares_count_text']); ?>">0</span>
                <?php endif; ?>
            </span>
        </div>
        
        <header class="hermes-header">
            <div class="hermes-header__description_wrap">
                <div class="hermes-header__description"><?php echo esc_html($description); ?></div>

                <?php do_action('fv/public/list_item/extra', $photo); ?>


                <?php if ( $display_author && $photo->getAuthorName() ): ?>
                    <div class="hermes-header__author">
                        <?php echo $photo->getAuthorAvatarHtml(25); ?>

                        <?php if ( get_option('fv-display-author') == 'link' && $photo->getAuthorLink() ): ?>
                            <a href="<?php echo esc_url($photo->getAuthorLink()); ?>" class="hermes-header__author-link fv-author-link" target="_blank">
                                <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                            </a>
                        <?php else: ?>
                            <span class="clg-by"><?php echo esc_html($photo->getAuthorName()); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($hide_votes == false): ?>
            <div class="hermes-header__stats">
                    <span class="hermes-header__stats-votes">
                        <i class="fvicon- fv-vote-icon"></i> <span class="sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    </span>
            </div>
            <?php endif; ?>

            <div class="hermes-title">
                <?php echo esc_html($name); ?>
            </div>
        </header><!-- .hermes-header -->
    </article>

</div><!-- .hetry-col -->
<?php
defined('ABSPATH') or die("No script kiddies please!");
/** @var FV_Competitor $photo */
/** @var FV_Contest $contest */
/** @var string $name           PHOTO NAME generated from List Photo Heading Tpl */
/** @var string $description    PHOTO DESCRIPTION generated from List Photo Description Tpl */
/** @var string $data_title     Title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a> */

/** @var string $theme          Skin slug */
/** @var bool $konurs_enabled   IS CONTEST active */

/** @var array $public_translated_messages     TRANSLATED MESSAGES (array) */
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * $id - PHOTO ID (int), the same as = $photo->id
 * $thumbnail - PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $image_full - PHOTO FULL SRC (string)
 * $photo->full_description - PHOTO FULL DESCRIPTION (string - max 1255)
*
*** OTHER ***
 * $fv_block_width - contest block width (int)
 * $contest_id - CONTEST ID (int), the same as = $contest->id
 * $page_url - PAGE URL (string)
 */
?>

<div class="contest-block" style="width: <?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH); ?>px;" data-id="<?php echo $id; ?>">
    <a href="<?php echo $image_full; ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" title="<?php echo $data_title ?>" data-title="<?php echo $data_title ?>">
        <div class="contest-block__thumbnail" style="background-image:url('<?php echo $thumbnail[0]; ?>')">
            <div class="contest-block__meta">
                <?php if( trim($description) ): ?>
                    <div class="contest-block__description"><?php echo $description; ?></div>
                <?php endif; ?>
                <br/>
                <div class="contest-block__title">
                    <?php echo $name; ?>
                </div>
            </div>
        </div>
    </a>

    <?php do_action('fv/public/list_item/extra', $photo); ?>

    <div class="contest-block__actions">


            <?php if ($konurs_enabled): ?>
                <span class="contest-block__action contest-block__action_vote" onclick="sv_vote(<?php echo $id ?>); return false;" title="<?php echo esc_attr($public_translated_messages['vote_button_text']); ?>">
                    <i class="fvicon- fv-vote-icon contest-block__vote-icon fv_vote" title="<?php echo $votes ?>"></i>

                    <?php if( $contest->isNeedHideVotes() == false ): ?>
                        <span class="contest-block__votes-count sv_votes_<?php echo $id ?>" title="<?php echo $votes ?>"><?php echo $votes ?></span>
                    <?php endif; ?>

                </span>
            <?php endif; ?>
<!--        <div class="contest-block__votes-wrap">        -->
<!--        </div>-->
        
        <div class="contest-block__actions__right">


            <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
            <span class="contest-block__action" onclick="FvModal.goShare(<?php echo $id ?>);">
                <span class="contest-block__action_share"></span>
                <?php if( fv_setting('soc-counter', false) ): ?>
                    <span class="contest-block__social-shares-count fv_svotes_<?php echo $id ?>" title="<?php echo esc_attr($public_translated_messages['shares_count_text']); ?>">0</span>
                <?php endif; ?>
            </span>
            <a class="contest-block__action contest-block__action_more" href="<?php echo $photo->getSingleViewLink(); ?>"></a>
        </div>
    </div>
</div>
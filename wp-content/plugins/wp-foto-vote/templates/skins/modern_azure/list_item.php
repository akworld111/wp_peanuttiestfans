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
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $votes - PHOTO VOTES COUNT (int)
*** OTHER ***
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $page_url - PAGE URL (string)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $hide_votes - NEED HIDE VOTES? (bool)
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
 */

$skin = FV_Skins::i()->get( $theme );
$socials = $skin->getCustomizedValue( 'socials' );

?>
<div class="sv_unit contest-block" style="width: <?php echo ( !$leaders )? $fv_block_width . 'px' : $fv_block_width . '%' ; ?>;" data-id="<?php echo $id; ?>">
	<div align="center" class="contest-block__image">
        <a name="<?php echo ( !$leaders )? 'photo-'.$id : ''; ?>" data-id="<?php echo $id; ?>" class="nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" rel="fw" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars($name); ?>" data-title="<?php echo $data_title ?>">
            <?php FV_Public_Gallery::render_image_html($thumbnail, $photo, '', $theme); ?>
        </a>
    </div>

    <?php do_action('fv/public/list_item/extra', $photo); ?>
    
	<div class="contest-block-title contest-block__title"><strong><?php echo $name; ?></strong></div>
	<div class="contest-block-description contest-block__description"><em><?php echo $description; ?></em></div>
        <?php if ( !$leaders ): ?>
            <div class="contest-block-votes contest-block__votes">
                <?php if( $hide_votes == false ): ?>
                    <?php echo $public_translated_messages['vote_count_text']; ?>:
                    &nbsp;<span class="contest-block-votes-count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                <?php endif; ?>
                <a href="#" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
                    <?php if( fv_setting('soc-counter', false) ): ?>
                        <span class="fv-soc-votes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                    <?php endif; ?>
                </a>
                <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
            </div>

            <?php if ( get_option('fv-display-author') && $photo->getAuthorName() ): ?>
                <div class="contest-block-author">
                    <?php echo $photo->getAuthorAvatarHtml(25); ?>

                    <?php if ( get_option('fv-display-author') == 'link' && $photo->getAuthorLink() ): ?>
                        <a href="<?php echo esc_url($photo->getAuthorLink()); ?>" class="contest-block-author__link" target="_blank">
                            <span class="contest-block-author__name"><?php echo esc_html($photo->getAuthorName()); ?></span>
                        </a>
                    <?php else: ?>
                        <span class="contest-block-author__name"><?php echo esc_html($photo->getAuthorName()); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="fv-share-btns text-center contest-block__sharing">
                <?php if ( in_array('fb', $socials) ): ?>
                    <a class="fv-share-btn ss_fb" href="#0" onclick="return sv_vote_send('fb', this,<?php echo $id ?>);"><i class="fvicon-facebook"></i></a>
                <?php endif; ?>
                <?php if ( in_array('gp', $socials) ): ?>
                    <a class="fv-share-btn ss_gp" href="#0" onclick="return sv_vote_send('gp', this,<?php echo $id ?>);"><i class="fvicon-googleplus"></i></a>
                <?php endif; ?>
                <?php if ( in_array('tw', $socials) ): ?>
                    <a class="fv-share-btn ss_tw" href="#0" onclick="return sv_vote_send('tw', this,<?php echo $id ?>);"><i class="fvicon-twitter"></i></a>
                <?php endif; ?>
                <?php if ( in_array('pi', $socials) ): ?>
                    <a class="fv-share-btn ss_pi" href="#0" onclick="return sv_vote_send('pi', this,<?php echo $id ?>);"><i class="fvicon-pinterest3"></i></a>
                <?php endif; ?>
                <?php if ( in_array('vk', $socials) ): ?>
                    <a class="fv-share-btn ss_vk" href="#0" onclick="return sv_vote_send('vk', this,<?php echo $id ?>);"><i class="fvicon-vk3"></i></a>
                <?php endif; ?>
            </div>

            <?php do_action('fv/contest_list_item/before_vote_button', $photo, $contest, $theme); ?>
            
            <?php if ($konurs_enabled): ?>
			<div class="fv_button text-center">
                    <button class="fv_vote" onclick="sv_vote(<?php echo $id ?>)"><i class="fvicon- fv-vote-icon"> <?php echo $public_translated_messages['vote_button_text']; ?></i></button>
                </div>    
            <?php endif; ?>         
        <?php endif; ?>         
</div>
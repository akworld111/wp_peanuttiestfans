<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
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
 */

$skin = FV_Skins::i()->get( $theme );
$columns = $skin->getCustomizedValue( 'columns' );

?>

<li class="wrap-post mosaic-2 column-<?php echo esc_attr($columns); ?> contest-block pr-thumb-port">
    <div class="wrap-image">
        
        <div class="wrap-caption shape-10">
            <div class="caption-post entry-caption-1 animated-1">
                <div class="btn-captions">
                    <span class="caption-btn btn-preview animated-1"  data-id="<?php echo $id; ?>" onclick="jQuery('a[name=photo-<?php echo $id ?>]').click();">
                        <i class="fvicon-expand"></i>
                    </span>

                    <span class="caption-btn btn-lb animated-1" onclick="FvModal.goShare(<?php echo $id ?>); return false;">
                        <?php if( fv_setting('soc-counter', false) ): ?>
                            <i class="fvicon-share fv-share-counter"> <span class="fv_svotes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span></i>
                        <?php else: ?>
                            <i class="fvicon-share"></i>
                        <?php endif; ?>
                    </span>


                    <?php if ($konurs_enabled): ?>
                        <span class="caption-btn btn-permalink animated-1 fv_vote" onclick="sv_vote(<?php echo $id ?>)" title="<?php echo $public_translated_messages['vote_button_text'] ; ?>">
                            <i class="fvicon- fv-vote-icon"></i>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="caption-elements">
                    <a name="photo-<?php echo $id ?>"  data-id="<?php echo $id; ?>" rel="fw" class="caption-title nolightbox no-lightbox noLightbox <?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox<?php endif; ?> <?php echo $photo->isLocalVideo() ? 'is-video' : ''; ?>" href="<?php echo $image_full ?>" data-title="<?php echo $data_title ?>">
                        <?php echo $name ?>
                    </a>
                    <?php if (mb_strlen($description, 'UTF-8') > 0): ?>
                        <div class="caption-subtitle "><?php echo substr($description, 0, 60) ?></div>
                    <?php endif;?>
                    <div class="meta-caption">
                        <span><?php echo $public_translated_messages['vote_count_text']; ?>: &nbsp;</span><span class="sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="wrap-post-image">
            <div class="post-image hover-1" style="background-image:<?php echo sprintf('url(%s);', $thumbnail[0]); ?>">
            </div>                
        </div>    
        
    </div>
</li>

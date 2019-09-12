<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * @var FV_Competitor $contestant
 *
 * $contestant->id - PHOTO ID (int)
 * $image - PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $contestant->image_id - PHOTO FULL SRC (string)
 * $contestant->image_full - PHOTO FULL SRC (string)
 * $contestant->name - PHOTO NAME (string)
 * $contestant->description - PHOTO DESCRIPTION (string)
 * $contestant->additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $contestant->votes_count - PHOTO VOTES COUNT (int)
 * $contestant->upload_info - json decoded Upload form fields*
*** OTHER ***
 * $prev_id
 * $next_id
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 *
 * @var FV_Contest $contest
 *
 * $contest_id - CONTEST ID (int)

 * $theme - USED SKIN (string)
 */
?>
<div class="photo-single-item contest-block fv-bs-grid clear clearfix">

    <div class="content-heading">
        <?php echo $contestant->name ?>
        <div class="back pull-right">
            <a href="<?php echo $contest_link; ?>">
                <i class="fvicon fvicon-login"></i> <?php echo esc_html($public_translated_messages['back_to_contest']); ?>
            </a>
        </div>
    </div>

    <div class="main-image col-md-12">
        <?php if( $prev_id ): ?>
            <div class="controlArrow controlArrow-prev "><a href="<?php echo fv_single_photo_link($prev_id, false, $contestant->contest_id); ?>" class="fvicon-arrow-left2"></a></div>
        <?php endif; ?>
        <?php if( $next_id ): ?>
            <div class="controlArrow controlArrow-next"><a href="<?php echo fv_single_photo_link($next_id, false, $contestant->contest_id); ?>" class="fvicon-arrow-right2"></a></div>
        <?php endif; ?>
        <p>
            <?php FV_Public_Single::render_main_image($image, $contestant, 'photo-single--main-image mainImage img-thumbnail'); ?>
        </p>
        <?php do_action('fv/public/single_item/extra', $contestant); ?>
    </div>


    <div class="col-md-8">
        <div class="clearfix">
            <div class="image-details">
                <?php do_action('fv/public/single_item/before_description', $contestant); ?>

                <?php if ( $contest->isFinished() && $contestant->place ) : ?>
                    <div class="photo-single-winner photo-single-winner--<?php echo $contestant->place; ?>-place">
                        <h4><i class="fvicon-trophy2"></i> <?php echo $contestant->getPlaceCaption(); ?></h4>
                    </div>
                <?php endif; ?>

                <h3 class="block-heading">
                    <?php echo $public_translated_messages['single_descr_heading']; ?>
                </h3>

                <p><?php echo fv_tpl_contestant_desc($contestant, 'single'); ?></p>

                <?php do_action('fv/public/single_item/after_description', $contestant); ?>
            </div>
        </div>


        <div class="comments-block">
            <h3 class="block-heading"><?php _e('Comments', 'fv') ?></h3>
            <?php
                FV_Public_Single::get_instance()->render_comments($contestant);
            ?>
            </div>

    <!--.col-md-8-->
    </div>

    <div class="col-md-4">

        <span class="sidebar-image-details">
            <?php if( ! $contest->isNeedHideVotes() ): ?>
                <i class="fvicon- fv-vote-icon"></i> <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->getVotes($contest); ?></span>
            <?php endif; ?>
            <?php if( fv_setting('soc-counter', false) ): ?>
                &nbsp;<i class="fvicon-share"></i> <span class="fv_svotes_<?php echo $contestant->id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            <?php endif; ?>
        </span>

        <?php if( $contest->isVotingDatesActive() ): ?>
            <button type="button" class="btn btn-success fv_vote" onclick="sv_vote(<?php echo $contestant->id ?>); return false;"><i class="fvicon- fv-vote-icon"></i> <?php echo $public_translated_messages['vote_button_text']; ?></button>
        <?php endif; ?>

        <div class="clearfix"></div>

        <?php do_action('fv/public/single_item/sidebar_extra', $contestant); ?>

        <?php if ( get_option('fv-display-author') && $contestant->getAuthorName() ): ?>
            <div class="photo-single-author">
                <h3 class="block-heading"><?php echo $public_translated_messages['single_author_heading']; ?></h3>
                <div>
                    <?php echo $contestant->getAuthorAvatarHtml(); ?>

                    <?php if ( get_option('fv-display-author') == 'link' && $contestant->getAuthorLink() ): ?>
                        <a href="<?php echo esc_url($contestant->getAuthorLink()); ?>" class="photo-single__author-link" target="_blank">
                            <span class="clg-by"><?php echo esc_html($contestant->getAuthorName()); ?></span>
                        </a>
                    <?php else: ?>
                        <span class="clg-by"><?php echo esc_html($contestant->getAuthorName()); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <h3 class="block-heading"><?php echo $public_translated_messages['single_share_heading']; ?></h3>
        <div class="clearfix">
            <div class="more-from-site">
                <ul class="action-bar clearfix">
                    <li>
                        <a href="#0" class="twitter" onclick="return sv_vote_send('tw', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-twitter"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="facebook" onclick="return sv_vote_send('fb', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-facebook"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="gplus" onclick="return sv_vote_send('gp', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-googleplus3"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="pintrest" onclick="return sv_vote_send('pi', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-pinterest3"></span>
                        </a>
                    </li>
                </ul>      </div>
        </div>
        <div class="exif" style="display: none">
            <h3 class="block-heading"><?php _e('EXIF Data', 'fv') ?></h3>
            <div class="clearfix exif-info">
                <p>
                    <strong><?php _e('Model', 'fv') ?> </strong>
                    <span class="exif-model">Canon EOS 5D Mark II</span>
                </p>
                <p>
                    <strong><?php _e('Focal Length', 'fv') ?> </strong>
                    <span class="exif-focal-length">24mm</span>
                </p>
                <p><strong><?php _e('Shutter Speed', 'fv') ?> </strong>
                    <span class="exif-shutter-speed">1/640</span></p>
                <p>
                    <strong><?php _e('Aperture', 'fv') ?> </strong>
                    <span class="exif-aperture">F5.6</span>
                </p>
                <p>
                    <strong><?php _e('ISO', 'fv') ?> </strong>
                    <span class="exif-iso">100</span>
                </p>
                <p>
                    <strong><?php _e('Taken At', 'fv') ?> </strong>
                    <span class="exif-taken-at">Sat, Oct 30, 2010 6:32 PM</span>
                </p>
            </div>
        </div>

        <h3 class="block-heading"><?php echo $public_translated_messages['single_more_heading']; ?></h3>
        <div class="clearfix">
            <div class="more-from-site">
                <?php
                if ( !empty($most_voted) ) :
                    $thumb_size = array('width' => 80, 'height' => 80, 'crop' => true, 'size_name'=>'fv-single-related-thumb');

                    /** @var FV_Competitor[] $most_voted */
                    foreach($most_voted as $MOST ):
                        //$image = FvFunctions::getPhotoThumbnailArr($MOST, $thumb_size);
                        ?>
                        <a href="<?php echo $MOST->getSingleViewLink(); ?>" class="more-photos--a">
                            <img src="<?php echo $MOST->getThumbUrl($thumb_size); ?>" alt="<?php echo esc_attr($MOST->getHeadingForTpl()); ?>" class="more-photos--img">
                        </a>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
    </div>

</div>
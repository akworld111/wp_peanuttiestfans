<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
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
 * next_id
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $contest_link - CONTEST link
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 */

/** @var FV_Competitor[] $most_voted */
/** @var FV_Competitor $contestant */

?>
<div class="photo-single-item contest-block fv-bs-grid" data-id="<?php echo $contestant->id; ?>">
    <div class="content-heading">
        <div class="fv-bs-row">
            <div class="col-xs-4 text-left">
                <?php if( $prev_id ): ?>
                    <a class="btn btn-danger photo-single-item--prev" href="<?php echo fv_single_photo_link($prev_id, false, $contestant->contest_id); ?>">
                        <i class="fvicon-arrow-left2"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="col-xs-4 pull-right text-right">
                <?php if( $next_id ): ?>
                    <a class="btn btn-danger photo-single-item--next" href="<?php echo fv_single_photo_link($next_id, false, $contestant->contest_id); ?>">
                        <i class="fvicon-arrow-right2"></i>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-4 pull-right-md">
                <a href="<?php echo $contest_link; ?>" class="btn-back photo-single-item--back">
                    <i class="fvicon fvicon-login"></i> <?php echo $public_translated_messages['back_to_contest'] ?>
                </a>
            </div>
        </div>
    </div>
    <div class="fv-bs-row photo-single-item--image">
        <div class="col-md-1 photo-single-item--actions">
            <a class="photo-single-item--action btn-like fv_vote" data-button="side-menu"
                  onclick="sv_vote(<?php echo $contestant->id ?>); return false;">
                <i class="fvicon-heart photo-single-item--action-icon"></i>
                <?php echo $public_translated_messages['vote_button_text']; ?>
            </a>
            <a class="photo-single-item--action btn-fb-share" onclick="return sv_vote_send('fb', this ,<?php echo $contestant->id ?>);">
                <i class="fvicon-facebook photo-single-item--action-icon"></i>
            </a>
            <a class="photo-single-item--action btn-share-widget" onclick="FvModal.goShare(<?php echo $contestant->id; ?>);">
                <i class="fvicon-share photo-single-item--action-icon"></i>
            </a>

            <div class="photo-single-details">
                <?php if( ! $contest->isNeedHideVotes() ): ?>
                    <div class="photo-single-detail" title="<?php echo $public_translated_messages['vote_count_text']; ?>">
                        <i class="fvicon-heart"></i> <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->getVotes($contest); ?></span>
                    </div>
                <?php endif; ?>
                <?php if( fv_setting('soc-counter', false) ): ?>
                    <div class="photo-single-detail" title="<?php echo $public_translated_messages['shares_count_text']; ?>">
                        <i class="fvicon-share"></i> <span class="fv_svotes_<?php echo $contestant->id ?>">0</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="main-image col-md-11">
            <?php FV_Public_Single::render_main_image($image, $contestant, 'photo-single--main-image mainImage img-thumbnail'); ?>

            <?php do_action('fv/public/single_item/extra', $contestant); ?>

        </div>
    </div>
    <div class="fv-bs-row">
        <div class="col-md-8">

            <div class="photo-single-description">
                <h3 class="block-heading"><?php echo $public_translated_messages['single_descr_heading']; ?></h3>
                <p><?php echo fv_tpl_contestant_desc($contestant, 'single'); ?></p>
            </div>

            <?php //comment_form( array(), $contestant->image_id ); ?>
            <!--<h3 class="block-heading"><?php //_e('Comments', 'fv') ?></h3>-->
            <div class="clearfix">
                <div class="comments-block">
                    <?php
                    FV_Public_Single::get_instance()->render_comments($contestant);
                    ?>
                </div>
            </div>

        </div> <!--.col-md-8-->

        <div class="col-md-4">
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
                    </ul>
                </div>
            </div>


            <h3 class="block-heading"><?php echo $public_translated_messages['single_more_heading']; ?></h3>
            <div class="clearfix">
                <div class="more-photos"><?php
                    $thumb_size = array('width' => 80, 'height' => 80, 'crop' => true, 'size_name'=>'fv-single-related-thumb');

                    if ( !empty($most_voted) ): foreach( $most_voted as $MOST ): ?>
                        <a href="<?php echo $MOST->getSingleViewLink(); ?>" class="more-photos--a">
                            <img src="<?php echo $MOST->getThumbUrl($thumb_size); ?>" alt="<?php echo esc_attr($MOST->getHeadingForTpl()); ?>" class="more-photos--img">
                        </a>
                        <?php
                    endforeach; endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
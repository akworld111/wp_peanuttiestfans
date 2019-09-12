<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * @var FV_Contest $CONTEST      Contest object
 * $args => shortcode params:
 *          array(
 *           'theme' => 'default',
 *           'type' => 'active',     // active, upload_opened, finished
 *           'count' => '6',
 *           'on_row' => '4',
 *          )
 */

$contest_link = $CONTEST->getPublicUrl();
?>
<div class="FV_CL__one contest-list-display centry-col contest-list-skin-<?php echo esc_attr($skin); ?>" data-contenttype="image" data-id="<?php echo $CONTEST->id; ?>">

    <article class="centry" style="background-image: url( <?php echo $CONTEST->cover_image_url; ?> );">
        <a href="<?php echo $contest_link; ?>">
            <div class="centry-overlay"></div>
        </a>
        <?php if ( $CONTEST->isFinished() ): ?>
            <span class="contest-list-not-active"><i class="fvicon fvicon-trophy3"></i></span>
        <?php endif; ?>
        
        <header class="centry-header">
            <div class="FV_CL__one_details">
                <span><?php echo $CONTEST->cover_text_voting; ?></span>
                <br>
                <span><?php echo $CONTEST->cover_text_upload; ?></span>
            </div>
            
            <div class="FV_CL__one_stats">
                <span class="FV_CL__one_stats_photos"><i class="fvicon-camera2"></i> <?php echo $CONTEST->competitors_count; ?></span> &nbsp;
                <span class="FV_CL__one_stats_votes"><i class="fvicon fv-vote-icon"></i> <?php echo $CONTEST->votes_count_summary ? $CONTEST->votes_count_summary : 0; ?></span>
            </div>

            <h2 class="centry-title">
                <a href="<?php echo $contest_link; ?>" class="centry-link"><?php echo esc_attr($CONTEST->name); ?></a>
            </h2>
        </header><!-- .centry-header -->
    </article><!-- #post-## -->

</div><!-- .hetry-col -->
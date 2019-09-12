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

<div class="contest-list-display col-md-<?php echo round(12/$per_row_md); ?> col-sm-<?php echo round(12/$per_row_sm); ?> col-xs-<?php echo round(12/$per_row_xs); ?>">
    <figure>
        <a href="<?php echo $contest_link; ?>">
            <img alt="<?php echo esc_attr($CONTEST->name); ?>" class="display-image" style="display: block;" src="<?php echo esc_attr($CONTEST->cover_image_url); ?>">
        </a>
        <a href="<?php echo $contest_link; ?>" class="figcaption">
            <h3><?php echo $CONTEST->name; ?></h3>
            <span></span>
        </a>
        <?php if ( $CONTEST->isFinished() ): ?>
            <span class="contest-list-not-active"><i class="fvicon fvicon-trophy3"></i></span>
        <?php endif; ?>
    </figure>
    <!--figure-->
    <div class="box-detail">

        <ul class="list-inline contest-list-details clearfix">
            <li class="pull-left contest-list-details-text">
                <span><?php echo $CONTEST->cover_text_voting; ?></span>
                <br>
                <span><?php echo $CONTEST->cover_text_upload; ?></span>
            </li>
            <li class="pull-right contest-list-details-stats">
                <span class="photos-summary"><i class="fvicon-camera2"></i> <?php echo $CONTEST->competitors_count; ?></span> &nbsp;
                <span class="votes-summary"><i class="fvicon- fv-vote-icon"></i> <?php echo $CONTEST->votes_count_summary; ?></span>
            </li>
        </ul>
    </div>
    <!--.box-detail-->
</div>
<!--.contest-list-display-->

<?php if ( FvFunctions::curr_user_can() && false ): ?>
    <a title="<?php _e('Visible only for admins', 'fv') ?>" href="<?php echo $CONTEST->getAdminUrl(); ?>" target="_blank">
        <i class="fvicon-pencil"></i>
    </a>
<?php endif; ?>
<?php
/*
 * rendered from "wp-foto-vote\admin\class-fv-admin-pages.php"
  ## Variables: ##
    $show
    $contest_id
    $contest
    $contest_att_page
    $contest_redirect_after_upload_to_page
    $competitors_count
    $all_contests
    $all_forms
    $countdowns
 */
?>
<div class="wrap fv-page" id="contest_edit">
    <?php do_action('fv_admin_notices'); ?>
    <h2>
        <?php echo __('Manage contest', 'fv') . ' #' . $contest->id; ?>
        <a href="<?php echo admin_url( 'admin.php?page=fv&show=new-contest'); ?>" class="add-new-h2"><?php echo __('Add new', 'fv'); ?></a>
        <a href="<?php echo admin_url( 'admin.php?page=fv'); ?>" class="add-new-h2"><span class="fvicon-backward"></span> <?php echo __('Back to contests list', 'fv'); ?></a>
        <?php if ( $contest->page_id ) : ?>
            <a href="<?php the_permalink($contest->page_id); ?>" class="add-new-h2" target="_blank">
                <span class="fvicon-eye"></span>
                <?php _e('View contest page', 'fv'); ?>
            </a>
        <?php endif; ?>
    </h2>

    <div class="b-wrap Contest_nav">
        <ul class="nav-pills nav-justified">
            <li role="presentation" class="section-config <?php echo ('config' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl(); ?>"><i class="fvicon-pencil"></i> <?php _e('Configuration', 'fv'); ?></a>
            </li>
            <li role="presentation" class="section-competitors <?php echo ('description' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl('description'); ?>"><i class="fvicon-info"></i> <?php _e('Description & rules', 'fv'); ?></a>
            </li>
            <li role="presentation" class="section-competitors <?php echo ('categories' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl('categories'); ?>"><i class="fvicon-info"></i> <?php _e('Categories', 'fv'); ?></a>
            </li>
            <li role="presentation" class="section-competitors <?php echo ('competitors' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl('competitors', array('order'=>'desc','orderby'=>'id')); ?>"><i class="fvicon-pictures"></i> <?php _e('Competitors', 'fv'); ?> [<?php echo $contest->getCompetitorsCount(false), ' ', __('total', 'fv'); ?>]</a>
            </li>
            <li role="presentation" class="section-winners <?php echo ('winners' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl('winners'); ?>">
                    <?php echo $contest->isFinished() ? '<strong>' : ''; ?>
                        <i class="fvicon-trophy3"></i> <?php _e('Winners', 'fv'); ?>
                    <?php echo $contest->isFinished() ? '</strong>' : ''; ?>
                </a>
            </li>
            <li role="presentation" class="section-stats <?php echo ('stats' == $show) ? 'active' : ''; ?>">
                <a href="<?php echo $contest->getAdminUrl('stats'); ?>"><i class="fvicon-stats"></i> <?php _e('Stats', 'fv'); ?></a>
            </li>

        </ul>
    </div>
    <?php
    switch($show) {
        case 'config':
            include 'contest/_contest-settings.php';
            break;
        case 'competitors':
            include 'contest/_contestants.php';
            break;
        case 'description':
            include 'contest/Description.php';
            break;
        case 'categories':
            include 'contest/Categories.php';
            break;
        case 'winners':
            include 'contest/Winners.php';
            break;
        case 'stats':
            include 'contest/Stats.php';
            break;
    }
    ?>

</div>  <!-- .wrap :: END -->

<!-- modal popup -->
<div class="modal fade b-wrap fv_popup" id="fv_popup" tabindex="-1" role="dialog" aria-labelledby="fv_popup_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>


<!-- edit compeitor popup -->
<div id="fv_competitor_popup_wrap">
    <div class="modal fade b-wrap fv_popup" id="fv_competitor_popup" tabindex="-1" role="dialog" aria-labelledby="fv_popup_label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
</div>
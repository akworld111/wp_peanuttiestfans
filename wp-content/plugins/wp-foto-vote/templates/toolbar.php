<?php
/**
 * @var $contest FV_Contest
*/
?>
<div class="fv_toolbar--container">
    <ul class="fv_toolbar <?php echo ($search) ? "fv_toolbar--hide-dropdown" : ""; ?>" data-search="<?php echo ($search) ? "focused" : "unfocused"; ?>">
        <li class="fv_toolbar--left">
            <a href="<?php echo $search ? esc_url( remove_query_arg('fv-search') ) . '#contest' : '#0'; ?>" class="tabbed_a active" data-target=".fv_photos-container" data-contest-id="<?php echo $contest->id; ?>">
                <i class="fvicon-images"></i> <?php echo fv_get_transl_msg('toolbar_title_gallery'); ?>
            </a>
        </li>
        <?php if ($upload_enabled): ?>
            <li class="fv_toolbar--left">
                <a href="#0" class="tabbed_a" data-target=".fv_upload" data-contest-id="<?php echo $contest->id; ?>"><i class="fvicon-download2"></i>
                    <?php echo fv_get_transl_msg('toolbar_title_upload'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ( !fv_setting('toolbar-hide-details') ): ?>
            <li class="fv_toolbar--left">
                <a href="#0" class="tabbed_a" data-target=".fv-contest-description-wrap" data-contest-id="<?php echo $contest->id; ?>"><i class="fvicon-info"></i>
                    <?php echo fv_get_transl_msg('toolbar_title_details'); ?>
                </a>
            </li>
        <?php endif; ?>

        <?php do_action('fv/toolbar/after_tabs_hook', $contest); ?>

        <?php if ( !fv_setting('toolbar-search') ): ?>
            <li class="fv_toolbar__search fv_toolbar--right">
                <form method="get" class="fv_toolbar__search_form" action="#contest">
                <label class="fv_toolbar__search_label">
                    <input class="fv_toolbar__search_input" type="search" placeholder="" pattern=".{2,20}" name="fv-search" value="<?php echo ($search) ? $search : ""; ?>">
                    <span class="fv_toolbar__search_submit">
                        <i class="fvicon-arrow-right2"></i>
                    </span>
                </label>
                    <input type="hidden" name="fv-sorting" value="<?php echo ($fv_sorting) ? $fv_sorting : ""; ?>">
                </form>
            </li>
        <?php endif; ?>

        <?php do_action('fv/toolbar/middle_hook', $contest); ?>

        <?php if ( !fv_setting('toolbar-order') ): ?>
            <li class="fv_toolbar-dropdown fv_toolbar--right">
                <span>
                    <?php echo fv_get_transl_msg('toolbar_title_sorting'); ?>
                </span>
                <select class="fv_sorting">
                    <?php foreach( fv_get_sotring_types_arr() as $sort_type => $sort_name ) : ?>
                        <option value="<?php echo fv_set_query_arg('fv-sorting', $sort_type); ?>#contest" <?php selected($sort_type, $fv_sorting) ?> data-order="<?php echo $sort_type; ?>">
                            <?php echo fv_get_transl_msg('toolbar_title_sorting_' . $sort_type, $sort_name);  ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>
        <?php endif; ?>

        <?php if ( $contest->isCategoriesEnabled() ): ?>
            <li class="fv_toolbar-dropdown fv_toolbar--right">
                <span>
                    <?php echo fv_get_transl_msg('toolbar_title_category'); ?>
                </span>
                <select class="fv_category">
                    <option value="<?php echo remove_query_arg( array('fv-category', 'fv-page') ); ?>#contest">-</option>
                    <?php foreach ($contest->getCategories() as $category) : ?>
                        <option value="<?php echo add_query_arg( array('fv-category' => $category->slug, 'fv-page' => false) ); ?>#contest" <?php selected($category->slug, $category_filter); ?>>
                            <?php echo $category->name; ?>
                            <?php
                            if ( !defined("FV_PUBLIC_HIDE_CATEGORY_COUNTER") ) {
                                echo ' (', $category->count, ')';
                            }
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>
        <?php endif; ?>

    </ul>
</div>

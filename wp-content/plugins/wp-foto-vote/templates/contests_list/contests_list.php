<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * @var FV_Contest[] $contests_arr      Contest array
 * $contests->cover_image_url - CONTEST THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $args => shortcode params:
 *          array(
 *           'theme' => 'default',
 *           'type' => 'active',     // active, upload_opened, finished
 *           'count' => '6',
 *           'on_row' => '4',
 *          )
 */
?>

<div class="FV_CL contest-list fv-bs-grid fv-bs-row contest-list-skin-<?php echo esc_attr($args['skin']); ?>">
    <?php
    foreach($contests_arr as $CONTEST):
        $args["CONTEST"] = $CONTEST;

        FV_Templater::render(
            FV_Templater::locate($args['skin'], 'contest_item.php', 'contests_list'),
            $args,
            false,
            'contests_list_one'
        );
    endforeach;
    ?>
</div>


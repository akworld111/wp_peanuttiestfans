<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>
<div class="fv_leaders"><?php
    //$orders = fv_get_sotring_types_arr();
    $selected_order = array_rand( fv_get_sotring_types_arr() );
    $related_photos = ModelCompetitors::query()
        ->where_not('id', $contestant->id)
        ->where_all(array('contest_id'=> $contestant->contest_id, 'status'=> ST_PUBLISHED))
        ->limit( 3 )
        ->set_sort_by_type($selected_order)
        ->find(false, false, true, false, true);

    //fv_new_year_most_voted($contestant->contest_id);
    $thumbnails_size = array(
        'width'     =>fv_setting('lead-thumb-width', 280),
        'height'    =>fv_setting('lead-thumb-height', 200),
        'crop'      =>fv_setting('lead-thumb-crop', true),
        'thumb_name'=> 'fv-leaders-thumb',
    );

    foreach ($related_photos as $key => $photo): ?>
		<div class="fv_constest_item contest-block" style="width: 30%; position: relative;">
			 <div class="fv_photo">
				  <a href="<?php echo $photo->getSingleViewLink(); ?>" title="<?php echo esc_attr($photo->getHeadingForTpl()) ?>">
                    <img src="<?php echo $photo->getThumbUrl($thumbnails_size); ?>" class="attachment-thumbnail"/>
				  </a>
			 </div>

			 <div class="fv_name">
				  <div><?php echo $photo->getHeadingForTpl(); ?></div>
			 </div>

		</div>
    <?php endforeach; ?>
</div>
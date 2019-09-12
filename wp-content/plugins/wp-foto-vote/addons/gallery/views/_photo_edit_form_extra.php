<div class="form-group form-group-gallery">
	<h4>Gallery images</h4>

	<div class="fv-gallery-entries">
	<?php FOREACH($gallery_items as $gallery_item_id => $gallery_item): ?>
		<div class="col-md-8 fv-gallery-entries--row">
			<div class="">
				<?php if ($gallery_item['photo_url']): ?>
				<a href="<?php echo esc_url($gallery_item['photo_url']); ?>" target="_blank">
				<?php endif; ?>
					<img src="<?php echo esc_attr($gallery_item['photo_src']); ?>" title="<?php echo esc_attr($gallery_item['photo_title']); ?>" id="gallery-image<?php echo $gallery_item_id; ?>-thumb" class="img-responsive">
				<?php if ($gallery_item['photo_url']): ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="fv-gallery-entries--row-buttons">
				<button type="button" class="btn" onclick="fv_wp_media_upload('input#gallery-image<?php echo $gallery_item_id; ?>', 'input#gallery-image<?php echo $gallery_item_id; ?>-id', 'img#gallery-image<?php echo $gallery_item_id; ?>-thumb');">Select</button>
				&nbsp;
				<a class="gallery--remove" href="#0" data-call="Competitor.removeMetaRow"><span class="dashicons dashicons-trash"></span></a>
			</div>

			<input value="" type="text" class="hidden" id="gallery-image<?php echo $gallery_item_id; ?>" placeholder="image url">
			<input type="hidden" name="form[meta_val][<?php echo $gallery_item_id; ?>]" id="gallery-image<?php echo $gallery_item_id; ?>-id" value="<?php echo $gallery_item['photo_id']; ?>">
			<input name="form[meta_key][<?php echo $gallery_item_id; ?>]" class="" type="hidden" value="gallery"/>

			<input class="meta--type" name="form[meta_type][<?php echo $gallery_item_id; ?>]" type="hidden" value="exists"/>
			<input name="form[meta_core][<?php echo $gallery_item_id; ?>]" type="hidden" value="1"/>
		</div>
	<?php endforeach; ?>
	</div>

	<div class="clearfix"></div>
	<br/>
	<button type="button" class="btn btn-default pull-right fv-add-gallery-item">Add gallery item</button>

</div>

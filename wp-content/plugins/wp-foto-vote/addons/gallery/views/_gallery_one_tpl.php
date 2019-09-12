<script type="text/html" id="tmpl-fv-contestant-gallery-row">
	<div class="col-md-8 fv-gallery-entries--row">
		<div class="">
			<img src="<?php echo FV::$ASSETS_URL ?>img/no-photo-square.jpg" alt="" id="gallery-image{{ data }}-thumb" class="img-responsive">
		</div>
		<div class="fv-gallery-entries--row-buttons">
			<button type="button" class="btn" onclick="fv_wp_media_upload('input#gallery-image{{ data }}', 'input#gallery-image{{ data }}-id', 'img#gallery-image{{ data }}-thumb');">Select</button>
			&nbsp;
			<a class="meta--remove" href="#0" data-call="Competitor.removeMetaRow"><span class="dashicons dashicons-trash"></span></a>
		</div>

		<input type="hidden" id="gallery-image{{ data }}" value="src"/>
		<input name="form[meta_key][{{ data }}]" class="" type="hidden" value="gallery"/>
		<input name="form[meta_val][{{ data }}]" type="hidden" id="gallery-image{{ data }}-id" value="{{ data }}"/>

		<input class="meta--type" name="form[meta_type][{{ data }}]" type="hidden" value="new"/>
		<input name="form[meta_core][{{ data }}]" type="hidden" value="1"/>
	</div>
</script>

<style>
	.fv-gallery-entries--row {
		height: 200px;
	}
	.fv-gallery-entries--row-buttons {
		margin-top: 10px;
	}
</style>

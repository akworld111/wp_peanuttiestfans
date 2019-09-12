<div class="xoo-aff-field-selector">
	<span>Select one of the field below.</span>
	<ul class="xoo-aff-select-fields-type">

		<?php
			$fields_obj = xoo_aff_fields();
			$types  	= $fields_obj::$types;
		?>

		<?php foreach ( $types as $type_id => $type_data ): ?>
			<?php if( $type_data['is_selectable'] === "yes" ): ?>
				<li data-field="<?php echo $type_id; ?>"><?php echo $type_data['title']; ?></li>
			<?php endif; ?>
		<?php endforeach; ?>

	</ul>
</div>
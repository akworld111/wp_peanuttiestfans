<?php
/**
 * Events section part
 */
?>

<div id="events">

	<div class="tablenav top">
		<?php $this->get_view( 'parts/events/tablenav' ); ?>
	</div>

	<div class="events tile">

		<?php $this->get_view( 'parts/events/header-row' ); ?>

		<?php foreach ( $this->get_var( 'events' ) as $event ): ?>
			<?php $this->set_var( 'event', $event, true ); ?>
			<?php $this->get_view( 'parts/events/row' ); ?>
		<?php endforeach ?>

		<?php $this->remove_var( 'event' ); ?>

		<?php $this->get_view( 'parts/events/header-row' ); ?>

	</div>

	<div class="tablenav bottom">
		<?php $this->get_view( 'parts/events/tablenav' ); ?>
	</div>

	<?php $this->get_view( 'elements/add-event-button' ); ?>

</div>

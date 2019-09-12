<div class="fv-countdown fv-final-countdown" data-final="<?php echo $count_to; ?>" data-d="<?php echo $days_leave; ?>"
 data-h="<?php echo $hours_leave; ?>" data-m="<?php echo $minutes_leave; ?>" data-s="<?php echo $secs_leave; ?>">
    <div class="fv-countdown__title fv-countdown--title"><?php echo esc_html(fv_get_transl_msg($header_text_key)); ?></div>

    <div class="fc-time fc-days <?php echo ($days_leave > 99)? 'fv-time-wide' : ''; ?>">
        <span class="fc-count curr top"><?php echo $days_leave; ?></span>
        <span class="fc-count next top"><?php echo $days_leave - 1; ?></span>
        <span class="fc-count next bottom"><?php echo $days_leave - 1; ?></span>
        <span class="fc-count curr bottom"><?php echo $days_leave; ?></span>
        <span class="fc-label"><?php echo fv_get_transl_msg('timer_days', 'days'); ?></span>
    </div>

    <div class="fc-time fc-hours">
        <span class="fc-count curr top"><?php echo $hours_leave; ?></span>
        <span class="fc-count next top"><?php echo $hours_leave - 1; ?></span>
        <span class="fc-count next bottom"><?php echo $hours_leave - 1; ?></span>
        <span class="fc-count curr bottom"><?php echo $hours_leave; ?></span>
        <span class="fc-label"><?php echo fv_get_transl_msg('timer_hours', 'hours'); ?></span>
    </div>

    <div class="fc-time fc-minutes">
        <span class="fc-count curr top"><?php echo $minutes_leave; ?></span>
        <span class="fc-count next top"><?php echo $minutes_leave - 1; ?></span>
        <span class="fc-count next bottom"><?php echo $minutes_leave - 1; ?></span>
        <span class="fc-count curr bottom"><?php echo $minutes_leave; ?></span>
        <span class="fc-label"><?php echo fv_get_transl_msg('timer_minutes', 'minutes'); ?></span>
    </div>

    <div class="fc-time fc-seconds">
        <span class="fc-count curr top"><?php echo $secs_leave; ?></span>
        <span class="fc-count next top"><?php echo $secs_leave - 1; ?></span>
        <span class="fc-count next bottom"><?php echo $secs_leave - 1; ?></span>
        <span class="fc-count curr bottom"><?php echo $secs_leave; ?></span>
        <span class="fc-label"><?php echo fv_get_transl_msg('timer_secs', 'seconds'); ?></span>
    </div>
</div>

<div class="fv-countdown fv-countdown-default" data-image="<?php echo $this->addonUrl . 'images/sprites.png' ?>">
    <div class="fv-countdown__title fv-countdown--title fv-countdown-default--title"><?php echo esc_html(fv_get_transl_msg($header_text_key)); ?></div>

    <em class="clock"></em>
    <div class="c-block c-block-<?php echo ( $days_leave > 99 )? '3' : '2'; ?>"><div class="bl-inner"><span><?php echo $days_leave; ?></span></div>
        <span class="etitle etitle-1"> <?php echo fv_get_transl_msg('timer_days', 'days'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $hours_leave; ?></span></div>
        <span class="etitle etitle-2"> <?php echo fv_get_transl_msg('timer_hours', 'hours'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $minutes_leave; ?></span></div>
        <span class="etitle etitle-3"> <?php echo fv_get_transl_msg('timer_minutes', 'minutes'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $secs_leave; ?></span></div>
        <span class="etitle etitle-4"> <?php echo fv_get_transl_msg('timer_secs', 'seconds'); ?></span>
    </div>
</div>
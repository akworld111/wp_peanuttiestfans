<!-- This file is used to markup the administration form of the widget. -->
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'fv'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('contest_id'); ?>"><?php _e('Select contest:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('contest_id'); ?>" id="<?php echo $this->get_field_id('contest_id'); ?>" class="widefat">
        <?php foreach ($contest_list as $contest) : ?>
            <option value="<?php echo $contest->id ?>" <?php selected($contest_id, $contest->id); ?>><?php echo $contest->name ?></option>
        <?php endforeach; ?>        
    </select>
    <br />
    <small><?php _e('Select contest, from show contestants.', 'fv'); ?></small>
</p>

<p>
    <label for="<?php echo $this->get_field_id('shows_count'); ?>"><?php _e('Items count:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('shows_count'); ?>" id="<?php echo $this->get_field_id('shows_count'); ?>" class="widefat">
        <?php foreach ( array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16) as $count) : ?>
        <option value="<?php echo $count ?>" <?php selected($shows_count, $count); ?>><?php echo $count ?></option>
        <?php endforeach; ?>        
    </select>
    <small><?php _e('With Order "Winners" - does not work', 'fv'); ?></small>
</p>
<p>
    <label for="<?php echo $this->get_field_id('shows_sort'); ?>"><?php _e('Order:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('shows_sort'); ?>" id="<?php echo $this->get_field_id('shows_sort'); ?>" class="widefat">
        <?php foreach ( array('newest','oldest','popular','unpopular','winners') as $sort) : ?>
        <option value="<?php echo $sort ?>" <?php selected($shows_sort, $sort); ?>><?php echo __($sort, 'fv') ?></option>
        <?php endforeach; ?>        
    </select>
    <small><?php _e('With "Winners" - until contest finished will be displayed Most Popular items', 'fv'); ?></small>
</p>

<!-- show_photo_size -->
<p>
    <label for="<?php echo $this->get_field_id('show_photo_size'); ?>"><?php _e('Thumbnail width:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('show_photo_size'); ?>" id="<?php echo $this->get_field_id('show_photo_size'); ?>" class="widefat">
        <?php foreach ( array('1/1', '1/2', '1/3', '1/4') as $size) : ?>
        <option value="<?php echo $size ?>" <?php selected($show_photo_size, $size); ?>><?php echo $size ?></option>
        <?php endforeach; ?>        
    </select>	 
</p>

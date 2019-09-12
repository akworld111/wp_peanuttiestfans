<!-- This file is used to markup the administration form of the widget. -->
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'fv'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('shows_count'); ?>"><?php _e('Items count:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('shows_count'); ?>" id="<?php echo $this->get_field_id('shows_count'); ?>" class="widefat">
        <?php foreach ( array(2,3,4,5,6,7,8) as $count) : ?>
        <option value="<?php echo $count ?>" <?php selected($shows_count, $count); ?>><?php echo $count ?></option>
        <?php endforeach; ?>        
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id('shows_sort'); ?>"><?php _e('Order:', 'fv'); ?></label>
    <select name="<?php echo $this->get_field_name('shows_sort'); ?>" id="<?php echo $this->get_field_id('shows_sort'); ?>" class="widefat">
        <?php foreach ( array('newest','oldest','popular','unpopular') as $sort) : ?>
        <option value="<?php echo $sort ?>" <?php selected($shows_sort, $sort); ?>><?php echo __($sort, 'fv') ?></option>
        <?php endforeach; ?>        
    </select>
</p>

<!-- show_photo -->
<p>
    <label for="<?php echo $this->get_field_id('show_photo'); ?>"><?php _e('Show thumbnails?', 'fv'); ?></label>
    <input class="checkbox" type="checkbox" <?php checked($show_photo, true) ?> id="<?php echo $this->get_field_id('show_photo'); ?>" name="<?php echo $this->get_field_name('show_photo'); ?>" /><br />
    <small><?php _e('If unchecked, it will hide thumbnails.', 'fv'); ?></small>
</p>

<!-- show_photo_size -->
<p>
    <label for="<?php echo $this->get_field_id('show_photo_size'); ?>"><?php _e('Thumbnail size:', 'fv'); ?></label>
    <input id="<?php echo $this->get_field_id('show_photo_size'); ?>" name="<?php echo $this->get_field_name('show_photo_size'); ?>" type="text" value="<?php echo $show_photo_size; ?>" size="3" /> px.<br />
    <small><?php _e('more than 25 px.', 'fv'); ?></small>
</p>
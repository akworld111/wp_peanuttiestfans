<table class="form-table">

    <!-- ============ Leaders Vote ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Voting leaders', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('How many leaders show on page?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select count of voting leaders', 'fv') ); ?>
        <td>
            <select name="fotov-leaders-count">
                <?php for($N=1; $N<=10; $N++): ?>
                    <option value="<?php echo $N ?>" <?php selected( get_option('fotov-leaders-count', 3), $N ); ?>><?php echo $N ?> items</option>
                <?php endfor; ?>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Leaders style type?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how to display contest leaders?', 'fv') ); ?>
        <td>
            <select name="fotov-leaders-type">
                <?php foreach (FV_Leaders_Skins::i()->getList() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( get_option('fotov-leaders-type', 'block'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>


    <tr valign="top">
        <th scope="row"><?php _e('Leaders thumbnails size & leaders block item width (for "block" and "table" types)', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Thumbnails size in leaders block & leaders block item width', 'fv') ); ?>
        <td> width:<input type="number" name="fv[lead-thumb-width]" value="<?php echo fv_setting('lead-thumb-width', 280); ?>" min="0" max="1200"/> px. /
            height:<input type="number" name="fv[lead-thumb-height]" value="<?php echo fv_setting('lead-thumb-height', 350); ?>" min="0" max="1200"/> px.
            / hard crop: <input type="checkbox" name="fv[lead-thumb-crop]" <?php echo checked( fv_setting('lead-thumb-crop') ); ?>/>
            <small><?php _e('Hard crop means thumbnail will be equal entered dimension (if checked), or proportional (if unchecked) as defined by the larger dimension.', 'fv'); ?></small>
        </td>
    </tr>


    <tr valign="top">
        <th scope="row"><?php _e('Primary background color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Title background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[lead-primary-bg]" class="color" value="<?php echo fv_setting('lead-primary-bg', '#e6e6e6', false, 7); ?>"/>
            <span>Select color <button type="button" class="button" onclick="fv_reset_color(this, '#e6e6e6');">reset</button></span>
            &nbsp;<small>(For 'Block' this selects color for Title Background. For 'Table' this selects color for header background. This will not work in 'New Year' theme.)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Primary text color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Title text color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[lead-primary-color]" class="color" value="<?php echo fv_setting('lead-primary-color', '#ffffff', false, 7); ?>"/>
            <span>Select color <button type="button" class="button" onclick="fv_reset_color(this, '#ffffff');">reset</button></span>
            &nbsp;<small>(In "block" type used as title text color, in "table" type used as photo name color, in "New year" theme not work)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Round leaders thumbnails corners?', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Set up 1-5% for slightly round corners, or set up 50% for give circled image. [used "border-radius" css attribute]', 'fv') ); ?>
        <td> width:<input type="number" name="fv[lead-thumb-round]" value="<?php echo fv_setting('lead-thumb-round', 0); ?>" min="0" max="50"/> %
            &nbsp;<small>(<?php _e('2% - a few rounded, 50% - circled images', 'fv'); ?>)</small>
        </td>
    </tr>

</table>
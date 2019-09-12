<table class="form-table">

    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Toolbar', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Show toolbar (under contest)?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If want allow user change photos order and it looks good on your design, then enable it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[show-toolbar]" <?php checked( fv_setting('show-toolbar', false) ); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('<strong>Hide<strong/> some toolbar blocks:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('You have too few competitors - you can hide order/search blocks', 'fv') ); ?>
        <td class="socials">
            <span><?php _e('Description', 'fv') ?>:</span>
            <input type="checkbox" name="fv[toolbar-hide-details]" <?php checked( fv_setting('toolbar-hide-details') ); ?>/> Hide It<br />

            <span><?php _e('Order dropdown', 'fv') ?>:</span>
            <input type="checkbox" name="fv[toolbar-order]" <?php checked( fv_setting('toolbar-order', false) ); ?>/> Hide It<br />

            <span><?php _e('Search', 'fv') ?>:</span>
            <input type="checkbox" name="fv[toolbar-search]" <?php checked( fv_setting('toolbar-search', false) ); ?>/> Hide It<br />
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar background color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Toolbar background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-bg-color]" class="color" value="<?php echo fv_setting('toolbar-bg-color', '#232323', false, 7); ?>"/>
            Select color <button type="button" class="button" onclick="fv_reset_color(this, '#232323');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar text / links color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Text and link color placed on Toolbar', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-text-color]" class="color" value="<?php echo fv_setting('toolbar-text-color', '#FFFFFF', false, 7); ?>"/>
            Select color <button type="button" class="button" onclick="fv_reset_color(this, '#FFFFFF');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar active links background', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Link active background color placed on Toolbar', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-link-abg-color]" class="color" value="<?php echo fv_setting('toolbar-link-abg-color', '#454545', false, 7); ?>"/>
            Select color <button type="button" class="button" onclick="fv_reset_color(this, '#454545');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar select color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Input and select fields placed on Toolbar background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-select-color]" class="color" value="<?php echo fv_setting('toolbar-select-color', '#1f7f5c', false, 7); ?>"/>
            Select color <button type="button" class="button" onclick="fv_reset_color(this, '#1f7f5c');">reset</button>
        </td>
    </tr>

</table>
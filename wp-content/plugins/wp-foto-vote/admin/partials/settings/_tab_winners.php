<table class="form-table">

    <!-- ============ Design BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Winners', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Skin', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select how your winners block will looks.', 'fv') ); ?>
        <td>
            <select name="fv-winners-skin" class="form-control">
                <?php foreach (FV_Winners_Skins::i()->getList() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( get_option('fv-winners-skin', 'red'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Winners block width (min. 180 px.)', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Change to fit the width of the winners blocks to your site content', 'fv') ); ?>
        <td>
            <input type="number" name="fv-winners-block-width" value="<?php echo get_option('fv-winners-block-width', FV_CONTEST_BLOCK_WIDTH); ?>" min="0" max="1000"/> px.
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Winner block Heading template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( 'Heading template' ); ?>
        <td>
            <input name="fv[winner-head-tpl]" class="large-text" type="text" value="<?php echo fv_setting('winner-head-tpl', '{name}'); ?>"/> <br/>
            <small>You can use <code>{name}</code>, <code>{description}</code>, <code>{meta_field_key}</code></small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Winner block Description template:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( 'Description template' ); ?>
        <td>
            <textarea name="fv[winner-desc-tpl]" class="large-text" type="text"><?php echo fv_setting('winner-desc-tpl', '{description}'); ?></textarea> <br/>
            You can use <code>{name}</code>, <code>{description}</code>, <code>{full_description}</code>, <code>{meta_<strong>field_key</strong>}</code> (example: <code>{meta_<strong>website</strong>}</code>),
            <code>{categories_comma_separated}</code>, <code>{category_first}</code>.
        </td>
    </tr>


    <tr valign="top">
        <th scope="row"><?php _e('Winner block thumbnail size <small>(changes on the fly)</small>', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( 'Thumbnails size' ); ?>
        <td> width:<input type="number" name="fv-winners-thumb-width" value="<?php echo get_option('fv-winners-thumb-width', 300); ?>" min="0" max="1200"/> px. /
            height:<input type="number" name="fv-winners-thumb-height" value="<?php echo get_option('fv-winners-thumb-height', 220); ?>" min="0" max="1200"/> px.
            / hard crop: <input type="checkbox" name="fv-winners-thumb-crop" <?php echo checked( get_option('fv-winners-thumb-crop', true) ); ?>/>
            <small><?php _e('Hard crop means that the thumbnail size will be equal entered dimension (if checked) or proportional (if unchecked) using the larger dimension.', 'fv'); ?></small>
        </td>
    </tr>

</table>
<form class="form-inline photos_list_form">
    <div class="form-group col-md-6">
        <label><?php echo __('Name', 'fv') ?></label>
    </div><!-- .misc-pub-section -->

    <div class="form-group col-md-7">
        <label><?php echo __('Short Descr', 'fv') ?></label> <small>max 500 symb.</small>
    </div><!-- .misc-pub-section -->

    <div class="form-group col-md-2">
        <label><?php echo __('Votes', 'fv') ?></label>
    </div>

    <div class="form-group photo col-md-8">
        <label><?php echo __('Photo', 'fv') ?> </label>
    </div><!-- .misc-pub-section -->

    <div class="clearfix"></div>

    <div class="form-group col-md-6">
        <input type="text" name="form[name]" value="{{data.title}}"  class="form-control">
    </div><!-- .form-group -->

    <div class="form-group col-md-7">
        <input type="text" name="form[description]" value="{{data.description}}"  class="form-control">
    </div><!-- .form-group -->

    <div class="form-group col-md-2">
        <input type="text" name="form[votes]" value="" class="form-control"/>
    </div><!-- .form-group -->

    <div class="form-group photo col-md-7">
        <input type="text" name="form[image]" class="form-control" value="{{data.sizes.full.url}}">
        <input type="hidden" name="form[image_id]" value='{{data.id}}' />
    </div><!-- .form-group -->

    <div class="form-group col-md-2">
        <a href="{{data.sizes.full.url}}" target="_blank">
            <img src="{{data.sizes.thumbnail.url}}" height="27" style="margin-top: 2px; margin-left: 2px; display: inline-block;" />
        </a>
    </div><!-- .form-group -->

    <input type="hidden" name="form[social_description]" value='' />
    <input type="hidden" name="form[full_description]" value='' />
    <input type="hidden" name="form[status]" value='<?php echo ST_PUBLISHED; ?>' />
    <input type="hidden" name="form[id]" value="0" />
    <?php wp_nonce_field('save_contestant', 'fv_nonce'); ?>
</form>
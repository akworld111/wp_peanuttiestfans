<div class="meta-box-sortables">
    <div class="postbox ">
        <h3 class="hndle"><span>
            <?php echo __('Social sharing settings', 'fv') ?>
        </span></h3>
        <div class="inside">

            <span>
                <?php printf(
                    __( 'For change contest title, description and Picture that will be displayed in common Social networks - <a href="%s" target="_blank">use OG tags</a>.', 'fv'),
                    'https://kb.yoast.com/kb/getting-open-graph-for-your-articles/'
                ); ?>
            </span>
            <br/>
            <br/>

            <fieldset>

                <div class="row">
                    <div class="form-group col-sm-24">
                            <label><i class="fvicon fvicon-share"></i> <?php echo __('Title of contest contest for soc. networks ', 'fv') ?> (only for Vkontakte, Twitter and Pinterest)
                                <?php fv_get_tooltip_code(__('*name* will be replaced by name of the competitor.<br /> Work only for Vkontakte, Twitter and Pinterest, <br /> others socials take title from page title. <br/><br/>'
                                    . 'If not specified - \'Name\' field will be used', 'fv')) ?>
                            </label>
                            <input type="text" name="fv_social_title" class="form-control" value="<?php echo stripcslashes($contest->soc_title); ?>">
                    </div>
                    <div class="form-group col-sm-24">
                            <label><?php echo __('Description of contest for soc. networks', 'fv') ?> (only for Vkontakte and Twitter)
                                <?php fv_get_tooltip_code(__('*name* will be replaced by name of the contestant.<br /> Work only for Vkontakte and Twitter'
                                . '<br/> take the rest of the description of the description page. <br /> <br /> If not specified - soc. networks use at its discretion', 'fv')) ?>
                            </label>
                            <input type="text" name="fv_social_descr" class="form-control" value="<?php echo stripcslashes($contest->soc_description); ?>">
                    </div><!-- .misc-pub-section -->

                    <div class="form-group col-sm-24">
                            <label><?php echo __('Picture for social networks', 'fv') ?> (only for Vkontakte and Pinterest)
                                <?php fv_get_tooltip_code(__('Work only for Vkontakte and Pinterest.<br /><br /> If not specified - contestant image used', 'fv')) ?>
                            </label>
                            <input type="text" name="fv_social_photo" class="form-control" value="<?php echo stripcslashes($contest->soc_picture); ?>">
                    </div><!-- .misc-pub-section -->

                </div>

                <div class="clear"></div>

            </fieldset>

        </div>
    </div>
</div>
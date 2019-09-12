<?php
/** @var FV_Contest $contest */
?>
<div class="metabox-holder">
    <div class="postbox">
        <h3 class="hndle"><span><?php _e('Description & rules', 'fv') ?></span></h3>
        <div class="inside">
            <p><?php printf( __('It\'s recommended add contest details directly to Post/Page content for better SEO results 
                    (plugins like YoastSEO generate <a href="%s" target="_blank">"SEO description"</a>/<a href="%s" target="_blank">"OG meta tags"</a> based on page content, 
                    if you put all text here - page SEO description can be empty). Or you can can manually fill SEO description and OG tags.', 'fv'),
                    'https://yoast.com/meta-descriptions/', 'https://yoast.com/social-media-optimization-with-yoast-seo/' ) ?></p>
            <form method="POST">
                <?php wp_editor( $contest->getDescription(), 'meta_description', array('media_buttons'=>true, 'teeny'=>true, 'textarea_rows'=>8) ); ?>
                <br/>
                <button type="submit" name="save" id="save" class="button button-primary button-large" accesskey="s">Save</button>
                <input type="hidden" name="action" value="fv_save_contest_description">
                <?php wp_nonce_field('fv_save_contest_description'); ?>
            </form>
        </div>
        <div class="clearfix"></div>
    </div>
</div>



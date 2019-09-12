<?php
    // flat for Russian lang
    $isRus = ( get_bloginfo( 'language' ) == 'ru-RU' ) ? true : false;
    $supportEmail = ( $isRus ) ? 'ru@wp-vote.net' : 'support@wp-vote.net';
?>
 <div class="meta-box">
    <div class="postbox">
        <h3>
                <span><?php _e('Setup Tutorial', 'fv') ?></span>
        </h3>
        <div class="inside">
            <?php if ( $isRus ): ?>
                <a href="https://www.youtube.com/watch?v=xeuY6aaTeKY" target="_blank" class="docs_flex">
                    <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" width="100%" alt="" >
                    <span>Часть 1. Установка плагина</span>
                </a>

                <a href="https://www.youtube.com/watch?v=GL19stqal5U" target="_blank" class="docs_flex">
                    <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" width="100%" alt="" >
                    <span>Часть 2. Настройки плагина</span>
                </a>

                <br/>
                <a href="https://www.youtube.com/watch?v=nrDK6V9Dfew" target="_blank">
                    <span class="typcn typcn-social-youtube"></span> Создание пользовательской темы
                </a>
            <?php else: ?>
                <a href="https://www.youtube.com/watch?v=FtLgESz41HI" target="_blank" class="docs_flex">
                    <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" alt="" >
                    <span>How to install plugin and create contest</span>
                </a>
                <br/>
                <a href="http://wp-vote.net/instructions/" target="_blank"><span class="typcn typcn-link-outline"></span> Documentation</a>
                <br/><a href="https://wp-vote.net/doc/skins-and-templates/" target="_blank"><span class="typcn typcn-link-outline"></span> Templates / Skins customizing</a>
                <br/><a href="https://wp-vote.net/shortcode/" target="_blank"><span class="typcn typcn-link-outline"></span> Shortcodes</a>
            <?php endif; ?>
        </div>
    </div>

     <div class="postbox sidebar-addons">
         <?php
         $ad_sidebar_id = rand(1, 4);
         ?>
         <h3>
             <span><?php _e('Addons', 'fv') ?></span>
         </h3>
         <div class="inside">
             <a href="http://wp-vote.net/ad_sidebar-<?php echo $ad_sidebar_id; ?>" target="_blank">
                 <img src="<?php echo '//wp-vote.net/show/ad_sidebar-'  . $ad_sidebar_id . '.png'; ?>" alt="addons"/>
             </a>
         </div>
     </div>

     <div class="postbox">
         <?php
         $defaults = array('valid' => 0, 'expiration' => 'Key not entered!');
         $key = get_option('fv-update-key', '');
         $key_details = get_option('fv-update-key-details', $defaults);
         ?>

         <h3>
             <span><?php _e('Update status', 'fv') ?> <small>(curr. version <?php echo FV::VERSION ?>)</small></span>
         </h3>
         <div class="inside">
             <div class="gadash-title">
                 <a href="#" target="_blank">
                     <img width="32" src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/update.png') ?>" >
                 </a>
             </div>
             <div class="gadash-desc">
                 <strong><?php _e('Key status: ', 'fv') ?></strong>
                 <?php echo isset($key_details['status']) ? fv_get_update_key_status_as_text($key_details['status']) : 'not set'; ?>

                 <?php if ( isset($key_details['status']) && $key_details['status'] == 4 ):
                     ?>
                     <a href="http://wp-vote.net/extending-a-license/?license_key=<?php echo $key; ?>" target="_blank">Extend license >></a>
                     <?php
                 endif;
                 ?>
                 <br/><strong><?php _e('Key expiration: ', 'fv') ?></strong>
                 <?php echo isset($key_details['expiration']) ? $key_details['expiration'] : 'not set'; ?>
                 <br/><?php echo ($key)? __('<strong>Key</strong>: ', 'fv') . $key : __('Key not entered!', 'fv'); ?>
                  <a href="<?php echo admin_url("admin.php?page=fv-license"); ?>">edit</a>
             </div>
         </div>
     </div>

    <div class="postbox">
        <h3>
                <span><?php _e('Support &amp; Reviews', 'fv') ?></span>
        </h3>
        <div class="inside">
                <div class="gadash-title">
                    <a href="http://wp-vote.net/contact-us/" target="_blank">
                        <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/hire_me.png') ?>" width="32">
                    </a>
                </div>
                <div class="gadash-desc"><?php _e('Need customization or freelance developer? Write to', 'fv') ?> <br/> <strong><?php echo $supportEmail; ?></strong></div>
                <br/>
                <div class="gadash-title">
                    <a href="http://wp-vote.net/contact-us/" target="_blank">
                        <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/help.png') ?>" >
                    </a>
                </div>
                <div class="gadash-desc" style="padding-top: 7px;">
                    <strong>
                        <a href="<?php echo admin_url('admin.php?page=fv-help'); ?>" target="_blank"> <?php _e('Need support? Get it here>>', 'fv') ?></a>
                    </strong>
                </div>
                <br>
                <div class="gadash-title">
                        <a href="http://wp-vote.net/testimonials/">
                            <img src="<?php  echo plugins_url('wp-foto-vote/assets/img/admin/star.png') ?>">
                        </a>
                </div>
                <div class="gadash-desc"><?php _e('Your feedback and review are both important, <a href="http://wp-vote.net/testimonials/" target="_blank">write you testimonial</a>!', 'fv') ?>
                </div>
        </div>
    </div>
</div>
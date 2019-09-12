
<div class="cta-block">
    <div class="grid-container">
        <div class="cta-block_wrap grid-x grid-margin-x">
            <div class="cta-block__item cell large-6 medium-12 medium-offset-0 small-12">
                <div class="cta">
                    <div class="cta-title">ARE YOU THE<br>PEANUTTIEST FAN?</div>
                    <div class="cta-sub">SHARE YOUR PHOTO NOW!</div>
                    <?php 
                        if ( is_user_logged_in() ) {
                    ?>
                        <a href="/upload/" class="btn">Upload Now</a>
                    <?php 
                        } else {
                    ?>
                        <a class="btn xoo-el-reg-tgr">Upload Now</a>
                    <?php 
                        }
                    ?>
                </div>
            </div>
            <div class="cta-block__item cell large-6 medium-12 medium-offset-0 small-12">
                <div class="cta">
                    <div class="cta-title">SUPPORT YOUR<br>SC TEAM TODAY!</div>
                    <div class="cta-sub">CAST YOUR VOTE NOW!</div>
                    <a href="/vote-to-win/" class="btn">Vote Now</a>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center shell-yeah-img show-for-medium"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/shell-yeah.png" alt=""></div>
    <div class="text-center shell-yeah-img show-for-small-only"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/shell-yeah-mobile.png" alt=""></div>
</div>


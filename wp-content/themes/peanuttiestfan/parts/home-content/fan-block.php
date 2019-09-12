<div class="fan-block">
    <div class="grid-container">
        <h2 class="section-title">ARE YOU THE PEANUTTIEST FAN?</h2>
        <div class="fan-block-wrap grid-x grid-margin-x">
            <div class="fan-block__item cell large-6 medium-12 medium-offset-0 small-12">
                <div class="cascading-img">
                    <img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/fan.png" alt="ARE YOU THE PEANUTTIEST FAN?">
                    <div class="overlay-left"><img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/accent.png" alt=""></div>
                    <div class="overlay-right"><img class="img-wrap" src="<?php echo get_template_directory_uri(); ?>/assets/images/accent.png" alt=""></div>
                </div>
            </div>
            <div class="fan-block__item cell large-6 medium-12 medium-offset-0 small-12">
                <div class="fan-cta">
                    <div class="fan-cta-title">SHARE YOUR PHOTO NOW!</div>
                    <?php 
                        if ( is_user_logged_in() ) {
                    ?>
                        <a href="/upload/" class="btn">Upload</a>
                    <?php 
                        } else {
                    ?>
                        <a class="btn xoo-el-reg-tgr">Upload</a>
                    <?php 
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div><!--.fan-block-->

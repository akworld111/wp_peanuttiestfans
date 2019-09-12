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

<div class="winner-block">
    <div class="grid-container">
        <h2 class="section-title">LAST WEEKS WINNER</h2>
        <div class="winner-block-wrap grid-x grid-margin-x">
            <div class="winner-block__item cell large-8 large-offset-2 medium-10 medium-offset-1 small-10 small-offset-1">
                <div class="winner-slider">
                    <div class="orbit" role="region" aria-label="Contest Winners" data-orbit>
                        <div class="orbit-wrapper">
                            <ul class="orbit-container">
                                <li class="is-active orbit-slide">
                                    <figure class="orbit-figure">
                                    <img class="orbit-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/winner.png" alt="Space">
                                    </figure>
                                </li>
                                <li class="orbit-slide">
                                    <figure class="orbit-figure">
                                    <img class="orbit-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/winner.png" alt="Space">
                                    </figure>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="fan-block__item cell large-6 medium-6 medium-offset-0 small-10 small-offset-1">
                <div class="winner-meta">
                    <p>{FIRST LAST-INITIAL}<br>
                    FROM {CITY}<br>
                    {College}</p>
                </div>
            </div>
            <div class="fan-block__item cell large-6 medium-6 medium-offset-0 small-10 small-offset-1">
                <div class="fan-cta">
                    <div class="fan-cta-title">CAST YOUR VOTE TO WIN!</div>
                    <a href="#" class="btn">Vote Now</a>
                </div>
            </div>
        </div>
    </div>
</div><!--.winner-block-->


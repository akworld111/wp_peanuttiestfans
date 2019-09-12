<script>
    window.fbAsyncInit = function() {
        FB.init({appId: '<?php echo get_option('fotov-fb-apikey', ''); ?>',version:'v2.7',xfbml:true,status:true,cookie:true});
    };
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        <?php if ( ! defined("FV_LOAD_ALL_FB_SDK") ): ?>
        js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/<?php echo str_replace('-', '_', get_bloginfo('language')); ?>/sdk.js";fjs.parentNode.insertBefore(js, fjs);
        <?php else: ?>
        js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/<?php echo str_replace('-', '_', get_bloginfo('language')); ?>/all.js?ver=4.3.16";fjs.parentNode.insertBefore(js, fjs);
    <?php endif; ?>
    }(document, 'script', 'facebook-jssdk'));
</script>

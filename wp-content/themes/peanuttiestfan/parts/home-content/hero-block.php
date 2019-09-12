<?php

    $home_slogan = get_field('support_slogan');
    $home_hero_image = get_field('hero_image');

?>
<div class="hero-container">
    <div><img src="<?php if($home_slogan){ echo $home_slogan['url']; } ?>" alt="" class="hero-slogan"></div>
    <div><img src="<?php if($home_hero_image){ echo $home_hero_image['url']; } ?>" alt="" class="hero-img"></div>
</div>

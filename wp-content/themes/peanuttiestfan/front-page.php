<?php
/**
 * Home Page
 */

get_header(); ?>

    <div class="content">

        <div class="inner-content">

            <main class="main" role="main">
                <?php
                get_template_part('parts/home-content/hero', 'block'); // adds hero block
                get_template_part('parts/home-content/shoot-share-win', 'block'); // adds shoot share win block
                get_template_part('parts/home-content/prizes', 'block'); // adds prizes block
                get_template_part('parts/home-content/cta', 'block'); // adds cta block
                ?>

            </main> <!-- end #main -->

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->

<?php get_footer(); ?>
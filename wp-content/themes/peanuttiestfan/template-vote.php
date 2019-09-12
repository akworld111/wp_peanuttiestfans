<?php
/*
Template Name: Vote
*/

get_header(); ?>

<?php 
$bgimg = get_field( 'background_image' );
$query = ModelContest::query()
        ->where_early( 'date_start', current_time('timestamp', 0) )
        ->where_later( 'date_finish', current_time('timestamp', 0) );
$contests = $query->find();
?>


			
<div class="content">
	
    <div class="inner-content grid-x grid-margin-x grid-padding-x">

        <main class="main small-12 medium-12 large-12 cell" role="main">
            
            <div class="grid-container">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/WebPage">

                    <header class="article-header">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>
                                    
                    <section class="entry-content" itemprop="text">
                        <div class="vote-page">
                            <?php 
                            foreach($contests as $contest) {
                                if(($contest->id != 1) && ($contest->id != 2)) {
                                    echo '<div class="vote-contest-title">';
                                    echo $contest->name;
                                    echo '</div>';
                                    echo do_shortcode("[fv id={$contest->id}]");
                                }                                
                            }
                            ?>
                            <div class="previous-week-winners">
                                <h2 class="page-title">PAST PEANUTTIEST FANS WEEKLY WinnerS</h2>
                                <div class="contest-selector--wrap">
                                    <?php
                                        $oldcontests = ModelContest::query()
                                        ->where_early( 'date_finish', current_time('timestamp') )
                                        ->find();

                                        $ids = array();

                                        foreach($oldcontests as $contest) {
                                            $ids[] = $contest->id;
                                        }
                                        
                                        $highid = max($ids);

                                    ?>

                                    <?php
                                        if(sizeof($oldcontests) == 0) {
                                            echo 'No previous winner found';
                                        } else { ?>
                                    <div class="wrap">
                                        
                                        <form>
                                        
                                            <select name="contest" class="contest-selector" onchange="this.form.submit()">
                                              <?php  
                                                        foreach($oldcontests as $contest) {
                                                            if(($contest->id != 1) && ($contest->id != 2)) {
                                                                
                                                                if(!empty($_GET["contest"]) && ($contest->id == $_GET["contest"])) {
                                                                    echo '<option value="'.$contest->id.'" selected="selected">'.$contest->name.'</option>';
                                                                }   elseif(empty($_GET["contest"]) && ($contest->id == $highid)) {
                                                                    echo '<option value="'.$contest->id.'" selected="selected">'.$contest->name.'</option>';
                                                                }         else {
                                                                    echo '<option value="'.$contest->id.'">'.$contest->name.'</option>';
                                                                }
                                                                
                                                            }
                                                        } ?>
                                            </select>
                                           
                                        </form>
                                    </div>
                                    <?php    }   ?>
                                </div>

                                <div class="past-winners">
                                <?php
                                if(sizeof($oldcontests) == 0) {
                                   // echo 'No previous winner found';
                                } else { 
                                    if(isset($_GET["contest"])){
                                        $contest=$_GET["contest"];
                                        echo do_shortcode('[fv_winners contest_id='.$contest.' winners_skin="simple"]'); 
                                    } else {
                                        echo do_shortcode('[fv_winners contest_id='.$highid.' winners_skin="simple"]'); 
                                    }
                                }
                                ?>
                                </div>

                                
                            <div>
                        </div>
                    </section> <!-- end article section -->                                            
                                    
                </article> <!-- end article -->                                   
                    
                <?php endwhile; endif; ?>		
            </div>					

        </main> <!-- end #main -->
        
    </div> <!-- end #inner-content -->

</div> <!-- end #content -->

<?php get_footer(); ?>

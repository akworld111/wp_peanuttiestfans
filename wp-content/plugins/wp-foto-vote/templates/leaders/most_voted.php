<div class="fv_most_voted <?php echo get_option('fotov-leaders-type', 'text'); ?>">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo apply_filters('fv_text_leaders_title', $public_translated_messages['leaders_title'], $variables); ?></span>
        </span>
    <?php endif; ?>

    <?php if ( get_option('fotov-leaders-type', 'text') == 'theme' && in_array($theme, array('flickr','new_year')) ) :

            FvFunctions::render_template( FvFunctions::get_theme_path($theme, 'leaders.php'), $variables, false, "most_voted_theme"  );
		    //include plugin_dir_path(__FILE__) .$theme.'/leaders.php';

    elseif ( get_option('fotov-leaders-type', 'text') == 'text' ) : ?>
		<?php $i = 1; foreach ($most_voted as $key => $unit): ?>
			<a href="#photo-<?php echo $key ?>"><strong><?php echo $unit->name ?></strong></a>
				<span id="fv_most_votes_<?php echo $key ?>"> <?php echo $unit->votes_count ?></span>
				<?php if (($i != count($most_voted))) echo ', ' ?>	
		<?php $i++; endforeach;

	elseif ( get_option('fotov-leaders-type', 'text') == 'block' ) :
        if ( isset($leaders_width) ) {
            $block_leaders_width = (int)$leaders_width;
        } else {
            $block_leaders_width = '';
        }
    ?>
	    <div class="fv_most_voted_container">
            <?php $i = 1; foreach ($most_voted as $key => $unit):
                $thumb = FvFunctions::getPhotoThumbnailArr($unit, 'thumbnail');
                ?>
                <div class="fv_most_voted_item" style="width:<?php echo $block_leaders_width; ?>px;">
                    <a href="<?php echo fv_single_photo_link($photo->id) ?>">
                        <strong><?php echo $unit->name ?></strong> <?php echo $unit->votes_count ?>
                        <div class="fv_most_voted_image">
                            <img src="<?php echo $thumb[0] ?>" />
                        </div>
                    </a>
                </div>
            <?php $i++; endforeach; ?>
        </div>
    <?php endif; ?>


</div>

<div style="clear: both;"> </div>
<br/>

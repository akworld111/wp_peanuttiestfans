<?php
// VARS::'args','competitors','contest','show_photo_size','title', $block_size

/** @var FV_Competitor[] $competitors */
/** @var FV_Contest[] $contest */
/** @var integer $block_size */

$output = '';
if ($competitors) :
	if ($title) { $output .= $args['before_title'] . $title . $args['after_title']; }

    if ( in_array($show_photo_size, array('1/1', '1/2') ) ) {
        $thumb_size = array(
            'width' => 300,
            'height' => 300,
            'crop' => true,
			'size_name'=> 'gallery-widget-thumb',
        );
    } else {
        $thumb_size = array(
            'width' => 160,
            'height' => 160,
            'crop' => true,
			'size_name'=> 'gallery-widget-thumb',
        );
    }

	$thumb_size = apply_filters('fv/public/widget-gallery/thumb_size', $thumb_size, $show_photo_size, $contest);

	$output .= '<ul class="contestant-gallery">';
	foreach ($competitors as $contestant) :
		$output .=sprintf('<li class="contestant-thumb" style="width:%4$s;"><a href="%1$s" title="%3$s"><img src="%3$s" alt="%2$s" title="%2$s"/></a></li>',
			$contestant->getSingleViewLink(), __('View', 'fv'), $contestant->getThumbUrl($thumb_size), $block_size);
	endforeach;
	$output .= '</ul>';

endif;
echo $output;
?>
<style>
	ul.contestant-gallery,
	ul.contestant-gallery li.contestant-thumb {
		list-style: none;
		list-style-type: none;
	}
</style>

    
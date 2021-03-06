<!-- This file is used to markup the public-facing widget. -->
<?php
// VARS::'args','competitors','contest','show_photo_size','title','show_photo'

/** @var FV_Competitor[] $competitors */
/** @var FV_Contest $contest */

$output = '';
if ($competitors) :
    $thumb_size = array(
        'width' => $show_photo_size,
        'height' => $show_photo_size,
        'crop' => true,
        'size_name'=> 'list-widget-thumb',
    );

    $thumb_size = apply_filters('fv/public/widget-list/thumb_size', $thumb_size, $show_photo_size, $contest);

    if ($title)
        $output .= $args['before_title'] . $title . $args['after_title'];
    $output .= '<ul class="contestant-list">';
    foreach ($competitors as $contestant) :
        //$r->the_post();

        $photo_url = $contestant->getSingleViewLink();

        $post_content_class = 'contestant-content';
        if ($show_photo) {
            $img_src = $contestant->getThumbArr($thumb_size);
            $thumbnail = $img_src[0];
            $thumblink = sprintf('<div class="contestant-thumb"><a href="%1$s" title="%3$s"><img src="%3$s" alt="%2$s" title="%2$s" width="%4$d"/></a></div>', $photo_url, __('View', 'fv'), $thumbnail, $show_photo_size);
        } else {
            $thumblink = '';
            $post_content_class .= ' no-image';
        }
        if ( $contest->isFinished() && $shows_sort == 'winners' ) {
            $format = '<li>%1$s<div class="%2$s"><h4><a href="%3$s" title="%4$s">%4$s</a></h4><small>%5$s</small></div></li>';
            $output .= sprintf($format, $thumblink, $post_content_class, $photo_url, $contestant->getHeadingForTpl(), $contestant->getPlaceCaption());
        } else {
            $format = '<li>%1$s<div class="%2$s"><h4><a href="%3$s" title="%4$s">%4$s</a></h4><small>%5$s: %6$s</small></div></li>';
            $output .= sprintf($format, $thumblink, $post_content_class, $photo_url, $contestant->getHeadingForTpl(), __('Votes', 'fv'), $contestant->getVotes($contest));
        }
    endforeach;
    $output .= '</ul>';

endif;
echo $output;
?>
<style>
    ul.contestant-list,
	ul.contestant-list > li {
    list-style: none;
		list-style-type: none;
	}
</style>

<?php 
/**
 * Helper Functions
 * Some non-critical functions to assist with building sites.
 * @author jameelbokhari
 * @link https://gist.github.com/jbokhari
 * ^^orgin of some of these functions
****/

/**
 * Lazy way to print theme images dir
 */
function images_dir_uri($img = ""){
  echo get_images_dir_uri($img);
}

/**
 * Lazy way to get theme images dir
 */
function get_images_dir_uri($img = ""){
  return get_stylesheet_directory_uri() . "/assets/images/{$img}";
}

/**
 * custom_excerpt
 * truncate text based on word count, ignores punctuation (non-alpha)
 * TODO: may cut words like "don't" and "can't" and may need fix
 */
function custom_excerpt($content, $numwords = 20, $append = "&hellip;"){
	$content = strip_shortcodes($content);
	$content = strip_tags($content);
	$excerpt_length = $numwords;
	$words = explode(' ', $content, $excerpt_length + 1);
	$lngth = count($words);
	if( $lngth > $excerpt_length ) :
			array_pop($words);
			$words[$excerpt_length - 1] = preg_replace('/\W+$/', "", $words[($excerpt_length - 1)] );
			$content = implode(' ', $words) . $append;
	endif;
	return $content;
}

/**
 * Prints featured image src
 */
function the_featured_image_src($post_id = null, $size = "full") {
	echo get_featured_image_src($post_id, $size);
}

/**
 * Returns the featured image source of provided $post_id, or attempts to detect the current post
 */
function get_featured_image_src($post_id = null, $size = "full") {
	if ($post_id === null){
		global $post;
		$post_id = $post->ID;
	}
	$thumb_id = get_post_thumbnail_id( $post_id );
	$src = wp_get_attachment_image_src( $thumb_id, $size );
	return $src[0];
}

function get_image_alt($post_id = null){
    if ($post_id === null){
        global $post;
        $post_id = $post->ID;
    }
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    return $alt;
}

function anchorwave_link(){
	$rel = is_front_page() ? "" : " rel='nofollow'";
	echo "<a href='https://www.anchorwave.com/'{$rel}>Anchor Wave</a>";
}
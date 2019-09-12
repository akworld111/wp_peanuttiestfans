<?php
/**
 * Super Grid
 * @param $small (int)
 * @param $medium (int)
 * @param $large (int
 **/
function get_super_grid( $small = 12, $medium = 12, $large = 12, $group = "default" ){

	static $sizes = array("small", "medium", "large");
	static $groups = array();

	if (!is_null($group) && !isset($groups[$group]))
		$groups[$group] = array();

	$classes[] = "columns";
	$classes[] = "col-group-$group";

	foreach($sizes as $size){
		if (!isset($groups[$group][$size]))
			$groups[$group][$size] = 0;
		if ($$size && $$size > 0){
			// EG small-offset-$small, medium-offset-$medium
			$classes[] = "{$size}-{$$size}";
			if ( !empty($group) ){
				$groups[$group][$size] += $$size;
				if ( ( $groups[$group][$size] >= 12 ) && $$size != 12){
					$groups[$group][$size] = 0;
				}
			}
		}
	}

	return implode(" ", $classes);

}

/**
 * @uses get_super_grid()
 */
function super_grid( $small = 12, $medium = 12, $large = 12, $group = "default" ){
	echo get_super_grid($small, $medium, $large, $group);
}
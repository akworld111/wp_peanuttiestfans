<?php 
/**
 * AnchorTheme Iterators
 * Things that we do all the time, like this:
 * 
 * Desired output: leftrightleftright
 * $count = 0;
 * for($i = 0; $i < 4; $i++){
 *     echo $count++ % 2 ? "left" : "right";
 * }
 * 
 * Below is a collection of iterators made for AnchorTheme which solve the above problem and others
 * 
 * Simple, but why do that every time when we can have functions to handle it?
 * 
 * @example Outputs: leftrightleftright
 * for($i = 0; $i < 4; $i++){
 *     cycle_classes(array('left', 'right'), "uniquestring" );
 * }
 * 
 * @example Outputs: leftright
 * for($i = 0; $i < 4; $i++){
 * 	   // doesn't need unique id... left not used yet
 *     echo true_once('left'); 
 *     // doesn't need unique id... right not used yet
 *     echo true_once('right');
 *     // unique id needed here because true_once("left") would clash
 *     // offset is 2, meaning third time will echo
 *     echo true_once('left', "uniqueID-3", 2);
 *     // unique id needed here because true_once("right") would clash
 *     // offset is 3, meaning fourth time will echo
 *     echo true_once('right', "uniqueID-4", 3);
 * }
 * 
 * @example Outputs: leftrightleftright
 * for($i = 0; $i < 4; $i++){
 *     echo true_every(2, "uniquestring") ? "left" : "right";
 * }
 * 
 * 
 * most of these functions use a $uniqueInstance so that the same function can be used multiple times.
 * 
 **/

/**
 * true_every()
 * 
 * Simple function that is `true` every `$times` iterations, to be used inside loops. Use `$name` to set a unique instance. Use offset to offset the true instance.
 * @param int $times 
 * @param (optional) str $name 
 * @param (optional) int $offset 
 * @return bool
 */
function true_every($times, $uniqueInstance = "default", $offset = 0){
	static $count = array();
	if (!isset($count[$uniqueInstance])) $count[$uniqueInstance] = 0;
	$times = intval($times);
	$iteration = $count[$uniqueInstance] % $times;
	$compare = $times - $offset - 1;
	if ($compare  == $iteration)
		$return = true;
	else
		$return = false;
	$count[$uniqueInstance]++; 
	return $return;
}

/**
 * cycle_classes()
 * 
 * Echos strings inside first array argument in order each time function is called, then repeats after last element. For use inside loops
 * @param array $classes - An array of strings to iterate through and echo
 * @param string $uniqueInstance, default "default" - unique string for each instance (if function is used on the same page twice)
 * @example <div class="general-class <?php cycle_classes(array("first-block", "", "", "", "fifth-block"), "unique name for this instance"); ?>">
 **/
function cycle_classes( array $classes, $uniqueInstance = "default" ){
	echo get_cycle_classes($classes, $uniqueInstance);
}
/**
 * get_cycle_classes()
 * 
 * Same as cycle_classes() when you need to store the output in a variable.
 **/
function get_cycle_classes( array $classes, $uniqueInstance = "default" ){
	static $n = array();
	if (!isset($n[$uniqueInstance]))
		$n[$uniqueInstance] = 0;
	$count = count($classes);
	$i = $n[$uniqueInstance] % $count;
	$n[$uniqueInstance]++;
	return $classes[$i];
}

/**
 * true_once()
 * 
 * @param $uniqueInstance (string) used to group items. Provide a unique string each different time you use the function.
 * @param $offset (int)
 * To do a thing once. Returns true the first time only, or by offset
 * @example echo active class name for first item within a loop
 * markup: <div class='<?php echo true_once() ? "active" : "" ?>'>
 * @return (bool)
 * 
 */
function true_once( $uniqueInstance, $offset = 0 ){
	$offset = abs($offset);
	static $n = array();
	if (!isset($n[$uniqueInstance]))
		$n[$uniqueInstance] = 0;
	return ($n[$uniqueInstance]++ == $offset);
}

/**
 * Echo a thing once
 * If used multiple times, a uniqueInstance should be set to reset the iterator
 */
function echo_once( $thing, $uniqueInstance = "default", $offset = 0 ){
	$offset = abs($offset);
	static $n = array();
	$index = md5($thing . $uniqueInstance);
	if (!isset($n[$thing]))
		$n[$thing] = 0;
	return ($n[$thing]++ == $offset);
}

function count_up($uniqueInstance){
	static $n = array();
	if (!isset($n[$uniqueInstance]))
		$n[$uniqueInstance] = 0;
	return $n[$uniqueInstance]++;
}

<?php
/**
 * print_x()
 * Simple wrapper of print_r() for readability
 * 
 */
function print_x( $var, $return = false, $die = false ){
	$html = "<pre class='print-x print-pre'>";
	$html .= print_r($var, true);
	$html .= "</pre>";
	if ($return)
		return $html;
	else
		echo $html;
	if ($die)
		die();
}

/**
 * print_pre()
 * Alias of print_x()
 */
function print_pre( $var, $return = false, $die = false ){
	return print_x($var, $return, $die);
}

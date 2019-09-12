<?php
/**
 * avatars()
 * 
 * Generates fake avatars
 * 
 * @param (int) $num, size of array to generate
 * @return (array) A genenerated array of arrays, each with an 'image', 'first_name', 'last_name', 'description'.
 */
function avatars($num = 3){
	$num = abs($num);
	$count = 0;
	$avatars = array();
	static $firstnames = array(
		"Clint", "James", "Quintin", "Aldous", "Patty", "Deborah", "Aleckzander", "Rickity", "Julius", "Maximo", "Jules", "Jessica", "Django", "Sylvester", "Eli", "Ser", "Column", "Jazelle", "Logan", "Arya", "Ned", "Indiana", "Mr", "Quieous", "Mrs"
	);
	static $lastnames = array(
		"Earl", "Lee", "Euphrates", "Shalum", "Roberts", "Rodrick", "Infinity", "Overwood", "Westwood", "Allen", "Llamar", "Biel", "Stark", "Watts", "Degrasse Tyson", "Underton", "Bazzle", "Powers", "Turok", "Drogo", "Bean", "Null", "Falseman", "Bayaz"
	);
	while ( count($avatars) < $num ) {
		$randfirst = $firstnames[ mt_rand( 0, count($firstnames) -1 ) ];
		$randlast = $lastnames[ mt_rand( 0, count($lastnames) -1 ) ];
		$avatars[] = array( 
			"image" => cache_external_image("http://api.adorable.io/avatars/285/" . count($avatars) ),
			"first_name" => $randfirst,
			"last_name" => $randlast,
			"description" => get_lorem(20, 35)
		);
	}
	return $avatars;
}
/**
 * Generate lorem ipsum text, see function lorem() below
 * @uses get_lorem()
 **/
function lorem( $length = 40, $randmax = null ){
	echo get_lorem($length, $randmax);
}
/**
 * lorem()
 * Quickly create lorem text
 * Accepts two arguments. Defaults to 40 characters of text. First argument changes length. Adding second argument will randomly generate text with length in the range of two arguments. The words are completely random, sentences are capitalized and always include a period.
 * @todo sentences are always same length, only includes basic punctuation.
 * @example get_lorem() will echo 40 random words.
 * @example get_lorem(10) will echo 10 words.
 * @example get_lorem(4,8) will echo text between 4 and 8 words long.
 * @param $length (int) if only length is provided, the length of the string
 * @param $randmax (int) if provided, the length of the string will be random. The length being the two given values.
 * @return (string) randomly generated text
 */
function get_lorem( $length = 40, $randmax = null ){
	if ( !is_null( $randmax ) ){
		$vals = array( intval($length), intval($randmax) );
		$length = mt_rand( min( $vals ), max($vals) );
	} else {
		$length = intval( $length );
	}
	static $words = array( 'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipisicing', 'elit', 'explicabo', 'auror', 'odio', 'vel', 'en', 'commodi', 'nam', 'rem', 'beatae', 'eos', 'iure', 'esse', 'neque', 'modi', 'accusantium', 'repudiandae', 'vitae', 'harum', 'eius', 'facere', 'laboriosam', 'ratione', 'qui', 'voluptate', 'non', 'aliquid', 'dignissimos', 'excepturi', 'quos', 'temporibus', 'minus', 'provident', 'ea', 'reiciendis', 'quibusdam', 'nemo', 'molestiae', 'deleniti', 'tempore', 'porro', 'expedita', 'aperiam', 'sequi', 'incidunt', 'nesciunt', 'quae', 'similique', 'quo', 'nobis', 'ut', 'dolores', 'puupu', 'aperiam', 'iste', 'labore', 'magnam', 'saepe', 'ipsam', 'nulla', 'consequuntur', 'asperiores', 'doloribus', 'alias', 'dolorem', 'minima', 'eum', 'officia', 'debitis', 'illum', 'corporis', 'obcaecati', 'fugiat', 'reprehenderit', 'ad', 'id', 'officiis' );
	$lasti = count( $words ) - 1;
	$currentlength = 0;
	$phrase = array();
	//randomly arrange $length # of words
	while( $currentlength++ < $length ){
		$rand = mt_rand( 0, $lasti );
		$phrase[] = $words[ $rand ];
	}
	//sentence length between 5 and 15 chars
	$paragraph = "";
	foreach( array_chunk($phrase, mt_rand( 5, 15 ) ) as $chunk ){
		$sentence = implode( $chunk, " " );
		$sentence = ucfirst( $sentence ) . ".";
		$paragraph = $sentence . " " . $paragraph;
	}
	return $paragraph;

}

/**
 * Quickly create a wireframe-like dummy image.
 * @param $width, width of image
 * @param $height, height of image
 * @param $classes, classes to add to img tag
 * @return a placeholder image html
 */
function get_pimage($width = 150, $height = 150, $classes = ""){
	$src = "http://placehold.it/{$width}x{$height}";
	$src = cache_external_image($src);
	return "<img class='pimage {$classes}' width='{$width}' height='{$height}' src='{$src}'>";
}
/**
 * Same as get_pimage(), but prints the html rather than returning it
 */
function pimage($width = 150, $height = 150, $classes = ""){
	echo get_pimage($width, $height, $classes);
}

/**
 * Quickly embedd a dummy youtube video.
 * @param $width
 * @param $height
 * @param $classes
 * @return void
 */
function pvideo($width = 640, $height = 360, $classes = ""){
	// Filler video chosen semi-randomly
	echo "<iframe width='$width' height='$height' class='$classes' src='https://www.youtube.com/embed/ScMzIvxBSi4' frameborder='0' allowfullscreen></iframe>";
}

/**
 * Cache an external image
 * @return file name of cached file, if the file can be cached
 */
function cache_external_image( $url ){
    // this function is disabled. Needs to be assessed before developing further.
	return $url;

	// if ( ! ini_get('allow_url_fopen') ){
	// 	return $url;
	// }
	// $id = md5( $url );
	// $filename = "/external-image-cache/" . $id . ".jpg";
	// $filepath = ANCHORTHEME_DIR . '/assets/functions/dev' . $filename;
	// if ( !file_exists( $filepath ) ){
	// 	$r = fopen( $filepath, 'w' );
	// 	$content = file_get_contents( $url );
	// 	fwrite( $r, $content );
	// }
	// return get_stylesheet_directory_uri() . '/assets/functions/dev' . $filename;
	
}
<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


/**
 * Remove unwanted characters from custom classes
 *
 * @param string $input Classes string to process
 * @return string Sanitized html classes
 */
function kc_essentials_sanitize_html_classes( $input ) {
	if ( !is_array($input) ) {
		if ( strpos($input, ' ') )
			$input = explode( ' ', $input );
		else
			$input = array( $input );
	}

	$output = array();
	foreach ( $input as $c )
		$output[] = sanitize_html_class( $c );

	return join( ' ', $output );
}


function kc_essentials_sanitize_numbers( $value, $glue = ',' ) {
	$_sizes = explode( $glue, $value );
	foreach ( $_sizes as $idx => $_s ) {
		$_w = absint( $_s );
		if ( !$_w )
			unset( $_sizes[$idx] );
		else
			$_sizes[$idx] = $_w;
	}

	return implode( $glue, $_sizes );
}


?>

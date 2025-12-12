<?php

/**
 * Validate the user input
 *
 * @author Marcel Bootsman
 *
 * @param array $input Existing input fields
 *
 * @return array Sanitized input fields
 */
function ahab_validate_input( array $input ): array {
	// Create our array for storing the validated options
	$output = array();
	// Loop through each of the incoming options
	foreach ( $input as $key => $value ) {
		// Check to see if the current option has a value. If so, process it.
		if ( isset( $input[ $key ] ) ) {
			// Strip all HTML and PHP tags and properly handle quoted strings
			$output[ $key ] = \strip_tags( \stripslashes( $input[ $key ] ) );
		} // end if
	} // end foreach
	// Return the array processing any additional functions filtered by this action
	return \apply_filters( 'ahab_validate_input', $output, $input );
}
?>

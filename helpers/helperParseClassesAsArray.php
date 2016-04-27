<?php

function helperParseClassesAsArray( $classes ) {

	if ( is_string( $classes ) ) {

		// Create an array
		$data_classes_arr = explode( ',', $classes );

		// Trim the input for any whitespace
		$data_classes_arr = array_map( 'trim', $data_classes_arr );

	}

	if ( is_array( $classes ) ) {
		$data_classes_arr = $classes;
	}

	if ( ! empty( $data_classes_arr ) ) {
		return $data_classes_arr;
	} else {
		return false;
	}
}
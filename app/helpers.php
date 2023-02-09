<?php

declare( strict_types = 1 );


//	Debug
if ( ! function_exists('vr_debug')) {
	function vr_debug ( $object , $name = '' ) {		
        if ( $name != '' ) {
            echo('\'' . $name . '\' : ');
        }

		if ( is_array ( $object ) || is_object ( $object ) ) {
			echo('<pre>');
			print_r( $object ); 
			echo('</pre>');
		} else {
			var_dump ( $object );
		}	
	}
}
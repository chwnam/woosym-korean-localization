<?php

if ( ! function_exists( 'wskl_sym__alert' ) ) :

	/* Debug & Log Helper   Last Update 2015.3.6 */
	function wskl_sym__alert( $str ) {

		echo "<script>alert('" . $str . "');</script>";
	}

endif;

if ( ! function_exists( 'wskl_sym__log' ) ) :

	function wskl_sym__log( $message, $show_page = FALSE ) {

	$log_dir  = "wp-content";
	$time_msg = "[" . date_format( date_create(), 'Y-m-d-H-i-s' ) . "] ";

	$log_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $log_dir . "/";
	$log_file .= "sym__log.log";

	if ( is_array( $message ) || is_object( $message ) ) {
		$str_msg = print_r( $message, true );
	} else {
		$str_msg = $message;
	}

	//file_put_contents($log_file, $message, FILE_APPEND | LOCK_EX);
	error_log( $time_msg . $str_msg . "\r\n", 3, $log_file );
		if ( $show_page ) {
		print_r( $str_msg . "<br/>" );
	};

}

endif;

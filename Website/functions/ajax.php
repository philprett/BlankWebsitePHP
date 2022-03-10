<?php

/**
 * Return an error from an Ajax request
 */
function AjaxError($statusMessage, $statusCode = 400) {
	header ( "HTTP/1.0 ".$statusCode." Fehler" );
	echo $statusMessage;
	exit();	
}

/**
 * Return a success from an Ajax request
 */
function AjaxOk($statusMessage = "OK", $statusCode = 200) {
	header ( "HTTP/1.0 ".$statusCode." OK" );
	echo $statusMessage;
	exit();	
}
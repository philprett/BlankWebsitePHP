<?php

$MYSQLNEWGUID = 
	"LOWER(".
		"CONCAT(".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"'-', ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"'-', ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"'-', ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"'-', ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0'), ".
			"LPAD(HEX(FLOOR(RAND() * 0xff)), 2, '0')".
		")".
	")";

function CreateGuid() {

	$requestTime = isset($_SERVER["REQUEST_TIME"])   ? $_SERVER["REQUEST_TIME"]    : RandomString(20, true, true, true, true);
	$userAgent  = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : RandomString(20, true, true, true, true);
	$remoteAddr = isset($_SERVER["REMOTE_ADDR"])     ? $_SERVER["REMOTE_ADDR"]     : RandomString(20, true, true, true, true);
	$remotePort = isset($_SERVER["REMOTE_PORT"])     ? $_SERVER["REMOTE_PORT"]     : RandomString(20, true, true, true, true);

	$data = 
		uniqid("", true).
		"".
		md5(
			$requestTime.
			$userAgent.
			$remoteAddr.
			$remotePort);
		
	$hash = strtoupper(hash("ripemd128", $data));

	$guid =
		substr($hash, 0, 8) . "-".
		substr($hash, 8, 4) . "-".
		substr($hash, 12, 4) . "-".
		substr($hash, 16, 4) . "-".
		substr($hash, 20, 12);
				
	return $guid;

}

<?php

/**
 * Produce a randomly generated string
 * @param int $length
 * @param bool $lowercase
 * @param bool $uppercase
 * @param bool $numbers
 * @param bool $special
 * @return string
 */
function RandomString($length, $lowercase = true, $uppercase = false, $numbers = false, $special = false) {
	$chars = "";
	if ($lowercase) $chars .= "abcdefghijklmnopqrstuvwxyz";
	if ($uppercase) $chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($numbers)   $chars .= "0123456789";
	if ($special)   $chars .= "!$&/#+-_";

	$s = "";
	for ($a = 0; $a < $length; $a++) {
		$s .= substr($chars, rand(0, strlen($chars)), 1);
	}
	return $s;
}

function EncryptPassword($password) {
	return password_hash($password, PASSWORD_BCRYPT);
}

function VerifyPassword($password, $passwordHash) {
    return password_verify($password, $passwordHash);
}

function GeneratePassword() {
	return RandomString(6, true, false, true, false);
}
<?php

function IncludeDir($path) {
	$classesDir = opendir($path);
	if ($classesDir) {
		$files = array();
		while ($file = readdir($classesDir)) {
			if ($file != "." && $file != "..") {
				$files[] = $path."/".$file;
			}
		}
		closedir($classesDir);

		sort($files);
		foreach ($files as $file) {
			if (is_dir($file)) {
				IncludeDir($file);
			} else {
				include $file;
			}
		}
	}
}

if (!file_exists("config.php")) {
    echo "You must create a config.php file.<br><br>There is an example called config.sample.php.";
	exit();
}

include "config.php";

IncludeDir("functions");
IncludeDir("classes");
IncludeDir("pages");

$LANG = new Language("de");

$DB = new DB(
	$CONFIG["dbhost"],
	$CONFIG["dbusername"],
	$CONFIG["dbpassword"],
	$CONFIG["dbdatabase"]);

IncludeDir("dataclasses");

$cookieValue = isset($_COOKIE[$CONFIG["cookiename"]]) ? $_COOKIE[$CONFIG["cookiename"]] : RandomString(32);
$USER = User::GetByCookie($cookieValue);

Routing::Route();
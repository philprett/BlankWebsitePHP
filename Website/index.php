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
	die("Please create the config.php file.");
}
include "config.php";

IncludeDir("functions");
IncludeDir("classes");
IncludeDir("pages");

$LANG = new Language("en");

$DB = new DB(
	$CONFIG["dbhost"],
	$CONFIG["dbusername"],
	$CONFIG["dbpassword"],
	$CONFIG["dbdatabase"]);

if ($DB->TableExists("users") == false) {
	echo "Users table not found.<br>";
	echo "Please use the <a href=/dbcreate.php>Database Creator</a>.";
	exit();
}

IncludeDir("dataclasses");

$cookieValue = isset($_COOKIE[$CONFIG["cookiename"]]) ? $_COOKIE[$CONFIG["cookiename"]] : RandomString(32);
$USER = User_GetByCookie($cookieValue);

Routing::Route();
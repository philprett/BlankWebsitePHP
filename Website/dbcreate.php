<?php

if (!file_exists("config.php")) {
	die("Please create the config.php file.");
}
include "config.php";

include "classes/db.php";

$DB = new DB(
	$CONFIG["dbhost"],
	$CONFIG["dbusername"],
	$CONFIG["dbpassword"],
	$CONFIG["dbdatabase"]);

echo "<h1>Create Database Tables</h1>";

if (!$DB->TableExists("users")) {
	$result = $DB->Exec(
		"CREATE TABLE users ".
		"(".
		"user_id        VARCHAR(40) NOT NULL, ".
		"user_email     VARCHAR(300) NOT NULL, ".
		"user_firstname VARCHAR(300) NOT NULL, ".
		"user_surname   VARCHAR(300) NOT NULL, ".
		"user_password1 VARCHAR(300) NOT NULL, ".
		"user_password2 VARCHAR(300)     NULL, ".
		"user_cookie    VARCHAR(300) NOT NULL, ".
		"PRIMARY KEY (user_id) ".
		")",
		array(),
		false
	);
	if (!$result) {
		echo "Error creating table users<br>";
		echo $DB->$DB->LastError;
	} else {
		echo "Created table users<br>";
	}
} else {
	echo "Table users already exists<br>";
}

<?php

$CONFIG = array();

$CONFIG["dbhost"]     = "";     // The database server
$CONFIG["dbusername"] = "";     // The database username
$CONFIG["dbpassword"] = "";     // The password for the database user
$CONFIG["dbdatabase"] = "";     // The database on the server

$CONFIG["cookiename"]    = "";                              // The name of the cookie
$CONFIG["cookieexpires"] = time() + (60 * 60 * 24 * 365);   // The expiration time of the cookie. Can be 0 for a session cookie.

$CONFIG["allowuserselfcreation"] = true;

$CONFIG["smtpserver"]   = "";   // The SMTP server to use to send emails
$CONFIG["smtpport"]     = "";   // The port of the SMTP server
$CONFIG["smtpusername"] = "";   // The username to connect to the SMTP server
$CONFIG["smtppassword"] = "";   // The password for the SMTP server user
$CONFIG["smtpfrom"]     = "";   // The default from address of sent mails

?>
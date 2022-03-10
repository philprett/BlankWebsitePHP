<?php

class Language {

	private $currentLanguage;
	private $text =
		array(
			"applicationname" 			=>	array(	"en" => "Blank Website",
													"de" => "Leere Website"),
			"homepage" 					=>	array(	"en" => "Home",
													"de" => "Start"),
			"save" 						=> 	array(	"en" => "Save",
													"de" => "Speichern"),
			"logout" 					=> 	array(	"en" => "Logout",
													"de" => "Abmelden"),
			"email" 					=> 	array(	"en" => "Email",
													"de" => "E-Mail"),
			"password" 					=> 	array(	"en" => "Password",
													"de" => "Passwort"),
			"firstname" 				=> 	array(	"en" => "First name",
													"de" => "Vorname"),
			"surname" 					=> 	array(	"en" => "Surname",
													"de" => "Nachname"),
			"forgotpassword" 			=> 	array(	"en" => "Forgotten Password",
													"de" => "Passwort vergessen"),
			"newpasswordsent"			=>	array(	"en" => "We have sent a new password to your email address.",
													"de" => "Wir haben Ihnen ein neues Passwort an Ihre E-Mail-Adresse geschickt."),
			"newpasswordmailsubject"	=>	array(	"en" => "New Password for {applicationname} Website",
													"de" => "Neues Passwort für {applicationname} Webseite"),
			"newpasswordemailcontent"	=>	array(	"en" => "Hi {0},<br>".
															"<br>".
															"here is your new password for your account on the {applicationname} website:<br>".
															"<br>".
															"{1}<br>".
															"<br>".
															"If you did not request this mail, please just ignore it.<br>".
															"<br>".
															"Regards<br>".
															"The {applicationname} team.",
													"de" => "Hallo {0},<br>".
															"<br>".
															"hier ist dein neues Passwort für dein Konto auf der {applicationname} Webseite:<br>".
															"<br>".
															"{1}<br>".
															"<br>".
															"Wenn Du diese Mail nicht angefordert hast, bitte einfach ignorieren.<br>".
															"<br>".
															"Mit freundlichen Grüßen<br>".
															"Das {applicationname} team."),
			"createaccount" 			=> 	array(	"en" => "Create Account",
													"de" => "Konto anlegen"),
			"createdbtables"			=> 	array(	"en" => "Create database tables",
													"de" => "Datenbank Tabellen erstellen"),
			"dbtableexists"				=> 	array(	"en" => "Database table {0} exists",
													"de" => "Datenbank Tabelle {0} existiert bereits"),
			"createddbtable"			=> 	array(	"en" => "Create database table {0}",
													"de" => "Datenbank Tabelle {0} erstellt"),
			"errorcreatingdbtable"		=> 	array(	"en" => "Error creating database table {0}<br>{1}",
													"de" => "Fehler bei der Erstellung von Datenbank Tabelle {0}<br>{1}"),

			"login" 					=> 	array(	"en" => "Login",
													"de" => "Anmelden"),
			"logindetailsincorrect"		=> 	array(	"en" => "Your login details were incorrect. Please go back and try again.",
													"de" => "Ihre Anmeldedaten waren falsch. Bitte gehen Sie zurück und versuchen Sie es erneut."),

			"profile" 					=> 	array(	"en" => "Profile",
													"de" => "Profil"),

			"startpagecontentanon"		=> 	array(	"en" => "Welcome to the {applicationname} Website.",
													"de" => "Willkommen auf der {applicationname} Website."),

			"startpagecontentuser"		=> 	array(	"en" => "Welcome to the {applicationname} Website {0}.",
													"de" => "Willkommen auf der {applicationname} Website {0}."),


			"pagenotfound" 				=> 	array(	"en" => "Page not found",
													"de" => "Seite nicht gefunden"),

			"settings" 					=> 	array(	"en" => "Settings",
													"de" => "Einstellungen"),
			"useradmin" 				=> 	array(	"en" => "User Administration",
													"de" => "Benutzerverwaltung"),
			"isadmin" 					=> 	array(	"en" => "Is Admin?",
													"de" => "Ist Admin?"),
			"actions" 					=> 	array(	"en" => "Actions",
													"de" => "Aktionen"),
			"addnewuser" 				=> 	array(	"en" => "Add New User",
													"de" => "Neue Benutzer hinzufügen"),

			"template" 					=> 	array(	"en" => "Login",
													"de" => "Anmelden"),
		);

	public function __construct($language) {
		$this->currentLanguage = $language;
	}

	public function Get($key, $value0 = false, $value1 = false, $value2 = false, $value3 = false, $value4 = false, $value5 = false, $value6 = false, $value7 = false, $value8 = false, $value9 = false) {
		$key = strtolower($key);
		if (isset($this->text[$key])) {
			$text = $this->text[$key][$this->currentLanguage];
		} else {
			$text = "###".$key."###";
		}

		if ($value0 !== false) $text = str_replace("{0}", $value0, $text);
		if ($value1 !== false) $text = str_replace("{1}", $value1, $text);
		if ($value2 !== false) $text = str_replace("{2}", $value2, $text);
		if ($value3 !== false) $text = str_replace("{3}", $value3, $text);
		if ($value4 !== false) $text = str_replace("{4}", $value4, $text);
		if ($value5 !== false) $text = str_replace("{5}", $value5, $text);
		if ($value6 !== false) $text = str_replace("{6}", $value6, $text);
		if ($value7 !== false) $text = str_replace("{7}", $value7, $text);
		if ($value8 !== false) $text = str_replace("{8}", $value8, $text);
		if ($value9 !== false) $text = str_replace("{9}", $value9, $text);

		$s = strpos($text, "{");
		while ($s !== false) {
            $e = strpos($text, "}", $s);
			if ($e === false) {
                $s = false;
            } else {
				$before = substr($text, 0, $s);
				$after = substr($text, $e+1);

				$langDetails = explode("|", substr($text, $s+1, $e-$s-1));

				$lang = $this->Get($langDetails[0]);

				$text = $before.$lang.$after;
				$s = strpos($text, "{", $s);
            }
        }

		return $text;
	}

}
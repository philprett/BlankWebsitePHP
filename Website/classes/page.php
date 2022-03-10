<?php

abstract class Page {

	public $head;
	public $title;
	public $body;

	abstract public function PrepareData();
	abstract public function PerformActions();
	abstract public function PreparePage();

	public function __construct() {
		global $LANG;
		$this->head = "";
		$this->title = $LANG->Get("applicationname");
		$this->body = "";
	}

	public function AddHead($head) {
		$this->head .= $head;
	}

	public function AddBody($body, $langValue0 = false, $langValue1 = false, $langValue2 = false, $langValue3 = false, $langValue4 = false, $langValue5 = false, $langValue6 = false, $langValue7 = false, $langValue8 = false, $langValue9 = false) {

		global $LANG;

		$text = $body;
		$s = strpos($text, "{");
		while ($s !== false && strpos($text, "{{") === $s) {
			$text = substr($text, 0, $s) . substr($text,$s+1);
			$s = strpos($text, "{", $s+1);
		}
		while ($s !== false) {
            $e = strpos($text, "}", $s);
			if ($e === false) {
                $s = false;
            } else {
				$before = substr($text, 0, $s);
				$after = substr($text, $e+1);

				$langDetails = substr($text, $s+1, $e-$s-1);

				$lang = $LANG->Get($langDetails, $langValue0, $langValue1, $langValue2, $langValue3, $langValue4, $langValue5, $langValue6, $langValue7, $langValue8, $langValue9);

				$text = $before.$lang.$after;
				$s = strpos($text, "{", $s);
				while ($s !== false && strpos($text, "{{", $s) === $s) {
					$s = strpos($text, "{", $s+2);
				}
			}
        }

		$this->body .= $text;
	}

	public function Show() {

		global $LANG, $USER, $CONFIG;

        $applicationName = $LANG->Get("applicationname");

		$this->PrepareData();
		$this->PerformActions();
		$this->PreparePage();

		echo "<!DOCTYPE html>\n";
		echo "<html lang=\"de\">\n";
		echo "<head>\n";
		echo "<meta charset=\"utf-8\" />\n";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/assets/fonts/fontawesome-free-6.0.0-web/css/all.min.css\">\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/assets/css/styles.css\">\n";
		echo "<link rel=\"icon\" href=\"/favicon.ico\" />\n";
        //echo "<script src=\"/assets/ckeditor/ckeditor.js\"></script>\n";
		echo "<script src=\"/assets/scripts/script.js\"></script>\n";
		echo "<title>".$this->title."</title>\n";
		echo $this->head."\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<div class='Header'>\n";
		echo $applicationName;
		echo "</div>\n";
		echo "<div class='Menu'>\n";
		echo "		<a href='/'>".Icons::$Home." ".$LANG->Get("homepage")."</a>\n";
		if ($USER) {
			if ($USER->user_admin) {
				echo "		<a href='/admin'>".Icons::$Settings." ".$LANG->Get("Settings")."</a>\n";
			}
			echo "		<a href='/user/profile'>".Icons::$User." ".USER_GetFullName($USER)."</a>\n";
		} else {
			echo "		<a href='/user/login'>".Icons::$User." ".$LANG->Get("login")."</a>";
		}
		echo "</div>\n";
		echo "<div class='Main'>\n";
		echo $this->body."\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "</html>";
		exit();
	}

}
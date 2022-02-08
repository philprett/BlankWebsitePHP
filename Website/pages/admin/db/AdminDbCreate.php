<?php

class AdminDbCreate extends Page {

	public function PrepareData() {
		$this->messages = array();
	}

	public function PerformActions() {

		global $LANG, $DB;

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
				$this->messages[] = $LANG->Get("errorcreatingdbtable", "users", $DB->LastError);
			} else {
				$this->messages[] = $LANG->Get("createddbtable", "users");
			}
		} else {
			$this->messages[] = $LANG->Get("dbtableexists", "users");
		}

	}

	public function PreparePage() {
		global $LANG;
		$this->AddBody("<h1>".$LANG->Get("createdbtables")."</h1>");

		foreach ($this->messages as $message) {
			$this->AddBody($message);
			$this->AddBody("<br>");
		}
	}

}
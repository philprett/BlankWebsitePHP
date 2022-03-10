<?php

class AdminPage extends Page {

	public function PrepareData() {

		global $USER;

        if (!$USER || $USER->user_admin == false) {
            Page_ShowError404();
        }

	}

	public function PerformActions() {

	}

	public function PreparePage() {

		global $CONFIG;

		$this->AddBody("<h1>{Settings}</h1>");

        $this->AddBody("<a class='LinkButton' href='/admin/users'><i class='fa-solid fa-users'></i> {useradmin}</a>");

	}

}
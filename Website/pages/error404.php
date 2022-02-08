<?php

class Error404Page extends Page {

	public function PrepareData() {

	}

	public function PerformActions() {

	}

	public function PreparePage() {
		global $LANG;
		$this->AddBody("<h1>".$LANG->Get("NotFound")."</h1>");
	}

}
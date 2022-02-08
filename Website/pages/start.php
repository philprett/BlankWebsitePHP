<?php

class StartPage extends Page {

	public function PrepareData() {

	}

	public function PerformActions() {

	}

	public function PreparePage() {

		global $USER, $LANG;

		if ($USER) {
            $this->AddBody("{startpagecontentuser}", trim($USER->user_firstname." ".$USER->user_surname));
        } else {
            $this->AddBody("{startpagecontentanon}");
        }
	}

}
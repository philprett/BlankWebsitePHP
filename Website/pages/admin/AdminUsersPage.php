<?php

class AdminUsersPage extends Page {

	public function PrepareData() {

		global $USER;

        if (!$USER || $USER->user_admin == false) {
            Page_ShowError404();
        }

        $this->users = Data_User::Get("", array(), "user_surname, user_firstname");

	}

	public function PerformActions() {

        if (isset($_GET["addnew"])) {

            $new = new Data_User(CreateGuid(), "", "", "", "", "", "", false);
            $new->Save();

            header("Location: /admin/users");
            exit();

        }

        if (isset($_POST["ajaxupdateid"]) && isset($_POST["ajaxfield"]) && isset($_POST["ajaxvalue"])) {

            $record = Data_User::GetFirst("user_id = ?", array($_POST["ajaxupdateid"]));
            if ($record == false) {
                AjaxError("Could not find user to update");
            }

            if ($_POST["ajaxfield"] == "email") {
                $record->user_email = $_POST["ajaxvalue"];
            }
            else if ($_POST["ajaxfield"] == "surname") {
                $record->user_surname = $_POST["ajaxvalue"];
            }
            else if ($_POST["ajaxfield"] == "firstname") {
                $record->user_firstname = $_POST["ajaxvalue"];
            }
            else if ($_POST["ajaxfield"] == "isadmin") {
                $record->user_admin = $_POST["ajaxvalue"];
            }
            else {
                AjaxError("Unknown field for user");
            }

            $record->Save();
            AjaxOk();
        }

        if (isset($_POST["ajaxdeleteid"])) {

            $record = Data_User::GetFirst("user_id = ?", array($_POST["ajaxdeleteid"]));
            if ($record == false) {
                AjaxError("Could not find user to delete");
            }

            $record->Delete();
            AjaxOk();
        }

	}

	public function PreparePage() {

		global $CONFIG;

		$this->AddBody("<h1>{useradmin}</h1>");

        $this->AddBody("<div style='padding-bottom: 20px;'>");
        $this->AddBody("<a class='LinkButton' href='/admin/users?addnew=yes'>".Icons::$UserAdd." {addnewuser}</a>");
        $this->AddBody("</div>");

        $this->AddBody("<table cellspacing=0 cellpadding=5 border=1>");

        $this->AddBody("<tr>");
        $this->AddBody("<th>{email}</th>");
        $this->AddBody("<th>{surname}</th>");
        $this->AddBody("<th>{firstname}</th>");
        $this->AddBody("<th>{isadmin}</th>");
        $this->AddBody("<th>{actions}</th>");
        $this->AddBody("</tr>");

        foreach ($this->users as $user) {

            $this->AddBody("<tr'>");
            $this->AddBody("<td>");
            $this->AddBody("<input name=email type=text class='DynamicInputField' value='".$user->user_email."' onchange=\"AjaxUpdate(this, '".$user->user_id."', this.value)\">");
            $this->AddBody("</td>");
            $this->AddBody("<td>");
            $this->AddBody("<input name=surname type=text class='DynamicInputField' value='".$user->user_surname."' onchange=\"AjaxUpdate(this, '".$user->user_id."', this.value)\">");
            $this->AddBody("</td>");
            $this->AddBody("<td>");
            $this->AddBody("<input name=firstname type=text class='DynamicInputField' value='".$user->user_firstname."' onchange=\"AjaxUpdate(this, '".$user->user_id."', this.value)\">");
            $this->AddBody("</td>");
            $this->AddBody("<td align=center>");
            $this->AddBody("<input name=isadmin type=checkbox ".($user->user_admin ? "checked" : "")." onclick=\"AjaxUpdate(this, '".$user->user_id."', this.checked ? 1 : 0)\">");
            $this->AddBody("</td>");
            $this->AddBody("<td align=center>");
            $this->AddBody("<a class='LinkButton' href='' onclick=\"if (confirm('Do you really want to delete this user?')) {{ AjaxDelete(this, '".$user->user_id."'); } return false;\">".Icons::$Delete."</a>");
            $this->AddBody("</td>");
            $this->AddBody("</tr>");

        }

        $this->AddBody("</table>");

	}

}
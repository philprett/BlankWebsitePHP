<?php

class ForgotPasswordPage extends Page {

	public function PrepareData() {

		$this->ShowMailSent = false;

		$this->existingUsers = User_GetExistingUserCount();

	}

	public function PerformActions() {

		global $CONFIG, $LANG;

		if (isset($_POST["forgotpasswordemail"])) {

			$this->ShowMailSent = true;

            $existingUser = Data_User::GetFirst("user_email = ?", array($_POST["forgotpasswordemail"]));
            if (!$existingUser) {
                return;
            }

			$newPassword = RandomString(8, true, false, false, false);
			$existingUser->user_password2 = EncryptPassword($newPassword);
			$existingUser->Save();

			$applicationName = $LANG->Get("applicationname");

			$emailSubject = $LANG->Get("newpasswordmailsubject", $applicationName);
			$emailContent =
				$LANG->Get(
						"newpasswordemailcontent",
						trim($existingUser->user_firstname." ".$existingUser->user_surname),
						$newPassword);

			SendMailHtml($_POST["forgotpasswordemail"], $emailSubject, $emailContent);

            return;
        }

	}

	public function PreparePage() {

		global $CONFIG;

		$this->AddBody("<h1>{forgotpassword}</h1>");

		if ($this->ShowMailSent) {
            $this->AddBody("{newpasswordsent}");
			return;
        }

		$this->AddBody("<form action='/user/forgotpassword' method='post' class='StandardForm'>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{email}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory ValidEmail' name='forgotpasswordemail' value='' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<span class='LinkButton SubmitButton'>{forgotpassword}</span>");
		$this->AddBody("</div><br>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/login'\">{login}</span></div>");
		$this->AddBody("</div>");

		if ($CONFIG["allowuserselfcreation"] == true || $this->existingUsers == 0) {
			$this->AddBody("<div class='Field'>");
			$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/createaccount'\">{createaccount}</span></div>");
			$this->AddBody("</div>");
		}

		$this->AddBody("</form>");
    }

}
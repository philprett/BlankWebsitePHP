<?php

class LoginPage extends Page {

	public function PrepareData() {

		$this->incorrectLoginDetails = false;

	}

	public function PerformActions() {

		global $CONFIG;

		if (isset($_POST["loginemail"])) {

			$email = $_POST["loginemail"];
			$password = $_POST["loginpassword"];

			$user = User::GetFirst(
				"user_email = ?",
				array($email));
			if ($user === false) {
                $this->incorrectLoginDetails = true;
				return;
            }

			if (!VerifyPassword($password, $user->user_password1) &&
				!VerifyPassword($password, $user->user_password2))
            {
                $this->incorrectLoginDetails = true;
				return;
            }

            if (!VerifyPassword($password, $user->user_password1) &&
                VerifyPassword($password, $user->user_password2))
            {
				$user->user_password1 = $user->user_password2;
				$user->user_password2 = EncryptPassword(RandomString(128));
				$user->Save();
            }

			setcookie($CONFIG["cookiename"] , $user->user_cookie, $CONFIG["cookieexpires"], "/", null);
			header("Location: /");
			exit();

        }

	}

	public function PreparePage() {

		if ($this->incorrectLoginDetails) {
            $this->PrepareIncorrectPage();
			return;
        }

		$this->AddBody("<h1>{login}</h1>");

		$this->AddBody("<form action='/user/login' method='post' class='StandardForm'>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{email}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory ValidEmail' name='loginemail' value='' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{Password}</div>");
		$this->AddBody("<input type=password class='InputField Mandatory' name='loginpassword' value=''>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<span class='LinkButton SubmitButton'>{Login}</span>");
		$this->AddBody("</div><br>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/forgotpassword'\">{forgotpassword}</span></div>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/createaccount'\">{createaccount}</span></div>");
		$this->AddBody("</div>");

		$this->AddBody("</form>");
	}

	public function PrepareIncorrectPage() {

		$this->AddBody("<h1>{login}</h1>");

		$this->AddBody("{logindetailsincorrect}");

    }

}
<?php

class ProfilePage extends Page {

	public function PrepareData() {

	}

	public function PerformActions() {

		global $CONFIG, $USER;

		if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $cookie = RandomString(128, true, true, true, false);
            setcookie($CONFIG["cookiename"] , $cookie, $CONFIG["cookieexpires"], "/", null);
			header("Location: /");
			exit();
        }

		if (isset($_POST["profileemail"])) {
            $existingUser = User::GetFirst("user_email = ? AND user_id != ?", array($_POST["profileemail"]), $USER->user_id);
            if ($existingUser) {
                $this->emailAlreadyExists = true;
                return;
            }

            $USER->user_email = $_POST["profileemail"];
            $USER->user_firstname = $_POST["profilefirstname"];
            $USER->user_surname = $_POST["profilesurname"];
			if (trim($_POST["profilepassword1"]) != "") {
                $USER->user_password1 = EncryptPassword($_POST["profilepassword1"]);
				$USER->user_password2 = EncryptPassword(RandomString(128, true, true, true, false));
            }
			$USER->Save();

            header("Location: /");
            exit();
        }

	}

	public function PreparePage() {

		global $USER;

		$this->AddBody("<h1>{profile}</h1>");

		$this->AddBody("<form action='/user/profile' method='post' class='StandardForm'>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{email}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory ValidEmail' name='profileemail' value='".$USER->user_email."' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{firstname}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory' name='profilefirstname' value='".$USER->user_firstname."' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{surname}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory' name='profilesurname' value='".$USER->user_surname."' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{Password}</div>");
		$this->AddBody("<input type=password class='InputField PasswordVerification' name='profilepassword1' value=''>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{Password}</div>");
		$this->AddBody("<input type=password class='InputField PasswordVerification' name='profilepassword2' value=''>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<span class='LinkButton SubmitButton'>{save}</span>");
		$this->AddBody("</div><br>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/profile?action=logout'\">{logout}</span></div>");
		$this->AddBody("</div>");

		$this->AddBody("</form>");
	}

}
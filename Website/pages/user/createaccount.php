<?php

class CreateAccountPage extends Page {

	public function PrepareData() {

		$this->passwordsDoNotMatch = false;
		$this->emailAlreadyExists = false;

		$this->existingUsers = User_GetExistingUserCount();

	}

	public function PerformActions() {

		global $CONFIG, $DB;

		if ($CONFIG["allowuserselfcreation"] == false && $this->existingUsers > 0) {
			header("Location: /user/login");
			exit();
		}

		if (isset($_POST["createaccountemail"])) {

            $createaccountemail = $_POST["createaccountemail"];
            $createaccountfirstname = $_POST["createaccountfirstname"];
            $createaccountsurname = $_POST["createaccountsurname"];
            $createaccountpassword1 = $_POST["createaccountpassword1"];
            $createaccountpassword2 = $_POST["createaccountpassword2"];

			if ($createaccountpassword1 != $createaccountpassword2) {
                $this->passwordsDoNotMatch = false;
				return;
            }

            $existingUser = Data_User::GetFirst("user_email = ?", array($createaccountemail));
            if ($existingUser) {
                $this->emailAlreadyExists = true;
                return;
            }

			$passwordHash = EncryptPassword($createaccountpassword1);
			$cookie = RandomString(128, true, true, true, false);

			$this->existingUsers = User_GetExistingUserCount();

			$user = new Data_User(
				CreateGuid(), 
				$createaccountemail,
				$createaccountfirstname, 
				$createaccountsurname, 
				$passwordHash, 
				"", 
				$cookie, 
				$this->existingUsers == 0);
			$user->Save();

            header("Location: /user/login");
            exit();
        }

	}

	public function PreparePage() {

		$this->AddBody("<h1>{createaccount}</h1>");

		$this->AddBody("<form action='/user/createaccount' method='post' class='StandardForm'>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{email}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory ValidEmail' name='createaccountemail' value='' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{firstname}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory' name='createaccountfirstname' value='' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{surname}</div>");
		$this->AddBody("<input type=text class='InputField Mandatory' name='createaccountsurname' value='' />");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{Password}</div>");
		$this->AddBody("<input type=password class='InputField Mandatory PasswordVerification' name='createaccountpassword1' value=''>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div class='Title'>{Password}</div>");
		$this->AddBody("<input type=password class='InputField Mandatory PasswordVerification' name='createaccountpassword2' value=''>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<span class='LinkButton SubmitButton'>{createaccount}</span>");
		$this->AddBody("</div><br>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/login'\">{login}</span></div>");
		$this->AddBody("</div>");

		$this->AddBody("<div class='Field'>");
		$this->AddBody("<div><span class='LinkButton' onclick=\"location.href = '/user/forgotpassword'\">{forgotpassword}</span></div>");
		$this->AddBody("</div>");

		$this->AddBody("</form>");
    }

}
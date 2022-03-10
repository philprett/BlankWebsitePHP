<?php

	class Routing {

		public static function Route() {

			$url = $_SERVER["REQUEST_URI"];
			if (strpos($url, "?") !== false) {
                $url = substr($url, 0, strpos($url, "?"));
            }

			$page = new Error404Page();
			if ($url == "/") $page = new StartPage();
			else if ($url == "/user/login") $page = new LoginPage();
			else if ($url == "/user/profile") $page = new ProfilePage();
			else if ($url == "/user/createaccount") $page = new CreateAccountPage();
			else if ($url == "/user/forgotpassword") $page = new ForgotPasswordPage();
			else if ($url == "/admin/users") $page = new AdminUsersPage();
			else if ($url == "/admin") $page = new AdminPage();

			if ($page) {
				$page->Show();
			}
		}

	}
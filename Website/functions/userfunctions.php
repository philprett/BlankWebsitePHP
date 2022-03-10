<?php

/**
 * Load a user from the database based on a cookie value
 * @param string $cookie The value saved in the cookie
 * @return Data_User
 */
function User_GetByCookie($cookie) {

    global $DB;

    $data = Data_User::GetFirst("user_cookie = ?", array($cookie));
    return $data;
}

/**
 * Get the full name of the user
 * @param Data_User $user The user
 * @return string
 */
function User_GetFullName($user) {

    return trim($user->user_firstname." ".$user->user_surname);

}

function User_GetExistingUserCount() {

    global $DB;

    return $DB->ExecScalar("SELECT COUNT(*) FROM users");

}
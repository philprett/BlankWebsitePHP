<?php

/**
 * Represents a user
 */
class User extends DBEntity {

    protected static function GetTableName() { return "users"; }
    protected static function GetFields() {
        return array(
            "user_id        VARCHAR(40) NOT NULL",
	        "user_email     VARCHAR(300) NOT NULL",
	        "user_firstname VARCHAR(300) NOT NULL",
	        "user_surname   VARCHAR(300) NOT NULL",
	        "user_password1 VARCHAR(300) NOT NULL",
	        "user_password2 VARCHAR(300)     NULL",
	        "user_cookie    VARCHAR(300) NOT NULL");
    }
    protected static function GetPKFieldname() { return "user_id"; }

    public $user_id;
    public $user_email;
    public $user_firstname;
    public $user_surname;
    public $user_password1;
    public $user_password2;
    public $user_cookie;

    public function __construct($user_id = false, $user_email = false, $user_firstName = false, $user_surname = false, $user_password1 = false, $user_password2 = false, $user_cookie = false) {

        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->user_firstname = $user_firstName;
        $this->user_surname = $user_surname;
        $this->user_password1 = $user_password1;
        $this->user_password2 = $user_password2;
        $this->user_cookie = $user_cookie;

    }

    public static function FromDbObject($dbObject) {

        return new User(
            $dbObject->user_id,
            $dbObject->user_email,
            $dbObject->user_firstname,
            $dbObject->user_surname,
            $dbObject->user_password1,
            $dbObject->user_password2,
            $dbObject->user_cookie);

    }

    public static function GetByCookie($cookie) {

        global $DB;

        $data = static::GetFirst("user_cookie = ?", array($cookie));
        return $data;
    }

    public function Save() {

        global $DB;

        $existing = $this->user_id ? static::GetByPK($this->user_id) : false;
        if (!$existing) {
            $DB->Exec(
                "INSERT INTO users ".
                "(user_id, user_email, user_firstname, user_surname, user_password1, user_password2, user_cookie) ".
                "VALUES ".
                "(?, ?, ?, ?, ?, ?, ?)",
                array(
                    $this->user_id,
                    $this->user_email,
                    $this->user_firstname,
                    $this->user_surname,
                    $this->user_password1,
                    $this->user_password2,
                    $this->user_cookie),
                false);
        } else {
            $DB->Exec(
                "UPDATE users ".
                "SET    user_email = ?, ".
                "       user_firstname = ?, ".
                "       user_surname = ?, ".
                "       user_password1 = ?, ".
                "       user_password2 = ?, ".
                "       user_cookie = ? ".
                "WHERE  user_id = ?",
                array(
                    $this->user_email,
                    $this->user_firstname,
                    $this->user_surname,
                    $this->user_password1,
                    $this->user_password2,
                    $this->user_cookie,
                    $this->user_id),
                false);
        }

    }

    public function GetFullName() {

        return trim($this->user_firstname." ".$this->user_surname);

    }

}
<?php

/**
 * Class  Data_User
 * Table  users
 * Fields user_id, user_email, user_firstname, user_surname, user_password1, user_password2, user_cookie, user_admin
 *        user_id VARCHAR(40) NOT NULL
 *        user_email VARCHAR(300) NOT NULL
 *        user_firstname VARCHAR(300) NOT NULL
 *        user_surname VARCHAR(300) NOT NULL
 *        user_password1 VARCHAR(300) NOT NULL
 *        user_password2 VARCHAR(300) NULL
 *        user_cookie VARCHAR(300) NOT NULL
 *        user_admin TINYINT NOT NULL
 */
class Data_User {

    /**
     * string $user_id
     */
    public $user_id;

    /**
     * string $user_email
     */
    public $user_email;

    /**
     * string $user_firstname
     */
    public $user_firstname;

    /**
     * string $user_surname
     */
    public $user_surname;

    /**
     * string $user_password1
     */
    public $user_password1;

    /**
     * string $user_password2
     */
    public $user_password2;

    /**
     * string $user_cookie
     */
    public $user_cookie;

    /**
     * bool $user_admin
     */
    public $user_admin;

    /**
     * Constructor
     * @param string $user_id
     * @param string $user_email
     * @param string $user_firstname
     * @param string $user_surname
     * @param string $user_password1
     * @param string $user_password2
     * @param string $user_cookie
     * @param bool $user_admin
     */
    public function __construct($user_id = false, $user_email = false, $user_firstname = false, $user_surname = false, $user_password1 = false, $user_password2 = false, $user_cookie = false, $user_admin = false) {
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->user_firstname = $user_firstname;
        $this->user_surname = $user_surname;
        $this->user_password1 = $user_password1;
        $this->user_password2 = $user_password2;
        $this->user_cookie = $user_cookie;
        $this->user_admin = $user_admin;
    }

    /**
     * Create an object from the PDO
     * @param StdClass $dbObject
     */
    public static function FromDbObject($dbObject) {
        return new Data_User(
            $dbObject->user_id,
            $dbObject->user_email,
            $dbObject->user_firstname,
            $dbObject->user_surname,
            $dbObject->user_password1,
            $dbObject->user_password2,
            $dbObject->user_cookie,
            $dbObject->user_admin);
    }

    /**
     * Get multiple data entites from the database
     * @param string $where The WHERE part of the SQL statement
     * @param array $whereArgs The arguments for the WHERE part of the SQL statement
     * @param string $orderBy The order in which the results should be returned
     * @return array|bool
     */
    public static function Get($where = "", $whereArgs = array(), $orderBy = "") {
    
        global $DB;
    	
        $args = array();
        $sql = "SELECT * FROM users";
    
        if (trim($where) != "") {
            $sql .= " WHERE ".$where;
            if (is_array($whereArgs) && count($whereArgs) > 0) {
                $args = $whereArgs;
            }
        }
    
        if (trim($orderBy) != "") {
            $sql .= " ORDER BY ".$orderBy;
        }
    
        $data = $DB->Exec($sql, $args, true);
        if (!$data) {
            return false;
        }
    
        $ret = array();
        foreach ($data as $record) {
            $ret[] = static::FromDbObject($record);
        }
    
        return $ret;
    }

    /**
     * Get the first data entity from the database
     * @param string $where The WHERE part of the SQL statement
     * @param array $whereArgs The arguments for the WHERE part of the SQL statement
     * @param string $orderBy The order in which the results should be returned
     * @return mixed|bool
     */
    public static function GetFirst($where = "", $whereArgs = array(), $orderBy = "") {
        $data = static::Get($where, $whereArgs, $orderBy);
        if (!$data || count($data) == 0) {
            return false;
        }
        return $data[0];
    }

    /**
     * Get a record from the table based on the PK field.
     * @param mixed $pkValue The value of the PK field to retrieve
     * @return mixed|bool
     */
    public static function GetByPK($pkValue) {
        return static::GetFirst("user_id = ?", array($pkValue));
    }

    /**
     * Save this record to the table.
     * If it is new, an INSERT, otherwise an UPDATE.
     */
    public function Save() {
    
        global $DB;
    
        $existing = $this->user_id ? static::GetByPK($this->user_id) : false;
        if (!$existing) {
            $DB->Exec(
                "INSERT INTO users ".
                "(user_id, user_email, user_firstname, user_surname, user_password1, user_password2, user_cookie, user_admin) ".
                "VALUES ".
                "(?, ?, ?, ?, ?, ?, ?, ?)",
                array(
                    $this->user_id,
                    $this->user_email,
                    $this->user_firstname,
                    $this->user_surname,
                    $this->user_password1,
                    $this->user_password2,
                    $this->user_cookie,
                    $this->user_admin),
                false);
        } else {
            $DB->Exec(
                "UPDATE users ".
                "SET ".
                "       user_id = ?, ".
                "       user_email = ?, ".
                "       user_firstname = ?, ".
                "       user_surname = ?, ".
                "       user_password1 = ?, ".
                "       user_password2 = ?, ".
                "       user_cookie = ?, ".
                "       user_admin = ? ".
                "WHERE  user_id = ?",
                array(
                    $this->user_id,
                    $this->user_email,
                    $this->user_firstname,
                    $this->user_surname,
                    $this->user_password1,
                    $this->user_password2,
                    $this->user_cookie,
                    $this->user_admin,
                    $this->user_id),
                false);
        }
        
    }

    /**
     * Delete this record from the table.
     */
    public function Delete() {
    
        global $DB;
    
        $DB->Exec(
            "DELETE FROM users WHERE user_id = ?",
            array($this->user_id),
             false);
        
    }

}

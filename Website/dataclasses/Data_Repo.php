<?php

/**
 * Class  Data_Repo
 * Table  repos
 * Fields repo_id, repo_name, repo_url, repo_clonedir, repo_wwwdir
 *        repo_id VARCHAR(40) NOT NULL
 *        repo_name VARCHAR(300) NOT NULL
 *        repo_url VARCHAR(1000) NOT NULL
 *        repo_clonedir VARCHAR(1000) NOT NULL
 *        repo_wwwdir VARCHAR(1000) NOT NULL
 */
class Data_Repo {

    /**
     * string $repo_id
     */
    public $repo_id;

    /**
     * string $repo_name
     */
    public $repo_name;

    /**
     * string $repo_url
     */
    public $repo_url;

    /**
     * string $repo_clonedir
     */
    public $repo_clonedir;

    /**
     * string $repo_wwwdir
     */
    public $repo_wwwdir;

    /**
     * Constructor
     * @param string $repo_id
     * @param string $repo_name
     * @param string $repo_url
     * @param string $repo_clonedir
     * @param string $repo_wwwdir
     */
    public function __construct($repo_id = false, $repo_name = false, $repo_url = false, $repo_clonedir = false, $repo_wwwdir = false) {
        $this->repo_id = $repo_id;
        $this->repo_name = $repo_name;
        $this->repo_url = $repo_url;
        $this->repo_clonedir = $repo_clonedir;
        $this->repo_wwwdir = $repo_wwwdir;
    }

    /**
     * Create an object from the PDO
     * @param StdClass $dbObject
     */
    public static function FromDbObject($dbObject) {
        return new Data_Repo(
            $dbObject->repo_id,
            $dbObject->repo_name,
            $dbObject->repo_url,
            $dbObject->repo_clonedir,
            $dbObject->repo_wwwdir);
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
        $sql = "SELECT * FROM repos";
    
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
        return static::GetFirst("repo_id = ?", array($pkValue));
    }

    /**
     * Save this record to the table.
     * If it is new, an INSERT, otherwise an UPDATE.
     */
    public function Save() {
    
        global $DB;
    
        $existing = $this->repo_id ? static::GetByPK($this->repo_id) : false;
        if (!$existing) {
            $DB->Exec(
                "INSERT INTO repos ".
                "(repo_id, repo_name, repo_url, repo_clonedir, repo_wwwdir) ".
                "VALUES ".
                "(?, ?, ?, ?, ?)",
                array(
                    $this->repo_id,
                    $this->repo_name,
                    $this->repo_url,
                    $this->repo_clonedir,
                    $this->repo_wwwdir),
                false);
        } else {
            $DB->Exec(
                "UPDATE repos ".
                "SET ".
                "       repo_id = ?, ".
                "       repo_name = ?, ".
                "       repo_url = ?, ".
                "       repo_clonedir = ?, ".
                "       repo_wwwdir = ? ".
                "WHERE  repo_id = ?",
                array(
                    $this->repo_id,
                    $this->repo_name,
                    $this->repo_url,
                    $this->repo_clonedir,
                    $this->repo_wwwdir,
                    $this->repo_id),
                false);
        }
        
    }

    /**
     * Delete this record from the table.
     */
    public function Delete() {
    
        global $DB;
    
        $DB->Exec(
            "DELETE FROM repos WHERE repo_id = ?",
            array($this->repo_id),
             false);
        
    }

}

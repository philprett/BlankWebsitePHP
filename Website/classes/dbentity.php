<?php

/**
 * An entity in the database
 */
abstract class DBEntity {

    /**
     * The name of the table in the database
     * @var string
     */
    protected abstract static function GetTableName();

    /**
     * The fields in the database table
     * @var array
     */
    protected abstract static function GetFields();

    /**
     * The name of the primary key field
     * @var string
     */
    protected abstract static function GetPKFieldname();

    /**
     * Generate a database entity object from a pdo
     * @param mixed $dbObject
     */
    public abstract static function FromDbObject($dbObject);

    /**
     * Check if a table exists in the database
     */
    public static function CheckIfTableExists() {

        global $DB;

        $tableName = static::GetTableName();
        if (!$DB->TableExists($tableName)) {
            echo "The table ".static::GetTableName()." does not exist.<br>";
            echo "Please run the <a href='/admin/db/create'>database creator</a>.";
            exit();
        }

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
        $sql = "SELECT * FROM ".static::GetTableName();

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
     * Get a record from the table based on the PK field.
     * @param mixed $pkValue The value of the PKJ field to retrieve
     * @return mixed|bool
     */
    public static function GetByPK($pkValue) {

        return static::GetFirst(static::GetPKFieldname()." = ?", array($pkValue));

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
     * Save the entity to the database
     */
    public abstract function Save();

}

?>
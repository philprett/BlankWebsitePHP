<?php

define("DataClassesFolder", "dataclasses");

if (!file_exists("config.php")) {
	die("Please create the config.php file.");
}
include "config.php";

include "classes/db.php";

$DB = new DB(
	$CONFIG["dbhost"],
	$CONFIG["dbusername"],
	$CONFIG["dbpassword"],
	$CONFIG["dbdatabase"]);

$verbose = isset($_GET["verbose"]);

if ($verbose) echo "<h1>Create Database Tables</h1>";

$tables = array(	array(	"name" => "users",
							"fields" =>	array(	array(	"name" => "user_id", 		"type" => "VARCHAR(40)", 	"nullable" => false ),
												array(	"name" => "user_email", 	"type" => "VARCHAR(300)", 	"nullable" => false ),
												array(	"name" => "user_firstname", "type" => "VARCHAR(300)", 	"nullable" => false ),
												array(	"name" => "user_surname", 	"type" => "VARCHAR(300)", 	"nullable" => false ),
												array(	"name" => "user_password1", "type" => "VARCHAR(300)", 	"nullable" => false ),
												array(	"name" => "user_password2", "type" => "VARCHAR(300)", 	"nullable" => true ),
												array(	"name" => "user_cookie", 	"type" => "VARCHAR(300)", 	"nullable" => false ),
												array(	"name" => "user_admin", 	"type" => "TINYINT", 		"nullable" => false ),
											),
							"pk" => "user_id",
							"class" => "User",
						),
				);

// Create the tables in the database
foreach ($tables as $table) {
	if ($verbose) echo "Table: ".$table["name"];

	if (!$DB->TableExists($table["name"])) {
		// Doesn't exist. Create it.
		$sql = "CREATE TABLE ".$table["name"];
		foreach ($table["fields"] as $field) {
			if ($field["name"] == $table["pk"]) {
				$sql .= " (".$field["name"]." ".$field["type"]." ".($field["nullable"] ? "NULL" : "NOT NULL").")";
			}
		}
		$result = $DB->Exec($sql, array(), false);
		if (!$result) {
			echo " - ERROR CREATING TABLE<br>";
			echo $sql."<br>";
			echo $DB->$DB->LastError;
		} else {
			if ($verbose) echo " - CREATED<br>";
		}
	} else {
		if ($verbose) echo " - EXISTS<br>";
	}

	$existingFields = $DB->GetTableColumns($table["name"]);
	foreach ($table["fields"] as $field) {
		if ($verbose) echo "Field: ".$field["name"]." ".$field["type"]." ".($field["nullable"] ? "NULL" : "NOT NULL");

		$existingField = false;
		foreach ($existingFields as $e) {
			if ($e->COLUMN_NAME == $field["name"]) {
				$existingField = $e;
				break;
			}
		}

		if ($existingField) {
			// Field exists, check if it is correct
			if (strtolower(substr($e->COLUMN_TYPE,0,4)) == "int(") $e->COLUMN_TYPE = "int";
			if (strtolower(substr($e->COLUMN_TYPE,0,8)) == "tinyint(") $e->COLUMN_TYPE = "tinyint";

			if (strtolower($field["type"]) != strtolower($e->COLUMN_TYPE) ||
				($field["nullable"] ? "yes" : "no") != strtolower($e->IS_NULLABLE))
			{
				$sql = "ALTER TABLE ".$table["name"]." MODIFY ".$field["name"]." ".$field["type"]." ".($field["nullable"] ? "NULL" : "NOT NULL");
				$result = $DB->Exec($sql, array(), false);
				if (!$result) {
					echo " - ERROR UPDATING<br>";
					echo $sql."<br>";
					echo $DB->$DB->LastError;
				} else {
					if ($verbose) echo " - UPDATED<br>";
				}
			} else {
				if ($verbose) echo " - OK<br>";
			}
		} else {
			// Field doesn't exist. Create it
			$sql = "ALTER TABLE ".$table["name"]." ADD ".$field["name"]." ".$field["type"]." ".($field["nullable"] ? "NULL" : "NOT NULL");
			$result = $DB->Exec($sql, array(), false);
			if (!$result) {
				echo " - ERROR CREATING<br>";
				echo $sql."<br>";
				echo $DB->$DB->LastError;
			} else {
				if ($verbose) echo " - CREATED<br>";
			}
		}
	}

	if ($verbose) echo "<br>";
}

function GetPhpType($sqlType) {
	$type = strtolower($sqlType);
	$type = strpos($type, "(") === false ? $type : substr($type, 0, strpos($type, "("));
	if ($type == "tinyint") {
		return "bool";
	}
	if ($type == "int") {
		return "int";
	}
	if ($type == "varchar") {
		return "string";
	}
	return "mixed";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Now create the dataclasses
///////////////////////////////////////////////////////////////////////////////////////////////////////////
foreach ($tables as $table) {
	$php = "<?php\n";
	$php .= "\n";
	$php .= "/**\n";
	$php .= " * Class  Data_".$table["class"]."\n";
	$php .= " * Table  ".$table["name"]."\n";
	$php .= " * Fields ";
	foreach ($table["fields"] as $f => $field) {
		if ($f > 0) $php .= ", ";
		$php .= $field["name"];
	}
	$php .= "\n";

	//////////////////////////////////
	// Properties
	//////////////////////////////////
	foreach ($table["fields"] as $field) {
		$php .= " *        ".$field["name"]." ".$field["type"]." ".($field["nullable"] ? "NULL" : "NOT NULL")."\n";
	}
	$php .= " */\n";
	$php .= "class Data_".$table["class"]." {\n";
	$php .= "\n";
	foreach ($table["fields"] as $field) {
		$php .= "    /**\n";
		$php .= "     * ".GetPhpType($field["type"])." \$".$field["name"]."\n";
		$php .= "     */\n";
		$php .= "    public $".$field["name"].";\n";
		$php .= "\n";
	}

	//////////////////////////////////
	// Constructor
	//////////////////////////////////
	$php .= "    /**\n";
	$php .= "     * Constructor\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "     * @param ".GetPhpType($field["type"])." \$".$field["name"]."\n";
	}
	$php .= "     */\n";
	$php .= "    public function __construct(";
	foreach ($table["fields"] as $f => $field) {
		if ($f > 0) $php .= ", ";
		$php .= "$".$field["name"]." = false";
	}
	$php .= ") {\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "        \$this->".$field["name"]." = \$".$field["name"].";\n";
	}
	$php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// FromDbObject
	//////////////////////////////////
	$php .= "    /**\n";
	$php .= "     * Create an object from the PDO\n";
	$php .= "     * @param StdClass \$dbObject\n";
	$php .= "     */\n";
	$php .= "    public static function FromDbObject(\$dbObject) {\n";
	$php .= "        return new Data_".$table["class"]."(\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "            \$dbObject->".$field["name"].($f + 1 < count($table["fields"]) ? "," : ");")."\n";
	}
	$php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// Get
	//////////////////////////////////
    $php .= "    /**\n";
    $php .= "     * Get multiple data entites from the database\n";
    $php .= "     * @param string \$where The WHERE part of the SQL statement\n";
    $php .= "     * @param array \$whereArgs The arguments for the WHERE part of the SQL statement\n";
    $php .= "     * @param string \$orderBy The order in which the results should be returned\n";
    $php .= "     * @return array|bool\n";
    $php .= "     */\n";
    $php .= "    public static function Get(\$where = \"\", \$whereArgs = array(), \$orderBy = \"\") {\n";
	$php .= "    \n";
    $php .= "        global \$DB;\n";
	$php .= "    	\n";
    $php .= "        \$args = array();\n";
    $php .= "        \$sql = \"SELECT * FROM ".$table["name"]."\";\n";
	$php .= "    \n";
    $php .= "        if (trim(\$where) != \"\") {\n";
    $php .= "            \$sql .= \" WHERE \".\$where;\n";
    $php .= "            if (is_array(\$whereArgs) && count(\$whereArgs) > 0) {\n";
    $php .= "                \$args = \$whereArgs;\n";
    $php .= "            }\n";
    $php .= "        }\n";
	$php .= "    \n";
    $php .= "        if (trim(\$orderBy) != \"\") {\n";
    $php .= "            \$sql .= \" ORDER BY \".\$orderBy;\n";
    $php .= "        }\n";
	$php .= "    \n";
    $php .= "        \$data = \$DB->Exec(\$sql, \$args, true);\n";
    $php .= "        if (!\$data) {\n";
    $php .= "            return false;\n";
    $php .= "        }\n";
	$php .= "    \n";
    $php .= "        \$ret = array();\n";
    $php .= "        foreach (\$data as \$record) {\n";
    $php .= "            \$ret[] = static::FromDbObject(\$record);\n";
    $php .= "        }\n";
	$php .= "    \n";
    $php .= "        return \$ret;\n";
    $php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// GetFirst
	//////////////////////////////////
    $php .= "    /**\n";
    $php .= "     * Get the first data entity from the database\n";
    $php .= "     * @param string \$where The WHERE part of the SQL statement\n";
    $php .= "     * @param array \$whereArgs The arguments for the WHERE part of the SQL statement\n";
    $php .= "     * @param string \$orderBy The order in which the results should be returned\n";
    $php .= "     * @return mixed|bool\n";
    $php .= "     */\n";
    $php .= "    public static function GetFirst(\$where = \"\", \$whereArgs = array(), \$orderBy = \"\") {\n";
    $php .= "        \$data = static::Get(\$where, \$whereArgs, \$orderBy);\n";
    $php .= "        if (!\$data || count(\$data) == 0) {\n";
    $php .= "            return false;\n";
    $php .= "        }\n";
    $php .= "        return \$data[0];\n";
    $php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// GetByPK
	//////////////////////////////////
    $php .= "    /**\n";
    $php .= "     * Get a record from the table based on the PK field.\n";
    $php .= "     * @param mixed \$pkValue The value of the PK field to retrieve\n";
    $php .= "     * @return mixed|bool\n";
    $php .= "     */\n";
    $php .= "    public static function GetByPK(\$pkValue) {\n";
    $php .= "        return static::GetFirst(\"".$table["pk"]." = ?\", array(\$pkValue));\n";
    $php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// Save
	//////////////////////////////////
    $php .= "    /**\n";
    $php .= "     * Save this record to the table.\n";
    $php .= "     * If it is new, an INSERT, otherwise an UPDATE.\n";
    $php .= "     */\n";
	$php .= "    public function Save() {\n";
	$php .= "    \n";
    $php .= "        global \$DB;\n";
	$php .= "    \n";
    $php .= "        \$existing = \$this->".$table["pk"]." ? static::GetByPK(\$this->".$table["pk"].") : false;\n";
    $php .= "        if (!\$existing) {\n";
    $php .= "            \$DB->Exec(\n";
    $php .= "                \"INSERT INTO ".$table["name"]." \".\n";
    $php .= "                \"(";
	foreach ($table["fields"] as $f => $field) {
		$php .= $field["name"].($f + 1 != count($table["fields"]) ? ", " : "");
	}
	$php .= ") \".\n";
    $php .= "                \"VALUES \".\n";
    $php .= "                \"(";
	foreach ($table["fields"] as $f => $field) {
		$php .= "?".($f + 1 != count($table["fields"]) ? ", " : "");
	}
	$php .= ")\",\n";
    $php .= "                array(\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "                    \$this->".$field["name"].($f + 1 != count($table["fields"]) ? ",\n" : "),\n");
	}
    $php .= "                false);\n";
    $php .= "        } else {\n";
    $php .= "            \$DB->Exec(\n";
    $php .= "                \"UPDATE ".$table["name"]." \".\n";
    $php .= "                \"SET \".\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "                \"       ".$field["name"]." = ?".($f + 1 != count($table["fields"]) ? "," : "")." \".\n";
	}
    $php .= "                \"WHERE  ".$table["pk"]." = ?\",\n";
    $php .= "                array(\n";
	foreach ($table["fields"] as $f => $field) {
		$php .= "                    \$this->".$field["name"].",\n";
	}
	$php .= "                    \$this->".$table["pk"]."),\n";
    $php .= "                false);\n";
    $php .= "        }\n";
	$php .= "        \n";
    $php .= "    }\n";
	$php .= "\n";

	//////////////////////////////////
	// Delete
	//////////////////////////////////
    $php .= "    /**\n";
    $php .= "     * Delete this record from the table.\n";
    $php .= "     */\n";
	$php .= "    public function Delete() {\n";
	$php .= "    \n";
    $php .= "        global \$DB;\n";
	$php .= "    \n";
    $php .= "        \$DB->Exec(\n";
    $php .= "            \"DELETE FROM ".$table["name"]." WHERE ".$table["pk"]." = ?\",\n";
    $php .= "            array(\$this->".$table["pk"]."),\n";
    $php .= "             false);\n";
	$php .= "        \n";
    $php .= "    }\n";
	$php .= "\n";

	$php .= "}\n";

	$filename = DataClassesFolder."/Data_".$table["class"].".php";
	if ($verbose) echo "Class: ".$table["class"];
	$file = fopen($filename, "w");
	fwrite($file, $php);
	fclose($file);
	if ($verbose) echo " - OK<br>";
}
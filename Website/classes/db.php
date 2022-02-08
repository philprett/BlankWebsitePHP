<?php

class DB {

	public $Host;
	public $Username;
	public $Password;
	public $Database;
	public $LastError;
	public $LastSQL;
	public $LastInsertID;

	public $Connection;

	/////////////////////////////////////////////////////////////////////////////
	// Constructor
	/////////////////////////////////////////////////////////////////////////////
	public function __construct($host, $username, $password, $database) {
		$this->Host = $host;
		$this->Username = $username;
		$this->Password = $password;
		$this->Database = $database;
		$this->LastError = "";
		$this->LastSQL = "";


		$dsn = "mysql:host=".$this->Host.";dbname=".$this->Database;
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);

		$this->Connection = new PDO($dsn, $this->Username, $this->Password, $options);
		if (!$this->Connection) {
			return;
		}
		$this->Exec("SET NAMES utf8;");
	}

	/**
	 * Execute an SQL statement 
	 * @param string $sql 
	 * @param array $values 
	 * @param bool $recordSet 
	 * @return array|bool
	 */
	public function Exec($sql, $values = array(), $recordSet = true) {

		global $settings;

		if (!$this->Connection) return false;

		$this->LastSQL = $sql;
		$dbStatement = $this->Connection->prepare ( $sql );
		if (!$dbStatement->execute($values)) {
			$t = $dbStatement->errorInfo();
			$this->LastError = $t[2];
			return false;
		} else {
			$this->LastError = "";
			if (!$recordSet) {
				$this->LastInsertID = $this->Connection->lastInsertId();
				return true;
			} else {
				if ($dbStatement->rowCount() > 0) {
					return $dbStatement->fetchAll(PDO::FETCH_CLASS);
				} else {
					return array();
				}
			}
		}

	}
	
	public function ExecFirst($sql, $values = array()) {
		
		$data = $this->Exec($sql, $values, true);
		if (!$data || count($data) == 0) return false;
		
		return $data[0];
	
	}
	
	public function ExecScalar($sql, $values = array()) {
		
		$data = $this->Exec($sql, $values, true);
		if (!$data || count($data) == 0) return false;
		
		foreach ($data[0] as $value) return $value;
		
	}
	
	function GetAllTables() {
		$sql = "SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME";
		$tables = $this->Exec($sql, array($this->Database), true);
		if (count($tables) > 0) {
			return $tables;
		} else {
			return false;
		}
	}

	function GetTableColumns($tableName) {
		$sql = "SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE, COLUMN_KEY, EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION";
		$columns = $this->Exec($sql, array($this->Database, $tableName), true);
		if ($columns) {
			return $columns;
		} else {
			return false;
		}
	}

	function TableExists($tableName) {
		$sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
		$tables = $this->Exec($sql, array($this->Database, $tableName), true);
		if (count($tables) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function ColumnExists($tableName, $columnName) {
		//$sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db_name' AND TABLE_NAME = 'table_name' AND COLUMN_NAME = 'column_name'";
		$sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
		$columns = $this->Exec($sql, array($this->Database, $tableName, $columnName), true);
		if ($columns) {
			return true;
		} else {
			return false;
		}
	}

	/////////////////////////////////////////////////////////////////////////////
	// GetUniqueID
	/////////////////////////////////////////////////////////////////////////////
	public function GetUniqueID ( $table, $field ) {

		do {
			$newID = mt_rand(0,0x7FFFFFFF);
			$res = $this->Exec("SELECT COUNT(*) AS IDsFound FROM ".$table." WHERE ".$field." = ".$newID, array(), true);
			if ( $res === false ) return false;
		} while ( $res[0]->IDsFound > 0 );

		return $newID;

	}

	function GetUpdateFieldsSQLPart($fieldList) {
		$updateFields = "";
		$fields = explode(",", $fieldList);
		foreach ($fields as $field) {
			if ($updateFields) $updateFields .= ", ";
			$updateFields .= $field." = ?";
		}
		return $updateFields;
	}

	function GetInsertFieldsSQLPart($fieldList) {
		$insertFields = "";
		$fields = explode(",", $fieldList);
		for ($f = 0; $f < count($fields); $f++) {
			if ($f) $insertFields .= ", ";
			$insertFields .= "?";
		}
		return $insertFields;
	}

	function GetFieldsValuesArray($fieldList, $obj) {
		$updateValues = array();
		$fields = explode(",", $fieldList);
		foreach ($fields as $field) {
			$updateValues[] = $obj->$field;
		}
		return $updateValues;
	}

	function DumpTable($filename, $tableName, $includeData = true) {

		$insertsPerStatement = 100;

		$file = fopen($filename, "a");

		// COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE, COLUMN_KEY, EXTRA 
		$columns = $this->GetTableColumns($tableName);

		$columnList = "";
		foreach ($columns as $c) {
			if ($columnList != "") $columnList .= ",";
			$columnList .= $c->COLUMN_NAME;
		}

		fwrite($file, "-- -----------------------------------------------------------------------------\r\n");
		fwrite($file, "-- ".$tableName."\r\n");
		fwrite($file, "-- -----------------------------------------------------------------------------\r\n");
		fwrite($file, $this->GetCreateTableSql($tableName).";\r\n\r\n");

		if ($includeData) {
			fwrite($file, "DELETE FROM ".$tableName.";\r\n\r\n");
			$selectSql = "SELECT ".$columnList." FROM ".$tableName;
			$insertSql = "INSERT INTO ".$tableName." (".$columnList.") VALUES ";
			$dbStatement = $this->Connection->prepare($selectSql);
			if ($dbStatement->execute(array())) {
				if ($dbStatement->rowCount() > 0) {
					$rowCounter = 0;
					while ($row = $dbStatement->fetch(PDO::FETCH_ASSOC)) {
						if ($rowCounter % $insertsPerStatement == 0) {
							if ($rowCounter > 0) {
								fwrite($file, ";\r\n");
							}
							fwrite($file, $insertSql);
							fwrite($file, "\r\n	(");
						} else {
							fwrite($file, "\r\n	,(");
						}
						foreach ($columns as $n => $c) {
							if ($n > 0) fwrite($file, ", ");
							$this->DumpTableValue($file, $row, $c);
						}
						fwrite($file, ")");
						$rowCounter++;
					}
					if ($rowCounter > 0) {
						fwrite($file, ";\r\n");
					}
				}
			}
		}
		fwrite($file, "\r\n");
		fclose($file);
	}

	function GetCreateTableSql($tableName) {
		// TABLE_SCHEMA, TABLE_NAME, TABLE_TYPE, ENGINE, TABLE_ROWS, AVG_ROW_LENGTH, DATA_LENGTH, DATA_FREE, AUTO_INCREMENT, CREATE_TIME, TABLE_COLLATION
		$tableInfos = $this->Exec(
			"SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND TABLE_TYPE = ?", 
			array($this->Database, $tableName, "BASE TABLE"));
		if (!$tableInfos || count($tableInfos) == 0) return false;
		$tableInfo = $tableInfos[0];
		
		// TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, 
		// NUMERIC_PRECISION, NUMERIC_SCALE, DATETIME_PRECISION, CHARACTER_SET_NAME, COLLATION_NAME, 
		// COLUMN_TYPE, COLUMN_KEY, EXTRA
		$columnInfos = $this->Exec(
			"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION", 
			array($this->Database, $tableName));
		if (!$columnInfos || count($columnInfos) == 0) return false;
		
		$sql = "CREATE TABLE IF NOT EXISTS ".$tableName." (\r\n";

		$primaryKeyColumns = array();
		$keyColumns = array();
		foreach ($columnInfos as $no => $column) {
			if ($column->COLUMN_KEY == "PRI") {
				$primaryKeyColumns[] = $column->COLUMN_NAME;
			}
			if ($column->COLUMN_KEY == "MUL") {
				$keyColumns[] = $column->COLUMN_NAME;
			}
			$sql .= 
				"    ".
				$column->COLUMN_NAME." ".$column->COLUMN_TYPE.
				($column->COLLATION_NAME ? " COLLATE ".$column->COLLATION_NAME : "").
				($column->IS_NULLABLE == "YES" ? " NULL" : " NOT NULL").
				(stripos($column->EXTRA, "auto_increment") !== false ? " AUTO_INCREMENT" : "");
				if ($no != count($columnInfos) - 1 || count($primaryKeyColumns) > 0 || count($keyColumns) > 0) $sql .= ",";
				$sql .= "\r\n";
		}

		if (count($primaryKeyColumns) > 0) {
			$sql .= "    PRIMARY KEY (".implode(",", $primaryKeyColumns).")";
			if (count($keyColumns) > 0) $sql .= ",";
			$sql .= "\r\n";
		}
		if (count($keyColumns) > 0) {
			foreach ($keyColumns as $no => $keyColumn) {
				$sql .= "    KEY ".$keyColumn." (".$keyColumn.")";
				if ($no != count($keyColumns) - 1) $sql .= ",";
				$sql .= "\r\n";
			}
		}

		$sql .= ") ENGINE=".$tableInfo->ENGINE." COLLATE=".$tableInfo->TABLE_COLLATION."";

		return $sql;
	}

	function DumpTableValue($file, $row, $column) {
		$value = $row[$column->COLUMN_NAME];
		if ($value === null) {
			fwrite($file, "NULL");
		} else if (is_numeric($value)) {
			fwrite($file, $value);
		} else if (is_string($value)) {
			if (strpos($value, "\"") !== false || strpos($value, "'") !== false || 
				strpos($value, "\r") !== false || strpos($value, "\n") !== false) 
			{
				fwrite($file, "0x".bin2hex($value));
			} else {
				fwrite($file, "'".$value."'");
			}
		} else {
			fwrite($file, $value);
		}
	}
}

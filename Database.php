<?php
/**
 *
 * Database Wrapper class
 * @category Stale_Sites
 * @package  Stale_Sites
 * @author   footy <abadari1@binghamton.edu>
 * Its a wrapper class to handle SQlite database transactions.
 * 
**/

define('CONFIG_TB','config');
define('CONFIG_TB_BASE','S_BASE_DIR');
define('DB_CONN_STR','sqlite:config.db');
define('CACHE_FILE_PATH','cache.html');
class Database
{
	private $db = NULL; //contains database connection.


	private function connect() {
		try{
			$this->db = new PDO(DB_CONN_STR); 
			$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
			echo "Database connection error";
			exit;
		}
	}

	private function close() {
		$this->db = NULL;
	}

	// $table is the table name to query from and $colName 
	// is the column names to be get back the data to
	// We can pass any arguments required to be passed to the table
	private function getRows($table,array $colName) {
		if($this->db != NULL) {
			return $this->db->query("SELECT ".implode(',', $colName)." FROM ".$table);
		}
		else {
			throw new Exception("Database connection not established");
		}
	}

	private function cleanTable($table) {
		if($this->db != NULL) {
			$this->db->query("DELETE FROM ".$table);
		}
		else {
			throw new Exception("Database connection not established");
		}
	}

	private function insertInto($table,array $colName,array $columnVals) {
		$query = "INSERT INTO ".$table." (".implode(',',$colName).") VALUES (";
		for($i=0;$i<count($colName);$i++) {
			if($i!=0) 	$query .= ", :col$i";
			else 		$query .= ":col$i";
		}
		$query .= ")";
		
		$stmt = $this->db->prepare($query);

		if($stmt === false) {
			throw new Exception("Failed to prepare the statement");
		}

		try {
			$i=0;
			foreach ($columnVals as $colValue) {
				$stmt->bindParam(":col$i",$colValue);
				$i++;
			}
			$stmt->execute();
		}
		catch(Exception  $e) {
			echo "The insertion into the database failed! ".$e->getMessage();
		}
	}

	public function getBaseDir() {
		$this->connect();
		$rows = $this->getRows(CONFIG_TB,array(CONFIG_TB_BASE));		
		
		if($rows != null) {
			$row = $rows->fetch();
			$this->close();
			return $row[CONFIG_TB_BASE];
		}
		else {
			$this->close();
			throw new Exception("The base is not yet configured");
		}
	}

	public function replaceBaseDir($dir) {
		if(!is_dir($dir)) {
			throw new Exception("The given path is not a directory");
		}
		$this->connect();
		$this->cleanTable(CONFIG_TB);
		$this->insertInto(CONFIG_TB,array(CONFIG_TB_BASE),array($dir));
		$this->close();
		unlink(CACHE_FILE_PATH);
	}
	public function cleanDir() {
		$this->connect();
		$this->cleanTable(CONFIG_TB);
		$this->close();
		unlink(CACHE_FILE_PATH);
	}
}
?>
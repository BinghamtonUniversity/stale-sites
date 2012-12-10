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

define('EXCLUDE_PATH_TB','excludePath');
define('EXCLUDE_PATH_TB_PATH','S_PATH');

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
			echo "Database connection error: $e";
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

	private function cleanTable($table,$colName = null,$columnVals = array()) {
		$query = "DELETE FROM ".$table;
		
		if($colName != null) {
			$query .= " WHERE ";
			for($i=0;$i<count($colName);$i++) {
				if($i!=0) 	$query .= ", ".$colName[$i]." = :col$i";
				else 		$query .= $colName[$i]." = :col$i";
			}
		}
//echo $query;
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
			throw new Exception ( "The insertion into the database failed! ".$e->getMessage() );
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
		if(file_exists(CACHE_FILE_PATH))
			unlink(CACHE_FILE_PATH);
	}
	public function cleanDir() {
		$this->connect();
		$this->cleanTable(CONFIG_TB);
		$this->close();
		if(file_exists(CACHE_FILE_PATH))
			unlink(CACHE_FILE_PATH);
	}

	public function getExcludeDir() {
		$this->connect();
		$rows = $this->getRows(EXCLUDE_PATH_TB,array(EXCLUDE_PATH_TB_PATH));		
		
		$ans = array();
		while($row = $rows->fetch()) {
			$ans[] = $row[EXCLUDE_PATH_TB_PATH];
			
		}

		$this->close();


		return $ans;
	}

	public function addExcludePathDir($dirs = array()) {

		$this->connect();
		

		foreach ($dirs as $dir) {
			$dir=trim($dir);
			try {
				if($dir != "" )
					$this->insertInto(EXCLUDE_PATH_TB,array(EXCLUDE_PATH_TB_PATH),array($dir));
			}
			catch (Exception $e) {
				//ignore since already present
			}
		}

		$this->close();

		if(file_exists(CACHE_FILE_PATH)) {
			//read the cache and delete that line!
			$inp = array();
			$cache = fopen(CACHE_FILE_PATH, 'r');
            if($cache) {
                while(!feof($cache)) {
                	$tmp = fgets($cache);
                    $lineSplit = explode("|", $tmp);
                    if(strlen($lineSplit[0]) > 0 && !in_array($lineSplit[0], $dirs))
                        $inp[] = $tmp;
                }

                fclose($cache);
            }
         	$cache = fopen(CACHE_FILE_PATH, 'w');
            if($cache) {
                foreach ($inp as $val) {
                	fwrite($cache,$val);
                }
                fclose($cache);
            }   
		}
	}

	public function delExcludePathDir($dir) {
		$this->connect();
		$this->cleanTable(EXCLUDE_PATH_TB, array(EXCLUDE_PATH_TB_PATH),array(trim($dir)));
		$this->close();
		if(file_exists(CACHE_FILE_PATH))
			unlink(CACHE_FILE_PATH);
	}
}
?>

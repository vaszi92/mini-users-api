<?php
	
require_once("config.php");
class MySQLDatabase {
	
	private $connection;
	public $last_query;
	
	function __construct(){
		$this->open_connection();
	}
	
	public function open_connection(){
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
		if(!$this->connection){
			die("Database connection failed: ".mysqli_connect_error());
		} else {
			$db_select = mysqli_select_db($this->connection, DB_NAME);
			if(!$db_select){
				die("Database selection failed: ".mysqli_connect_error());
			}
		}
	}
	
	public function close_connection(){
		if(isset($this->connection)){
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	
	public function query($sql){
		global $connection;
		$this->last_query = $sql;
		$result = mysqli_query($this->connection, $sql);
		$this->confirm_query($result);
		return $result;
	}
	
	private function confirm_query($result){
		if(!$result){
			$output = "Database query failed: ".mysqli_connect_error()."<br />";
			$output .= "Last SQL query: ".$this->last_query;
			die($output);
		}
	}
	
	public function escape_value($value){
		$value = stripslashes($value);
		$value = mysqli_real_escape_string($this->connection, $value);
		return $value;
	}
	
	//database neutral methods
	public function fetch_array($result_set){
		return mysqli_fetch_array($result_set);
	}
	
	public function num_rows($result_set){
		return mysqli_num_rows($result_set);
	}
	
	public function affected_rows(){
		return mysqli_affected_rows($this->connection);
	}
	
}
	
$database = new MySQLDatabase();
//adding an "alias"
$db =& $database;
	
?>
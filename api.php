<?php

require_once("database.php");

class User {
    
	protected static $table_name="users";
	protected static $db_fields = array('id', 'username', 'email');

	public $id;
	public $username;
	public $email;

        public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
        }
  
        public static function find_by_id($id=0) {
                $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? $result_array : $result_array=("Status: 0 for id:".$id);
        }
  
        public static function find_by_sql($sql="") {
                global $database;
                $result_set = $database->query($sql);
                $object_array = array();
                while ($row = $database->fetch_array($result_set)) {
                  $object_array[] = self::instantiate($row);
                }
                return $object_array;
        }
	protected function attributes() { 
		// return an array of attribute names and their values
                $attributes = array();
                foreach(self::$db_fields as $field) {
                  if(property_exists($this, $field)) {
                    $attributes[$field] = $this->$field;
                  }
                }
                return $attributes;
	}
	
	protected function escaped_attributes() {
                global $database;
                $clean_attributes = array();
                // sanitize the values before submitting
                // Note: does not alter the actual value of each attribute
                foreach($this->attributes() as $key => $value){
                  $clean_attributes[$key] = $database->escape_value($value);
                }
                return $clean_attributes;
	}
        private static function instantiate($record) {
		// Could check that $record exists and is an array
                $object = new self;
		// Simple, long-form approach:
		// $object->id 		= $record['id'];
		// $object->username 	= $record['username'];
		// $object->email 	= $record['email'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
        private function has_attribute($attribute) {
                // We don't care about the value, we just want to know if the key exists
                // Will return true or false
                return array_key_exists($attribute, $this->escaped_attributes());
	}
	public static function delete($id=0) {
		global $database;
                
                $sql = "DELETE FROM ".self::$table_name;
                $sql .= " WHERE id=". $database->escape_value($id);
                $sql .= " LIMIT 1";
                $database->query($sql);
                return ($database->affected_rows() == 1) ? $result_array=("Status 1 OK") : $result_array=("Status: 0 for id:".$id);
	}
}


$possible_methods = array("get_list", "get", "delete");
$value = "Not allowed method!";

$apikey = "9af4d8381781baccb0f915e554f8798d";

if(isset($_GET["api_key"]) && $_GET["api_key"] === $apikey){ 
        if (isset($_GET["action"]) && in_array($_GET["action"], $possible_methods)){
            switch ($_GET["action"]){
                case "get_list":
                  $value = User::find_all();
                  break;
                case "get":
                  if (isset($_GET["id"]))
                    $value = User::find_by_id($_GET["id"]);
                  else
                  $value = "Missing argument";
                  break;
                case "delete":
                  if (isset($_GET["id"])){
                     $value = User::delete($_GET["id"]);
                  }
                  else
                  $value = "Missing argument";
                  break;
            }
        } 
} else {
    $value = "Wrong or missing API key";
}

exit(json_encode($value));

?>


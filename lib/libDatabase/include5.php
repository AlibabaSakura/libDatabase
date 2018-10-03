<?php

define("SQLITE3_MODE", 1);
define("MYSQL_MODE", 2);

if(!function_exists('mysqli_connect'))
    throw new Exception("mysqli library not enabled.");

if(!class_exists('SQLite3'))
    throw new Exception("SQLite3 library not enabled.");

/**
 * Base Abstract Database Wrapper
 * Contains only the mode of the database and the handler
 */
abstract class DatabaseWrapper{

    private $mode;
    private $handler;

    public function __construct($mode){

        if(!is_int($mode))
            throw new Exception("INTERNAL MODE CODE INVALID");

        $this->mode = $mode;
    }

    public function getMode(){
        switch ($this->mode) {
            case SQLITE3_MODE:
                return "SQLite3 Mode";
            case MYSQL_MODE:
                return "MySQL Mode";
            default:
                return "You are not supposed to see this. Bug found";
        }
    }
}

/**
 * Class for MySQL handling implementation
 */

class SQLite3DatabaseWrapper extends DatabaseWrapper{

    public function __construct($filename, $config = []){

        if(!is_array($config))
            throw new Exception("Config is not an array");

        if(!is_string($filename))
            throw new Exception("Filename is not a string");

        $this->mode = SQLITE3_MODE;

        if(isset($config['read_only']) && $config['read_only']) 
            $mode = SQLITE3_OPEN_READONLY | SQLITE3_OPEN_CREATE;
        else
            $mode = (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

        $this->handler = new SQLite3($filename, $mode);
        $this->handler->busyTimeout(5000);
    }

    public function query($query){

        if(!is_string($query))
            throw new Exception("Query is not a string");

        //When a query is not a SELECT, just do return exec
        if(stripos($query, 'SELECT') === false)
			return $this->db_handle->exec($query);
			
		$result = $this->db_handle->query($query);
			
		if($result == false)
            //return false;
            throw new Exception($this->handler->lastErrorMsg());
			
		$out = [];
			
		while($row = $result->fetchArray(SQLITE3_BOTH))
			$out[] = $row;
			
		return $out;
    }

    public function __toString(){
        return "Database Wrapper : " + $this->getMode();
    }

    public function __destruct() {
        $this->handler->close();
    }
}


/**
 * Class for MySQL handling implementation
 */
class MySQLDatabaseWrapper extends DatabaseWrapper{

    public function __construct($config){

        if(!is_array($config))
            throw new Exception("Config is not an array");

        $this->mode = MYSQL_MODE;

        $this->handler = new mysqli($config['server'], $config['username'], $config['password'], $config['db_name']);
    
        if($this->handler->connect_error)
            throw new Exception("Connection failed. Follows error: " + $this->handler->connect_error);
    }

    public function query($query){

        if(!is_string($query))
            throw new Exception("Query is not a string");

        //When a query is not a SELECT, just do return exec
        if(stripos($query, 'SELECT') === false)
			return $this->db_handle->exec($query);
			
		$result = $this->db_handle->query($query);
			
		if($result == false)
            //return false;
            throw new Exception($this->handler->lastErrorMsg());

		$out = [];
			
		while($row = $result->fetch_assoc())
			$out[] = $row;
			
		return $out;
    }

    public function __toString(){
        return "Database Wrapper : " + $this->getMode();
    }

    public function __destruct() {
        $this->handler->close();
    }

}
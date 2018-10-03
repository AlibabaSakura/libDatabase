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

    public function __construct(int $mode){
        $this->mode = $mode;
    }

    public function getMode() : string{
        switch ($this->mode) {
            case 1:
                return "SQLite3 Mode";
            case 2:
                return "MySQL Mode";
            default:
                return "You are not supposed to see this. Bug found";
        }
    }

}

class SQLite3DatabaseWrapper extends DatabaseWrapper{

    public function __construct(string $filename, array $config = []){
        $this->mode = 1;

        if(isset($config['read_only']) && $config['read_only']) 
            $mode = SQLITE3_OPEN_READONLY | SQLITE3_OPEN_CREATE;
        else
            $mode = (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

        $this->handler = new SQLite3($filename, $mode);
        $this->handler->busyTimeout(5000);
    }

    public function query(string $query){
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

    public function __toString() : string{
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

    public function __construct(array $config){
        $this->mode = 2;

        $this->handler = new mysqli($config['server'], $config['username'], $config['password'], $config['db_name']);
    
        if($this->handler->connect_error)
            throw new Exception("Connection failed. Follows error: " + $this->handler->connect_error);
    }

    public function query(string $query){
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

    public function __toString() : string{
        return "Database Wrapper : " + $this->getMode();
    }

    public function __destruct() {
        $this->handler->close();
    }

}
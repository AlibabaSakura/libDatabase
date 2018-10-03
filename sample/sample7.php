<?php
//PHP 7.0 or higher (recommended)
include_once __DIR__ . "/lib/libDatabase/include.php";

try{
    $my_handler = new SQLite3DatabaseWrapper("./db/db_sample.db");
    print($my_handler->getMode());
}catch(Exception $e){
    echo $e;
}
?>
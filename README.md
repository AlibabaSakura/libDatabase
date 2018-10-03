# libDatabase

Small and lightweight library for query access on a MySQL/SQLite3 database

Requirements:
* PHP 7.0 or higher (There is a PHP 5 version but for security reasons, PHP >=7 is recommended)
* SQLite3 library enabled on php.ini
* mysqli library enabled on php.ini

## Usage:

For import the library use the following code:

```php
<?php
//PHP 7.0 or higher (recommended)
include_once "./lib/libDatabase/include.php";
//PHP 5 version (not recommended)
include_once "./lib/libDatabase/include5.php";
?>
```

### - MySQL mode:

```php
<?php

$config = [
    "server" => "localhost:3000", //Server address
    "username" => "username",     //MySQL's username
    "password" => "password",     //MySQL's password
    "database" => "db_sample"     //Database's name
];

//Creating an object for the database
$my_handler = new MySQLDatabaseWrapper($config);

//Making a "SELECT" query
$sql = "SELECT * FROM table_1";
$result = $my_handler->query($sql);
?>
```

#### Functions of MySQL class

* `$obj->query(string $query)` : Execute a SQL query through this method. It throws an Exception if table looks like not existing.
* `$obj->__toString()` : Get information about this class (access data are not stored inside the class if not inside the mysqli private handler).
* `$obj->getMode()` : Obtain the type of database in use.

### - SQLite3 mode:

```php
<?php

$config = [
    "read_only" => false //If you want keep the database as read-only. Default is false.
];

//Creating an object for the database
$my_handler = new SQLite3DatabaseWrapper("./db/db_envi.db", $config);

//Making a "SELECT" query
$sql = "SELECT * FROM table_1";
$result = $my_handler->query($sql);
?>
```

#### Functions of SQLite3 class

* `$obj->query(string $query)` : Execute a SQL query through this method. It throws an Exception if table looks like not existing.
* `$obj->__toString()` : Get information about this class (access data are not stored inside the class if not inside the mysqli private handler).
* `$obj->getMode()` : Obtain the type of database in use.

#### Pro tip

You can obmit the configuration array. It will make the database writeable as default.

```php
<?php
$my_handler = new SQLite3DatabaseWrapper("./db/db_envi.db");
?>
```
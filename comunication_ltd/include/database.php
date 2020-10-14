<?php

// Set up connection to the database.
$username = 'root';
$password ='';
$db_name = 'comunication_ltd';

$DSN = "mysql:host = localhost; dbname=$db_name";
$db = new PDO($DSN,$username,$password);


?>
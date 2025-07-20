<?php
$username = "root";
$db = "taskmanager";
$password = '';
$hostname = "localhost";

$conn = mysqli_connect($hostname, $username, $password, $db);

if (!$conn) {
    echo "connection unsuccessfull with the database";
}

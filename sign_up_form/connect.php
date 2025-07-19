<?php

$HOSTNAME = 'localhost'; //127.0.0.1
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'sign_up_form';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if ($conn) {
    // echo "database connected successfully";
    // echo "continue the operations";
} else {
    die(mysqli_error($conn));
}

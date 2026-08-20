<?php

$host = "127.0.0.1";
$username = "root";
$password = "root";
$database = "employee_payroll";
$port = 8889;

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>
<?php

$host = "db";
$user = "demo";
$password = "demopassword";
$database = "sqldemo";

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conn->connect_error) {

    http_response_code(500);

    die("Database connection failed");
}

$conn->set_charset("utf8mb4");

<?php

$dbData = [
    "host" => $_ENV["DB_HOST"],
    "name" => $_ENV["DB_NAME"],
    "user" => $_ENV["DB_USER"],
    "pass" => $_ENV["DB_PASS"],
    "port" => $_ENV["DB_PORT"]
];

$db = mysqli_connect($dbData["host"],$dbData["user"],$dbData["pass"],$dbData["name"],$dbData["port"]);

if(!$db) {
    $errMsg = "An error occourred during database connection: ";
    $error = mysqli_connect_error();
    $errNo = mysqli_connect_errno();
    throw new \Exception($errMsg . $error . " | " . $errNo);
    exit;
}
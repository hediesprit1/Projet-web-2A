<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "web2A";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

?>
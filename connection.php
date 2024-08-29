<?php
$host="localhost:4306";
$username= "root";
$password= "";
$database = "customers";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

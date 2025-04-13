<?php
$api_key = "JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe";

$host = "localhost";
$db = "assistant_data";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
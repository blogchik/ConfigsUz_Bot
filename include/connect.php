<?php

// SQL Info
$servername = "localhost";
$username = "user";
$password = "3472004jabab";
$dbname = "configsuz";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {

    echo "Connection Error: " . $conn->connect_error;

    exit();

}

?>
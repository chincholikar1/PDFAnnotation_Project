<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default XAMPP password is blank
$dbname = "interview_db";  // aapke DB ka naam

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

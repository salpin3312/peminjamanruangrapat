<?php
$host = 'localhost';
$username = 'root'; // ganti dengan username MySQL Anda
$password = ''; // ganti dengan password MySQL Anda
$dbname = 'db_room';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<?php
define('DATABASE', 'quiz_app');
define('HOST', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('TABLE_PREFIX', 'qa_');

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
$data = [];

if ($conn->connect_error) {
    die('Database connection error.');
}
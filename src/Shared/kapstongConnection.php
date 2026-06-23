<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "trackingsystem";


// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection Failed : " . $conn->connect_error);
// }

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME'],
    $_ENV['DB_PORT']
);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

var_dump($_ENV['DB_HOST']);

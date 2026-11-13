<?php
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
    error_log("DB Connection Failed: " . $conn->connect_error);
    die("Something went wrong. Please try again later."); 
}

 $conn->query("SET time_zone = '+08:00'");

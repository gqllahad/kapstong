<?php
require_once("kapstongConnection.php");

if(!isset($_GET['rfid'])){
    die("No QR Data");
}

$rfid = $_GET['rfid'];

$stmt = $conn->prepare("SELECT * FROM user WHERE rfid=?");
$stmt->bind_param("s", $rfid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    die("RFID NOT FOUND");
}

echo "Welcome " . $user['name'];
?>
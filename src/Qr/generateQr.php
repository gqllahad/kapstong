<?php
include "../phpqrcode/qrlib.php";
require_once("kapstongConnection.php");


$result = mysqli_query($conn, "SELECT rfid FROM user");

while($row = mysqli_fetch_assoc($result)){

    $rfid = $row['rfid'];

    $data = "http://192.168.1.39/kapstong/kapstong/php/scan.php?rfid=" . $rfid;

    $file = __DIR__ . "/../phpqrcodes/" . $rfid . ".png";

    QRcode::png($data, $file, QR_ECLEVEL_L, 5);
}

echo "✅ QR Codes Generated!";
?>
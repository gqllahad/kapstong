<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $studentID = mysqli_real_escape_string($conn, $_POST['studentID']);

    $rfid_uid = mysqli_real_escape_string($conn, $_POST['rfid_uid']);

    $check = mysqli_query($conn, "
        SELECT *
        FROM users
        WHERE rfid_uid = '$rfid_uid'
    ");

    if(mysqli_num_rows($check) > 0)
    {
        exit("RFID already registered");
    }

    $update = mysqli_query($conn, "
        UPDATE users
        SET
            rfid_uid = '$rfid_uid',
            rfid_status = 'Registered'
        WHERE studentID = '$studentID'
    ");

    if($update)
    {
        echo "success";
    }
    else
    {
        echo "Failed to register RFID";
    }
}
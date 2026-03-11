<?php
include "kapstongConnection.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_POST['login-submit'])) {
    $userEmail = $_POST["loginEmail"];
    $userPassword = $_POST["loginPassword"];

    $sql_prep = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql_prep->bind_param("s", $userEmail);
    $sql_prep->execute();

    $result = $sql_prep->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($userPassword, $row['password'])) {

            session_start();

            $_SESSION['user_id'] = $row['userID'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['isVerified'] = $row['isVerified'];
            $_SESSION['mobileNumber'] = $row['mobileNumber'];

            header("Location: trackerMain.php?login=Logging In..");
            exit();
        } else {

            header("Location: loginPhase.php?error=Incorrect+Password");
            exit();
        }
    } else {

        header("Location: loginPhase.php?error=ID+not+Found");
        exit();
    }
}

if (isset($_POST['signupForm'])) {

    //Student Information 
    $fName = $_POST['firstName'];
    $lName = $_POST['lastName'];
    $mName = $_POST['middleName'];
    $fullName = $lName . $fName . $mName;

    $email = $_POST['signEmail'];
    $mobile = $_POST['signTel'];
    $gender = $_POST['gender'];
    $birth = $_POST['signBirth'];

    // Academic Information
    $course = $_POST['signCourse'];
    $level = $_POST['signLevel'];
    $schoolYear = $_POST['signSY'];

    // Account Details
    $studentID = $_POST['studentID'];
    $studentID = trim($studentID);

    $password = password_hash($_POST['signPassword'], PASSWORD_DEFAULT);
}

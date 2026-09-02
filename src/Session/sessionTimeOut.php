<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 600; 

if (!isset($_SESSION['LAST_ACTIVITY'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
}

if (isset($_SESSION['LAST_ACTIVITY'])) {

    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();

        header("Location: logoutPhase.php");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

?>
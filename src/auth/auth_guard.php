<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireRole($roles = []) {


    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        http_response_code(403);
        exit("Unauthorized");
    }

    if (!in_array($_SESSION['role'], $roles)) {
        http_response_code(403);
        exit("Access denied");
    }
}
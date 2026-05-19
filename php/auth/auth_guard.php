<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireRole($roles = []) {

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        http_response_code(403);
        exit("Unauthorized");
    }

    if (!in_array($_SESSION['role'], $roles)) {
        http_response_code(403);
        exit("Access denied");
    }
}
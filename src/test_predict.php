<?php

header('Content-Type: text/plain');

// require_once __DIR__ . '/src/Shared/functions.php';
// require_once __DIR__ . '/src/Shared/kapstongConnection.php';
require_once __DIR__ . '/Shared/kapstongConnection.php';
require_once __DIR__ . '/Shared/functions.php';


print_r(getStudentForecast($conn, '12345'));

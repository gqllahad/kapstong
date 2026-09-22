<?php
    require_once("../../auth/supervisor_auth.php");
    require_once("../../Shared/kapstongConnection.php");
    require_once("../../Shared/functions.php");
    echo renderAttendanceCalendar($conn, (int)$_POST['year'], (int)$_POST['month']);

?>
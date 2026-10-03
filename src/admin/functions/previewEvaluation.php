<?php
require_once("../../Shared/kapstongConnection.php");
require_once("../../auth/admin_auth.php");
require_once("../../Shared/functions.php");

$id = (int)($_GET['id'] ?? 0);
$row = $id ? getEvaluationData($conn, $id) : null;

echo $row ? buildEvaluationHTML($row) : "<p>Evaluation not found.</p>";
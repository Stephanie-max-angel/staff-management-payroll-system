<?php

session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


if (isset($_GET['id'])) {

    $payroll_id = intval($_GET['id']);

    $stmt = $conn->prepare("
        UPDATE payroll
        SET payment_status = 'Paid'
        WHERE payroll_id = ?
    ");

    $stmt->bind_param(
        "i",
        $payroll_id
    );

    $stmt->execute();

    $stmt->close();
}


header("Location: payroll.php");

exit();

?>
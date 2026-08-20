<?php
session_start();
require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: leave-requests.php");
    exit();
}

$leave_id = intval($_GET['id']);
$admin_id = $_SESSION['admin_id'];




$stmt = $conn->prepare("
SELECT status
FROM leave_requests
WHERE leave_id = ?
");

$stmt->bind_param("i", $leave_id);
$stmt->execute();

$result = $stmt->get_result();
$leave = $result->fetch_assoc();

$stmt->close();

if (!$leave) {
    header("Location: leave-requests.php");
    exit();
}




if($leave['status'] != "Pending"){

    header("Location: leave-requests.php");

    exit();

}




$stmt = $conn->prepare("
UPDATE leave_requests

SET

status='Approved',

approval_date = NOW(),

processed_by = ?

WHERE leave_id = ?
");

$stmt->bind_param("ii", $admin_id, $leave_id);





if($stmt->execute()){

    header("Location: leave-request.php?approved=1");

    exit();

}else{

    echo "<script>

    alert('Unable to approve leave.');

    window.location='leave-request.php';

    </script>";

}

$stmt->close();

$conn->close();
?>
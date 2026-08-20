<?php
session_start();
require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: leave-types.php");
    exit();
}

$leave_type_id = intval($_GET['id']);

// Prevent deletion if the leave type has been used
$check = $conn->prepare("
SELECT leave_id
FROM leave_requests
WHERE leave_type_id = ?
LIMIT 1
");

$check->bind_param("i", $leave_type_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0){

    echo "<script>
    alert('This leave type is already in use and cannot be deleted.');
    window.location='leave-types.php';
    </script>";

    exit();
}

$check->close();

$stmt = $conn->prepare("
DELETE FROM leave_types
WHERE leave_type_id = ?
");

$stmt->bind_param("i", $leave_type_id);

if($stmt->execute()){

    header("Location: leave-types.php?deleted=1");
    exit();

}else{

    echo "<script>
    alert('Unable to delete leave type.');
    window.location='leave-types.php';
    </script>";
}

$stmt->close();
$conn->close();
?>
<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: positions.php");
    exit();
}

$id = intval($_GET['id']);


// check if employee use this position

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM employees
    WHERE position_id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$stmt->close();

// Stop the Deletion if Employees Exist

if ($row['total'] > 0) {

    echo "<script>

        alert('This position cannot be deleted because employees are assigned to it.');

        window.location='positions.php';

    </script>";

    exit();

}


// Delete the Position
$stmt = $conn->prepare("
    DELETE FROM positions
    WHERE position_id = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: positions.php?deleted=1");
    exit();

} else {

    echo "<script>

        alert('Unable to delete the position.');

        window.location='positions.php';

    </script>";

}

$stmt->close();

$conn->close();
?>

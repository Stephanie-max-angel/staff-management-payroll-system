<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: departments.php");
    exit();
}

$id = intval($_GET['id']);


$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM employees
    WHERE department_id = ?
");

// Check if Employees Belong to this Department

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$stmt->close();

// If Employees Exist

if ($row['total'] > 0) {

    echo "<script>

        alert('This department cannot be deleted because employees are assigned to it.');

        window.location='departments.php';

    </script>";

    exit();

}

// Delete the Department

$stmt = $conn->prepare("
    DELETE FROM departments
    WHERE department_id = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: departments.php?deleted=1");
    exit();

} else {

    echo "<script>

        alert('Unable to delete department.');

        window.location='departments.php';

    </script>";

}

$stmt->close();

$conn->close();
?>

<!-- Show Success Messages -->

<?php

if(isset($_GET['deleted'])){

?>

<div class="alert alert-success">

Department deleted successfully.

</div>

<?php

}

if(isset($_GET['updated'])){

?>

<div class="alert alert-success">

Department updated successfully.

</div>

<?php

}

if(isset($_GET['added'])){

?>

<div class="alert alert-success">

Department added successfully.

</div>

<?php

}

?>
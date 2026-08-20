<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";

if (!isset($_GET['id'])) {
    header("Location: positions.php");
    exit();
}

$id = intval($_GET['id']);


$stmt = $conn->prepare("
    SELECT *
    FROM positions
    WHERE position_id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$position = $result->fetch_assoc();

$stmt->close();

if (!$position) {
    header("Location: positions.php");
    exit();
}

// Update Position

if(isset($_POST['update'])){

    $department_id = intval($_POST['department_id']);
    $position_name = trim($_POST['position_name']);
    $basic_salary = $_POST['basic_salary'];
    $description = trim($_POST['description']);

    // Check for duplicate position
    $check = $conn->prepare("
        SELECT position_id
        FROM positions
        WHERE department_id = ?
        AND position_name = ?
        AND position_id <> ?
    ");

    $check->bind_param(
        "isi",
        $department_id,
        $position_name,
        $id
    );

    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){

        $error = "A position with this name already exists in the selected department.";

    }else{

        $update = $conn->prepare("
            UPDATE positions
            SET
                department_id=?,
                position_name=?,
                basic_salary=?,
                description=?
            WHERE position_id=?
        ");

        $update->bind_param(
            "isdsi",
            $department_id,
            $position_name,
            $basic_salary,
            $description,
            $id
        );

        if($update->execute()){

            header("Location: positions.php?updated=1");
            exit();

        }else{

            $error = "Unable to update position.";

        }

        $update->close();

    }

    $check->close();
}
?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Position</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header text-white"
style="background:#1C5FA2;">

<h3>Edit Position</h3>

</div>

<div class="card-body">


<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>


<form method="POST">

<div class="row">



<div class="col-md-6 mb-3">

<label class="form-label">

Department

</label>

<select
name="department_id"
class="form-select"
required>

<?php

$result = $conn->query("
SELECT department_id, department_name
FROM departments
ORDER BY department_name
");

while($department = $result->fetch_assoc()){

?>

<option
value="<?= $department['department_id']; ?>"

<?= ($department['department_id']==$position['department_id']) ? "selected" : ""; ?>>

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php } ?>

</select>

</div>


<div class="col-md-6 mb-3">

<label>

Position Name

</label>

<input

type="text"

name="position_name"

class="form-control"

value="<?= htmlspecialchars($position['position_name']); ?>"

required>

</div>


<div class="col-md-6 mb-3">

<label>

Basic Salary (₦)

</label>

<input

type="number"

step="0.01"

name="basic_salary"

class="form-control"

value="<?= $position['basic_salary']; ?>"

required>

</div>



<div class="col-md-6 mb-3">

<label>

Description

</label>

<textarea

name="description"

rows="4"

class="form-control"><?= htmlspecialchars($position['description']); ?></textarea>

</div>


<div class="col-12">

<button

type="submit"

name="update"

class="btn text-white"

style="background:#0291DA;">

Update Position

</button>

<a

href="positions.php"

class="btn btn-secondary">

Cancel

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</body>

</html>
<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";


if(isset($_POST['save'])){

    $department_id = intval($_POST['department_id']);
    $position_name = trim($_POST['position_name']);
    $basic_salary = $_POST['basic_salary'];
    $description = trim($_POST['description']);

    // Check if the position already exists in the selected department
    $check = $conn->prepare("
        SELECT position_id
        FROM positions
        WHERE department_id = ?
        AND position_name = ?
    ");

    $check->bind_param(
        "is",
        $department_id,
        $position_name
    );

    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){

        $error = "This position already exists in the selected department.";

    }else{

        $stmt = $conn->prepare("
            INSERT INTO positions
            (
                department_id,
                position_name,
                basic_salary,
                description
            )
            VALUES(?,?,?,?)
        ");

        $stmt->bind_param(
            "isds",
            $department_id,
            $position_name,
            $basic_salary,
            $description
        );

        if($stmt->execute()){

            header("Location: positions.php?added=1");
            exit();

        }else{

            $error = "Unable to save position.";

        }

        $stmt->close();

    }

    $check->close();
}
?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Add Position</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>Add Position</h3>

</div>

<div class="card-body">

<?php

if($error!=""){

?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php

}

?>

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

<option value="">

Select Department

</option>

<?php

$result = $conn->query("
    SELECT department_id,
           department_name
    FROM departments
    ORDER BY department_name
");

while($department = $result->fetch_assoc()){

?>

<option
value="<?= $department['department_id']; ?>">

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php

}

?>

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

required>

</div>


<div class="col-md-6 mb-3">

<label>

Description

</label>

<textarea

name="description"

rows="3"

class="form-control">

</textarea>

</div>

<div class="col-12">

<button

type="submit"

name="save"

class="btn text-white"

style="background:#0291DA;">

Save Position

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

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
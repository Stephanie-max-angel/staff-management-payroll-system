<?php
session_start();

require_once("../includes/auth.php");
require_once("../config/database.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}
$message = "";
$error = "";

if(isset($_POST['save'])){

    $department_name = trim($_POST['department_name']);
    $description = trim($_POST['description']);

    // Check if department already exists
    $check = $conn->prepare("SELECT department_id FROM departments WHERE department_name = ?");
    $check->bind_param("s", $department_name);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){

        $error = "Department already exists.";

    }else{

        $stmt = $conn->prepare("INSERT INTO departments (department_name, description) VALUES (?, ?)");

        $stmt->bind_param("ss", $department_name, $description);

        if($stmt->execute()){

            $message = "Department added successfully.";

        }else{

            $error = "Error adding department.";

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

<title>Add Department</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">



<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div class="card-header text-white"
style="background:#1C5FA2;">

<h3>Add Department</h3>

</div>

<div class="card-body">


<?php

if($message!=""){

?>

<div class="alert alert-success">

<?= $message; ?>

</div>

<?php

}

if($error!=""){

?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php

}

?>

<!-- DEPARTMENT FORM -->

<form method="POST">

<div class="mb-3">

<label class="form-label">

Department Name

</label>

<input

type="text"

name="department_name"

class="form-control"

required>

</div>

<!-- DESCRIPTION -->

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea

name="description"

class="form-control"

rows="5">

</textarea>

</div>

<button

type="submit"

name="save"

class="btn text-white"

style="background:#0291DA;">

Save Department

</button>

<a

href="departments.php"

class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>


<?php
session_start();

require_once("../config/database.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$message="";
$error="";

if(!isset($_GET['id'])){

    header("Location: departments.php");
    exit();

}

$id = intval($_GET['id']);

// LOAD DEPARTMENT
$stmt = $conn->prepare("SELECT * FROM departments WHERE department_id=?");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

$department=$result->fetch_assoc();

if(!$department){

    header("Location: departments.php");

    exit();

}

// UPDATE DEPARTMENT

if(isset($_POST['update'])){

    $department_name = trim($_POST['department_name']);
    $description = trim($_POST['description']);

    // Check if another department already has this name
    $check = $conn->prepare(
        "SELECT department_id
         FROM departments
         WHERE department_name=?
         AND department_id<>?"
    );

    $check->bind_param(
        "si",
        $department_name,
        $id
    );

    $check->execute();

    $check->store_result();

    if($check->num_rows>0){

        $error="Department name already exists.";

    }else{

        $update=$conn->prepare(

        "UPDATE departments

        SET

        department_name=?,

        description=?

        WHERE department_id=?"

        );

        $update->bind_param(

        "ssi",

        $department_name,

        $description,

        $id

        );

        if($update->execute()){

            header("Location: departments.php?updated=1");

            exit();

        }else{

            $error="Unable to update department.";

        }

    }

}
?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Department</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>Edit Department</h3>

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

<div class="mb-3">

<label class="form-label">

Department Name

</label>

<input

type="text"

name="department_name"

class="form-control"

required

value="<?= htmlspecialchars($department['department_name']); ?>">

</div>


<div class="mb-3">

<label>

Description

</label>

<textarea

name="description"

class="form-control"

rows="5"><?= htmlspecialchars($department['description']); ?></textarea>

</div>

<button

type="submit"

name="update"

class="btn text-white"

style="background:#0291DA;">

Update Department

</button>

<a

href="departments.php"

class="btn btn-secondary">

Cancel

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

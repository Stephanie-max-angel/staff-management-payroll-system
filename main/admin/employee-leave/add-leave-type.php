<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if(isset($_POST['save'])){

    $leave_name = trim($_POST['leave_name']);
    $max_days = intval($_POST['max_days']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if(empty($leave_name)){

        $error = "Leave type is required.";

    }else{

        // Check if leave type already exists
        $check = $conn->prepare("
            SELECT leave_type_id
            FROM leave_types
            WHERE leave_name = ?
        ");

        $check->bind_param("s",$leave_name);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){

            $error = "Leave type already exists.";

        }else{

            $stmt = $conn->prepare("
                INSERT INTO leave_types
                (
                    leave_name,
                    max_days,
                    description,
                    status
                )
                VALUES
                (?,?,?,?)
            ");

            $stmt->bind_param(
                "siss",
                $leave_name,
                $max_days,
                $description,
                $status
            );

            if($stmt->execute()){

                header("Location: leave-types.php?added=1");
                exit();

            }else{

                $error = "Unable to save leave type.";

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>




<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Leave Type</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">





<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div class="card-header text-white" style="background:#1C5FA2;">

<h3>Add Leave Type</h3>

</div>

<div class="card-body">




<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>





<form method="POST">

<div class="mb-3">

<label class="form-label">

Leave Type

</label>

<input

type="text"

name="leave_name"

class="form-control"

placeholder="Example: Annual Leave"

required>

</div>

<div class="mb-3">

<label class="form-label">

Maximum Days

</label>

<input

type="number"

name="max_days"

class="form-control"

min="1"

required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea

name="description"

rows="4"

class="form-control"

placeholder="Enter description..."></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

<button

type="submit"

name="save"

class="btn text-white"

style="background:#0291DA;">

Save Leave Type

</button>

<a

href="leave-types.php"

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






<?php if(isset($_GET['added'])){ ?>

<div class="alert alert-success">

Leave type added successfully.

</div>

<?php } ?>

<?php if(isset($_GET['updated'])){ ?>

<div class="alert alert-success">

Leave type updated successfully.

</div>

<?php } ?>

<?php if(isset($_GET['deleted'])){ ?>

<div class="alert alert-success">

Leave type deleted successfully.

</div>

<?php } ?>
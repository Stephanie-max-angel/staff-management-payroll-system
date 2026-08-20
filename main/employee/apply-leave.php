<?php
session_start();
require_once("../admin/config/database.php");
require("includes/auth.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");


if(!isset($_SESSION['employee_id'])){
    header("Location:login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

$error="";
$success="";





if(isset($_POST['apply'])){

    $leave_type_id = intval($_POST['leave_type']);

    $start_date = $_POST['start_date'];

    $end_date = $_POST['end_date'];

    $reason = trim($_POST['reason']);

    if(empty($reason)){

        $error="Reason is required.";

    }elseif(strtotime($end_date) < strtotime($start_date)){

        $error="End date cannot be before start date.";

    }else{

        $total_days = (strtotime($end_date)-strtotime($start_date))/86400 + 1;

        $stmt = $conn->prepare("
        INSERT INTO leave_requests
        (
            employee_id,
            leave_type_id,
            start_date,
            end_date,
            total_days,
            reason,
            status,
            created_at
        )

        VALUES
        (?,?,?,?,?,?, 'Pending', NOW())
        ");

        $stmt->bind_param(

        "iissis",

        $employee_id,

        $leave_type_id,

        $start_date,

        $end_date,

        $total_days,

        $reason

        );

        if($stmt->execute()){

            header("location: my-leaves.php");
            $success="Leave request submitted successfully.";

        }else{

            $error="Unable to submit leave request.";

        }

        $stmt->close();

    }

}
?>




<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>

Apply For Leave

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

background:#F2F4F5;

}

.card-header{

background:#27348E;

color:white;

}

</style>

</head>

<body>





<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header">

<h3>

Apply For Leave

</h3>

</div>

<div class="card-body">





 <?php

if($error!=""){

?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php }

if($success!=""){

?>

<div class="alert alert-success">

<?= htmlspecialchars($success); ?>

</div>

<?php } ?>



<form method="POST">

    <div class="mb-3">

        <label class="form-label">
            Leave Type
        </label>

        <select
            name="leave_type"
            class="form-select"
            required>

            <option value="">
                Choose Leave Type
            </option>

            <?php

            $result = mysqli_query($conn, "
                SELECT *
                FROM leave_types
                WHERE status='Active'
                ORDER BY leave_name
            ");

            while ($type = mysqli_fetch_assoc($result)) {

            ?>

                <option value="<?= $type['leave_type_id']; ?>">

                    <?= htmlspecialchars($type['leave_name']); ?>

                </option>

            <?php } ?>

        </select>

    </div>


    <div class="row">

        <div class="col-md-6">

            <label class="form-label">
                Start Date
            </label>

            <input
                type="date"
                name="start_date"
                id="start_date"
                class="form-control"
                required>

        </div>


        <div class="col-md-6">

            <label class="form-label">
                End Date
            </label>

            <input
                type="date"
                name="end_date"
                id="end_date"
                class="form-control"
                required>

        </div>

    </div>


    <div class="mt-3">

        <label class="form-label">
            Total Days
        </label>

        <input
            type="number"
            id="days"
            class="form-control"
            readonly>

    </div>


    <div class="mt-3">

        <label class="form-label">
            Reason
        </label>

        <textarea
            name="reason"
            rows="5"
            class="form-control"
            required></textarea>

    </div>


    <div class="mt-4">

        <button
            type="submit"
            name="apply"
            class="btn text-white"
            style="background:#0291DA;">

            Submit Leave

        </button>


        <a
            href="my-leaves.php"
            class="btn btn-secondary">

            Back

        </a>

    </div>

</form>




<script>

const start=document.getElementById("start_date");

const end=document.getElementById("end_date");

const days=document.getElementById("days");

function calculateDays(){

if(start.value && end.value){

const s=new Date(start.value);

const e=new Date(end.value);

const diff=(e-s)/(1000*60*60*24)+1;

days.value=diff>0?diff:0;

}

}

start.addEventListener("change",calculateDays);

end.addEventListener("change",calculateDays);

</script>

</body>

</html>
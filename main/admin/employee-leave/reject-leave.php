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

if (!isset($_GET['id'])) {
    header("Location: leave-requests.php");
    exit();
}

$leave_id = intval($_GET['id']);
$admin_id = $_SESSION['admin_id'];
$error = "";




$stmt = $conn->prepare("
SELECT
leave_requests.*,
employees.employee_number,
employees.first_name,
employees.last_name,
leave_types.leave_name

FROM leave_requests

INNER JOIN employees
ON leave_requests.employee_id = employees.employee_id

INNER JOIN leave_types
ON leave_requests.leave_type_id = leave_types.leave_type_id

WHERE leave_requests.leave_id = ?
");

$stmt->bind_param("i",$leave_id);
$stmt->execute();

$result = $stmt->get_result();
$leave = $result->fetch_assoc();

$stmt->close();

if(!$leave){

header("Location: leave-requests.php");
exit();

}

if($leave['status']!="Pending"){

header("Location: leave-requests.php");
exit();

}





if(isset($_POST['reject'])){

$reason = trim($_POST['reason']);

if(empty($reason)){

$error = "Please enter a rejection reason.";

}else{

$stmt = $conn->prepare("

UPDATE leave_requests

SET

status='Rejected',

rejected_reason=?,

processed_by=?,

approval_date=NOW()

WHERE leave_id=?

");

$stmt->bind_param(

"sii",

$reason,

$admin_id,

$leave_id

);

if($stmt->execute()){

header("Location: leave-request.php?rejected=1");

exit();

}else{

$error="Unable to reject leave.";

}

$stmt->close();

}

}
?>





<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Reject Leave</title>

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
style="background:#CF251E;">

<h3>

Reject Leave Request

</h3>

</div>

<div class="card-body">





<table class="table table-bordered">

<tr>

<th width="35%">

Employee

</th>

<td>

<?= htmlspecialchars($leave['employee_number']); ?>

-

<?= htmlspecialchars($leave['first_name']." ".$leave['last_name']); ?>

</td>

</tr>

<tr>

<th>

Leave Type

</th>

<td>

<?= htmlspecialchars($leave['leave_name']); ?>

</td>

</tr>

<tr>

<th>

Duration

</th>

<td>

<?= htmlspecialchars($leave['start_date']); ?>

to

<?= htmlspecialchars($leave['end_date']); ?>

</td>

</tr>

<tr>

<th>

Days

</th>

<td>

<?= $leave['total_days']; ?>

Day(s)

</td>

</tr>

</table>





<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>




<form method="POST">

<div class="mb-3">

<label class="form-label">

Reason For Rejection

</label>

<textarea

name="reason"

rows="5"

class="form-control"

placeholder="Enter reason..."

required></textarea>

</div>

<button

type="submit"

name="reject"

class="btn btn-danger">

Reject Leave

</button>

<a

href="leave-requests.php"

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
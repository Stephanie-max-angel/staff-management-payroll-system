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
?>




<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>My Leave Requests</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

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
<div class = "text-end">
<a href="apply-leave.php"
class = "btn btn-primary">
    Apply for Leave
</a>
</div>

<div class="card shadow">

<div class="card-header">

<h3>

My Leave Requests

</h3>
</div>

<div class="card-body">





<table
id="leaveTable"
class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>Leave Type</th>

<th>Start Date</th>

<th>End Date</th>

<th>Total Days</th>

<th>Status</th>

<th>Applied On</th>

<th>Action</th>

</tr>

</thead>

<tbody>





<?php

$stmt = $conn->prepare("

SELECT

leave_requests.*,

leave_types.leave_name

FROM leave_requests

INNER JOIN leave_types

ON leave_requests.leave_type_id =
leave_types.leave_type_id

WHERE employee_id=?

ORDER BY leave_id DESC

");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result=$stmt->get_result();

while($row=$result->fetch_assoc()){

?>




<tr>

<td>

<?= htmlspecialchars($row['leave_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['start_date']); ?>

</td>

<td>

<?= htmlspecialchars($row['end_date']); ?>

</td>

<td>

<?= $row['total_days']; ?>

Day(s)

</td>

<td>

<?php

if($row['status']=="Pending"){

?>

<span class="badge bg-warning">

Pending

</span>

<?php

}elseif($row['status']=="Approved"){

?>

<span class="badge bg-success">

Approved

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Rejected

</span>

<?php

}

?>

</td>

<td>

<?= htmlspecialchars($row['created_at']); ?>

</td>

<td>

<a

href="view-leave.php?id=<?= $row['leave_id'];?>"

class="btn btn-primary btn-sm">

View

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>





</div>

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>

new DataTable("#leaveTable");

</script>

</body>

</html>
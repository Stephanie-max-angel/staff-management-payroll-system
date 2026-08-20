<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}
?>




<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Leave Requests</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

</head>

<body class="bg-light">





<div class="container mt-5">

<div class="d-flex justify-content-between mb-4">

<h2>

Employee Leave Requests

</h2>
<a

href="leave-types.php"

class="btn text-white"

style="background:#0291DA;">

Manage Leaves

</a>

</div>




<?php

if(isset($_GET['approved'])){

?>

<div class="alert alert-success">

Leave approved successfully.

</div>

<?php }

if(isset($_GET['rejected'])){

?>

<div class="alert alert-danger">

Leave rejected successfully.

</div>

<?php }

?>





<table

id="leaveTable"

class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>Employee</th>

<th>Leave Type</th>

<th>Start Date</th>

<th>End Date</th>

<th>Total Days</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>





<?php

$sql="

SELECT

leave_requests.*,

employees.employee_number,

employees.first_name,

employees.last_name,

leave_types.leave_name

FROM leave_requests

INNER JOIN employees

ON leave_requests.employee_id=

employees.employee_id

INNER JOIN leave_types

ON leave_requests.leave_type_id=

leave_types.leave_type_id

ORDER BY leave_requests.leave_id DESC

";

$result=mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result)){

?>





<tr>

<td>

<strong>

<?= htmlspecialchars($row['employee_number']); ?>

</strong>

<br>

<?= htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

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

Days

</td>

<td>

<?php

if($row['status']=="Pending"){

?>

<span class="badge bg-warning">

Pending

</span>

<?php }

elseif($row['status']=="Approved"){

?>

<span class="badge bg-success">

Approved

</span>

<?php }

else{

?>

<span class="badge bg-danger">

Rejected

</span>

<?php } ?>

</td>

<td>

<a

href="view-leave.php?id=<?= $row['leave_id'];?>"

class="btn btn-info btn-sm">

View

</a>

<?php if($row['status']=="Pending"){ ?>

<a

href="approve-leave.php?id=<?= $row['leave_id'];?>"

class="btn btn-success btn-sm"

onclick="return confirm('Approve this leave request?')">

Approve

</a>

<a

href="reject-leave.php?id=<?= $row['leave_id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Reject this leave request?')">

Reject

</a>

<?php } ?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>






<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>

new DataTable("#leaveTable");

</script>

</body>

</html>
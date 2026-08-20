<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if(!isset($_SESSION['admin_id'])){
    header("Location:login.php");
    exit();
}
?>



<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Leave Types</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

</head>

<body class="bg-light">





<div class="container mt-5">

<div class="d-flex justify-content-between mb-4">

<h2>

Leave Types

</h2>
<a

href="leave-request.php"

class="btn btn-secondary"

style="margin-left: 750px;" >

Back

</a>
<a

href="add-leave-type.php"

class="btn text-white"

style="background:#0291DA;">

Add Leave Type

</a>

</div>




<table

id="leaveTable"

class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>ID</th>

<th>Leave Type</th>

<th>Maximum Days</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>




<?php

$result = mysqli_query(

$conn,

"SELECT * FROM leave_types ORDER BY leave_name"

);

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?= $row['leave_type_id']; ?></td>

<td><?= htmlspecialchars($row['leave_name']); ?></td>

<td><?= $row['max_days']; ?></td>

<td>

<?php

if($row['status']=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php }else{ ?>

<span class="badge bg-danger">

Inactive

</span>

<?php } ?>

</td>

<td>

<a

href="edit-leave-type.php?id=<?= $row['leave_type_id'];?>"

class="btn btn-warning btn-sm">

Edit

</a>

<a

href="delete-leave-type.php?id=<?= $row['leave_type_id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this leave type?')">

Delete

</a>

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
<?php
session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Employees</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

</head>

<body class="bg-light">


<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">

Employees

</h2>

<a
href="add-employee.php"
class="btn text-white"
style="background:#0291DA;">

Add Employee

</a>

</div>

<table
id="employeeTable"
class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>No.</th>

<th>Photo</th>

<th>Name</th>

<th>Department</th>

<th>Position</th>

<th>Email</th>

<th>Status</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php

$sql = "

SELECT

employees.*,

departments.department_name,

positions.position_name

FROM employees

INNER JOIN departments

ON employees.department_id =
departments.department_id

INNER JOIN positions

ON employees.position_id =
positions.position_id

ORDER BY employees.employee_id DESC

";

$result = mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result)){

?>


<tr>

<td>

<?= htmlspecialchars($row['employee_number']); ?>

</td>

<td>

<?php

if(!empty($row['photo'])){

?>

<img
src="../uploads/<?= htmlspecialchars($row['photo']); ?>"
width="50"
height="50"
class="rounded-circle">

<?php

}else{

?>

<img
src="../assets/images/default-user.png"
width="50"
height="50"
class="rounded-circle">

<?php

}

?>

</td>

<td>

<?= htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['department_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['position_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['email']); ?>

</td>

<td>

<?php

if($row['status']=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Inactive

</span>

<?php

}

?>

</td>

<td>

<a
href="view-employee.php?id=<?= $row['employee_id'];?>"
class="btn btn-info btn-sm">

View

</a>

<a
href="edit-employee.php?id=<?= $row['employee_id'];?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="delete-employee.php?id=<?= $row['employee_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this employee?');">

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

new DataTable("#employeeTable");

</script>

</body>

</html>

<?php
require_once("../includes/footer.php")
?>
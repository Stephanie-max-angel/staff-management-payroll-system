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

<!-- <!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Positions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

</head> -->

<body class="bg-light">

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">

Positions

</h2>

<a

href="add-positions.php"

class="btn text-white"

style="background:#0291DA;">

Add Position

</a>

</div>

<!-- Success Messages -->

<?php

if(isset($_GET['added'])){

?>

<div class="alert alert-success">

Position added successfully.

</div>

<?php

}

if(isset($_GET['updated'])){

?>

<div class="alert alert-success">

Position updated successfully.

</div>

<?php

}

if(isset($_GET['deleted'])){

?>

<div class="alert alert-success">

Position deleted successfully.

</div>

<?php

}

?>

<!-- Create the Table -->

<div class="table-responsive">
 <table

id="positionTable"

class="table table-hover table-bordered">

<thead
class="table-primary">

<tr>

<th>ID</th>

<th>Position</th>

<th>Department</th>

<th>Basic Salary</th>

<th>Actions</th>

</tr>

</thead>

<tbody>


<!-- Display Positions -->

<?php

$sql = "

SELECT

positions.position_id,

positions.position_name,

positions.basic_salary,

departments.department_name

FROM positions

INNER JOIN departments

ON positions.department_id =
departments.department_id

ORDER BY positions.position_name

";

$result = mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?= $row['position_id']; ?>

</td>

<td>

<?= htmlspecialchars($row['position_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['department_name']); ?>

</td>

<td>

₦<?= number_format($row['basic_salary'],2); ?>

</td>

<td>

<a

href="edit-positions.php?id=<?= $row['position_id']; ?>"

class="btn btn-warning btn-sm">

Edit

</a>

<a

href="delete-positions.php?id=<?= $row['position_id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this position?')">

Delete

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


<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>

new DataTable("#positionTable");

</script>

<!-- </body>

</html> -->

<?php require_once("../includes/footer.php"); ?>
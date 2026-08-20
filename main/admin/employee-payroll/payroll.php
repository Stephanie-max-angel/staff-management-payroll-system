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

<title>Payroll</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

</head>

<body class="bg-light">


<div class="container mt-5">

<div class="d-flex justify-content-between mb-4">

<h2>

Payroll Management

</h2>

<a

href="process-payroll.php"

class="btn text-white"

style="background:#0291DA;">

Generate Payroll

</a>

</div>



<table

id="payrollTable"

class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>Employee</th>

<th>Month</th>

<th>Year</th>

<th>Basic Salary</th>

<th>Allowances</th>

<th>Deductions</th>

<th>Tax</th>

<th>Pension</th>

<th>Net Salary</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>





<?php

$sql="

SELECT

payroll.*,

employees.first_name,

employees.last_name,

employees.employee_number

FROM payroll

INNER JOIN employees

ON payroll.employee_id=

employees.employee_id

ORDER BY payroll.payroll_id DESC

";

$result=mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result)){

?>



<tr>

<td>

<?= htmlspecialchars($row['employee_number']); ?>

<br>

<?= htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['payroll_month']); ?>

</td>

<td>

<?= htmlspecialchars($row['payroll_year']); ?>

</td>

<td>

₦<?= number_format($row['basic_salary'],2); ?>

</td>

<td>

₦<?= number_format($row['allowances'],2); ?>

</td>

<td>

₦<?= number_format($row['deductions'],2); ?>

</td>


<td>

₦<?= number_format($row['tax'],2); ?>

</td>


<td>

₦<?= number_format($row['pension'],2); ?>

</td>



<td>

<strong>

₦<?= number_format($row['net_salary'],2); ?>

</strong>

</td>

<td>

<?php

if($row['payment_status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php }else{ ?>

<span class="badge bg-warning">

Pending

</span>

<?php } ?>

</td>

<td>

<a

href="view-payroll.php?id=<?= $row['payroll_id'];?>"

class="btn btn-info btn-sm">

View

</a>

<a

href="delete-payroll.php?id=<?= $row['payroll_id'];?>"

class="btn btn-danger btn-sm">

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

new DataTable("#payrollTable");

</script>

</body>

</html>


<?php require_once("../includes/footer.php"); ?>
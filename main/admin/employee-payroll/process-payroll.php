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



if(isset($_POST['generate'])){

    $employee_id = intval($_POST['employee_id']);

    $month = $_POST['payroll_month'];

//     echo "<pre>";
// var_dump($_POST['payroll_month']);
// exit();

    $year = intval($_POST['payroll_year']);



  $check = $conn->prepare("
SELECT payroll_id
FROM payroll
WHERE employee_id=?
AND payroll_month=?
AND payroll_year=?
");

$check->bind_param(
"isi",
$employee_id,
$month,
$year
);

$check->execute();

$check->store_result();

if($check->num_rows>0){

$error="Payroll has already been generated for this employee.";

}else{  



$stmt = $conn->prepare("

SELECT

positions.basic_salary

FROM employees

INNER JOIN positions

ON employees.position_id=

positions.position_id

WHERE employee_id=?

");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result=$stmt->get_result();

$salary=$result->fetch_assoc();

$stmt->close();

$basic_salary=$salary['basic_salary'];




$stmt = $conn->prepare("

SELECT

SUM(allowances.amount) AS total

FROM employee_allowances

INNER JOIN allowances

ON employee_allowances.allowance_id=

allowances.allowance_id

WHERE employee_allowances.employee_id=?

");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result=$stmt->get_result();

$row=$result->fetch_assoc();

$allowances=$row['total'] ?? 0;

$stmt->close();




$stmt = $conn->prepare("

SELECT

SUM(deductions.amount) AS total

FROM employee_deductions

INNER JOIN deductions

ON employee_deductions.deduction_id=

deductions.deduction_id

WHERE employee_deductions.employee_id=?

");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result=$stmt->get_result();

$row=$result->fetch_assoc();

$deductions=$row['total'] ?? 0;

$stmt->close();




$settings = $conn->query("SELECT tax_percentage FROM payroll_settings LIMIT 1");
$config = $settings->fetch_assoc();

$tax = ($basic_salary * $config['tax_percentage']) / 100;




$net_salary =
$basic_salary
+
$allowances
-
$deductions
-
$tax
-
$pension;








$stmt=$conn->prepare("

INSERT INTO payroll(

employee_id,

payroll_month,

payroll_year,

basic_salary,

allowances,

deductions,

tax,

net_salary

)

VALUES(

?,?,?,?,?,?,?,?

)

");

$stmt->bind_param(

"isiddddd",

$employee_id,

$month,

$year,

$basic_salary,

$allowances,

$deductions,

$tax,

$net_salary

);



if($stmt->execute()){

$success="Payroll generated successfully.";

}else{

$error="Unable to generate payroll.";

}

$stmt->close();

}

$check->close();

}
?>



<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Generate Payroll</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">




<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>Generate Payroll</h3>

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

if($success!=""){

?>

<div class="alert alert-success">

<?= $success; ?>

</div>

<?php

}

?>





<form method="POST">




<div class="mb-3">

<label>

Employee

</label>

<select

name="employee_id"

class="form-select"

required>

<option value="">Select Employee</option>

<?php

$result=mysqli_query(

$conn,

"SELECT employee_id,
employee_number,
first_name,
last_name

FROM employees

WHERE status='Active'

ORDER BY first_name"

);

while($row=mysqli_fetch_assoc($result)){

?>

<option value="<?= $row['employee_id'];?>">

<?= htmlspecialchars(
$row['employee_number']." - ".$row['first_name']." ".$row['last_name']
); ?>

</option>

<?php } ?>

</select>

</div>





<div class="mb-3">

<label>

Payroll Month

</label>

<select
name="payroll_month"
class="form-select"
required>

<option>January</option>

<option>February</option>

<option>March</option>

<option>April</option>

<option>May</option>

<option>June</option>

<option>July</option>

<option>August</option>

<option>September</option>

<option>October</option>

<option>November</option>

<option>December</option>

</select>

</div>




<div class="mb-3">

<label>

Payroll Year

</label>

<input

type="number"

name="payroll_year"

class="form-control"

value="<?= date('Y');?>"

required>

</div>





<button

type="submit"

name="generate"

class="btn text-white"

style="background:#0291DA;">

Generate Payroll

</button>

<a

href="payroll.php"

class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
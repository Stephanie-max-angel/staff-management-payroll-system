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
    header("Location: payroll.php");
    exit();
}

$payroll_id = intval($_GET['id']);
$error = "";




$stmt = $conn->prepare("
SELECT *
FROM payroll
WHERE payroll_id = ?
");

$stmt->bind_param("i", $payroll_id);

$stmt->execute();

$result = $stmt->get_result();

$payroll = $result->fetch_assoc();

$stmt->close();

if (!$payroll) {
    header("Location: payroll.php");
    exit();
}




if(isset($_POST['update'])){

    $allowances = floatval($_POST['allowances']);

    $deductions = floatval($_POST['deductions']);

    $tax = floatval($_POST['tax']);

    $status = $_POST['payment_status'];

    $basic_salary = $payroll['basic_salary'];

    $net_salary =
        $basic_salary +
        $allowances -
        $deductions -
        $tax;





        $stmt = $conn->prepare("

UPDATE payroll

SET

allowances=?,

deductions=?,

tax=?,

net_salary=?,

payment_status=?

WHERE payroll_id=?

");

$stmt->bind_param(

"ddddsi",

$allowances,

$deductions,

$tax,

$net_salary,

$status,

$payroll_id

);




if($stmt->execute()){

header("Location: payroll.php?updated=1");

exit();

}else{

$error="Unable to update payroll.";

}

$stmt->close();

}
?>






<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Payroll</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">





<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>

Edit Payroll

</h3>

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

?>






<form method="POST">





<div class="mb-3">

<label>

Basic Salary

</label>

<input

type="text"

class="form-control"

value="₦<?= number_format($payroll['basic_salary'],2); ?>"

readonly>

</div>






<div class="mb-3">

<label>

Allowances

</label>

<input

type="number"

step="0.01"

name="allowances"

class="form-control"

value="<?= $payroll['allowances']; ?>"

required>

</div>






<div class="mb-3">

<label>

Deductions

</label>

<input

type="number"

step="0.01"

name="deductions"

class="form-control"

value="<?= $payroll['deductions']; ?>"

required>

</div>





<div class="mb-3">

<label>

Tax

</label>

<input

type="number"

step="0.01"

name="tax"

class="form-control"

value="<?= $payroll['tax']; ?>"

required>

</div>





<div class="mb-3">

<label>

Payment Status

</label>

<select

name="payment_status"

class="form-select">

<option
value="Pending"

<?= ($payroll['payment_status']=="Pending") ? "selected" : ""; ?>>

Pending

</option>

<option
value="Paid"

<?= ($payroll['payment_status']=="Paid") ? "selected" : ""; ?>>

Paid

</option>

</select>

</div>






<button

type="submit"

name="update"

class="btn text-white"

style="background:#0291DA;">

Update Payroll

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





<?php

if(isset($_GET['updated'])){

?>

<div class="alert alert-success">

Payroll updated successfully.

</div>

<?php

}

?>
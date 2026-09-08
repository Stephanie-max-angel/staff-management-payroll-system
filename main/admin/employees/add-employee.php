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

$employeeNumber = "EMP0001";

$result = mysqli_query(
    $conn,
    "SELECT employee_number
     FROM employees
     ORDER BY employee_id DESC
     LIMIT 1"
);

if ($row = mysqli_fetch_assoc($result)) {

    $number = intval(substr($row['employee_number'], 3));

    $number++;

    $employeeNumber = "EMP" . str_pad($number, 4, "0", STR_PAD_LEFT);

}

if(isset($_POST['save'])){

    $employee_number = $_POST['employee_number'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $department_id = intval($_POST['department_id']);
    $position_id = intval($_POST['position_id']);

    $employment_type = $_POST['employment_type'];
    $employment_date = $_POST['employment_date'];

    $username = trim($_POST['username']);

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $status = $_POST['status'];


    $check = $conn->prepare("
SELECT employee_id
FROM employees
WHERE email = ?
OR username = ?
");

$check->bind_param(
    "ss",
    $email,
    $username
);

$check->execute();

$check->store_result();

if($check->num_rows > 0){

    $error = "Email or Username already exists.";

}else{


$photo = "";

if(!empty($_FILES['photo']['name'])){

    $photo = time()."_".$_FILES['photo']['name'];

    move_uploaded_file(

        $_FILES['photo']['tmp_name'],

        "../uploads/".$photo

    );

}

$stmt = $conn->prepare("

INSERT INTO employees(

employee_number,

first_name,

last_name,

gender,

date_of_birth,

email,

phone,

address,

department_id,

position_id,

employment_type,

employment_date,

username,

password,

photo,

status

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?

)

");

$stmt->bind_param(

"ssssssssiissssss",

$employee_number,

$first_name,

$last_name,

$gender,

$dob,

$email,

$phone,

$address,

$department_id,

$position_id,

$employment_type,

$employment_date,

$username,

$password,

$photo,

$status

);


if($stmt->execute()){

    header("Location: employees.php?added=1");
    exit();

}else{

    $error = "Unable to save employee.";

}

$stmt->close();

}

$check->close();

}
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Employee</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">


<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="card shadow">

                <div class="card-header text-white"
                     style="background:#1C5FA2;">

                    <h3>Add New Employee</h3>

                </div>

                <div class="card-body">


                <?php if($error != ""){ ?>

<div class="alert alert-danger">

    <?= $error; ?>

</div>

<?php } ?>


<form method="POST" enctype="multipart/form-data">

<div class="row">



<div class="col-md-4 mb-3">

<label class="form-label">

Employee Number

</label>

<input
type="text"
name="employee_number"
class="form-control"
value="<?= $employeeNumber; ?>"
readonly>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

First Name

</label>

<input
type="text"
name="first_name"
class="form-control"
required>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

Last Name

</label>

<input
type="text"
name="last_name"
class="form-control"
required>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

Gender

</label>

<select
name="gender"
class="form-select"
required>

<option value="">Select Gender</option>

<option value="Male">Male</option>

<option value="Female">Female</option>

</select>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

Date of Birth

</label>

<input
type="date"
name="date_of_birth"
class="form-control"
required>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Phone Number

</label>

<input
type="text"
name="phone"
class="form-control"
required>

</div>



<div class="col-md-8 mb-3">

<label class="form-label"> 

Address

</label>

<textarea
name="address"
class="form-control"
rows="3"
required></textarea>

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Department

</label>

<select
name="department_id"
class="form-select"
required>

<option value="">Select Department</option>

<?php

$departments = $conn->query("
SELECT department_id, department_name
FROM departments
ORDER BY department_name
");

while($department = $departments->fetch_assoc()){

?>

<option value="<?= $department['department_id']; ?>">

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php } ?>

</select>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Position

</label>

<select
name="position_id"
class="form-select"
required>

<option value="">Select Position</option>

<?php

$positions = $conn->query("
SELECT position_id, position_name
FROM positions
ORDER BY position_name
");

while($position = $positions->fetch_assoc()){

?>

<option value="<?= $position['position_id']; ?>">

<?= htmlspecialchars($position['position_name']); ?>

</option>

<?php } ?>

</select>

</div>


<div class="col-md-6 mb-3">

    <label class="form-label">
        Employment Type
    </label>

    <select
        name="employment_type"
        class="form-select"
        required>

        <option value="">Select Employment Type</option>

        <option value="Full-Time">Full-Time</option>

        <option value="Part-Time">Part-Time</option>

        <option value="Contract">Contract</option>

        <option value="Intern">Intern</option>

    </select>

</div>


<div class="col-md-6 mb-3">

    <label class="form-label">

        Employment Date

    </label>

    <input
        type="date"
        name="employment_date"
        class="form-control"
        required>

</div>


<div class="col-md-6 mb-3">

    <label class="form-label">

        Username

    </label>

    <input
        type="text"
        name="username"
        class="form-control"
        required>

</div>


<div class="col-md-6 mb-3">

    <label class="form-label">

        Password

    </label>

    <input
        type="password"
        name="password"
        class="form-control"
        required>

</div>


<div class="col-md-6 mb-3">

    <label class="form-label">

        Status

    </label>

    <select
        name="status"
        class="form-select"
        required>

        <option value="Active">

            Active

        </option>

        <option value="Inactive">

            Inactive

        </option>

    </select>

</div>

<div class="col-md-6 mb-3">

    <label class="form-label">

        Employee Photo

    </label>

    <input
        type="file"
        name="photo"
        class="form-control"
        accept="image/*">

</div>


<div class="col-12 mt-3">

    <button
        type="submit"
        name="save"
        class="btn text-white"
        style="background:#0291DA;">

        <i class="bi bi-check-circle"></i>

        Save Employee

    </button>

    <a
        href="employees.php"
        class="btn btn-secondary">

        Cancel

    </a>

</div>
<?php
require_once("../includes/sidebar.php");
?>

<!-- <div class="col-12 mt-3">

    <button
        type="submit"
        name="save"
        class="btn text-white"
        style="background:#0291DA;">

        <i class="bi bi-check-circle"></i>

        Save Employee

    </button>

    <a
        href="employees.php"
        class="btn btn-secondary">

        Cancel

    </a>

</div> -->
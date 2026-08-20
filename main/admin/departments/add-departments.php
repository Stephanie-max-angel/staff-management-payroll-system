<?php

session_start();

require_once("../config/database.php");

if(isset($_POST['save'])){

$name = trim($_POST['department_name']);

$description = trim($_POST['description']);

$stmt = $conn->prepare(

"INSERT INTO departments
(department_name,description)

VALUES(?,?)"

);

$stmt->bind_param("ss",$name,$description);

$stmt->execute();

header("Location: departments.php");

exit();

}

?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header text-white"
style="background:#1C5FA2;">

<h4>Add Department</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label>

Department Name

</label>

<input

type="text"

name="department_name"

class="form-control"

required>

</div>

<div class="mb-3">

<label>

Description

</label>

<textarea

name="description"

class="form-control"

rows="4">

</textarea>

</div>

<button

class="btn text-white"

style="background:#0291DA;"

name="save">

Save Department

</button>

</form>

</div>

</div>

</div>
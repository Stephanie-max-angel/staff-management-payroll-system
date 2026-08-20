<?php

session_start();
require_once("config/database.php");
// require_once("../includes/auth.php");
// require_once("../includes/header.php");
// require_once("../includes/sidebar.php");
// require_once("../includes/navbar.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s",$username);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1){

        $admin = $result->fetch_assoc();

        if(password_verify($password,$admin['password'])){

            $_SESSION['admin_id']=$admin['admin_id'];
            $_SESSION['admin_name']=$admin['fullname'];

            header("Location: dashboard.php");
            exit();

        }else{

            $error="Incorrect password.";

        }

    }else{

        $error="Administrator account not found.";

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Administrator Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container-fluid vh-100">

<div class="row h-100">

<div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center text-white"
style="background:#1C5FA2;">

<div class="text-center">

<h1 class="display-4 fw-bold">
Employee Management
</h1>

<h3>& Payroll System</h3>

<p class="mt-4">
Administrator Portal
</p>

</div>

</div>

<div class="col-lg-6 d-flex justify-content-center align-items-center">

<div class="card shadow-lg p-4" style="width:420px;border:none;border-radius:20px;">

<h2 class="text-center mb-4">Admin Login</h2>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Username</label>

<input
type="text"
name="username"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<div class="input-group">

<input
type="password"
id="password"
name="password"
class="form-control"
required>

<button
type="button"
class="btn btn-outline-secondary"
onclick="togglePassword()">

<i class="bi bi-eye"></i>

</button>

</div>

</div>

<button
class="btn w-100 text-white"
style="background:#0291DA;">

Login

</button>

</form>

</div>

</div>

</div>

</div>

<script>

function togglePassword(){

const password=document.getElementById("password");

if(password.type==="password"){

password.type="text";

}else{

password.type="password";

}

}

</script>

</body>

</html>



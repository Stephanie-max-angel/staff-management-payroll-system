
<?php

session_start();

require_once("../admin/config/database.php");

$error = "";

if(isset($_POST['login'])){

    $employee_number = trim($_POST['employee_number']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT *
        FROM employees
        WHERE employee_number = ?
    ");

    $stmt->bind_param("s", $employee_number);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1){

        $employee = $result->fetch_assoc();

        if(password_verify($password, $employee['password'])){

            $_SESSION['employee_id'] = $employee['employee_id'];
            $_SESSION['employee_name'] =
                $employee['first_name']." ".$employee['last_name'];

            header("Location: dashboard.php");
            exit();

        }else{

            $error = "Incorrect password.";

        }

    }else{

        $error = "Employee not found.";

    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>Employee Login</title>

<meta name="viewport"
content="width=device-width, initial-scale=1">


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Bootstrap Icons -->

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


<!-- Poppins -->

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


<style>

*{

    box-sizing:border-box;

    font-family:'Poppins', sans-serif;

}


body{

    margin:0;

    min-height:100vh;

    background:#f2f5f8;

    display:flex;

    align-items:center;

    justify-content:center;

}


/* =========================================
   LOGIN WRAPPER
========================================= */

.login-wrapper{

    width:100%;

    max-width:1050px;

    padding:30px;

}


/* =========================================
   LOGIN CARD
========================================= */

.login-card{

    background:white;

    border:none;

    border-radius:20px;

    overflow:hidden;

    box-shadow:0 15px 45px rgba(0,0,0,0.09);

}


/* =========================================
   LEFT BRANDING PANEL
========================================= */

.login-brand{

    background:#1C5FA2;

    color:white;

    min-height:520px;

    padding:50px 40px;

    display:flex;

    flex-direction:column;

    justify-content:center;

    position:relative;

    overflow:hidden;

}


/* Decorative circles */

.login-brand::before{

    content:"";

    position:absolute;

    width:230px;

    height:230px;

    border-radius:50%;

    background:rgba(255,255,255,0.06);

    top:-90px;

    right:-80px;

}


.login-brand::after{

    content:"";

    position:absolute;

    width:180px;

    height:180px;

    border-radius:50%;

    background:rgba(255,255,255,0.05);

    bottom:-70px;

    left:-60px;

}


.brand-icon{

    width:70px;

    height:70px;

    border-radius:18px;

    background:rgba(255,255,255,0.15);

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:32px;

    margin-bottom:25px;

}


.login-brand h1{

    font-size:30px;

    font-weight:600;

    margin-bottom:12px;

}


.login-brand p{

    font-size:14px;

    line-height:1.8;

    opacity:0.88;

    max-width:350px;

}


.brand-feature{

    display:flex;

    align-items:center;

    gap:12px;

    margin-top:30px;

    font-size:13px;

    opacity:0.9;

}


.brand-feature i{

    font-size:18px;

}


/* =========================================
   RIGHT LOGIN PANEL
========================================= */

.login-panel{

    padding:50px 45px;

    display:flex;

    flex-direction:column;

    justify-content:center;

    min-height:520px;

}


.login-panel h2{

    margin:0;

    font-size:25px;

    font-weight:600;

    color:#222;

}


.login-subtitle{

    margin-top:7px;

    margin-bottom:30px;

    font-size:13px;

    color:#7b8490;

}


/* =========================================
   ERROR MESSAGE
========================================= */

.alert{

    border-radius:9px;

    font-size:13px;

}


/* =========================================
   LOGIN FOOTER
========================================= */

.login-footer{

    text-align:center;

    margin-top:25px;

    color:#929aa5;

    font-size:12px;

}


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:768px){

    body{

        align-items:flex-start;

    }


    .login-wrapper{

        padding:20px;

        margin-top:20px;

    }


    .login-brand{

        min-height:auto;

        padding:35px 30px;

    }


    .login-brand h1{

        font-size:25px;

    }


    .login-panel{

        min-height:auto;

        padding:35px 30px;

    }

}

</style>

</head>


<body>


<div class="login-wrapper">

    <div class="login-card">

        <div class="row g-0">


            <!-- =========================================
                 BRANDING
            ========================================== -->

            <div class="col-md-5">

                <div class="login-brand">

                    <div class="brand-icon">

                        <i class="bi bi-person-workspace"></i>

                    </div>


                    <h1>
                        Employee Portal
                    </h1>


                    <p>

                        Welcome to the Employee Management System.
                        Sign in to access your employee services,
                        payslips and leave management.

                    </p>


                    <div class="brand-feature">

                        <i class="bi bi-shield-check"></i>

                        <span>
                            Secure employee access
                        </span>

                    </div>


                    <div class="brand-feature">

                        <i class="bi bi-receipt"></i>

                        <span>
                            Access your payslips
                        </span>

                    </div>


                    <div class="brand-feature">

                        <i class="bi bi-calendar-check"></i>

                        <span>
                            Manage your leave requests
                        </span>

                    </div>

                </div>

            </div>



            <!-- =========================================
                 LOGIN FORM
            ========================================== -->

            <div class="col-md-7">

                <div class="login-panel">


                    <h2>
                        Employee Login
                    </h2>


                    <p class="login-subtitle">

                        Enter your employee credentials
                        to continue.

                    </p>


                    <?php

                    if($error!=""){

                    ?>

                        <div class="alert alert-danger">

                            <?= htmlspecialchars($error); ?>

                        </div>

                    <?php

                    }

                    ?>


                    <form method="POST">


                        <div class="mb-3">

                            <label>
                                Employee Number
                            </label>

                            <input
                                type="text"
                                name="employee_number"
                                class="form-control"
                                required>

                        </div>


                        <div class="mb-3">

                            <label>
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>


                        <button
                            name="login"
                            class="btn btn-login w-100">

                            Login

                        </button>

                    </form>


                    <div class="login-footer">

                        <i class="bi bi-lock-fill"></i>

                        Your account information is securely protected.

                    </div>


                </div>

            </div>


        </div>

    </div>

</div>


<style>

/* Login button */

.btn-login{

    background:#1C5FA2;

    color:white;

    border:none;

    padding:11px;

    border-radius:8px;

    font-size:14px;

    font-weight:500;

    transition:all 0.2s ease;

}


.btn-login:hover{

    background:#154d85;

    color:white;

    transform:translateY(-1px);

}


/* Keep the existing form controls clean */

.login-panel label{

    font-size:13px;

    font-weight:500;

    color:#444;

    margin-bottom:7px;

}


.login-panel .form-control{

    border-radius:8px;

    font-size:13px;

}


</style>


<?php

require_once("includes/footer.php");

?>


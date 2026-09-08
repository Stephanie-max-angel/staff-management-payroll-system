
<?php

session_start();

require_once("main/admin/config/database.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    /*
    ==========================================
    ADMIN LOGIN
    ==========================================
    */

    if ($role === "admin") {

        $stmt = $conn->prepare("
            SELECT *
            FROM admins
            WHERE username = ?
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {

                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['role'] = 'admin';

                header("Location: main/admin/dashboard.php");
                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Administrator account not found.";

        }

        $stmt->close();
    }


    /*
    ==========================================
    EMPLOYEE LOGIN
    ==========================================
    */

    elseif ($role === "employee") {

        $stmt = $conn->prepare("
            SELECT *
            FROM employees
            WHERE employee_number = ?
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $employee = $result->fetch_assoc();

            if (password_verify($password, $employee['password'])) {

                $_SESSION['employee_id'] = $employee['employee_id'];

                $_SESSION['employee_name'] =
                    $employee['first_name'] . " " . $employee['last_name'];

                $_SESSION['role'] = 'employee';

                header("Location: /employee-management-system/main/employee/dashboard.php");
exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Employee account not found.";

        }

        $stmt->close();
    }

    else {

        $error = "Please select a user type.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login | Staff Management System</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


<style>

* {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    margin: 0;
    min-height: 100vh;
    background: #f2f5f8;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-wrapper {
    width: 100%;
    max-width: 1050px;
    padding: 30px;
}

.login-card {
    background: white;
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(0,0,0,0.09);
}

.login-brand {
    background: #1C5FA2;
    color: white;
    min-height: 520px;
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.login-brand::before {
    content: "";
    position: absolute;
    width: 230px;
    height: 230px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    top: -90px;
    right: -80px;
}

.login-brand::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    bottom: -70px;
    left: -60px;
}

.brand-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 25px;
}

.login-brand h1 {
    font-size: 30px;
    font-weight: 600;
    margin-bottom: 12px;
}

.login-brand p {
    font-size: 14px;
    line-height: 1.8;
    opacity: 0.88;
    max-width: 350px;
}

.brand-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 30px;
    font-size: 13px;
    opacity: 0.9;
}

.brand-feature i {
    font-size: 18px;
}

.login-panel {
    padding: 50px 45px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 520px;
}

.login-panel h2 {
    margin: 0;
    font-size: 25px;
    font-weight: 600;
    color: #222;
}

.login-subtitle {
    margin-top: 7px;
    margin-bottom: 30px;
    font-size: 13px;
    color: #7b8490;
}

.alert {
    border-radius: 9px;
    font-size: 13px;
}

.login-panel label {
    font-size: 13px;
    font-weight: 500;
    color: #444;
    margin-bottom: 7px;
}

.login-panel .form-control,
.login-panel .form-select {
    border-radius: 8px;
    font-size: 13px;
    padding: 11px;
}

.btn-login {
    background: #1C5FA2;
    color: white;
    border: none;
    padding: 11px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-login:hover {
    background: #154d85;
    color: white;
    transform: translateY(-1px);
}

.login-footer {
    text-align: center;
    margin-top: 25px;
    color: #929aa5;
    font-size: 12px;
}

@media(max-width:768px) {

    body {
        align-items: flex-start;
    }

    .login-wrapper {
        padding: 20px;
        margin-top: 20px;
    }

    .login-brand {
        min-height: auto;
        padding: 35px 30px;
    }

    .login-brand h1 {
        font-size: 25px;
    }

    .login-panel {
        min-height: auto;
        padding: 35px 30px;
    }

}

</style>

</head>


<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="row g-0">


            <!-- BRANDING -->

            <div class="col-md-5">

                <div class="login-brand">

                    <div class="brand-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>

                    <h1>
                        Staff Management
                    </h1>

                    <p>
                        Welcome to the Staff Management,
                        Employment and Payroll System.
                        Sign in to access your account.
                    </p>

                    <div class="brand-feature">

                        <i class="bi bi-shield-check"></i>

                        <span>
                            Secure system access
                        </span>

                    </div>


                    <div class="brand-feature">

                        <i class="bi bi-receipt"></i>

                        <span>
                            Manage payroll and payslips
                        </span>

                    </div>


                    <div class="brand-feature">

                        <i class="bi bi-calendar-check"></i>

                        <span>
                            Manage employee leave
                        </span>

                    </div>

                </div>

            </div>


            <!-- LOGIN FORM -->

            <div class="col-md-7">

                <div class="login-panel">

                    <h2>
                        Welcome Back
                    </h2>

                    <p class="login-subtitle">
                        Sign in to continue to your account.
                    </p>


                    <?php if ($error != "") { ?>

                        <div class="alert alert-danger">

                            <?= htmlspecialchars($error); ?>

                        </div>

                    <?php } ?>


                    <form method="POST">


                        <!-- USERNAME / EMPLOYEE NUMBER -->

                        <div class="mb-3">

                            <label>
                                Username / Employee Number
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Enter your username or employee number"
                                required>

                        </div>


                        <!-- PASSWORD -->

                        <div class="mb-3">

                            <label>
                                Password
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword()">

                                    <i
                                        id="eyeIcon"
                                        class="bi bi-eye">
                                    </i>

                                </button>

                            </div>

                        </div>


                        <!-- ROLE -->

                        <div class="mb-4">

                            <label>
                                Login As
                            </label>

                            <select
                                name="role"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select account type
                                </option>

                                <option value="admin">
                                    Administrator
                                </option>

                                <option value="employee">
                                    Employee
                                </option>

                            </select>

                        </div>


                        <!-- LOGIN BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-login w-100">

                            <i class="bi bi-box-arrow-in-right me-2"></i>

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


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const eyeIcon =
        document.getElementById("eyeIcon");

    if (password.type === "password") {

        password.type = "text";

        eyeIcon.classList.remove("bi-eye");

        eyeIcon.classList.add("bi-eye-slash");

    } else {

        password.type = "password";

        eyeIcon.classList.remove("bi-eye-slash");

        eyeIcon.classList.add("bi-eye");

    }

}

</script>

</body>

</html>


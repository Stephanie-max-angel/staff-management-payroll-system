<?php

session_start();

require_once("../admin/config/database.php");
require("includes/auth.php");

if (!isset($_SESSION['employee_id'])) {

    header("Location: login.php");
    exit();

}

require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");


$employee_id = $_SESSION['employee_id'];


/* =========================================
   GET EMPLOYEE INFORMATION
========================================= */

$stmt = $conn->prepare("

    SELECT

        employees.*,

        departments.department_name,

        positions.position_name,

        positions.basic_salary

    FROM employees

    LEFT JOIN departments

        ON employees.department_id =
        departments.department_id

    LEFT JOIN positions

        ON employees.position_id =
        positions.position_id

    WHERE employees.employee_id = ?

");


$stmt->bind_param("i", $employee_id);

$stmt->execute();

$result = $stmt->get_result();

$employee = $result->fetch_assoc();

$stmt->close();


if (!$employee) {

    header("Location: dashboard.php");
    exit();

}

?>


<!-- =========================================
     PROFILE PAGE
========================================= -->

<div class="main-content">

    <div class="employee-profile-page">

        <div class="row justify-content-center">

             <div class="col-xl-9 col-lg-10 col-md-11 col-12">
                    

                <!-- PROFILE CARD -->

                <div class="card shadow-sm border-0 employee-profile-card">


                    <!-- HEADER -->

                    <div
                        class="card-header text-white"
                        style="background:#27348E;"
                    >

                        <h3 class="mb-0">

                            <i class="bi bi-person-circle"></i>

                            My Profile

                        </h3>

                    </div>



                    <div class="card-body p-4">


                        <!-- =========================================
                             PROFILE PHOTO
                        ========================================== -->

                        <div class="text-center mb-4">


                            <?php if (!empty($employee['photo'])) { ?>

                                <img

                                    src="../admin/uploads/<?= htmlspecialchars($employee['photo']); ?>"

                                    alt="Employee Photo"

                                    width="130"

                                    height="130"

                                    class="rounded-circle border"

                                    style="
                                        width:130px;
                                        height:130px;
                                        object-fit:cover;
                                        border:4px solid #0291DA !important;
                                        display:block;
                                        margin:0 auto;
                                    "

                                >

                            <?php } else { ?>

                                <div

                                    class="rounded-circle bg-light border d-flex align-items-center justify-content-center"

                                    style="
                                        width:130px;
                                        height:130px;
                                        margin:0 auto;
                                        border:4px solid #0291DA !important;
                                    "

                                >

                                    <i
                                        class="bi bi-person-fill text-secondary"
                                        style="font-size:60px;"
                                    ></i>

                                </div>

                            <?php } ?>


                            <h3 class="mt-3 mb-1">

                                <?= htmlspecialchars(
                                    $employee['first_name']
                                    . " "
                                    . $employee['last_name']
                                ); ?>

                            </h3>


                            <p class="text-muted mb-0">

                                <?= htmlspecialchars(
                                    $employee['position_name'] ?? 'Employee'
                                ); ?>

                            </p>


                        </div>



                        <hr class="my-4">



                        <!-- =========================================
                             PERSONAL INFORMATION
                        ========================================== -->

                        <h5 class="fw-bold mb-3 text-primary">

                            <i class="bi bi-person"></i>

                            Personal Information

                        </h5>


                        <div class="table-responsive">

                            <table class="table table-bordered align-middle">


                                <tr>

                                    <th width="35%">

                                        Employee Number

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['employee_number']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        First Name

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['first_name']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Last Name

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['last_name']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Email

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['email']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Phone

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['phone']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Gender

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['gender']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Department

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['department_name']
                                            ?? 'Not assigned'
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Position

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['position_name']
                                            ?? 'Not assigned'
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Date Employed

                                    </th>

                                    <td>

                                        <?= htmlspecialchars(
                                            $employee['employment_date']
                                        ); ?>

                                    </td>

                                </tr>


                                <tr>

                                    <th>

                                        Salary

                                    </th>

                                    <td>

                                        ₦<?= number_format(
                                            $employee['basic_salary'] ?? 0,
                                            2
                                        ); ?>

                                    </td>

                                </tr>


                            </table>

                        </div>



                        <!-- =========================================
                             BUTTONS
                        ========================================== -->

                        <div class="mt-4 d-flex flex-wrap gap-2">


                            <a
                                href="edit-profile.php"
                                class="btn text-white"
                                style="background:#0291DA;"
                            >

                                <i class="bi bi-pencil"></i>

                                Edit Profile

                            </a>


                            <a
                                href="change-password.php"
                                class="btn btn-warning"
                            >

                                <i class="bi bi-key"></i>

                                Change Password

                            </a>


                            <a
                                href="dashboard.php"
                                class="btn btn-secondary"
                            >

                                <i class="bi bi-speedometer2"></i>

                                Dashboard

                            </a>


                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>

</div>


<?php

require_once("includes/footer.php");

?>
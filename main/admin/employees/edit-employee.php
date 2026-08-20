<?php

session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");
    exit();

}

$error = "";


/* =========================================
   CHECK EMPLOYEE ID
========================================= */

if (!isset($_GET['id'])) {

    header("Location: employees.php");
    exit();

}

$employee_id = intval($_GET['id']);


/* =========================================
   GET EMPLOYEE
========================================= */

$stmt = $conn->prepare("

    SELECT *

    FROM employees

    WHERE employee_id = ?

");

$stmt->bind_param("i", $employee_id);

$stmt->execute();

$result = $stmt->get_result();

$employee = $result->fetch_assoc();

$stmt->close();


if (!$employee) {

    header("Location: employees.php");
    exit();

}


/* =========================================
   GET DEPARTMENTS
========================================= */

$department_stmt = $conn->prepare("

    SELECT
        department_id,
        department_name

    FROM departments

    ORDER BY department_name ASC

");

$department_stmt->execute();

$departments = $department_stmt->get_result();


/* =========================================
   GET POSITIONS
========================================= */

$position_stmt = $conn->prepare("

    SELECT
        position_id,
        position_name

    FROM positions

    ORDER BY position_name ASC

");

$position_stmt->execute();

$positions = $position_stmt->get_result();


/* =========================================
   UPDATE EMPLOYEE
========================================= */

if (isset($_POST['update'])) {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];

    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $department_id = intval($_POST['department_id']);
    $position_id = intval($_POST['position_id']);

    $employment_type = $_POST['employment_type'];
    $employment_date = $_POST['employment_date'];

    $username = trim($_POST['username']);

    $status = $_POST['status'];


    /* =========================================
       CHECK EMAIL / USERNAME
    ========================================== */

    $check = $conn->prepare("

        SELECT employee_id

        FROM employees

        WHERE
            (email = ? OR username = ?)

            AND employee_id <> ?

    ");

    $check->bind_param(
        "ssi",
        $email,
        $username,
        $employee_id
    );

    $check->execute();

    $check->store_result();


    if ($check->num_rows > 0) {

        $error = "Email or Username already exists.";

    } else {


        /* =========================================
           PHOTO
        ========================================== */

        $photo = $employee['photo'];

        if (!empty($_FILES['photo']['name'])) {

            $photo = time() . "_" . basename($_FILES['photo']['name']);

            move_uploaded_file(

                $_FILES['photo']['tmp_name'],

                "../uploads/" . $photo

            );

        }


        /* =========================================
           PASSWORD
        ========================================== */

        $password = $employee['password'];

        if (!empty($_POST['password'])) {

            $password = password_hash(
                $_POST['password'],
                PASSWORD_DEFAULT
            );

        }


        /* =========================================
           UPDATE
        ========================================== */

        $update = $conn->prepare("

            UPDATE employees SET

                first_name = ?,
                last_name = ?,
                gender = ?,
                date_of_birth = ?,
                email = ?,
                phone = ?,
                address = ?,
                department_id = ?,
                position_id = ?,
                employment_type = ?,
                employment_date = ?,
                username = ?,
                password = ?,
                photo = ?,
                status = ?

            WHERE employee_id = ?

        ");


        $update->bind_param(

            "sssssssiissssssi",

            $first_name,
            $last_name,
            $gender,
            $date_of_birth,
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
            $status,
            $employee_id

        );


        if ($update->execute()) {

            header("Location: employees.php?updated=1");
            exit();

        } else {

            $error = "Unable to update employee.";

        }


        $update->close();

    }


    $check->close();

}

?>


<div class="main-content">


    <!-- =========================================
         PAGE HEADER
    ========================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                Edit Employee

            </h2>

            <p class="text-muted mb-0">

                Update employee information and account details.

            </p>

        </div>


        <a
            href="employees.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left"></i>

            Back to Employees

        </a>

    </div>



    <!-- =========================================
         ERROR
    ========================================== -->

    <?php if ($error != "") { ?>

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-circle"></i>

            <?= htmlspecialchars($error); ?>

        </div>

    <?php } ?>



    <!-- =========================================
         FORM CARD
    ========================================== -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">

                <i class="bi bi-person-gear text-primary"></i>

                Employee Information

            </h5>

        </div>


        <div class="card-body p-4">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- =========================================
                     BASIC INFORMATION
                ========================================== -->

                <h6 class="fw-bold text-primary mb-3">

                    Basic Information

                </h6>


                <div class="row g-3">


                    <!-- Employee Number -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Employee Number

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['employee_number']); ?>"
                            readonly
                        >

                    </div>



                    <!-- First Name -->

                    <div class="col-md-6">

                        <label class="form-label">

                            First Name

                        </label>

                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['first_name']); ?>"
                            required
                        >

                    </div>



                    <!-- Last Name -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Last Name

                        </label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['last_name']); ?>"
                            required
                        >

                    </div>



                    <!-- Gender -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Gender

                        </label>

                        <select
                            name="gender"
                            class="form-select"
                            required
                        >

                            <option value="Male"
                                <?= ($employee['gender'] == "Male") ? "selected" : ""; ?>>

                                Male

                            </option>

                            <option value="Female"
                                <?= ($employee['gender'] == "Female") ? "selected" : ""; ?>>

                                Female

                            </option>

                        </select>

                    </div>



                    <!-- Date of Birth -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Date of Birth

                        </label>

                        <input
                            type="date"
                            name="date_of_birth"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['date_of_birth']); ?>"
                        >

                    </div>


                </div>



                <hr class="my-4">



                <!-- =========================================
                     CONTACT INFORMATION
                ========================================== -->

                <h6 class="fw-bold text-primary mb-3">

                    Contact Information

                </h6>


                <div class="row g-3">


                    <!-- Email -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Email Address

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['email']); ?>"
                            required
                        >

                    </div>



                    <!-- Phone -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Phone Number

                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['phone']); ?>"
                        >

                    </div>



                    <!-- Address -->

                    <div class="col-12">

                        <label class="form-label">

                            Address

                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="3"
                        ><?= htmlspecialchars($employee['address']); ?></textarea>

                    </div>


                </div>



                <hr class="my-4">



                <!-- =========================================
                     EMPLOYMENT INFORMATION
                ========================================== -->

                <h6 class="fw-bold text-primary mb-3">

                    Employment Information

                </h6>


                <div class="row g-3">


                    <!-- Department -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Department

                        </label>

                        <select
                            name="department_id"
                            class="form-select"
                            required
                        >

                            <option value="">

                                Select Department

                            </option>


                            <?php while ($department = $departments->fetch_assoc()) { ?>

                                <option
                                    value="<?= $department['department_id']; ?>"

                                    <?= ($department['department_id'] == $employee['department_id'])
                                        ? "selected"
                                        : ""; ?>
                                >

                                    <?= htmlspecialchars(
                                        $department['department_name']
                                    ); ?>

                                </option>

                            <?php } ?>


                        </select>

                    </div>



                    <!-- Position -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Position

                        </label>

                        <select
                            name="position_id"
                            class="form-select"
                            required
                        >

                            <option value="">

                                Select Position

                            </option>


                            <?php while ($position = $positions->fetch_assoc()) { ?>

                                <option
                                    value="<?= $position['position_id']; ?>"

                                    <?= ($position['position_id'] == $employee['position_id'])
                                        ? "selected"
                                        : ""; ?>
                                >

                                    <?= htmlspecialchars(
                                        $position['position_name']
                                    ); ?>

                                </option>

                            <?php } ?>


                        </select>

                    </div>



                    <!-- Employment Type -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Employment Type

                        </label>

                        <select
                            name="employment_type"
                            class="form-select"
                            required
                        >

                            <option value="Full-Time"
                                <?= ($employee['employment_type'] == "Full-Time") ? "selected" : ""; ?>>

                                Full-Time

                            </option>

                            <option value="Part-Time"
                                <?= ($employee['employment_type'] == "Part-Time") ? "selected" : ""; ?>>

                                Part-Time

                            </option>

                            <option value="Contract"
                                <?= ($employee['employment_type'] == "Contract") ? "selected" : ""; ?>>

                                Contract

                            </option>

                        </select>

                    </div>



                    <!-- Employment Date -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Employment Date

                        </label>

                        <input
                            type="date"
                            name="employment_date"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['employment_date']); ?>"
                            required
                        >

                    </div>


                </div>



                <hr class="my-4">



                <!-- =========================================
                     ACCOUNT INFORMATION
                ========================================== -->

                <h6 class="fw-bold text-primary mb-3">

                    Account Information

                </h6>


                <div class="row g-3">


                    <!-- Username -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Username

                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['username']); ?>"
                            required
                        >

                    </div>



                    <!-- Password -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Password

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Leave blank to keep current password"
                        >

                    </div>



                    <!-- Status -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Status

                        </label>

                        <select
                            name="status"
                            class="form-select"
                            required
                        >

                            <option
                                value="Active"

                                <?= ($employee['status'] == "Active")
                                    ? "selected"
                                    : ""; ?>
                            >

                                Active

                            </option>

                            <option
                                value="Inactive"

                                <?= ($employee['status'] == "Inactive")
                                    ? "selected"
                                    : ""; ?>
                            >

                                Inactive

                            </option>

                        </select>

                    </div>


                </div>



                <!-- =========================================
                     BUTTONS
                ========================================== -->

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="employees.php"
                        class="btn btn-secondary"
                    >

                        Cancel

                    </a>


                    <button
                        type="submit"
                        name="update"
                        class="btn btn-primary"
                    >

                        <i class="bi bi-check-circle"></i>

                        Update Employee

                    </button>

                </div>


            </form>

        </div>

    </div>


</div>


<?php

require_once("../includes/footer.php");

?>
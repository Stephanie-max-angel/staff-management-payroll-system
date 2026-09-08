<?php
session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/*
 * Payroll Dashboard Statistics
 */

// Total active employees
$activeEmployeesQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM employees
    WHERE status = 'Active'
");

$activeEmployees = $activeEmployeesQuery->fetch_assoc()['total'] ?? 0;


// Total net payroll
$totalPayrollQuery = $conn->query("
    SELECT COALESCE(SUM(net_salary), 0) AS total
    FROM payroll
");

$totalPayroll = $totalPayrollQuery->fetch_assoc()['total'] ?? 0;


// Pending payroll
$pendingQuery = $conn->query("
    SELECT 
        COUNT(*) AS total_records,
        COALESCE(SUM(net_salary), 0) AS total_amount
    FROM payroll
    WHERE payment_status = 'Pending'
");

$pendingData = $pendingQuery->fetch_assoc();

$pendingPayroll = $pendingData['total_records'] ?? 0;
$pendingAmount = $pendingData['total_amount'] ?? 0;


// Paid payroll
$paidQuery = $conn->query("
    SELECT 
        COUNT(*) AS total_records,
        COALESCE(SUM(net_salary), 0) AS total_amount
    FROM payroll
    WHERE payment_status = 'Paid'
");

$paidData = $paidQuery->fetch_assoc();

$paidPayroll = $paidData['total_records'] ?? 0;
$paidAmount = $paidData['total_amount'] ?? 0;


require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");
?>


<div class="container mt-5">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            Payroll Management
        </h2>

        <a
            href="process-payroll.php"
            class="btn text-white"
            style="background:#0291DA;"
        >
            Generate Payroll
        </a>

    </div>


    <!-- Payroll Summary Cards -->
    <div class="row g-4 mb-4">

        <!-- Active Employees -->
        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Active Employees
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format($activeEmployees); ?>
                            </h3>

                        </div>

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center"
                            style="
                                width:50px;
                                height:50px;
                                background:#e7f3ff;
                                color:#0291DA;
                                font-size:22px;
                            "
                        >
                            <i class="bi bi-people-fill"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Total Payroll -->
        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Total Net Payroll
                            </p>

                            <h3 class="fw-bold mb-0">

                                ₦<?= number_format(
                                    $totalPayroll,
                                    2
                                ); ?>

                            </h3>

                        </div>

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center"
                            style="
                                width:50px;
                                height:50px;
                                background:#e8f7ee;
                                color:#198754;
                                font-size:22px;
                            "
                        >
                            <i class="bi bi-cash-stack"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Pending Payroll -->
        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Pending Payroll
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format($pendingPayroll); ?>
                            </h3>

                            <small class="text-muted">
                                ₦<?= number_format(
                                    $pendingAmount,
                                    2
                                ); ?>
                            </small>

                        </div>

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center"
                            style="
                                width:50px;
                                height:50px;
                                background:#fff3cd;
                                color:#856404;
                                font-size:22px;
                            "
                        >
                            <i class="bi bi-clock-history"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Paid Payroll -->
        <div class="col-md-6 col-lg-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <p class="text-muted mb-1">
                                Paid Payroll
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format($paidPayroll); ?>
                            </h3>

                            <small class="text-muted">
                                ₦<?= number_format(
                                    $paidAmount,
                                    2
                                ); ?>
                            </small>

                        </div>

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center"
                            style="
                                width:50px;
                                height:50px;
                                background:#e8f7ee;
                                color:#198754;
                                font-size:22px;
                            "
                        >
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Payroll Table -->
    <div class="table-responsive">

        <table
            id="payrollTable"
            class="table table-bordered table-hover"
        >

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

                $sql = "

                SELECT

                    payroll.*,

                    employees.first_name,

                    employees.last_name,

                    employees.employee_number

                FROM payroll

                INNER JOIN employees

                    ON payroll.employee_id =
                       employees.employee_id

                ORDER BY
                    payroll.payroll_year DESC,
                    payroll.payroll_id DESC

                ";

                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {

                ?>

                <tr>

                    <td>

                        <?= htmlspecialchars(
                            $row['employee_number']
                        ); ?>

                        <br>

                        <?= htmlspecialchars(
                            $row['first_name'] . " " .
                            $row['last_name']
                        ); ?>

                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['payroll_month']
                        ); ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['payroll_year']
                        ); ?>
                    </td>


                    <td>
                        ₦<?= number_format(
                            $row['basic_salary'],
                            2
                        ); ?>
                    </td>


                    <td>
                        ₦<?= number_format(
                            $row['allowances'],
                            2
                        ); ?>
                    </td>


                    <td>
                        ₦<?= number_format(
                            $row['deductions'],
                            2
                        ); ?>
                    </td>


                    <td>
                        ₦<?= number_format(
                            $row['tax'],
                            2
                        ); ?>
                    </td>


                    <td>
                        ₦<?= number_format(
                            $row['pension'],
                            2
                        ); ?>
                    </td>


                    <td>

                        <strong>

                            ₦<?= number_format(
                                $row['net_salary'],
                                2
                            ); ?>

                        </strong>

                    </td>


                    <td>

                        <?php
                        if ($row['payment_status'] == "Paid") {
                        ?>

                            <span class="badge bg-success">
                                Paid
                            </span>

                        <?php
                        } else {
                        ?>

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                        <?php } ?>

                    </td>


                    <td>

                        <a
                            href="view-payroll.php?id=<?= $row['payroll_id']; ?>"
                            class="btn btn-info btn-sm"
                        >
                            View
                        </a>


                        <?php if ($row['payment_status'] == "Pending") { ?>

                            <a
                                href="mark-paid.php?id=<?= $row['payroll_id']; ?>"
                                class="btn btn-success btn-sm"
                                style="font-size:11px; padding:5px 8px;"
                                onclick="return confirm(
                                    'Are you sure you want to mark this payroll as Paid?'
                                );"
                            >
                                Mark as Paid
                            </a>

                        <?php } ?>


                        <a
                            href="delete-payroll.php?id=<?= $row['payroll_id']; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm(
                                'Are you sure you want to delete this payroll record?'
                            );"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>


<script>

new DataTable("#payrollTable");

</script>


<?php require_once("../includes/footer.php"); ?>
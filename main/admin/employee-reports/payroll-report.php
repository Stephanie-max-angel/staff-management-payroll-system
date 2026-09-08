<?php

session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");


/*
|--------------------------------------------------------------------------
| Payroll Report
|--------------------------------------------------------------------------
*/

$sql = "
SELECT

    employees.employee_number,

    CONCAT(
        employees.first_name,
        ' ',
        employees.last_name
    ) AS employee,

    payroll.payroll_month,

    payroll.payroll_year,

    payroll.basic_salary,

    payroll.allowances,

    payroll.deductions,

    payroll.tax,

    payroll.pension,

    payroll.net_salary,

    payroll.payment_status

FROM payroll

INNER JOIN employees

    ON payroll.employee_id = employees.employee_id

ORDER BY
    payroll.payroll_year DESC,
    payroll.payroll_month DESC
";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Error loading payroll report: "
        . mysqli_error($conn)
    );

}


/*
|--------------------------------------------------------------------------
| Total Payroll Records
|--------------------------------------------------------------------------
*/

$totalPayroll = mysqli_num_rows($result);


/*
|--------------------------------------------------------------------------
| Total Gross Salary
|--------------------------------------------------------------------------
*/

$grossQuery = "
SELECT
    COALESCE(
        SUM(basic_salary + allowances),
        0
    ) AS total_gross

FROM payroll
";


$grossResult = mysqli_query($conn, $grossQuery);

$grossRow = mysqli_fetch_assoc($grossResult);

$totalGross = $grossRow['total_gross'] ?? 0;


/*
|--------------------------------------------------------------------------
| Total Net Salary
|--------------------------------------------------------------------------
*/

$netQuery = "
SELECT
    COALESCE(
        SUM(net_salary),
        0
    ) AS total_net

FROM payroll
";


$netResult = mysqli_query($conn, $netQuery);

$netRow = mysqli_fetch_assoc($netResult);

$totalNet = $netRow['total_net'] ?? 0;


/*
|--------------------------------------------------------------------------
| Paid Payroll
|--------------------------------------------------------------------------
*/

$paidQuery = "
SELECT COUNT(*) AS total

FROM payroll

WHERE payment_status = 'Paid'
";


$paidResult = mysqli_query($conn, $paidQuery);

$paidRow = mysqli_fetch_assoc($paidResult);

$totalPaid = $paidRow['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| Pending Payroll
|--------------------------------------------------------------------------
*/

$pendingQuery = "
SELECT COUNT(*) AS total

FROM payroll

WHERE payment_status = 'Pending'
";


$pendingResult = mysqli_query($conn, $pendingQuery);

$pendingRow = mysqli_fetch_assoc($pendingResult);

$totalPending = $pendingRow['total'] ?? 0;

?>

<div class="payroll-page">

    <!-- PAGE HEADER -->
    <div class="page-header">

        <div class="page-title">

            <div class="title-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>

            <div>
                <h1>Payroll Report</h1>

                <p>
                    View and monitor employee payroll information.
                </p>
            </div>

        </div>


        <div class="page-actions">


            <a
                href="reports.php"
                class="btn btn-light">

                <i class="bi bi-arrow-left"></i>

                Back to Reports

            </a>

        </div>

    </div>



    <!-- SUMMARY CARDS -->

    <div class="summary-grid">


        <!-- PAYROLL RECORDS -->

        <div class="summary-card">

            <div class="summary-content">

                <span class="summary-label">
                    Payroll Records
                </span>

                <h3>
                    <?= number_format($totalPayroll); ?>
                </h3>

                <small>
                    Total records
                </small>

            </div>


            <div class="summary-icon blue">

                <i class="bi bi-file-earmark-text"></i>

            </div>

        </div>



        <!-- GROSS SALARY -->

        <div class="summary-card">

            <div class="summary-content">

                <span class="summary-label">
                    Total Gross Salary
                </span>

                <h3>
                    ₦<?= number_format(
                        $totalGross,
                        2
                    ); ?>
                </h3>

                <small>
                    Before deductions
                </small>

            </div>


            <div class="summary-icon green">

                <i class="bi bi-cash-stack"></i>

            </div>

        </div>



        <!-- NET SALARY -->

        <div class="summary-card">

            <div class="summary-content">

                <span class="summary-label">
                    Total Net Salary
                </span>

                <h3>
                    ₦<?= number_format(
                        $totalNet,
                        2
                    ); ?>
                </h3>

                <small>
                    After deductions
                </small>

            </div>


            <div class="summary-icon purple">

                <i class="bi bi-wallet2"></i>

            </div>

        </div>



        <!-- PAID -->

        <div class="summary-card">

            <div class="summary-content">

                <span class="summary-label">
                    Paid
                </span>

                <h3>
                    <?= number_format($totalPaid); ?>
                </h3>

                <small>
                    Completed payments
                </small>

            </div>


            <div class="summary-icon success">

                <i class="bi bi-check-circle"></i>

            </div>

        </div>



        <!-- PENDING -->

        <div class="summary-card">

            <div class="summary-content">

                <span class="summary-label">
                    Pending
                </span>

                <h3>
                    <?= number_format($totalPending); ?>
                </h3>

                <small>
                    Awaiting payment
                </small>

            </div>


            <div class="summary-icon warning">

                <i class="bi bi-clock"></i>

            </div>

        </div>

    </div>



    <!-- REPORT CARD -->

    <div class="report-card">


        <!-- CARD HEADER -->

        <div class="report-card-header">

            <div>

                <h2>
                    Payroll Records
                </h2>

                <p>
                    Detailed employee payroll information
                </p>

            </div>


            <!-- SEARCH -->

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="payrollSearch"
                    placeholder="Search payroll..."
                    onkeyup="searchPayroll()">

            </div>

        </div>



        <!-- TABLE -->

        <div class="table-wrapper">
            <div class="table-responsive">

            <table id="payrollTable">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Employee
                        </th>

                        <th>
                            Payroll Month
                        </th>

                        <th>
                            Gross Salary
                        </th>

                        <th>
                            Net Salary
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($totalPayroll > 0): ?>


                    <?php

                    $sn = 1;

                    while (
                        $row =
                        mysqli_fetch_assoc($result)
                    ):

                        $grossSalary =
                            (float)$row['basic_salary']
                            +
                            (float)$row['allowances'];

                    ?>


                        <tr>


                            <!-- NUMBER -->

                            <td class="serial">

                                <?= $sn++; ?>

                            </td>



                            <!-- EMPLOYEE -->

                            <td>

                                <div class="employee-info">

                                    <div class="employee-avatar">

                                        <?= strtoupper(
                                            substr(
                                                $row['employee'],
                                                0,
                                                1
                                            )
                                        ); ?>

                                    </div>


                                    <div>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $row['employee']
                                            ); ?>

                                        </strong>


                                        <span>

                                            <?= htmlspecialchars(
                                                $row['employee_number']
                                            ); ?>

                                        </span>

                                    </div>

                                </div>

                            </td>



                            <!-- MONTH -->

                            <td>

                                <div class="pay-month">

                                    <i class="bi bi-calendar3"></i>

                                    <span>

                                        <?= htmlspecialchars(
                                            $row['payroll_month']
                                        ); ?>

                                        <?= htmlspecialchars(
                                            $row['payroll_year']
                                        ); ?>

                                    </span>

                                </div>

                            </td>



                            <!-- GROSS -->

                            <td>

                                <strong class="salary">

                                    ₦<?= number_format(
                                        $grossSalary,
                                        2
                                    ); ?>

                                </strong>

                            </td>



                            <!-- NET -->

                            <td>

                                <strong class="net-salary">

                                    ₦<?= number_format(
                                        (float)$row['net_salary'],
                                        2
                                    ); ?>

                                </strong>

                            </td>



                            <!-- STATUS -->

                            <td>

                                <?php

                                $status =
                                    strtolower(
                                        trim(
                                            $row[
                                                'payment_status'
                                            ]
                                        )
                                    );

                                ?>


                                <?php if (
                                    $status === 'paid'
                                ): ?>

                                    <span
                                        class="status-badge paid">

                                        <span
                                            class="status-dot">
                                        </span>

                                        Paid

                                    </span>


                                <?php elseif (
                                    $status === 'pending'
                                ): ?>

                                    <span
                                        class="status-badge pending">

                                        <span
                                            class="status-dot">
                                        </span>

                                        Pending

                                    </span>


                                <?php elseif (
                                    $status === 'failed'
                                ): ?>

                                    <span
                                        class="status-badge failed">

                                        <span
                                            class="status-dot">
                                        </span>

                                        Failed

                                    </span>


                                <?php else: ?>

                                    <span
                                        class="status-badge">

                                        <?= htmlspecialchars(
                                            $row[
                                                'payment_status'
                                            ]
                                        ); ?>

                                    </span>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            class="empty-state">

                            <i
                                class="bi bi-file-earmark-text">
                            </i>

                            <h3>
                                No payroll records found
                            </h3>

                            <p>
                                There are currently no payroll records
                                available.
                            </p>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>
            </div>

        </div>

    </div>

</div>



<style>

/* ==================================================
   PAYROLL REPORT
================================================== */

.payroll-page {

    padding: 25px;

    background: #f6f8fc;

    min-height: calc(100vh - 70px);

}


/* ==================================================
   PAGE HEADER
================================================== */

.page-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 28px;

    gap: 20px;

}


.page-title {

    display: flex;

    align-items: center;

    gap: 15px;

}


.title-icon {

    width: 52px;

    height: 52px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 12px;

    background: #eaf1ff;

    color: #2563eb;

    font-size: 21px;

}


.page-title h1 {

    margin: 0;

    font-size: 26px;

    font-weight: 700;

    color: #1e293b;

}


.page-title p {

    margin: 5px 0 0;

    color: #64748b;

    font-size: 14px;

}


.page-actions {

    display: flex;

    gap: 10px;

}


/* ==================================================
   BUTTONS
================================================== */

.btn {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 11px 16px;

    border-radius: 8px;

    border: none;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

    cursor: pointer;

    transition: 0.2s ease;

}


.btn-primary {

    background: #2563eb;

    color: #fff;

}


.btn-primary:hover {

    background: #1d4ed8;

}


.btn-light {

    background: #fff;

    color: #475569;

    border: 1px solid #e2e8f0;

}


.btn-light:hover {

    background: #f8fafc;

}


/* ==================================================
   SUMMARY GRID
================================================== */

.summary-grid {

    display: grid;

    grid-template-columns:
        repeat(5, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 25px;

}


.summary-card {

    background: #fff;

    border: 1px solid #edf0f5;

    border-radius: 12px;

    padding: 20px;

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    min-height: 125px;

    box-shadow:
        0 2px 8px rgba(15, 23, 42, 0.04);

}


.summary-label {

    display: block;

    font-size: 13px;

    color: #64748b;

    margin-bottom: 8px;

}


.summary-content h3 {

    margin: 0;

    font-size: 20px;

    color: #1e293b;

    font-weight: 700;

}


.summary-content small {

    display: block;

    margin-top: 7px;

    color: #94a3b8;

    font-size: 12px;

}


.summary-icon {

    width: 42px;

    height: 42px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 17px;

}


.summary-icon.blue {

    background: #eaf1ff;

    color: #2563eb;

}


.summary-icon.green {

    background: #eaf8f0;

    color: #16a34a;

}


.summary-icon.purple {

    background: #f1eaff;

    color: #7c3aed;

}


.summary-icon.success {

    background: #e8f8ef;

    color: #16a34a;

}


.summary-icon.warning {

    background: #fff6df;

    color: #d97706;

}


/* ==================================================
   REPORT CARD
================================================== */

.report-card {

    background: #fff;

    border: 1px solid #edf0f5;

    border-radius: 12px;

    box-shadow:
        0 2px 8px rgba(15, 23, 42, 0.04);

    overflow: hidden;

}


.report-card-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 22px 24px;

    border-bottom: 1px solid #edf0f5;

}


.report-card-header h2 {

    margin: 0;

    font-size: 17px;

    color: #1e293b;

}


.report-card-header p {

    margin: 5px 0 0;

    font-size: 13px;

    color: #94a3b8;

}


/* ==================================================
   SEARCH
================================================== */

.search-box {

    width: 280px;

    position: relative;

}


.search-box i {

    position: absolute;

    left: 13px;

    top: 50%;

    transform: translateY(-50%);

    color: #94a3b8;

    font-size: 13px;

}


.search-box input {

    width: 100%;

    height: 40px;

    padding: 0 14px 0 38px;

    border: 1px solid #e2e8f0;

    border-radius: 8px;

    outline: none;

    font-size: 13px;

    color: #334155;

}


.search-box input:focus {

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, 0.08);

}


/* ==================================================
   TABLE
================================================== */

.table-wrapper {

    width: 100%;

    overflow-x: auto;

}


#payrollTable {

    width: 100%;

    border-collapse: collapse;

    min-width: 850px;

}


#payrollTable thead {

    background: #f8fafc;

}


#payrollTable th {

    padding: 14px 20px;

    text-align: left;

    font-size: 12px;

    font-weight: 600;

    color: #64748b;

    text-transform: uppercase;

    letter-spacing: 0.3px;

    border-bottom: 1px solid #edf0f5;

}


#payrollTable td {

    padding: 16px 20px;

    border-bottom: 1px solid #f1f5f9;

    color: #475569;

    font-size: 13px;

}


#payrollTable tbody tr {

    transition: background 0.2s ease;

}


#payrollTable tbody tr:hover {

    background: #f8fafc;

}


#payrollTable tbody tr:last-child td {

    border-bottom: none;

}


/* ==================================================
   SERIAL
================================================== */

.serial {

    color: #94a3b8 !important;

    font-size: 12px !important;

}


/* ==================================================
   EMPLOYEE
================================================== */

.employee-info {

    display: flex;

    align-items: center;

    gap: 11px;

}


.employee-avatar {

    width: 38px;

    height: 38px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eaf1ff;

    color: #2563eb;

    font-size: 13px;

    font-weight: 700;

}


.employee-info strong {

    display: block;

    color: #1e293b;

    font-size: 13px;

    font-weight: 600;

}


.employee-info span {

    display: block;

    margin-top: 3px;

    color: #94a3b8;

    font-size: 11px;

}


/* ==================================================
   PAYROLL MONTH
================================================== */

.pay-month {

    display: flex;

    align-items: center;

    gap: 8px;

}


.pay-month i {

    color: #94a3b8;

}


.pay-month span {

    color: #475569;

}


/* ==================================================
   SALARY
================================================== */

.salary {

    color: #334155;

    font-weight: 600;

}


.net-salary {

    color: #16a34a;

    font-weight: 600;

}


/* ==================================================
   STATUS BADGES
================================================== */

.status-badge {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: 600;

    background: #f1f5f9;

    color: #64748b;

}


.status-dot {

    width: 6px;

    height: 6px;

    border-radius: 50%;

    background: currentColor;

}


.status-badge.paid {

    background: #ecfdf3;

    color: #16a34a;

}


.status-badge.pending {

    background: #fff7e6;

    color: #d97706;

}


.status-badge.failed {

    background: #fef2f2;

    color: #dc2626;

}


/* ==================================================
   EMPTY STATE
================================================== */

.empty-state {

    text-align: center;

    padding: 60px 20px !important;

}


.empty-state i {

    font-size: 35px;

    color: #cbd5e1;

    margin-bottom: 12px;

}


.empty-state h3 {

    margin: 0;

    color: #475569;

    font-size: 15px;

}


.empty-state p {

    margin: 6px 0 0;

    color: #94a3b8;

    font-size: 13px;

}


/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 1200px) {

    .summary-grid {

        grid-template-columns:
            repeat(3, 1fr);

    }

}


@media (max-width: 768px) {

    .payroll-page {

        padding: 15px;

    }


    .page-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .page-actions {

        width: 100%;

    }


    .summary-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .report-card-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .search-box {

        width: 100%;

    }

}


@media (max-width: 500px) {

    .summary-grid {

        grid-template-columns: 1fr;

    }

}


/* ==================================================
   PRINT
================================================== */

@media print {

    .payroll-page {

        padding: 0;

        background: #fff;

    }


    .page-actions,
    .search-box {

        display: none !important;

    }


    .summary-grid {

        grid-template-columns:
            repeat(3, 1fr);

    }


    .summary-card {

        box-shadow: none;

    }


    .report-card {

        box-shadow: none;

        border: 1px solid #ddd;

    }


    #payrollTable {

        min-width: 0;

    }

}

</style>

<?php require_once("../includes/footer.php"); ?>
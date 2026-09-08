<?php
session_start();

require_once ('../config/database.php');
require_once ('../includes/auth.php');
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

// Page title
$pageTitle = "Reports";
?>


        <div class="content" style="padding:20px;">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="fw-bold">Reports</h2>
                    <p>Generate and view reports for employees, payroll, leave, departments and positions.</p>
                </div>
            </div>

            <!-- Report Cards -->
            <div class="reports-grid">

                <!-- Employee Report -->
                <div class="report-card">
                    <div class="report-icon">
                        👥
                    </div>

                    <div class="report-content">
                        <h3>Employee Report</h3>
                        <p>View detailed information about all employees in the organization.</p>

                        <a href="employee-report.php" class="report-btn">
                            View Report
                        </a>
                    </div>
                </div>


                <!-- Departmental Report -->
                <div class="report-card">
                    <div class="report-icon">
                        🏢
                    </div>

                    <div class="report-content">
                        <h3>Departmental Report</h3>
                        <p>View employees and information organized according to departments.</p>

                        <a href="department-report.php" class="report-btn">
                            View Report
                        </a>
                    </div>
                </div>


                <!-- Position Report -->
                <div class="report-card">
                    <div class="report-icon">
                        💼
                    </div>

                    <div class="report-content">
                        <h3>Position Report</h3>
                        <p>View employees based on their assigned positions within the organization.</p>

                        <a href="position-report.php" class="report-btn">
                            View Report
                        </a>
                    </div>
                </div>


                <!-- Leave Report -->
                <div class="report-card">
                    <div class="report-icon">
                        📅
                    </div>

                    <div class="report-content">
                        <h3>Leave Report</h3>
                        <p>View employee leave applications, dates, types and approval status.</p>

                        <a href="leave-report.php" class="report-btn">
                            View Report
                        </a>
                    </div>
                </div>


                <!-- Payroll Report -->
                <div class="report-card">
                    <div class="report-icon">
                        💰
                    </div>

                    <div class="report-content">
                        <h3>Payroll Report</h3>
                        <p>View salary information, deductions, allowances and employee net pay.</p>

                        <a href="payroll-report.php" class="report-btn">
                            View Report
                        </a>
                    </div>
                </div>


                <!-- Export Report -->
                <div class="report-card">
                    <div class="report-icon">
                        📊
                    </div>

                    <div class="report-content">
                        <h3>Export Reports</h3>
                        <p>Export generated reports into Excel or PDF format for records and printing.</p>

                        <div class="export-buttons">

                            <a href="export-excel.php" class="report-btn excel-btn">
                                Export Excel
                            </a>

                            <a href="export-pdf.php" class="report-btn pdf-btn">
                                Export PDF
                            </a>

                        </div>
                    </div>
                </div>

            </div>

        </div>




<style>

.reports-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-top: 30px;
}

.report-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: 0.3s ease;
    border: 1px solid #eeeeee;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.report-icon {
    width: 55px;
    height: 55px;
    border-radius: 10px;
    background: #f1f5ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 27px;
    margin-bottom: 18px;
}

.report-content h3 {
    margin-bottom: 10px;
    font-size: 19px;
}

.report-content p {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.report-btn {
    display: inline-block;
    padding: 10px 16px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-size: 14px;
    transition: 0.3s;
}

.report-btn:hover {
    background: #1d4ed8;
}

.export-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.excel-btn {
    background: #198754;
}

.excel-btn:hover {
    background: #157347;
}

.pdf-btn {
    background: #dc3545;
}

.pdf-btn:hover {
    background: #bb2d3b;
}


/* Responsive */

@media (max-width: 1100px) {

    .reports-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 700px) {

    .reports-grid {
        grid-template-columns: 1fr;
    }

}

</style>

<?php
require_once("../includes/footer.php");
?>


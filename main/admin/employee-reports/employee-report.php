
<?php
session_start();

require_once ("../config/database.php");
require_once ("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

/*
|--------------------------------------------------------------------------
| Employee Report
|--------------------------------------------------------------------------
| Retrieves employee information together with department and position.
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        employees.employee_number,
        CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
        departments.department_name,
        positions.position_name,
        employees.email,
        employees.phone,
        employees.employment_date

    FROM employees

    LEFT JOIN departments
        ON employees.department_id = departments.department_id

    LEFT JOIN positions
        ON employees.position_id = positions.position_id

    ORDER BY employees.first_name ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error loading employee report: " . mysqli_error($conn));
}

/*
|--------------------------------------------------------------------------
| Total Employees
|--------------------------------------------------------------------------
*/

$totalEmployees = mysqli_num_rows($result);
?>



<div class="dashboard-container">



    <main class="main-content">

     

        <div class="content">

            <!-- Page Header -->
            <div class="report-header">

                <div>
                    <h1>Employee Report</h1>
                    <p>
                        View a detailed list of employees in the organization.
                    </p>
                </div>

                <div class="report-actions">

                    <a href="reports.php" class="back-btn">
                        ← Back to Reports
                    </a>
                </div>

            </div>


            <!-- Employee Summary -->
            <div class="summary-card">

                <div class="summary-icon">
                    👥
                </div>

                <div>
                    <span>Total Employees</span>
                    <strong><?= $totalEmployees; ?></strong>
                </div>

            </div>


            <!-- Search -->
            <div class="search-container">

                <input
                    type="text"
                    id="employeeSearch"
                    placeholder="Search employees..."
                    onkeyup="searchEmployees()"
                >

            </div>


            <!-- Employee Table -->
            <div class="table-container">

                <table id="employeeTable">

                    <thead>

                        <tr>
                            <th>Employee No</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date Employed</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($totalEmployees > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($row['employee_number'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['employee_name'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row['department_name'] ?? 'Not Assigned'
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row['position_name'] ?? 'Not Assigned'
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['email'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['phone'] ?? ''); ?>
                                </td>

                                <td>

                                    <?php
                                    if (!empty($row['employment_date'])) {
                                        echo date(
                                            "d M Y",
                                            strtotime($row['employment_date'])
                                        );
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7" class="no-data">
                                No employees found.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<style>

/* --------------------------------------------------
   Report Header
-------------------------------------------------- */

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.report-header h1 {
    margin: 0 0 6px;
    font-size: 28px;
}

.report-header p {
    margin: 0;
    color: #777;
}


/* --------------------------------------------------
   Report Buttons
-------------------------------------------------- */

.report-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.print-btn,
.back-btn {
    border: none;
    padding: 10px 16px;
    border-radius: 7px;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
}

.print-btn {
    background: #2563eb;
    color: white;
}

.print-btn:hover {
    background: #1d4ed8;
}

.back-btn {
    background: #f1f1f1;
    color: #333;
}

.back-btn:hover {
    background: #e5e5e5;
}


/* --------------------------------------------------
   Summary Card
-------------------------------------------------- */

.summary-card {
    display: flex;
    align-items: center;
    gap: 15px;

    background: white;

    padding: 20px;

    border-radius: 10px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);

    width: 250px;

    margin-bottom: 25px;
}

.summary-icon {
    width: 50px;
    height: 50px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f1f5ff;

    border-radius: 10px;

    font-size: 25px;
}

.summary-card span {
    display: block;
    color: #777;
    font-size: 13px;
}

.summary-card strong {
    display: block;
    font-size: 24px;
    margin-top: 3px;
}


/* --------------------------------------------------
   Search
-------------------------------------------------- */

.search-container {
    margin-bottom: 20px;
}

#employeeSearch {
    width: 100%;
    max-width: 400px;

    padding: 12px 15px;

    border: 1px solid #ddd;

    border-radius: 7px;

    font-size: 14px;

    outline: none;
}

#employeeSearch:focus {
    border-color: #2563eb;
}


/* --------------------------------------------------
   Table
-------------------------------------------------- */

.table-container {
    width: 100%;
    overflow-x: auto;

    background: white;

    border-radius: 10px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
}

#employeeTable {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

#employeeTable th {
    background: #f5f7fb;

    padding: 14px;

    text-align: left;

    font-size: 13px;

    color: #444;

    border-bottom: 1px solid #ddd;
}

#employeeTable td {
    padding: 14px;

    border-bottom: 1px solid #eee;

    font-size: 13px;

    color: #555;
}

#employeeTable tbody tr:hover {
    background: #fafafa;
}


/* --------------------------------------------------
   No Data
-------------------------------------------------- */

.no-data {
    text-align: center !important;
    padding: 30px !important;
    color: #888 !important;
}


/* --------------------------------------------------
   Responsive
-------------------------------------------------- */

@media (max-width: 768px) {

    .report-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .report-actions {
        width: 100%;
        flex-wrap: wrap;
    }

}


/* --------------------------------------------------
   Print
-------------------------------------------------- */

@media print {

    .sidebar,
    .navbar,
    .report-actions,
    .search-container {
        display: none !important;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }

    .report-header h1 {
        font-size: 24px;
    }

    .summary-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .table-container {
        box-shadow: none;
    }

    #employeeTable {
        min-width: 100%;
    }

}

</style>


<script>

/*
|--------------------------------------------------------------------------
| Employee Search
|--------------------------------------------------------------------------
*/

function searchEmployees() {

    const input =
        document.getElementById("employeeSearch");

    const filter =
        input.value.toLowerCase();

    const table =
        document.getElementById("employeeTable");

    const rows =
        table.getElementsByTagName("tbody")[0]
             .getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {

        const text =
            rows[i].textContent.toLowerCase();

        if (text.includes(filter)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }

    }

}

</script>


<?php

require_once("../includes/footer.php");

?>



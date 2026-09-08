```php
<?php

session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");


/*
|--------------------------------------------------------------------------
| Position Report
|--------------------------------------------------------------------------
| Displays the number of employees assigned to each position.
|--------------------------------------------------------------------------
*/


$sql = "
SELECT
    positions.position_id,
    positions.position_name,
    COUNT(employees.employee_number) AS total_employees

FROM positions

LEFT JOIN employees
    ON positions.position_id = employees.position_id

GROUP BY
    positions.position_id,
    positions.position_name

ORDER BY
    positions.position_name ASC
";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Error loading position report: "
        . mysqli_error($conn)
    );

}


/*
|--------------------------------------------------------------------------
| Total Positions
|--------------------------------------------------------------------------
*/

$totalPositions = mysqli_num_rows($result);


/*
|--------------------------------------------------------------------------
| Total Employees
|--------------------------------------------------------------------------
*/

$totalEmployeeQuery = "
SELECT COUNT(*) AS total
FROM employees
";


$totalEmployeeResult =
    mysqli_query($conn, $totalEmployeeQuery);


$totalEmployeeRow =
    mysqli_fetch_assoc($totalEmployeeResult);


$totalEmployees =
    $totalEmployeeRow['total'] ?? 0;

?>


<div class="content">

    <!-- Page Header -->

    <div class="report-header">

        <div>

            <h1>Position Report</h1>

            <p>
                View the number of employees assigned to each position.
            </p>

        </div>


        <div class="report-actions">

            <a
                href="reports.php"
                class="back-btn">

                ← Back to Reports

            </a>

        </div>

    </div>



    <!-- Summary Cards -->

    <div class="summary-container">


        <div class="summary-card">

            <div class="summary-icon">
                💼
            </div>

            <div>

                <span>Total Positions</span>

                <strong>
                    <?= $totalPositions; ?>
                </strong>

            </div>

        </div>



        <div class="summary-card">

            <div class="summary-icon">
                👥
            </div>

            <div>

                <span>Total Employees</span>

                <strong>
                    <?= $totalEmployees; ?>
                </strong>

            </div>

        </div>


    </div>



    <!-- Search -->

    <div class="search-container">

        <input
            type="text"
            id="positionSearch"
            placeholder="Search position..."
            onkeyup="searchPositions()">

    </div>



    <!-- Position Table -->

    <div class="table-container">
        <div class="table-responsive">
        <table id="positionTable">

            <thead>

                <tr>

                    <th>S/N</th>

                    <th>Position</th>

                    <th>Total Employees</th>

                </tr>

            </thead>


            <tbody>


            <?php if ($totalPositions > 0): ?>


                <?php

                $sn = 1;

                while (
                    $row = mysqli_fetch_assoc($result)
                ):

                ?>


                    <tr>

                        <td>
                            <?= $sn++; ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $row['position_name'] ?? 'Unknown'
                            ); ?>
                        </td>


                        <td>

                            <span class="employee-count">

                                <?= $row['total_employees']; ?>

                            </span>

                        </td>

                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="3"
                        class="no-data">

                        No positions found.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>
        </div>

    </div>

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
   Buttons
-------------------------------------------------- */

.report-actions {

    display: flex;

    gap: 10px;

    align-items: center;

}


.print-btn,
.back-btn {

    padding: 10px 16px;

    border-radius: 7px;

    border: none;

    cursor: pointer;

    text-decoration: none;

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
   Summary
-------------------------------------------------- */

.summary-container {

    display: flex;

    gap: 20px;

    flex-wrap: wrap;

    margin-bottom: 25px;

}


.summary-card {

    display: flex;

    align-items: center;

    gap: 15px;

    background: white;

    padding: 20px;

    border-radius: 10px;

    box-shadow: 0 3px 12px rgba(0,0,0,0.07);

    min-width: 230px;

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


#positionSearch {

    width: 100%;

    max-width: 400px;

    padding: 12px 15px;

    border: 1px solid #ddd;

    border-radius: 7px;

    font-size: 14px;

    outline: none;

}


#positionSearch:focus {

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

    box-shadow: 0 3px 12px rgba(0,0,0,0.07);

}


#positionTable {

    width: 100%;

    border-collapse: collapse;

}


#positionTable th {

    background: #f5f7fb;

    padding: 14px;

    text-align: left;

    font-size: 13px;

    color: #444;

    border-bottom: 1px solid #ddd;

}


#positionTable td {

    padding: 15px;

    border-bottom: 1px solid #eee;

    font-size: 14px;

    color: #555;

}


#positionTable tbody tr:hover {

    background: #fafafa;

}


/* Employee Count */

.employee-count {

    display: inline-block;

    background: #eef2ff;

    color: #2563eb;

    padding: 5px 12px;

    border-radius: 20px;

    font-weight: 600;

    font-size: 13px;

}


/* No Data */

.no-data {

    text-align: center !important;

    padding: 30px !important;

    color: #888 !important;

}


/* Responsive */

@media (max-width: 768px) {

    .report-header {

        flex-direction: column;

        align-items: flex-start;

    }


    .report-actions {

        flex-wrap: wrap;

    }

}


/* Print */

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


    .table-container {

        box-shadow: none;

    }

}

</style>



<script>

/*
|--------------------------------------------------------------------------
| Position Search
|--------------------------------------------------------------------------
*/

function searchPositions() {

    const input =
        document.getElementById("positionSearch");

    const filter =
        input.value.toLowerCase();

    const table =
        document.getElementById("positionTable");

    const rows =
        table
        .getElementsByTagName("tbody")[0]
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
```

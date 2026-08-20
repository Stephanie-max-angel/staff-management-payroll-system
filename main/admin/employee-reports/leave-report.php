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
| Leave Report
|--------------------------------------------------------------------------
| Displays employee leave requests together with leave type and status.
|--------------------------------------------------------------------------
*/


$sql = "
SELECT
    leave_types.leave_name,
    employees.first_name,
    employees.last_name,
    leave_requests.start_date,
    leave_requests.end_date,
    leave_requests.status

FROM leave_requests

INNER JOIN employees
    ON leave_requests.employee_id = employees.employee_id

INNER JOIN leave_types
    ON leave_requests.leave_type_id = leave_types.leave_type_id

ORDER BY leave_requests.start_date DESC
";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Error loading leave report: "
        . mysqli_error($conn)
    );

}


/*
|--------------------------------------------------------------------------
| Leave Summary
|--------------------------------------------------------------------------
*/

$totalLeave = mysqli_num_rows($result);


/*
|--------------------------------------------------------------------------
| Pending Leave
|--------------------------------------------------------------------------
*/

$pendingQuery = "
SELECT COUNT(*) AS total
FROM leave_requests
WHERE status = 'Pending'
";


$pendingResult = mysqli_query($conn, $pendingQuery);

$pendingRow = mysqli_fetch_assoc($pendingResult);

$pendingLeave = $pendingRow['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| Approved Leave
|--------------------------------------------------------------------------
*/

$approvedQuery = "
SELECT COUNT(*) AS total
FROM leave_requests
WHERE status = 'Approved'
";


$approvedResult = mysqli_query($conn, $approvedQuery);

$approvedRow = mysqli_fetch_assoc($approvedResult);

$approvedLeave = $approvedRow['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| Rejected Leave
|--------------------------------------------------------------------------
*/

$rejectedQuery = "
SELECT COUNT(*) AS total
FROM leave_requests
WHERE status = 'Rejected'
";


$rejectedResult = mysqli_query($conn, $rejectedQuery);

$rejectedRow = mysqli_fetch_assoc($rejectedResult);

$rejectedLeave = $rejectedRow['total'] ?? 0;

?>


<div class="content">


    <!-- Page Header -->

    <div class="report-header">

        <div>

            <h1>Leave Report</h1>

            <p>
                View employee leave requests and their approval status.
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


        <!-- Total Leave -->

        <div class="summary-card">

            <div class="summary-icon">
                📋
            </div>

            <div>

                <span>Total Requests</span>

                <strong>
                    <?= $totalLeave; ?>
                </strong>

            </div>

        </div>



        <!-- Pending -->

        <div class="summary-card">

            <div class="summary-icon">
                ⏳
            </div>

            <div>

                <span>Pending</span>

                <strong>
                    <?= $pendingLeave; ?>
                </strong>

            </div>

        </div>



        <!-- Approved -->

        <div class="summary-card">

            <div class="summary-icon">
                ✅
            </div>

            <div>

                <span>Approved</span>

                <strong>
                    <?= $approvedLeave; ?>
                </strong>

            </div>

        </div>



        <!-- Rejected -->

        <div class="summary-card">

            <div class="summary-icon">
                ❌
            </div>

            <div>

                <span>Rejected</span>

                <strong>
                    <?= $rejectedLeave; ?>
                </strong>

            </div>

        </div>


    </div>



    <!-- Search -->

    <div class="search-container">

        <input
            type="text"
            id="leaveSearch"
            placeholder="Search employee, leave type or status..."
            onkeyup="searchLeave()">

    </div>



    <!-- Leave Table -->

    <div class="table-container">

        <table id="leaveTable">

            <thead>

                <tr>

                    <th>S/N</th>

                    <th>Employee</th>

                    <th>Leave Type</th>

                    <th>Start Date</th>

                    <th>End Date</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody>


            <?php if ($totalLeave > 0): ?>


                <?php

                $sn = 1;

                while ($row = mysqli_fetch_assoc($result)):

                ?>


                    <tr>

                        <td>
                            <?= $sn++; ?>
                        </td>


                        <td>

                            <?= htmlspecialchars(
                                ($row['first_name'] ?? '') .
                                ' ' .
                                ($row['last_name'] ?? '')
                            ); ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row['leave_name'] ?? 'N/A'
                            ); ?>

                        </td>


                        <td>

                            <?php

                            if (!empty($row['start_date'])) {

                                echo date(
                                    "d M Y",
                                    strtotime($row['start_date'])
                                );

                            } else {

                                echo "N/A";

                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            if (!empty($row['end_date'])) {

                                echo date(
                                    "d M Y",
                                    strtotime($row['end_date'])
                                );

                            } else {

                                echo "N/A";

                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            $status =
                                strtolower(
                                    trim($row['status'] ?? '')
                                );

                            ?>


                            <?php if ($status === 'approved'): ?>

                                <span class="status approved">
                                    Approved
                                </span>


                            <?php elseif ($status === 'pending'): ?>

                                <span class="status pending">
                                    Pending
                                </span>


                            <?php elseif ($status === 'rejected'): ?>

                                <span class="status rejected">
                                    Rejected
                                </span>


                            <?php else: ?>

                                <span class="status">
                                    <?= htmlspecialchars(
                                        $row['status'] ?? 'Unknown'
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
                        class="no-data">

                        No leave requests found.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

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
   Summary Cards
-------------------------------------------------- */

.summary-container {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;

    margin-bottom: 25px;

}


.summary-card {

    display: flex;

    align-items: center;

    gap: 15px;

    background: white;

    padding: 18px;

    border-radius: 10px;

    box-shadow: 0 3px 12px rgba(0,0,0,0.07);

    min-width: 190px;

}


.summary-icon {

    width: 48px;

    height: 48px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #f1f5ff;

    border-radius: 10px;

    font-size: 23px;

}


.summary-card span {

    display: block;

    color: #777;

    font-size: 13px;

}


.summary-card strong {

    display: block;

    font-size: 23px;

    margin-top: 3px;

}


/* --------------------------------------------------
   Search
-------------------------------------------------- */

.search-container {

    margin-bottom: 20px;

}


#leaveSearch {

    width: 100%;

    max-width: 450px;

    padding: 12px 15px;

    border: 1px solid #ddd;

    border-radius: 7px;

    font-size: 14px;

    outline: none;

}


#leaveSearch:focus {

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


#leaveTable {

    width: 100%;

    border-collapse: collapse;

    min-width: 800px;

}


#leaveTable th {

    background: #f5f7fb;

    padding: 14px;

    text-align: left;

    font-size: 13px;

    color: #444;

    border-bottom: 1px solid #ddd;

}


#leaveTable td {

    padding: 14px;

    border-bottom: 1px solid #eee;

    font-size: 14px;

    color: #555;

}


#leaveTable tbody tr:hover {

    background: #fafafa;

}


/* --------------------------------------------------
   Status
-------------------------------------------------- */

.status {

    display: inline-block;

    padding: 5px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;

    background: #f1f1f1;

    color: #555;

}


.status.approved {

    background: #e8f7ee;

    color: #198754;

}


.status.pending {

    background: #fff4d6;

    color: #b77900;

}


.status.rejected {

    background: #fde8e8;

    color: #dc3545;

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


    .table-container {

        box-shadow: none;

    }

}

</style>



<script>

/*
|--------------------------------------------------------------------------
| Leave Search
|--------------------------------------------------------------------------
*/

function searchLeave() {

    const input =
        document.getElementById("leaveSearch");

    const filter =
        input.value.toLowerCase();

    const table =
        document.getElementById("leaveTable");

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

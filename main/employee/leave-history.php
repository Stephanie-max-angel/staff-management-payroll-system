<?php

session_start();

require_once("../admin/config/database.php");
require("includes/auth.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");

if (!isset($_SESSION['employee_id'])) {

    header("Location:login.php");
    exit();

}

$employee_id = $_SESSION['employee_id'];



/* =========================================
   GET LEAVE SUMMARY
========================================= */

$summary_stmt = $conn->prepare("

    SELECT

        COUNT(*) AS total_requests,

        SUM(CASE
            WHEN status = 'Pending' THEN 1
            ELSE 0
        END) AS pending_requests,

        SUM(CASE
            WHEN status = 'Approved' THEN 1
            ELSE 0
        END) AS approved_requests,

        SUM(CASE
            WHEN status = 'Rejected' THEN 1
            ELSE 0
        END) AS rejected_requests

    FROM leave_requests

    WHERE employee_id = ?

");

$summary_stmt->bind_param("i", $employee_id);

$summary_stmt->execute();

$summary_result = $summary_stmt->get_result();

$summary = $summary_result->fetch_assoc();

$summary_stmt->close();



/* =========================================
   GET EMPLOYEE LEAVE HISTORY
========================================= */

$stmt = $conn->prepare("

    SELECT

        leave_requests.leave_id,

        leave_requests.start_date,

        leave_requests.end_date,

        leave_requests.total_days,

        leave_requests.status,

        leave_requests.reason,

        leave_requests.approval_date,

        leave_types.leave_name

    FROM leave_requests

    INNER JOIN leave_types

        ON leave_requests.leave_type_id =
           leave_types.leave_type_id

    WHERE leave_requests.employee_id = ?

    ORDER BY leave_requests.leave_id DESC

");

$stmt->bind_param("i", $employee_id);

$stmt->execute();

$result = $stmt->get_result();

?>



<!-- =========================================
     LEAVE HISTORY PAGE
========================================= -->

<div class="leave-history-page">


    <!-- PAGE HEADER -->

    <div class="history-header">

        <div>

            <div class="title-wrapper">

                <div class="title-icon">

                    <i class="bi bi-clock-history"></i>

                </div>

                <div>

                    <h1>Leave History</h1>

                    <p>
                        View and track all your submitted leave requests.
                    </p>

                </div>

            </div>

        </div>


        <a href="my-leaves.php" class="apply-leave-btn">

            <i class="bi bi-calendar-plus"></i>

            Apply for Leave

        </a>

    </div>



    <!-- =========================================
         SUMMARY CARDS
    ========================================== -->

    <div class="summary-grid">


        <!-- Total -->

        <div class="summary-card">

            <div class="summary-icon total-icon">

                <i class="bi bi-calendar2-week"></i>

            </div>

            <div>

                <span>Total Requests</span>

                <strong>
                    <?= (int)($summary['total_requests'] ?? 0); ?>
                </strong>

            </div>

        </div>



        <!-- Pending -->

        <div class="summary-card">

            <div class="summary-icon pending-icon">

                <i class="bi bi-hourglass-split"></i>

            </div>

            <div>

                <span>Pending</span>

                <strong>
                    <?= (int)($summary['pending_requests'] ?? 0); ?>
                </strong>

            </div>

        </div>



        <!-- Approved -->

        <div class="summary-card">

            <div class="summary-icon approved-icon">

                <i class="bi bi-check-circle-fill"></i>

            </div>

            <div>

                <span>Approved</span>

                <strong>
                    <?= (int)($summary['approved_requests'] ?? 0); ?>
                </strong>

            </div>

        </div>



        <!-- Rejected -->

        <div class="summary-card">

            <div class="summary-icon rejected-icon">

                <i class="bi bi-x-circle-fill"></i>

            </div>

            <div>

                <span>Rejected</span>

                <strong>
                    <?= (int)($summary['rejected_requests'] ?? 0); ?>
                </strong>

            </div>

        </div>

    </div>



    <!-- =========================================
         HISTORY CARD
    ========================================== -->

    <div class="history-card">


        <!-- Card Header -->

        <div class="history-card-header">

            <div>

                <h2>My Leave Requests</h2>

                <p>
                    A record of your submitted leave applications.
                </p>

            </div>

            <div class="record-count">

                <?= (int)($summary['total_requests'] ?? 0); ?>

                Request(s)

            </div>

        </div>



        <?php if ($result->num_rows > 0) { ?>


            <!-- =========================================
                 DESKTOP TABLE
            ========================================== -->

            <div class="table-responsive">

                <table class="leave-table">

                    <thead>

                        <tr>

                            <th>Leave Type</th>

                            <th>Start Date</th>

                            <th>End Date</th>

                            <th>Days</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($leave = $result->fetch_assoc()) { ?>


                        <?php

                        $status = $leave['status'];

                        $status_class = 'pending';

                        $status_icon = 'bi-hourglass-split';


                        if ($status === 'Approved') {

                            $status_class = 'approved';

                            $status_icon = 'bi-check-circle-fill';

                        }

                        elseif ($status === 'Rejected') {

                            $status_class = 'rejected';

                            $status_icon = 'bi-x-circle-fill';

                        }

                        ?>


                        <tr>


                            <!-- Leave Type -->

                            <td>

                                <div class="leave-type">

                                    <div class="leave-type-icon">

                                        <i class="bi bi-calendar-event"></i>

                                    </div>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $leave['leave_name']
                                        ); ?>

                                    </strong>

                                </div>

                            </td>



                            <!-- Start Date -->

                            <td>

                                <?= htmlspecialchars(
                                    $leave['start_date']
                                ); ?>

                            </td>



                            <!-- End Date -->

                            <td>

                                <?= htmlspecialchars(
                                    $leave['end_date']
                                ); ?>

                            </td>



                            <!-- Total Days -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $leave['total_days']
                                    ); ?>

                                </strong>

                            </td>



                            <!-- Status -->

                            <td>

                                <span class="status-badge <?= $status_class; ?>">

                                    <i class="bi <?= $status_icon; ?>"></i>

                                    <?= htmlspecialchars($status); ?>

                                </span>

                            </td>



                            <!-- Action -->

                            <td>

                                <a
                                    href="view-leave.php?id=<?= (int)$leave['leave_id']; ?>"
                                    class="view-btn"
                                >

                                    <i class="bi bi-eye"></i>

                                    View

                                </a>

                            </td>


                        </tr>


                    <?php } ?>

                    </tbody>

                </table>

            </div>


        <?php } else { ?>


            <!-- =========================================
                 EMPTY STATE
            ========================================== -->

            <div class="empty-state">

                <div class="empty-icon">

                    <i class="bi bi-calendar-x"></i>

                </div>

                <h3>No Leave Requests Yet</h3>

                <p>

                    You haven't submitted any leave requests.
                    Your leave history will appear here once you apply.

                </p>

                <a href="my-leaves.php" class="empty-button">

                    <i class="bi bi-calendar-plus"></i>

                    Apply for Leave

                </a>

            </div>


        <?php } ?>


    </div>


</div>



<style>

/* =========================================
   LEAVE HISTORY PAGE
========================================= */

.leave-history-page {

    width: 100%;

    font-family: 'Poppins', sans-serif;
    padding: 10px;

}


/* =========================================
   HEADER
========================================= */

.history-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.title-wrapper {

    display: flex;

    align-items: center;

    gap: 15px;

}


.title-icon {

    width: 52px;

    height: 52px;

    border-radius: 13px;

    background: #e8f1fb;

    color: #1C5FA2;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 23px;

}


.title-wrapper h1 {

    margin: 0;

    font-size: 25px;

    font-weight: 600;

    color: #222;

}


.title-wrapper p {

    margin: 5px 0 0;

    font-size: 13px;

    color: #777;

}


.apply-leave-btn {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 11px 17px;

    border-radius: 8px;

    background: #1C5FA2;

    color: white;

    text-decoration: none;

    font-size: 13px;

    font-weight: 500;

    transition: 0.2s ease;

}


.apply-leave-btn:hover {

    background: #154d85;

    color: white;

    transform: translateY(-1px);

}


/* =========================================
   SUMMARY GRID
========================================= */

.summary-grid {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 30px;

}


.summary-card {

    background: white;

    border: 1px solid #edf0f4;

    border-radius: 14px;

    padding: 22px;

    display: flex;

    align-items: center;

    gap: 15px;

    box-shadow: 0 3px 12px rgba(0,0,0,0.04);

}


.summary-icon {

    width: 48px;

    height: 48px;

    border-radius: 11px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

}


.total-icon {

    background: #e8f1fb;

    color: #1C5FA2;

}


.pending-icon {

    background: #fff4df;

    color: #b87300;

}


.approved-icon {

    background: #e8f7ef;

    color: #198754;

}


.rejected-icon {

    background: #fdebed;

    color: #c03952;

}


.summary-card span {

    display: block;

    color: #8a94a6;

    font-size: 12px;

    margin-bottom: 4px;

}


.summary-card strong {

    display: block;

    color: #222;

    font-size: 23px;

    font-weight: 600;

}


/* =========================================
   HISTORY CARD
========================================= */

.history-card {

    background: white;

    border: 1px solid #edf0f4;

    border-radius: 16px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.04);

    overflow: hidden;

}


.history-card-header {

    padding: 25px 28px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    border-bottom: 1px solid #edf0f4;

}


.history-card-header h2 {

    margin: 0;

    font-size: 18px;

    font-weight: 600;

    color: #222;

}


.history-card-header p {

    margin: 5px 0 0;

    font-size: 12px;

    color: #888;

}


.record-count {

    padding: 7px 12px;

    border-radius: 20px;

    background: #f1f5f9;

    color: #64748b;

    font-size: 12px;

    font-weight: 500;

}


/* =========================================
   TABLE
========================================= */

.table-responsive {

    width: 100%;

    overflow-x: auto;

}


.leave-table {

    width: 100%;

    border-collapse: collapse;

    font-family: 'Poppins', sans-serif;

}


.leave-table thead {

    background: #f8fafc;

}


.leave-table th {

    padding: 15px 20px;

    text-align: left;

    color: #7a8494;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.4px;

    white-space: nowrap;

}


.leave-table td {

    padding: 17px 20px;

    border-top: 1px solid #edf0f4;

    color: #555;

    font-size: 13px;

    white-space: nowrap;

}


.leave-table tbody tr {

    transition: background 0.2s ease;

}


.leave-table tbody tr:hover {

    background: #f9fbfd;

}


/* =========================================
   LEAVE TYPE
========================================= */

.leave-type {

    display: flex;

    align-items: center;

    gap: 10px;

}


.leave-type-icon {

    width: 34px;

    height: 34px;

    border-radius: 8px;

    background: #e8f1fb;

    color: #1C5FA2;

    display: flex;

    align-items: center;

    justify-content: center;

}


.leave-type strong {

    color: #333;

    font-weight: 500;

}


/* =========================================
   STATUS BADGES
========================================= */

.status-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: 600;

}


.status-badge.pending {

    background: #fff4df;

    color: #b87300;

}


.status-badge.approved {

    background: #e8f7ef;

    color: #198754;

}


.status-badge.rejected {

    background: #fdebed;

    color: #c03952;

}


/* =========================================
   VIEW BUTTON
========================================= */

.view-btn {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 7px 12px;

    border-radius: 7px;

    background: #e8f1fb;

    color: #1C5FA2;

    text-decoration: none;

    font-size: 12px;

    font-weight: 500;

    transition: 0.2s ease;

}


.view-btn:hover {

    background: #1C5FA2;

    color: white;

}


/* =========================================
   EMPTY STATE
========================================= */

.empty-state {

    padding: 65px 30px;

    text-align: center;

}


.empty-icon {

    width: 75px;

    height: 75px;

    margin: 0 auto 20px;

    border-radius: 50%;

    background: #e8f1fb;

    color: #1C5FA2;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 30px;

}


.empty-state h3 {

    margin: 0 0 8px;

    font-size: 18px;

    font-weight: 600;

    color: #333;

}


.empty-state p {

    max-width: 450px;

    margin: 0 auto 22px;

    color: #888;

    font-size: 13px;

    line-height: 1.7;

}


.empty-button {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 10px 17px;

    border-radius: 8px;

    background: #1C5FA2;

    color: white;

    text-decoration: none;

    font-size: 13px;

    font-weight: 500;

}


.empty-button:hover {

    background: #154d85;

    color: white;

}


/* =========================================
   TABLET
========================================= */

@media (max-width: 1100px) {

    .summary-grid {

        grid-template-columns: repeat(2, 1fr);

    }

}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 700px) {

    .history-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 20px;

    }


    .summary-grid {

        grid-template-columns: 1fr;

    }


    .history-card-header {

        align-items: flex-start;

        gap: 15px;

        flex-direction: column;

    }

}

</style>



<?php

$stmt->close();

require_once("includes/footer.php");

?>
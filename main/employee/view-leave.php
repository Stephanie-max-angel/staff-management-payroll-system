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


if (!isset($_GET['id'])) {

    header("Location:my-leaves.php");
    exit();

}


$leave_id = intval($_GET['id']);


$stmt = $conn->prepare("

    SELECT

        leave_requests.*,

        leave_types.leave_name

    FROM leave_requests

    INNER JOIN leave_types

        ON leave_requests.leave_type_id =
           leave_types.leave_type_id

    WHERE

        leave_requests.leave_id = ?

        AND leave_requests.employee_id = ?

");


$stmt->bind_param("ii", $leave_id, $employee_id);

$stmt->execute();

$result = $stmt->get_result();

$leave = $result->fetch_assoc();

$stmt->close();


if (!$leave) {

    header("Location:my-leaves.php");
    exit();

}


/* Determine status styling */

$status = $leave['status'];

$status_class = 'status-pending';

$status_icon = 'bi-hourglass-split';


if ($status === 'Approved') {

    $status_class = 'status-approved';

    $status_icon = 'bi-check-circle-fill';

} elseif ($status === 'Rejected') {

    $status_class = 'status-rejected';

    $status_icon = 'bi-x-circle-fill';

}

?>



<div class="leave-page">


    <!-- Page Header -->

    <div class="page-header">

        <div>

            <div class="page-title">

                <i class="bi bi-calendar-check"></i>

                <div>

                    <h1>Leave Request Details</h1>

                    <p>
                        View the details and current status of your leave request.
                    </p>

                </div>

            </div>

        </div>


        <a href="my-leaves.php" class="back-button">

            <i class="bi bi-arrow-left"></i>

            Back to Leave Requests

        </a>

    </div>



    <!-- Leave Details Card -->

    <div class="leave-card">


        <!-- Card Header -->

        <div class="leave-card-header">

            <div>

                <span class="request-label">
                    LEAVE REQUEST
                </span>

                <h2>
                    <?= htmlspecialchars($leave['leave_name']); ?>
                </h2>

            </div>


            <!-- Status -->

            <div class="status <?= $status_class; ?>">

                <i class="bi <?= $status_icon; ?>"></i>

                <?= htmlspecialchars($status); ?>

            </div>

        </div>



        <!-- Leave Information -->

        <div class="leave-information">


            <!-- Leave Type -->

            <div class="information-item">

                <div class="information-icon">

                    <i class="bi bi-briefcase"></i>

                </div>

                <div>

                    <span>Leave Type</span>

                    <strong>
                        <?= htmlspecialchars($leave['leave_name']); ?>
                    </strong>

                </div>

            </div>



            <!-- Start Date -->

            <div class="information-item">

                <div class="information-icon">

                    <i class="bi bi-calendar-event"></i>

                </div>

                <div>

                    <span>Start Date</span>

                    <strong>
                        <?= htmlspecialchars($leave['start_date']); ?>
                    </strong>

                </div>

            </div>



            <!-- End Date -->

            <div class="information-item">

                <div class="information-icon">

                    <i class="bi bi-calendar-event"></i>

                </div>

                <div>

                    <span>End Date</span>

                    <strong>
                        <?= htmlspecialchars($leave['end_date']); ?>
                    </strong>

                </div>

            </div>



            <!-- Total Days -->

            <div class="information-item">

                <div class="information-icon">

                    <i class="bi bi-calendar2-week"></i>

                </div>

                <div>

                    <span>Total Days</span>

                    <strong>
                        <?= htmlspecialchars($leave['total_days']); ?>
                        day(s)
                    </strong>

                </div>

            </div>



            <!-- Processed Date -->

            <div class="information-item">

                <div class="information-icon">

                    <i class="bi bi-clock-history"></i>

                </div>

                <div>

                    <span>Processed Date</span>

                    <strong>

                        <?php

                        if (!empty($leave['approval_date'])) {

                            echo htmlspecialchars($leave['approval_date']);

                        } else {

                            echo "Not yet processed";

                        }

                        ?>

                    </strong>

                </div>

            </div>

        </div>



        <!-- Reason -->

        <div class="reason-section">

            <div class="section-heading">

                <i class="bi bi-chat-left-text"></i>

                <h3>Reason for Leave</h3>

            </div>

            <div class="reason-box">

                <?= nl2br(htmlspecialchars($leave['reason'])); ?>

            </div>

        </div>



        <!-- Rejection Reason -->

        <?php if ($leave['status'] === "Rejected") { ?>

            <div class="rejection-section">

                <div class="section-heading">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <h3>Reason for Rejection</h3>

                </div>

                <div class="rejection-box">

                    <?= nl2br(
                        htmlspecialchars($leave['rejected_reason'])
                    ); ?>

                </div>

            </div>

        <?php } ?>


    </div>



    <!-- Bottom Back Button -->

    <div class="bottom-action">

        <a href="my-leaves.php" class="back-button">

            <i class="bi bi-arrow-left"></i>

            Back to Leave Requests

        </a>

    </div>


</div>



<style>

/* =========================================
   LEAVE PAGE
========================================= */
.leave-page,
.leave-page * {
    font-family: 'Poppins', sans-serif;
}

.leave-page {

    width: 100%;

}


/* =========================================
   PAGE HEADER
========================================= */

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.page-title {

    display: flex;

    align-items: center;

    gap: 15px;

}


.page-title > i {

    font-size: 28px;

    color: #1C5FA2;

}


.page-title h1 {

    margin: 0;

    font-size: 28px;

    font-weight: 700;

    color: #222;

}


.page-title p {

    margin: 5px 0 0;

    color: #777;

    font-size: 14px;

}


/* =========================================
   BACK BUTTON
========================================= */

.back-button {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 10px 17px;

    border-radius: 8px;

    background: #1C5FA2;

    color: white;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

    transition: all 0.2s ease;

}


.back-button:hover {

    background: #154d85;

    color: white;

    transform: translateY(-1px);

}


/* =========================================
   MAIN CARD
========================================= */

.leave-card {

    background: white;

    border-radius: 16px;

    border: 1px solid #e8edf3;

    box-shadow: 0 5px 20px rgba(0,0,0,0.05);

    overflow: hidden;

}


/* =========================================
   CARD HEADER
========================================= */

.leave-card-header {

    padding: 28px 32px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    border-bottom: 1px solid #edf0f4;

}


.request-label {

    font-size: 11px;

    letter-spacing: 1.5px;

    color: #8a94a6;

    font-weight: 600;

}


.leave-card-header h2 {

    margin: 7px 0 0;

    font-size: 22px;

    color: #222;

}


/* =========================================
   STATUS
========================================= */

.status {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 9px 15px;

    border-radius: 30px;

    font-size: 13px;

    font-weight: 600;

}


.status-pending {

    background: #fff4df;

    color: #b87300;

}


.status-approved {

    background: #e7f7ee;

    color: #198754;

}


.status-rejected {

    background: #fdebed;

    color: #c03952;

}


/* =========================================
   INFORMATION GRID
========================================= */

.leave-information {

    padding: 30px 32px;

    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 25px;

    border-bottom: 1px solid #edf0f4;

}


.information-item {

    display: flex;

    align-items: center;

    gap: 13px;

}


.information-icon {

    width: 45px;

    height: 45px;

    flex-shrink: 0;

    border-radius: 10px;

    background: #eaf2fb;

    color: #1C5FA2;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 19px;

}


.information-item span {

    display: block;

    font-size: 12px;

    color: #8a94a6;

    margin-bottom: 4px;

}


.information-item strong {

    display: block;

    font-size: 14px;

    color: #333;

}


/* =========================================
   REASON SECTION
========================================= */

.reason-section {

    padding: 30px 32px;

}


.section-heading {

    display: flex;

    align-items: center;

    gap: 9px;

    margin-bottom: 13px;

}


.section-heading i {

    color: #1C5FA2;

}


.section-heading h3 {

    margin: 0;

    font-size: 16px;

    font-weight: 600;

    color: #333;

}


.reason-box {

    padding: 18px;

    background: #f7f9fc;

    border-radius: 10px;

    color: #555;

    font-size: 14px;

    line-height: 1.7;

    border: 1px solid #edf0f4;

}


/* =========================================
   REJECTION SECTION
========================================= */

.rejection-section {

    margin: 0 32px 30px;

}


.rejection-section .section-heading i {

    color: #c03952;

}


.rejection-box {

    padding: 18px;

    background: #fff5f6;

    border: 1px solid #f4d5d9;

    border-left: 4px solid #c03952;

    border-radius: 8px;

    color: #8d303d;

    font-size: 14px;

    line-height: 1.7;

}


/* =========================================
   BOTTOM ACTION
========================================= */

.bottom-action {

    margin-top: 20px;

}


/* =========================================
   TABLET
========================================= */

@media (max-width: 900px) {

    .leave-information {

        grid-template-columns: repeat(2, 1fr);

    }

}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 650px) {

    .page-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 20px;

    }


    .leave-card-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }


    .leave-information {

        grid-template-columns: 1fr;

        padding: 25px;

    }


    .reason-section {

        padding: 25px;

    }


    .rejection-section {

        margin-left: 25px;

        margin-right: 25px;

    }

}

</style>



<?php

require_once("includes/footer.php");

?>
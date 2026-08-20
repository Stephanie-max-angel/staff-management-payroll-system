<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: leave-requests.php");
    exit();
}

$leave_id = intval($_GET['id']);




$stmt = $conn->prepare("
SELECT

leave_requests.*,

employees.employee_number,
employees.first_name,
employees.last_name,
employees.email,
employees.phone,

departments.department_name,

positions.position_name,

leave_types.leave_name

FROM leave_requests

INNER JOIN employees
ON leave_requests.employee_id = employees.employee_id

INNER JOIN departments
ON employees.department_id = departments.department_id

INNER JOIN positions
ON employees.position_id = positions.position_id

INNER JOIN leave_types
ON leave_requests.leave_type_id = leave_types.leave_type_id

WHERE leave_requests.leave_id=?

");

$stmt->bind_param("i",$leave_id);

$stmt->execute();

$result=$stmt->get_result();

$leave=$result->fetch_assoc();

$stmt->close();

if(!$leave){

header("Location: leave-requests.php");

exit();

}
?>




<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Leave Request Details</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background: #F2F4F5;
}

.leave-container {
    max-width: 1000px;
    margin: 30px auto;
}

.card {
    border: none;
    border-radius: 12px;
}

.card-header {
    background: #27348E;
    color: white;
    padding: 18px 25px;
}

.card-header h3 {
    margin: 0;
}

.section-title {
    color: #27348E;
    font-weight: 600;
    margin-bottom: 15px;
}

.table {
    margin-bottom: 30px;
}

.table th {
    width: 30%;
    background: #f1f3f5;
    font-weight: 600;
}

.table td,
.table th {
    padding: 12px;
    vertical-align: middle;
}

.reason-box {
    white-space: normal;
    line-height: 1.6;
}

.action-buttons {
    margin-top: 20px;
}

</style>

</head>

<body>

<div class="main-content">

    <div class="leave-container">

        <div class="card shadow">

            <div class="card-header">

                <h3>
                    Leave Request Details
                </h3>

            </div>

            <div class="card-body p-4">

                <!-- Employee Information -->

                <h5 class="section-title">
                    Employee Information
                </h5>

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <tr>
                            <th>Employee Number</th>
                            <td>
                                <?= htmlspecialchars($leave['employee_number']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Employee Name</th>
                            <td>
                                <?= htmlspecialchars(
                                    $leave['first_name']." ".$leave['last_name']
                                ); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Department</th>
                            <td>
                                <?= htmlspecialchars($leave['department_name']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Position</th>
                            <td>
                                <?= htmlspecialchars($leave['position_name']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>
                                <?= htmlspecialchars($leave['email']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Phone</th>
                            <td>
                                <?= htmlspecialchars($leave['phone']); ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <!-- Leave Information -->

                <h5 class="section-title">
                    Leave Information
                </h5>

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <tr>
                            <th>Leave Type</th>
                            <td>
                                <?= htmlspecialchars($leave['leave_name']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Start Date</th>
                            <td>
                                <?= htmlspecialchars($leave['start_date']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>End Date</th>
                            <td>
                                <?= htmlspecialchars($leave['end_date']); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Total Days</th>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($leave['total_days']); ?>
                                </strong>
                                Day(s)
                            </td>
                        </tr>

                        <tr>
                            <th>Reason</th>
                            <td class="reason-box">
                                <?= nl2br(htmlspecialchars($leave['reason'])); ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>

                                <?php if($leave['status'] == "Pending"){ ?>

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                <?php } elseif($leave['status'] == "Approved"){ ?>

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                <?php } else { ?>

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                <?php } ?>

                            </td>
                        </tr>

                        <tr>
                            <th>Application Date</th>
                            <td>
                                <?= htmlspecialchars($leave['created_at']); ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <!-- Buttons -->

                <div class="action-buttons">

                    <?php if($leave['status'] == "Pending"){ ?>

                        <a
                            href="approve-leave.php?id=<?= $leave['leave_id']; ?>"
                            class="btn btn-success"
                            onclick="return confirm('Approve this leave request?')">

                            <i class="bi bi-check-circle"></i>
                            Approve Leave

                        </a>

                        <a
                            href="reject-leave.php?id=<?= $leave['leave_id']; ?>"
                            class="btn btn-danger"
                            onclick="return confirm('Reject this leave request?')">

                            <i class="bi bi-x-circle"></i>
                            Reject Leave

                        </a>

                    <?php } ?>

                    <a
                        href="leave-request.php"
                        class="btn btn-secondary">

                        Back

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>
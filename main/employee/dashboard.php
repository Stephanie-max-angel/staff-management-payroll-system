<?php
session_start();

require_once("../admin/config/database.php");
require("includes/auth.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");

if (!isset($_SESSION['employee_id'])) {

    header("Location: login.php");
    exit();

}

$employee_id = $_SESSION['employee_id'];

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

?>

<div class="employee-dashboard">

    <!-- Welcome Banner -->
    <div class="welcome-card">

        <div class="welcome-text">

            <span class="welcome-label">
                EMPLOYEE PORTAL
            </span>

            <h1>
                Welcome back,
                <?= htmlspecialchars($employee['first_name']); ?> 👋
            </h1>

            <p>
                Manage your profile, payslips and leave requests
                all from one place.
            </p>

        </div>

        <div class="welcome-icon">
            <i class="bi bi-person-workspace"></i>
        </div>

    </div>


    <!-- Quick Access -->
    <div class="section-heading">

        <div>
            <h2>Quick Access</h2>

            <p>
                Access your employee services
            </p>
        </div>

    </div>


    <!-- Dashboard Cards -->
    <div class="employee-grid">


        <!-- Profile -->
        <a href="profile.php" class="employee-card">

            <div class="employee-card-top">

                <div class="employee-icon profile-icon">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="card-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

            </div>

            <h3>My Profile</h3>

            <p>
                View and manage your personal
                employee information.
            </p>

            <span class="card-link">
                View Profile
                <i class="bi bi-arrow-right"></i>
            </span>

        </a>


        <!-- Payslips -->
        <a href="payslips.php" class="employee-card">

            <div class="employee-card-top">

                <div class="employee-icon payslip-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <div class="card-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

            </div>

            <h3>My Payslips</h3>

            <p>
                View and download your salary
                payslips whenever you need them.
            </p>

            <span class="card-link">
                View Payslips
                <i class="bi bi-arrow-right"></i>
            </span>

        </a>


        <!-- Apply Leave -->
        <a href="my-leaves.php" class="employee-card">

            <div class="employee-card-top">

                <div class="employee-icon leave-icon">
                    <i class="bi bi-calendar-plus"></i>
                </div>

                <div class="card-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

            </div>

            <h3>Apply for Leave</h3>

            <p>
                Submit a leave request and
                manage your time away from work.
            </p>

            <span class="card-link">
                Apply Leave
                <i class="bi bi-arrow-right"></i>
            </span>

        </a>


        <!-- Leave History -->
        <a href="leave-history.php" class="employee-card">

            <div class="employee-card-top">

                <div class="employee-icon history-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="card-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

            </div>

            <h3>Leave History</h3>

            <p>
                Check your previous leave requests
                and their current status.
            </p>

            <span class="card-link">
                View History
                <i class="bi bi-arrow-right"></i>
            </span>

        </a>


        <!-- Change Password -->
        <a href="change-password.php" class="employee-card">

            <div class="employee-card-top">

                <div class="employee-icon password-icon">
                    <i class="bi bi-key-fill"></i>
                </div>

                <div class="card-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

            </div>

            <h3>Change Password</h3>

            <p>
                Update your account password
                to keep your account secure.
            </p>

            <span class="card-link">
                Change Password
                <i class="bi bi-arrow-right"></i>
            </span>

        </a>


    </div>


    <!-- Account Information -->
    <div class="account-info">

        <div class="account-info-icon">
            <i class="bi bi-shield-check"></i>
        </div>

        <div>

            <h3>Your Account</h3>

            <p>
                Your employee account is active.
                Keep your information up to date.
            </p>

        </div>

    </div>

</div>


<style>

/* =========================================
   EMPLOYEE DASHBOARD
========================================= */

.employee-dashboard {

    margin-left: 260px;

    min-height: 100vh;

    padding: 40px;

    background: #f5f7fb;

    box-sizing: border-box;

}


/* =========================================
   WELCOME CARD
========================================= */

.welcome-card {

    background: #1C5FA2;

    border-radius: 18px;

    padding: 35px 40px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    color: white;

    box-shadow: 0 8px 25px rgba(28, 95, 162, 0.18);

    margin-bottom: 35px;

}


.welcome-label {

    font-size: 12px;

    font-weight: 600;

    letter-spacing: 1.5px;

    opacity: 0.8;

}


.welcome-card h1 {

    margin: 10px 0 8px;

    font-size: 32px;

    font-weight: 700;

}


.welcome-card p {

    margin: 0;

    font-size: 15px;

    opacity: 0.9;

}


.welcome-icon {

    width: 90px;

    height: 90px;

    border-radius: 50%;

    background: rgba(255,255,255,0.15);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 42px;

}


/* =========================================
   SECTION HEADING
========================================= */

.section-heading {

    margin-bottom: 20px;

}


.section-heading h2 {

    margin: 0;

    font-size: 24px;

    color: #222;

}


.section-heading p {

    margin: 5px 0 0;

    color: #777;

    font-size: 14px;

}


/* =========================================
   CARD GRID
========================================= */

.employee-grid {

    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 24px;

}


/* =========================================
   EMPLOYEE CARD
========================================= */

.employee-card {

    position: relative;

    background: white;

    padding: 27px;

    border-radius: 16px;

    text-decoration: none;

    color: inherit;

    min-height: 230px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.05);

    border: 1px solid #edf0f4;

    transition: all 0.25s ease;

}


.employee-card:hover {

    transform: translateY(-6px);

    box-shadow: 0 12px 28px rgba(0,0,0,0.10);

    border-color: #dce8f5;

}


/* =========================================
   CARD TOP
========================================= */

.employee-card-top {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 22px;

}


.employee-icon {

    width: 55px;

    height: 55px;

    border-radius: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 24px;

}


/* Icon backgrounds */

.profile-icon {

    background: #e8f1fb;

    color: #1C5FA2;

}


.payslip-icon {

    background: #e9f7ef;

    color: #198754;

}


.leave-icon {

    background: #fff4df;

    color: #d88900;

}


.history-icon {

    background: #f0ebff;

    color: #7257c7;

}


.password-icon {

    background: #fcebed;

    color: #c03952;

}


/* =========================================
   ARROW
========================================= */

.card-arrow {

    width: 34px;

    height: 34px;

    border-radius: 50%;

    background: #f3f6fa;

    color: #1C5FA2;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.25s ease;

}


.employee-card:hover .card-arrow {

    background: #1C5FA2;

    color: white;

    transform: translate(3px, -3px);

}


/* =========================================
   CARD TEXT
========================================= */

.employee-card h3 {

    margin: 0 0 9px;

    font-size: 19px;

    color: #222;

}


.employee-card p {

    margin: 0;

    color: #777;

    font-size: 14px;

    line-height: 1.6;

}


/* =========================================
   CARD LINK
========================================= */

.card-link {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    margin-top: 20px;

    color: #1C5FA2;

    font-size: 13px;

    font-weight: 600;

}


.card-link i {

    transition: transform 0.2s ease;

}


.employee-card:hover .card-link i {

    transform: translateX(4px);

}


/* =========================================
   ACCOUNT INFORMATION
========================================= */

.account-info {

    margin-top: 30px;

    background: white;

    border-radius: 15px;

    padding: 22px 25px;

    display: flex;

    align-items: center;

    gap: 18px;

    border: 1px solid #edf0f4;

    box-shadow: 0 3px 12px rgba(0,0,0,0.04);

}


.account-info-icon {

    width: 48px;

    height: 48px;

    border-radius: 50%;

    background: #e8f7ef;

    color: #198754;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;

}


.account-info h3 {

    margin: 0 0 4px;

    font-size: 16px;

}


.account-info p {

    margin: 0;

    color: #777;

    font-size: 13px;

}


/* =========================================
   TABLET
========================================= */

@media (max-width: 1050px) {

    .employee-grid {

        grid-template-columns: repeat(2, 1fr);

    }

}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 700px) {

    .employee-dashboard {

        margin-left: 0;

        padding: 20px;

    }


    .welcome-card {

        padding: 25px;

    }


    .welcome-card h1 {

        font-size: 25px;

    }


    .welcome-icon {

        width: 65px;

        height: 65px;

        font-size: 30px;

    }


    .employee-grid {

        grid-template-columns: 1fr;

    }

}

</style>


<?php
require_once("includes/footer.php");
?>
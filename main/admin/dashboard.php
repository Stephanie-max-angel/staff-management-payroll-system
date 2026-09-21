<?php
require_once("includes/auth.php");
require_once("config/database.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");
?>

<div class="dashboard-content">

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>
                Welcome back,
                <strong>
                    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </strong> 👋
            </p>
        </div>
    </div>


    <!-- Quick Access Section -->
    <div class="section-title">
        <h2>Quick Access</h2>
        <p>Manage your staff management system from here.</p>
    </div>


    <!-- Dashboard Cards -->
    <div class="dashboard-grid">

        <!-- Employees -->
         <a href="/employee-management-system/main/admin/employees/employees.php"
   class="dashboard-card">
       

            <div class="card-icon">
                <i class="bi bi-people-fill"></i>
            </div>

            <div class="card-content">
                <h3>Employees</h3>
                <p>Manage employee records and staff information.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>


        <!-- Departments -->
        <a href="/employee-management-system/main/admin/departments/departments.php"
           class="dashboard-card">

            <div class="card-icon">
                <i class="bi bi-building"></i>
            </div>

            <div class="card-content">
                <h3>Departments</h3>
                <p>Manage university departments and organization.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>


        <!-- Positions -->
        <a href="/employee-management-system/main/admin/positions/positions.php"
           class="dashboard-card">

            <div class="card-icon">
                <i class="bi bi-briefcase-fill"></i>
            </div>

            <div class="card-content">
                <h3>Positions</h3>
                <p>Manage employee positions and job roles.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>


        <!-- Payroll -->
        <a href="/employee-management-system/main/admin/employee-payroll/payroll.php"
           class="dashboard-card">

            <div class="card-icon">
                <i class="bi bi-cash-stack"></i>
            </div>

            <div class="card-content">
                <h3>Payroll</h3>
                <p>Manage salaries, deductions and employee payroll.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>


        <!-- Leave -->
        <a href="/employee-management-system/main/admin/employee-leave/leave-types.php"
           class="dashboard-card">

            <div class="card-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div class="card-content">
                <h3>Leave Management</h3>
                <p>Manage employee leave types and requests.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>


        <!-- Reports -->
        <a href="/employee-management-system/main/admin/employee-reports/reports.php"
           class="dashboard-card">

            <div class="card-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>

            <div class="card-content">
                <h3>Reports</h3>
                <p>View and generate staff and payroll reports.</p>
            </div>

            <div class="card-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>

        </a>

    </div>
    <?php
// Get the 5 most recent audit logs
$audit_sql = "
    SELECT 
        audit_logs.action,
        audit_logs.description,
        audit_logs.created_at,
        admins.username
    FROM audit_logs
    LEFT JOIN admins
        ON audit_logs.admin_id = admins.admin_id
    ORDER BY audit_logs.created_at DESC
    LIMIT 5
";

$audit_result = mysqli_query($conn, $audit_sql);
?>

<!-- Recent Audit Activity -->
<div class="recent-activity">

    <div class="activity-header">
        <div>
            <h2>Recent Activity</h2>
            <p>Latest activities performed by administrators.</p>
        </div>

        <a href="/employee-management-system/main/admin/audit-log/audit-log.php"
           class="view-all-btn">
            View All Audit Logs
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="activity-table-wrapper">

        <table class="activity-table">

            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Description</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($audit_result && mysqli_num_rows($audit_result) > 0): ?>

                    <?php while ($audit = mysqli_fetch_assoc($audit_result)): ?>

                        <tr>

                            <td>
                                <?= date(
                                    'd M Y, h:i A',
                                    strtotime($audit['created_at'])
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $audit['username'] ?? 'Unknown'
                                ); ?>
                            </td>

                            <td>
                                <span class="activity-badge">
                                    <?= htmlspecialchars($audit['action']); ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($audit['description']); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="no-activity">
                            No recent activity found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
</div>


<style>

.dashboard-content {
    /* margin-left: 100px; */
    padding: 40px;
    min-height: 100vh;
    background: #f5f7fb;
}


/* Dashboard Header */

.dashboard-header {
    background: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 35px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
}

.dashboard-header h1 {
    margin: 0;
    font-size: 30px;
    font-weight: 700;
    color: #1c5fa2;
}

.dashboard-header p {
    margin-top: 8px;
    margin-bottom: 0;
    color: #6c757d;
    font-size: 16px;
}


/* Section */

.section-title {
    margin-bottom: 20px;
}

.section-title h2 {
    margin-bottom: 5px;
    font-size: 23px;
    color: #222;
}

.section-title p {
    margin: 0;
    color: #777;
}


/* Cards Grid */

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}


/* Dashboard Card */

.dashboard-card {
    position: relative;
    background: white;
    padding: 28px;
    border-radius: 15px;
    text-decoration: none;
    color: inherit;
    min-height: 190px;

    display: flex;
    flex-direction: column;

    box-shadow: 0 3px 15px rgba(0,0,0,0.06);

    transition: all 0.25s ease;
}


/* Hover */

.dashboard-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

/* Recent Activity */

.recent-activity {
    background: white;
    margin-top: 40px;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.activity-header h2 {
    margin: 0 0 5px;
    font-size: 23px;
    color: #222;
}

.activity-header p {
    margin: 0;
    color: #777;
    font-size: 14px;
}

.view-all-btn {
    background: #1c5fa2;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.25s;
}

.view-all-btn:hover {
    background: #0291da;
    color: white;
}

.view-all-btn i {
    margin-left: 5px;
}

.activity-table-wrapper {
    overflow-x: auto;
}

.activity-table {
    width: 100%;
    border-collapse: collapse;
}

.activity-table th {
    background: #f5f7fb;
    color: #444;
    font-weight: 600;
    padding: 14px;
    text-align: left;
    font-size: 14px;
}

.activity-table td {
    padding: 15px 14px;
    border-bottom: 1px solid #eee;
    color: #666;
    font-size: 14px;
}

.activity-table tr:last-child td {
    border-bottom: none;
}

.activity-badge {
    display: inline-block;
    background: #e8f1fb;
    color: #1c5fa2;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.no-activity {
    text-align: center !important;
    padding: 30px !important;
    color: #999 !important;
}

/* Recent Activity Mobile */

@media (max-width: 700px) {

    .activity-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .view-all-btn {
        display: inline-block;
    }

    .recent-activity {
        padding: 20px;
    }
}


/* Icon */

.card-icon {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #e8f1fb;
    color: #1c5fa2;

    border-radius: 12px;

    font-size: 25px;

    margin-bottom: 20px;
}


/* Card Text */

.card-content h3 {
    margin: 0 0 8px;

    font-size: 20px;
    font-weight: 600;

    color: #222;
}

.card-content p {
    margin: 0;

    font-size: 14px;
    line-height: 1.6;

    color: #777;
}


/* Arrow */

.card-arrow {
    position: absolute;

    right: 25px;
    bottom: 25px;

    width: 35px;
    height: 35px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #1c5fa2;
    color: white;

    transition: 0.25s;
}

.dashboard-card:hover .card-arrow {
    transform: translateX(5px);
}


/* Tablet */

@media (max-width: 1000px) {

    .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}


/* Mobile */

@media (max-width: 700px) {

    .dashboard-content {
        margin-left: 0;
        padding: 20px;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

}

</style>


<?php
require_once("includes/footer.php");
?>
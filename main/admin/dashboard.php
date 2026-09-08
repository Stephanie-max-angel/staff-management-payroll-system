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
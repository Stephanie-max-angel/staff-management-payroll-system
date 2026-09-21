<div class="admin-sidebar" id="adminSidebar">

    <!-- Sidebar Header -->
    <div class="sidebar-header">

        <h3>Employee Management System</h3>

        <!-- Mobile Close Button -->
        <button
            type="button"
            class="sidebar-close"
            onclick="closeSidebar()"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    <hr>

    <ul class="nav nav-pills flex-column">

        <!-- Dashboard -->
        <li class="nav-item mb-2">
            <a
                href="/employee-management-system/main/admin/dashboard.php"
                class="nav-link text-white"
            >
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <!-- Employees -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/employees/employees.php"
                class="nav-link text-white"
            >
                <i class="bi bi-people-fill"></i>
                Employees
            </a>
        </li>

        <!-- Departments -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/departments/departments.php"
                class="nav-link text-white"
            >
                <i class="bi bi-building"></i>
                Departments
            </a>
        </li>

        <!-- Positions -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/positions/positions.php"
                class="nav-link text-white"
            >
                <i class="bi bi-briefcase-fill"></i>
                Positions
            </a>
        </li>

        <!-- Payroll -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/employee-payroll/payroll.php"
                class="nav-link text-white"
            >
                <i class="bi bi-cash-stack"></i>
                Payroll
            </a>
        </li>

        <!-- Leave -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/employee-leave/leave-request.php"
                class="nav-link text-white"
            >
                <i class="bi bi-calendar-check"></i>
                Leave
            </a>
        </li>

        <!-- Reports -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/employee-reports/reports.php"
                class="nav-link text-white"
            >
                <i class="bi bi-file-earmark-text"></i>
                Reports
            </a>
        </li>

        <!-- Audit Logs -->
        <li class="mb-2">
            <a
                href="/employee-management-system/main/admin/audit-log/audit-log.php"
                class="nav-link text-white"
            >
                <i class="bi bi-journal-text"></i>
                Audit Logs
            </a>
        </li>

        <!-- Logout -->
        <li class="mt-5">
            <a
                href="/employee-management-system/main-logout.php"
                class="nav-link text-warning"
            >
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </li>

    </ul>

</div>


<!-- Mobile Overlay -->
<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    onclick="closeSidebar()">
</div>


<style>

/* =====================================
   ADMIN SIDEBAR
===================================== */

.admin-sidebar {
    width: 260px;
    height: 100vh;
    background: #1C5FA2;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    padding: 16px;
    color: white;
    overflow-y: auto;
    transition: left 0.3s ease;
}


/* Sidebar Header */

.sidebar-header {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-header h3 {
    text-align: center;
    margin: 0;
    font-size: 20px;
}


/* Close Button */

.sidebar-close {
    display: none;
    position: absolute;
    right: 0;
    top: 0;
    border: none;
    background: transparent;
    color: white;
    font-size: 20px;
    cursor: pointer;
}


/* Sidebar Links */

.admin-sidebar .nav-link {
    border-radius: 8px;
    padding: 10px 12px;
}

.admin-sidebar .nav-link i {
    margin-right: 8px;
}

.admin-sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
}


/* Overlay */

.sidebar-overlay {
    display: none;
}


/* =====================================
   MOBILE SIDEBAR
===================================== */

@media (max-width: 768px) {

    .admin-sidebar {
        left: -260px;
        transition: left 0.3s ease;
    }

    .admin-sidebar.open {
        left: 0;
    }

    .sidebar-close {
        display: block;
    }

    .sidebar-overlay.open {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1040;
    }

}

</style>


<script>

function openSidebar() {

    const sidebar = document.getElementById("adminSidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (sidebar) {
        sidebar.classList.add("open");
    }

    if (overlay) {
        overlay.classList.add("open");
    }

}


function closeSidebar() {

    const sidebar = document.getElementById("adminSidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (sidebar) {
        sidebar.classList.remove("open");
    }

    if (overlay) {
        overlay.classList.remove("open");
    }

}

</script>
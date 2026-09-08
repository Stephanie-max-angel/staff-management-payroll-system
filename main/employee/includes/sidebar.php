
<!-- Employee Sidebar -->

<div class="employee-sidebar d-flex flex-column flex-shrink-0 p-3 text-white">

    <h4 class="text-center mb-4">
        Employee Portal
    </h4>

    <hr>

    <ul class="nav nav-pills flex-column">

        <li class="mb-2">
            <a href="dashboard.php" class="nav-link text-white">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li class="mb-2">
            <a href="profile.php" class="nav-link text-white">
                <i class="bi bi-person-fill"></i>
                My Profile
            </a>
        </li>

        <li class="mb-2">
            <a href="payslips.php" class="nav-link text-white">
                <i class="bi bi-receipt"></i>
                My Payslips
            </a>
        </li>

        <li class="mb-2">
            <a href="my-leaves.php" class="nav-link text-white">
                <i class="bi bi-calendar-plus"></i>
                Apply Leave
            </a>
        </li>

        <li class="mb-2">
            <a href="leave-history.php" class="nav-link text-white">
                <i class="bi bi-clock-history"></i>
                Leave History
            </a>
        </li>

        <li class="mb-2">
            <a href="change-password.php" class="nav-link text-white">
                <i class="bi bi-key-fill"></i>
                Change Password
            </a>
        </li>

        <li class="mt-5">
            <a href="/employee-management-system/main-logout.php" class="nav-link text-warning">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </li>

    </ul>

</div>

<!-- Mobile / Tablet Hamburger -->

<button class="sidebar-toggle" type="button" onclick="toggleEmployeeSidebar()">
    <i class="bi bi-list"></i>
</button>

<script>
function toggleEmployeeSidebar() {
    document.querySelector('.employee-sidebar')
        .classList.toggle('sidebar-open');
}
</script>


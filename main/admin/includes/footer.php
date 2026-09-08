</div> <!-- End main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const toggle = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".admin-sidebar");

    if (toggle && sidebar) {
        toggle.addEventListener("click", function () {
            sidebar.classList.toggle("sidebar-open");
        });
    }

});
</script>

</body>

</html>







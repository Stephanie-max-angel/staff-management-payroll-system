<div class="container-fluid">
    <h4 class="fw-bold mb-0">
        Employee Dashboard
    </h4>

    <div class="d-flex align-items-center">
        <div class="me-3 text-end">
            <small class="text-muted">Welcome</small>
            <br>
            <strong>
                <?php echo $_SESSION['employee_name']; ?>
            </strong>
        </div>

        <div
            class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
            style="width:45px;height:45px;"
        >
            <i class="bi bi-person-fill"></i>
        </div>
    </div>
</div>
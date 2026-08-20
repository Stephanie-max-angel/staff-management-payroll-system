<?php
session_start();

require_once("../includes/auth.php");
require_once("../config/database.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

// Get all departments
$sql = "
SELECT *
FROM departments
ORDER BY department_name ASC
";

$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Departments</h2>

        <a href="addd-departments.php" class="btn btn-primary">

            <i class="fa fa-plus"></i>

            Add Department

        </a>

    </div>

    <?php if(isset($_GET['success'])){ ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($_GET['success']); ?>

        </div>

    <?php } ?>

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            Department List

        </div>

        <div class="card-body">

            <table id="departmentTable" class="table table-bordered table-striped table-hover">

                <thead>

                <tr>

                    <th width="8%">S/N</th>

                    <th>Department</th>

                    <th>Description</th>

                    <th width="18%">Actions</th>

                </tr>

                </thead>

                <tbody>

                <?php

                $sn = 1;

                while($row = mysqli_fetch_assoc($result)){

                ?>

                <tr>

                    <td><?= $sn++; ?></td>

                    <td><?= htmlspecialchars($row['department_name']); ?></td>

                    <td><?= htmlspecialchars($row['description']); ?></td>

                    <td>

                        <a href="edit-department.php?id=<?= $row['department_id']; ?>"

                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <a href="delete-department.php?id=<?= $row['department_id']; ?>"

                           class="btn btn-danger btn-sm"

                           onclick="return confirm('Delete this department?')">

                            Delete

                        </a>

                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- <link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

<script src="https://code.jquery.com/jquery-3.7.1.js"></script> -->

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>

new DataTable('#departmentTable');

</script>

<?php require_once("../includes/footer.php"); ?>
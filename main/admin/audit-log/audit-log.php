<?php

session_start();

require_once '../config/database.php';

require_once '../includes/auth.php';

require_once '../includes/header.php';

require_once '../includes/sidebar.php';

require_once '../includes/navbar.php';



// Get audit logs

$sql = "

    SELECT 

        audit_logs.log_id,

        audit_logs.action,

        audit_logs.description,

        audit_logs.created_at,

        admins.username

    FROM audit_logs

    LEFT JOIN admins

        ON audit_logs.admin_id = admins.admin_id

    ORDER BY audit_logs.created_at DESC

";

$result = mysqli_query($conn, $sql);

if (!$result) {

    die("Error loading audit logs: " . mysqli_error($conn));

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta

        name="viewport"

        content="width=device-width, initial-scale=1.0"

    >

    <title>Audit Log</title>



    <!-- Bootstrap -->

    <link

        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"

        rel="stylesheet"

    >



    <!-- Bootstrap Icons -->

    <link

        rel="stylesheet"

        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"

    >



    <!-- DataTables -->

    <link

        rel="stylesheet"

        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"

    >



    <style>

        /* =====================================
           GENERAL
        ===================================== */

        body {

            margin: 0;

            background: #f5f7fb;

            font-family: Arial, sans-serif;

        }



        /* =====================================
           MAIN CONTENT
        ===================================== */

        .main-content {

            margin-left: 260px;

            padding: 30px;

        }



        /* =====================================
           PAGE TITLE
        ===================================== */

        .page-title {

            color: #1C5FA2;

            font-weight: 600;

        }



        /* =====================================
           BACK TO DASHBOARD
        ===================================== */

        .back-dashboard {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            background: #1C5FA2;

            color: white;

            border: none;

            border-radius: 7px;

            padding: 9px 15px;

            text-decoration: none;

            margin-bottom: 20px;

        }



        .back-dashboard:hover {

            background: #154b80;

            color: white;

        }



        /* =====================================
           AUDIT CARD
        ===================================== */

        .audit-card {

            background: #ffffff;

            border-radius: 12px;

            padding: 25px;

            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);

        }



        /* =====================================
           AUDIT ICON
        ===================================== */

        .audit-icon {

            width: 50px;

            height: 50px;

            background: #e8f2fb;

            color: #1C5FA2;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 24px;

        }



        /* =====================================
           ACTION BADGE
        ===================================== */

        .badge-action {

            background: #e8f2fb;

            color: #1C5FA2;

            padding: 7px 12px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;

        }



        /* =====================================
           TABLE
        ===================================== */

        .table th {

            background: #1C5FA2;

            color: white;

            white-space: nowrap;

        }



        .table td {

            vertical-align: middle;

        }



        .description {

            max-width: 400px;

        }



        /* =====================================
           MOBILE
        ===================================== */

        @media (max-width: 768px) {

            .main-content {

                margin-left: 0 !important;

                padding: 20px 15px;

                width: 100%;

            }



            /* Audit card */

            .audit-card {

                padding: 18px;

            }



            /* Page title */

            .page-title {

                font-size: 22px;

            }



            /* Audit icon */

            .audit-icon {

                width: 45px;

                height: 45px;

                font-size: 20px;

            }



            .audit-card .d-flex {

                align-items: flex-start !important;

            }



            .audit-card h5 {

                font-size: 17px;

            }



            .audit-card p {

                font-size: 14px;

            }

        }



        /* =====================================
           SMALL MOBILE
        ===================================== */

        @media (max-width: 480px) {

            .main-content {

                padding: 15px 10px;

            }



            .audit-card {

                padding: 15px;

                border-radius: 10px;

            }



            .page-title {

                font-size: 20px;

            }



            .audit-icon {

                width: 40px;

                height: 40px;

                font-size: 18px;

                flex-shrink: 0;

            }

        }

    </style>

</head>



<body>



    <!-- Main Content -->

    <div class="main-content">



        <!-- Back to Dashboard -->

      <div class="d-flex justify-content-end mb-3">

        <a
            href="/employee-management-system/main/admin/dashboard.php"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Back to Dashboard
        </a>

      </div>



        <!-- Page Header -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="page-title mb-1">

                    <i class="bi bi-journal-text"></i>

                    Audit Log

                </h2>

                <p class="text-muted mb-0">

                    Track important activities performed by administrators.

                </p>

            </div>

        </div>



        <!-- Audit Information Card -->

        <div class="audit-card mb-4">

            <div class="d-flex align-items-center">

                <div class="audit-icon me-3">

                    <i class="bi bi-journal-text"></i>

                </div>

                <div>

                    <h5 class="mb-1">

                        System Activity Log

                    </h5>

                    <p class="text-muted mb-0">

                        This section records important administrative

                        activities performed within the staff management

                        system.

                    </p>

                </div>

            </div>

        </div>



        <!-- Audit Table -->

        <div class="audit-card">

            <div class="table-responsive">

                <table

                    id="auditTable"

                    class="table table-bordered table-hover align-middle"

                >

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Date &amp; Time</th>

                            <th>Admin</th>

                            <th>Action</th>

                            <th>Description</th>

                        </tr>

                    </thead>



                    <tbody>

                        <?php if (mysqli_num_rows($result) > 0): ?>

                            <?php $count = 1; ?>



                            <?php while ($log = mysqli_fetch_assoc($result)): ?>

                                <tr>

                                    <td>

                                        <?= $count++ ?>

                                    </td>



                                    <td>

                                        <?= date(

                                            'd M Y, h:i A',

                                            strtotime($log['created_at'])

                                        ) ?>

                                    </td>



                                    <td>

                                        <i class="bi bi-person-circle me-1"></i>

                                        <?= htmlspecialchars(

                                            $log['username'] ?? 'Unknown'

                                        ) ?>

                                    </td>



                                    <td>

                                        <span class="badge-action">

                                            <?= htmlspecialchars(

                                                $log['action']

                                            ) ?>

                                        </span>

                                    </td>



                                    <td class="description">

                                        <?= htmlspecialchars(

                                            $log['description'] ?? ''

                                        ) ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>



                        <?php else: ?>

                            <tr>

                                <td class="text-center text-muted">

                                    -

                                </td>

                                <td class="text-center text-muted">

                                    -

                                </td>

                                <td class="text-center text-muted">

                                    -

                                </td>

                                <td class="text-center text-muted">

                                    -

                                </td>

                                <td class="text-center text-muted">

                                    No audit records found.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>



    </div>



    <!-- jQuery -->

    <script

        src="https://code.jquery.com/jquery-3.7.1.min.js">

    </script>



    <!-- Bootstrap -->

    <script

        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

    </script>



    <!-- DataTables -->

    <script

        src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js">

    </script>



    <script

        src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js">

    </script>



    <!-- DataTables -->

    <script>

        $(document).ready(function () {

            $('#auditTable').DataTable({

                pageLength: 10,

                order: [[1, 'desc']]

            });

        });

    </script>



<?php require_once '../includes/footer.php'; ?>

</body>

</html>
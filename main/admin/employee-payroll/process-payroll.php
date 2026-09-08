<?php
session_start();

require_once("../config/database.php");
require_once("../includes/auth.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if (isset($_POST['generate'])) {

    $month = $_POST['payroll_month'];
    $year = intval($_POST['payroll_year']);

    // Fixed pension contribution
    $pension = 40000;

    // Get tax percentage
    $settings = $conn->query("
        SELECT tax_percentage
        FROM payroll_settings
        LIMIT 1
    ");

    $config = $settings->fetch_assoc();

    $tax_percentage = $config['tax_percentage'] ?? 0;

    // Get all active employees
    $employees_query = $conn->query("
        SELECT 
            employee_id,
            employee_number,
            first_name,
            last_name,
            position_id
        FROM employees
        WHERE status = 'Active'
        ORDER BY first_name, last_name
    ");

    if (!$employees_query) {

        $error = "Unable to retrieve employees.";

    } else {

        $generated = 0;
        $skipped = 0;

        while ($employee = $employees_query->fetch_assoc()) {

            $employee_id = $employee['employee_id'];

            /*
             * Check if payroll already exists
             * for this employee, month and year.
             */
            $check = $conn->prepare("
                SELECT payroll_id
                FROM payroll
                WHERE employee_id = ?
                AND payroll_month = ?
                AND payroll_year = ?
            ");

            $check->bind_param(
                "isi",
                $employee_id,
                $month,
                $year
            );

            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {

                $skipped++;

                $check->close();

                continue;
            }

            $check->close();


            /*
             * Get basic salary from employee's position.
             */
            $stmt = $conn->prepare("
                SELECT basic_salary
                FROM positions
                WHERE position_id = ?
            ");

            $stmt->bind_param(
                "i",
                $employee['position_id']
            );

            $stmt->execute();

            $result = $stmt->get_result();
            $salary_data = $result->fetch_assoc();

            $stmt->close();

            $basic_salary = $salary_data['basic_salary'] ?? 0;


            /*
             * Get total allowances.
             */
            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(allowances.amount), 0) AS total
                FROM employee_allowances
                INNER JOIN allowances
                    ON employee_allowances.allowance_id =
                       allowances.allowance_id
                WHERE employee_allowances.employee_id = ?
            ");

            $stmt->bind_param(
                "i",
                $employee_id
            );

            $stmt->execute();

            $result = $stmt->get_result();
            $allowance_data = $result->fetch_assoc();

            $stmt->close();

            $allowances = $allowance_data['total'] ?? 0;


            /*
             * Get total deductions.
             */
            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(deductions.amount), 0) AS total
                FROM employee_deductions
                INNER JOIN deductions
                    ON employee_deductions.deduction_id =
                       deductions.deduction_id
                WHERE employee_deductions.employee_id = ?
            ");

            $stmt->bind_param(
                "i",
                $employee_id
            );

            $stmt->execute();

            $result = $stmt->get_result();
            $deduction_data = $result->fetch_assoc();

            $stmt->close();

            $deductions = $deduction_data['total'] ?? 0;


            /*
             * Calculate tax.
             */
            $tax = ($basic_salary * $tax_percentage) / 100;


            /*
             * Calculate net salary.
             */
            $net_salary =
                $basic_salary
                + $allowances
                - $deductions
                - $tax
                - $pension;


            /*
             * Insert payroll.
             */
            $stmt = $conn->prepare("
                INSERT INTO payroll (
                    employee_id,
                    payroll_month,
                    payroll_year,
                    basic_salary,
                    allowances,
                    deductions,
                    tax,
                    pension,
                    net_salary,
                    payment_status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
            ");

            $stmt->bind_param(
                "isidddddd",
                $employee_id,
                $month,
                $year,
                $basic_salary,
                $allowances,
                $deductions,
                $tax,
                $pension,
                $net_salary
            );

            if ($stmt->execute()) {
                $generated++;
            }

            $stmt->close();
        }


        /*
         * Create Audit Log
         * Only log the payroll generation if at least
         * one payroll record was successfully generated.
         */
        if ($generated > 0) {

            $admin_id = $_SESSION['admin_id'];

            if ($skipped > 0) {

                $description =
                    "Generated payroll for " .
                    $month . " " . $year .
                    " for " . $generated .
                    " active employee(s). " .
                    $skipped .
                    " employee(s) were skipped because payroll already exists.";

            } else {

                $description =
                    "Generated payroll for " .
                    $month . " " . $year .
                    " for " . $generated .
                    " active employee(s).";
            }

            $audit_stmt = $conn->prepare("
                INSERT INTO audit_logs (
                    admin_id,
                    action,
                    description
                )
                VALUES (?, ?, ?)
            ");

            $action = "Payroll Generated";

            $audit_stmt->bind_param(
                "iss",
                $admin_id,
                $action,
                $description
            );

            if (!$audit_stmt->execute()) {
    $error = "Audit log error: " . $audit_stmt->error;
}

$audit_stmt->close();
        }


        /*
         * Display result message.
         */
        if ($generated > 0 && $skipped > 0) {

            $success =
                $generated .
                " payroll record(s) generated successfully. " .
                $skipped .
                " employee(s) were skipped because payroll already exists.";

        } elseif ($generated > 0) {

            $success =
                $generated .
                " payroll record(s) generated successfully.";

        } elseif ($skipped > 0) {

            $error =
                "Payroll has already been generated for all selected active employees.";

        } else {

            $error =
                "No payroll records were generated.";

        }
    }
}

require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");
?>


<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow">

                <div
                    class="card-header text-white"
                    style="background:#1C5FA2;"
                >
                    <h3 class="mb-0">
                        Generate Payroll
                    </h3>
                </div>

                <div class="card-body">

                    <?php if ($error != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>

                    <?php } ?>

                    <?php if ($success != "") { ?>

                        <div class="alert alert-success">
                            <?= htmlspecialchars($success); ?>
                        </div>

                    <?php } ?>


                    <div class="alert alert-info">

                        <strong>Payroll Generation</strong>

                        <br>

                        Payroll will be generated for
                        <strong>all active employees</strong>
                        for the selected month and year.

                        <br><br>

                        Existing payroll records for the selected
                        month and year will not be duplicated.

                    </div>


                    <form method="POST">


                        <div class="mb-3">

                            <label class="form-label">
                                Payroll Month
                            </label>

                            <select
                                name="payroll_month"
                                class="form-select"
                                required
                            >

                                <?php
                                $months = [
                                    "January",
                                    "February",
                                    "March",
                                    "April",
                                    "May",
                                    "June",
                                    "July",
                                    "August",
                                    "September",
                                    "October",
                                    "November",
                                    "December"
                                ];

                                foreach ($months as $month_name) {
                                ?>

                                    <option value="<?= $month_name; ?>">
                                        <?= $month_name; ?>
                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Payroll Year
                            </label>

                            <input
                                type="number"
                                name="payroll_year"
                                class="form-control"
                                value="<?= date('Y'); ?>"
                                min="2000"
                                max="2100"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            name="generate"
                            class="btn text-white"
                            style="background:#0291DA;"
                            onclick="return confirm(
                                'Are you sure you want to generate payroll for all active employees for the selected month and year?'
                            );"
                        >
                            Generate Payroll for All Staff
                        </button>


                        <a
                            href="payroll.php"
                            class="btn btn-secondary"
                        >
                            Cancel
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once("../includes/footer.php"); ?>
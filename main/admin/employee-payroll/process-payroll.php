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

    $month = $_POST['payroll_month'] ?? "";
    $year = intval($_POST['payroll_year']);

    $generation_type = $_POST['generation_type'] ?? "all";
    $selected_employee_id = intval($_POST['employee_id'] ?? 0);

    // Fixed pension contribution
    $pension = 40000;

    /*
     * Store selected employee name
     * so it remains available after the loop.
     */
    $selected_employee_name = "";


    /*
     * Validate staff selection
     */
    if ($generation_type === "one" && $selected_employee_id <= 0) {

        $error = "Please select an employee.";

    } else {

        /*
         * Get tax percentage
         */
        $settings = $conn->query("
            SELECT tax_percentage
            FROM payroll_settings
            LIMIT 1
        ");

        $config = $settings ? $settings->fetch_assoc() : null;

        $tax_percentage = $config['tax_percentage'] ?? 0;


        /*
         * Get employees to process
         */
        if ($generation_type === "one") {

            $employees_query = $conn->prepare("
                SELECT
                    employee_id,
                    employee_number,
                    first_name,
                    last_name,
                    position_id
                FROM employees
                WHERE employee_id = ?
                AND status = 'Active'
            ");

            $employees_query->bind_param(
                "i",
                $selected_employee_id
            );

            $employees_query->execute();

            $employees_result = $employees_query->get_result();

        } else {

            $employees_result = $conn->query("
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
        }


        /*
         * Check employees query
         */
        if (!$employees_result) {

            $error = "Unable to retrieve employee information.";

        } elseif ($employees_result->num_rows === 0) {

            $error = "No active employee was found.";

        } else {

            $generated = 0;
            $skipped = 0;


            /*
             * Process employee(s)
             */
            while ($employee = $employees_result->fetch_assoc()) {

                /*
                 * Save the employee name for
                 * single-staff generation.
                 */
                if ($generation_type === "one") {

                    $selected_employee_name =
                        $employee['first_name'] . " " .
                        $employee['last_name'];
                }


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
                $tax =
                    ($basic_salary * $tax_percentage) / 100;


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
             * Close prepared employee statement
             */
            if ($generation_type === "one") {
                $employees_query->close();
            }


            /*
             * Create Audit Log
             */
            if ($generated > 0) {

                $admin_id = $_SESSION['admin_id'];

                if ($generation_type === "one") {

                    $description =
                        "Generated payroll for " .
                        $selected_employee_name .
                        " for " .
                        $month . " " . $year . ".";

                } else {

                    if ($skipped > 0) {

                        $description =
                            "Generated payroll for " .
                            $month . " " . $year .
                            " for " .
                            $generated .
                            " active employee(s). " .
                            $skipped .
                            " employee(s) were skipped because payroll already exists.";

                    } else {

                        $description =
                            "Generated payroll for " .
                            $month . " " . $year .
                            " for " .
                            $generated .
                            " active employee(s).";
                    }
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

                    $error =
                        "Payroll generated, but audit log could not be created: "
                        . $audit_stmt->error;
                }

                $audit_stmt->close();
            }


            /*
             * Display result message
             */
            if ($generation_type === "one") {

                if ($generated > 0) {

                    $success =
                        "Payroll generated successfully for " .
                        $selected_employee_name .
                        " for " .
                        $month . " " . $year . ".";

                } elseif ($skipped > 0) {

                    $error =
                        "Payroll already exists for " .
                        $selected_employee_name .
                        " for " .
                        $month . " " . $year . ".";

                } else {

                    $error =
                        "No payroll record was generated.";
                }

            } else {

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
                        "Payroll has already been generated for all active employees.";

                } else {

                    $error =
                        "No payroll records were generated.";
                }
            }
        }
    }
}


/*
 * Get active employees for the dropdown
 */
$active_employees = $conn->query("
    SELECT
        employee_id,
        employee_number,
        first_name,
        last_name
    FROM employees
    WHERE status = 'Active'
    ORDER BY first_name, last_name
");


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

                        You can generate payroll for
                        <strong>all active employees</strong>
                        or for
                        <strong>one employee</strong>.

                        <br><br>

                        Existing payroll records for the selected
                        month and year will not be duplicated.

                    </div>


                    <form method="POST">


                        <!-- Generation Type -->

                        <div class="mb-3">

                            <label class="form-label">
                                Generate Payroll For
                            </label>

                            <select
                                name="generation_type"
                                id="generation_type"
                                class="form-select"
                                required
                            >

                                <option value="all">
                                    All Active Staff
                                </option>

                                <option value="one">
                                    One Staff
                                </option>

                            </select>

                        </div>


                        <!-- Employee Selection -->

                        <div
                            class="mb-3"
                            id="employeeSelection"
                            style="display:none;"
                        >

                            <label class="form-label">
                                Select Staff
                            </label>

                            <select
                                name="employee_id"
                                id="employee_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Select Staff --
                                </option>

                                <?php
                                if ($active_employees) {

                                    while (
                                        $employee_option =
                                        $active_employees->fetch_assoc()
                                    ) {
                                ?>

                                    <option
                                        value="<?= $employee_option['employee_id']; ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $employee_option['first_name']
                                            . " "
                                            . $employee_option['last_name']
                                            . " ("
                                            . $employee_option['employee_number']
                                            . ")"
                                        ); ?>

                                    </option>

                                <?php
                                    }
                                }
                                ?>

                            </select>

                        </div>


                        <!-- Payroll Month -->

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

                                    <option
                                        value="<?= $month_name; ?>"
                                    >

                                        <?= $month_name; ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- Payroll Year -->

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


                        <!-- Generate Button -->

                        <button
                            type="submit"
                            name="generate"
                            class="btn text-white"
                            style="background:#0291DA;"
                            onclick="return confirmGeneration();"
                        >

                            <i class="bi bi-calculator"></i>

                            Generate Payroll

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


<script>

function confirmGeneration() {

    const type =
        document.getElementById("generation_type").value;

    const month =
        document.querySelector(
            '[name="payroll_month"]'
        ).value;

    const year =
        document.querySelector(
            '[name="payroll_year"]'
        ).value;


    if (type === "one") {

        const employee =
            document.getElementById("employee_id");

        if (employee.value === "") {

            alert("Please select a staff member.");

            return false;
        }

        const employeeName =
            employee.options[
                employee.selectedIndex
            ].text;


        return confirm(
            "Are you sure you want to generate payroll for " +
            employeeName +
            " for " +
            month +
            " " +
            year +
            "?"
        );

    }


    return confirm(
        "Are you sure you want to generate payroll for all active employees for " +
        month +
        " " +
        year +
        "?"
    );
}


/*
 * Show / hide employee selection
 */
document
    .getElementById("generation_type")
    .addEventListener("change", function () {

        const employeeSelection =
            document.getElementById("employeeSelection");

        const employee =
            document.getElementById("employee_id");


        if (this.value === "one") {

            employeeSelection.style.display = "block";

            employee.required = true;

        } else {

            employeeSelection.style.display = "none";

            employee.required = false;

            employee.value = "";

        }

    });

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<?php require_once("../includes/footer.php"); ?>
<?php

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../../vendor/autoload.php");

use Dompdf\Dompdf;
use Dompdf\Options;


// Fetch payroll records
$sql = "
SELECT
    employees.employee_number,
    CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
    payroll.payroll_month,
    payroll.payroll_year,
    payroll.basic_salary,
    payroll.allowances,
    payroll.deductions,
    payroll.tax,
    payroll.pension,
    payroll.net_salary,
    payroll.payment_status

FROM payroll

INNER JOIN employees
ON payroll.employee_id = employees.employee_id

ORDER BY
    payroll.payroll_year DESC,
    payroll.payroll_id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}


// Configure Dompdf
$options = new Options();

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);


$dompdf = new Dompdf($options);


// Start HTML
$html = '

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<style>

@page {
    size: A4 landscape;
    margin: 25px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    color: #333;
    font-size: 9px;
}

.header {
    text-align: center;
    margin-bottom: 15px;
}

.system-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
}

.report-title {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
}

.generated {
    font-size: 9px;
    color: #666;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background-color: #1F4E78;
    color: white;
    padding: 7px 4px;
    border: 1px solid #ccc;
    font-size: 8px;
}

td {
    padding: 6px 4px;
    border: 1px solid #ccc;
    font-size: 8px;
}

tbody tr:nth-child(even) {
    background-color: #f5f7fa;
}

.money {
    text-align: right;
}

.center {
    text-align: center;
}

.footer {
    margin-top: 15px;
    text-align: center;
    font-size: 8px;
    color: #777;
}

</style>

</head>

<body>

<div class="header">

<div class="system-title">
EMPLOYEE MANAGEMENT & PAYROLL SYSTEM
</div>

<div class="report-title">
PAYROLL REPORT
</div>

<div class="generated">
Generated: ' . date("d M Y, h:i A") . '
</div>

</div>


<table>

<thead>

<tr>

<th>Employee No</th>

<th>Employee Name</th>

<th>Month</th>

<th>Year</th>

<th>Basic Salary</th>

<th>Allowances</th>

<th>Gross Salary</th>

<th>Deductions</th>

<th>Tax</th>

<th>Pension</th>

<th>Net Salary</th>

<th>Status</th>

</tr>

</thead>

<tbody>
';


// Add payroll records
while ($row = mysqli_fetch_assoc($result)) {

    $gross_salary =
        (float)$row["basic_salary"]
        +
        (float)$row["allowances"];


    $html .= '

<tr>

<td class="center">
' . htmlspecialchars($row["employee_number"]) . '
</td>

<td>
' . htmlspecialchars($row["employee_name"]) . '
</td>

<td class="center">
' . htmlspecialchars($row["payroll_month"]) . '
</td>

<td class="center">
' . htmlspecialchars($row["payroll_year"]) . '
</td>

<td class="money">
' . number_format((float)$row["basic_salary"], 2) . '
</td>

<td class="money">
' . number_format((float)$row["allowances"], 2) . '
</td>

<td class="money">
' . number_format($gross_salary, 2) . '
</td>

<td class="money">
' . number_format((float)$row["deductions"], 2) . '
</td>

<td class="money">
' . number_format((float)$row["tax"], 2) . '
</td>

<td class="money">
' . number_format((float)$row["pension"], 2) . '
</td>

<td class="money">
' . number_format((float)$row["net_salary"], 2) . '
</td>

<td class="center">
' . htmlspecialchars($row["payment_status"]) . '
</td>

</tr>

';

}


$html .= '

</tbody>

</table>


<div class="footer">

Employee Management & Payroll System
<br>
Generated on ' . date("d M Y") . '

</div>


</body>

</html>

';


// Load HTML
$dompdf->loadHtml($html);


// Set paper size
$dompdf->setPaper(
    'A4',
    'landscape'
);


// Generate PDF
$dompdf->render();


// Download PDF
$filename =
    "Payroll_Report_" .
    date("Y-m-d_H-i-s") .
    ".pdf";


$dompdf->stream(
    $filename,
    [
        "Attachment" => true
    ]
);

exit;

?>
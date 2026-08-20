<?php

require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


// Get payroll records
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


// Create spreadsheet
$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle("Payroll Report");


// Main title
$sheet->mergeCells("A1:L1");

$sheet->setCellValue(
    "A1",
    "EMPLOYEE MANAGEMENT & PAYROLL SYSTEM"
);

$sheet->getStyle("A1")->getFont()->setBold(true);
$sheet->getStyle("A1")->getFont()->setSize(16);

$sheet->getStyle("A1")->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);


// Report title
$sheet->mergeCells("A2:L2");

$sheet->setCellValue(
    "A2",
    "PAYROLL REPORT"
);

$sheet->getStyle("A2")->getFont()->setBold(true);
$sheet->getStyle("A2")->getFont()->setSize(13);

$sheet->getStyle("A2")->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Date generated
$sheet->mergeCells("A3:L3");

$sheet->setCellValue(
    "A3",
    "Generated: " . date("d M Y, h:i A")
);

$sheet->getStyle("A3")->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Column headings
$headers = [
    "Employee No",
    "Employee Name",
    "Payroll Month",
    "Payroll Year",
    "Basic Salary",
    "Allowances",
    "Gross Salary",
    "Deductions",
    "Tax",
    "Pension",
    "Net Salary",
    "Payment Status"
];

$column = "A";

foreach ($headers as $header) {

    $sheet->setCellValue(
        $column . "5",
        $header
    );

    $column++;
}


// Header styling
$sheet->getStyle("A5:L5")->getFont()->setBold(true);

$sheet->getStyle("A5:L5")->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$sheet->getStyle("A5:L5")->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB("1F4E78");

$sheet->getStyle("A5:L5")->getFont()
    ->getColor()
    ->setARGB("FFFFFF");


// Insert payroll records
$rowNumber = 6;

while ($row = mysqli_fetch_assoc($result)) {

    // Gross salary calculation
    $gross_salary =
        (float)$row["basic_salary"]
        +
        (float)$row["allowances"];


    $sheet->setCellValue(
        "A" . $rowNumber,
        $row["employee_number"]
    );

    $sheet->setCellValue(
        "B" . $rowNumber,
        $row["employee_name"]
    );

    $sheet->setCellValue(
        "C" . $rowNumber,
        $row["payroll_month"]
    );

    $sheet->setCellValue(
        "D" . $rowNumber,
        $row["payroll_year"]
    );

    $sheet->setCellValue(
        "E" . $rowNumber,
        $row["basic_salary"]
    );

    $sheet->setCellValue(
        "F" . $rowNumber,
        $row["allowances"]
    );

    $sheet->setCellValue(
        "G" . $rowNumber,
        $gross_salary
    );

    $sheet->setCellValue(
        "H" . $rowNumber,
        $row["deductions"]
    );

    $sheet->setCellValue(
        "I" . $rowNumber,
        $row["tax"]
    );

    $sheet->setCellValue(
        "J" . $rowNumber,
        $row["pension"]
    );

    $sheet->setCellValue(
        "K" . $rowNumber,
        $row["net_salary"]
    );

    $sheet->setCellValue(
        "L" . $rowNumber,
        $row["payment_status"]
    );

    $rowNumber++;
}


// Currency formatting
if ($rowNumber > 6) {

    $sheet->getStyle(
        "E6:K" . ($rowNumber - 1)
    )
    ->getNumberFormat()
    ->setFormatCode(
        '#,##0.00'
    );

}


// Add borders
$sheet->getStyle(
    "A5:L" . ($rowNumber - 1)
)
->getBorders()
->getAllBorders()
->setBorderStyle(
    Border::BORDER_THIN
);


// Vertical alignment
$sheet->getStyle(
    "A5:L" . ($rowNumber - 1)
)
->getAlignment()
->setVertical(
    Alignment::VERTICAL_CENTER
);


// Auto-size columns
foreach (range("A", "L") as $columnID) {

    $sheet->getColumnDimension($columnID)
        ->setAutoSize(true);

}


// Freeze table headings
$sheet->freezePane("A6");


// Filename
$filename =
    "Payroll_Report_" .
    date("Y-m-d_H-i-s") .
    ".xlsx";


// Download headers
header(
    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
);

header(
    "Content-Disposition: attachment; filename=\"" .
    $filename .
    "\""
);

header("Cache-Control: max-age=0");


// Generate Excel file
$writer = new Xlsx($spreadsheet);

$writer->save("php://output");

exit;

?>
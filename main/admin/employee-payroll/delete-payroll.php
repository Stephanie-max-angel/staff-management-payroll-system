<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: payroll.php");
    exit();
}

$payroll_id = intval($_GET['id']);





$stmt = $conn->prepare("
SELECT payroll_id
FROM payroll
WHERE payroll_id=?
");

$stmt->bind_param("i",$payroll_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    header("Location: payroll.php");

    exit();

}

$stmt->close();





$stmt = $conn->prepare("
DELETE FROM payroll
WHERE payroll_id=?
");

$stmt->bind_param("i",$payroll_id);





if($stmt->execute()){

    header("Location: payroll.php?deleted=1");

    exit();

}else{

    echo "<script>

    alert('Unable to delete payroll.');

    window.location='payroll.php';

    </script>";

}

$stmt->close();

$conn->close();

?>






<?php

if(isset($_GET['deleted'])){

?>

<div class="alert alert-success">

Payroll deleted successfully.

</div>

<?php

}

?>




<td>

<a
href="view-payroll.php?id=<?= $row['payroll_id'];?>"
class="btn btn-info btn-sm">

View

</a>

<a
href="edit-payroll.php?id=<?= $row['payroll_id'];?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="payslip.php?id=<?= $row['payroll_id'];?>"
class="btn btn-success btn-sm">

Payslip

</a>

<a
href="delete-payroll.php?id=<?= $row['payroll_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this payroll record?');">

Delete

</a>

</td>






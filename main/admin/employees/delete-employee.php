<?php
session_start();

require_once("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

$employee_id = intval($_GET['id']);



$stmt = $conn->prepare("
SELECT photo
FROM employees
WHERE employee_id=?
");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result = $stmt->get_result();

$employee = $result->fetch_assoc();

$stmt->close();

if(!$employee){

    header("Location: employees.php");

    exit();

}

if(!empty($employee['photo'])){

    $photoPath = "../uploads/".$employee['photo'];

    if(file_exists($photoPath)){

        unlink($photoPath);

    }

}

$stmt = $conn->prepare("
DELETE FROM employees
WHERE employee_id=?
");

$stmt->bind_param("i",$employee_id);


if($stmt->execute()){

    header("Location: employees.php?deleted=1");

    exit();

}else{

    echo "<script>

    alert('Unable to delete employee.');

    window.location='employees.php';

    </script>";

}

$stmt->close();

$conn->close();

?>


<?php

if(isset($_GET['added'])){

?>

<div class="alert alert-success">

Employee added successfully.

</div>

<?php

}

if(isset($_GET['updated'])){

?>

<div class="alert alert-success">

Employee updated successfully.

</div>

<?php

}

if(isset($_GET['deleted'])){

?>

<div class="alert alert-success">

Employee deleted successfully.

</div>

<?php

}

?>
<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: leave-types.php");
    exit();
}

$leave_type_id = intval($_GET['id']);
$error = "";

// Retrieve leave type
$stmt = $conn->prepare("
SELECT *
FROM leave_types
WHERE leave_type_id = ?
");

$stmt->bind_param("i", $leave_type_id);
$stmt->execute();

$result = $stmt->get_result();
$leave = $result->fetch_assoc();
$stmt->close();

if (!$leave) {
    header("Location: leave-types.php");
    exit();
}





if(isset($_POST['update'])){

    $leave_name = trim($_POST['leave_name']);
    $max_days = intval($_POST['max_days']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    // Check duplicate name (excluding current record)
    $check = $conn->prepare("
        SELECT leave_type_id
        FROM leave_types
        WHERE leave_name = ?
        AND leave_type_id != ?
    ");

    $check->bind_param(
        "si",
        $leave_name,
        $leave_type_id
    );

    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){

        $error = "Leave type already exists.";

    }else{

        $stmt = $conn->prepare("
        UPDATE leave_types

        SET

        leave_name=?,

        max_days=?,

        description=?,

        status=?

        WHERE leave_type_id=?
        ");

        $stmt->bind_param(
            "sissi",
            $leave_name,
            $max_days,
            $description,
            $status,
            $leave_type_id
        );

        if($stmt->execute()){

            header("Location: leave-types.php?updated=1");
            exit();

        }else{

            $error = "Unable to update leave type.";

        }

        $stmt->close();

    }

    $check->close();

}
?>












<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>Edit Leave Type</h3>

</div>

<div class="card-body">






<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>





<form method="POST">

<div class="mb-3">

<label>Leave Type</label>

<input

type="text"

name="leave_name"

class="form-control"

value="<?= htmlspecialchars($leave['leave_name']); ?>"

required>

</div>

<div class="mb-3">

<label>Maximum Days</label>

<input

type="number"

name="max_days"

class="form-control"

value="<?= $leave['max_days']; ?>"

required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea

name="description"

rows="4"

class="form-control"><?= htmlspecialchars($leave['description']); ?></textarea>

</div>

<div class="mb-3">

<label>Status</label>

<select

name="status"

class="form-select">

<option value="Active"

<?= ($leave['status']=="Active") ? "selected" : ""; ?>>

Active

</option>

<option value="Inactive"

<?= ($leave['status']=="Inactive") ? "selected" : ""; ?>>

Inactive

</option>

</select>

</div>

<button

type="submit"

name="update"

class="btn text-white"

style="background:#0291DA;">

Update Leave Type

</button>

<a

href="leave-types.php"

class="btn btn-secondary">

Cancel

</a>

</form>





</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once("../includes/footer.php");
?>
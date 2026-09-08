<?php

session_start();

session_unset();
session_destroy();

header("Location: /employee-management-system/main-login.php");
exit();

?>
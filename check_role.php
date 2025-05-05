<?php
session_start();

function checkRole($allowedRoles) {
    if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], $allowedRoles)) {
        echo '<script>alert("Access Denied!!.."); window.location.href="/parksystem/login.html";</script>';
        exit();
    }
}
?>

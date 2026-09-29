<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();

if(isset($_SESSION['id'])){
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
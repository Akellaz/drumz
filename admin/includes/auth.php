<?php
require_once __DIR__ . '/../../config.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $current = basename($_SERVER['SCRIPT_NAME']);
    if ($current !== 'login.php') {
        header('Location: login.php');
        exit;
    }
}
?>
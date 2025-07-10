<?php
// src/View/templates/header.php
// --------------------------------------------------
// Assumes PROJECT_ROOT is one level above /public
// Build a URL prefix that always points to /public
$publicUrl = dirname($_SERVER['SCRIPT_NAME']); //  → '/hkid_appointment_system/public'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'HKID App') ?></title>

    <!-- main stylesheet -->
    <link rel="stylesheet"
          href="<?= $publicUrl ?>/css/style.css?v=1.0">
          <!--  ?v=1.0 “cache-buster” forces the browser to fetch on change -->

    <!-- simple reset so table borders etc. look cleaner -->
    <style>
        * { box-sizing:border-box; }
        body { margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif; }
        table { border-collapse:collapse; width:100%; }
        th,td { padding:.5rem; border:1px solid #ddd; }
        th { background:#f1f1f1; text-align:left; }
        a { color:#4b0082; }
    </style>
</head>
<body>
<header style="padding:.5rem 1rem; border-bottom:1px solid #ccc;">
    <nav style="display:flex;gap:1rem;">
        <a href="<?= $publicUrl ?>/index.php?page=appointment_form">Home (Appointment Form)</a>
        <a href="<?= $publicUrl ?>/index.php?page=admin_login">Admin Login</a>
    </nav>
</header>

<main style="padding:1rem;max-width:1200px;margin:0 auto;">



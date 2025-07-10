<?php
// src/View/layout.php
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'HKID Appointment System', ENT_QUOTES, 'UTF-8') ?></title>
  <!-- relative path into public/css/ -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <header>
    <nav>
      <a href="index.php?page=appointment_form">Home (Appointment Form)</a>
      <a href="index.php?page=admin_login">Admin Login</a>
    </nav>
  </header>

  <main class="container">
    <?= $content ?>
  </main>

  <footer>
    <p>&copy; <?= date('Y') ?> HKID Appointment System</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>



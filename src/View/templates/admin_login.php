<?php
// src/View/templates/admin_login.php
// $errors : array of error messages
// $old    : array of old input (e.g. username)

$errors = $errors   ?? [];
$old    = $old      ?? [];
?>

<div class="container" style="max-width:400px;margin:2rem auto;">
  <h1>Admin Login</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert" style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:4px;margin-bottom:1rem;">
      <ul style="margin:0; padding-left:1.25rem;">
        <?php foreach($errors as $e): ?>
          <li><?= htmlspecialchars($e,ENT_QUOTES,'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="index.php?page=admin_authenticate" method="POST" novalidate>
    <div class="form-group" style="margin-bottom:1rem;">
      <label for="username">Username</label>
      <input
        type="text"
        id="username"
        name="username"
        required
        autofocus
        value="<?= htmlspecialchars($old['username'] ?? '',ENT_QUOTES,'UTF-8') ?>"
        style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;"
      >
    </div>

    <div class="form-group" style="margin-bottom:1.5rem;">
      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:4px;"
      >
    </div>

    <button
      type="submit"
      style="background:#007bff;color:#fff;padding:0.75rem 1.5rem;border:none;border-radius:4px;cursor:pointer;"
    >
      Log In
    </button>
  </form>
</div>



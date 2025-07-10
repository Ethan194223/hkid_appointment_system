<?php
// src/View/templates/appointment_form.php

// Ensure these exist (the controller should populate them, or we fall back to empty arrays)
$errors   = $errors   ?? [];
$formData = $formData ?? [];
?>

<h1>Book Your HKID Appointment</h1>
<p>Please fill in the details below to book your appointment.</p>

<?php if (!empty($errors)): ?>
  <div class="alert" style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
    <strong>Please correct the following errors:</strong>
    <ul style="margin: 0; padding-left: 1.25rem;">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form action="index.php?page=submit_appointment" method="POST" id="appointmentForm">
    
  <div class="form-group" style="margin-bottom: 1rem;">
    <label for="name" style="display:block; margin-bottom:0.5rem;">Full Name:</label>
    <input
      type="text"
      id="name"
      name="name"
      required
      value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
      style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
    >
  </div>

  <div class="form-group" style="margin-bottom: 1rem;">
    <label for="email" style="display:block; margin-bottom:0.5rem;">Email Address:</label>
    <input
      type="email"
      id="email"
      name="email"
      required
      value="<?= htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
      style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
    >
  </div>

  <div class="form-group" style="margin-bottom: 1rem;">
    <label for="phone" style="display:block; margin-bottom:0.5rem;">Phone Number:</label>
    <input
      type="tel"
      id="phone"
      name="phone"
      required
      pattern="[0-9]{8,15}"
      title="Please enter 8–15 digits"
      value="<?= htmlspecialchars($formData['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
      style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
    >
  </div>

  <div class="form-group" style="margin-bottom: 1.5rem;">
    <label for="date" style="display:block; margin-bottom:0.5rem;">Preferred Date:</label>
    <input
      type="date"
      id="date"
      name="date"
      required
      min="<?= date('Y-m-d') ?>"
      value="<?= htmlspecialchars($formData['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
      style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
    >
  </div>

  <button
    type="submit"
    style="background-color:#007bff; color:#fff; padding:0.75rem 1.5rem; border:none; border-radius:4px; cursor:pointer;"
  >
    Book Appointment
  </button>
</form>


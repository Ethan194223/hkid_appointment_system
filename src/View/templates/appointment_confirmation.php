<?php
// src/View/templates/appointment_confirmation.php
// ------------------------------------------------

// Make sure the session is available (should already be started in index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pull the reference ID the controller stored:
$ref = $_SESSION['last_appointment_ref'] ?? null;

// remove it so it won’t appear again by accident (optional during dev)
unset($_SESSION['last_appointment_ref']);

// Handy helper: figure out “…/public” URL prefix once
$publicUrl = dirname($_SERVER['SCRIPT_NAME']); // e.g. /hkid_appointment_system/public
?>
<div class="card">

    <h2 style="margin-top:0">Thank You!</h2>

    <?php if ($ref): ?>
        <p>Your appointment has been successfully booked.</p>
        <p>
            Your appointment reference is:
            <span style="display:inline-block;
                         font-size:1.15em;
                         font-weight:600;
                         padding:.15em .35em;
                         background:#e8f3ff;
                         color:#0069d9;
                         border-radius:4px;">
                <?= htmlspecialchars($ref) ?>
            </span>
        </p>
        <p>Please keep this reference for your records.
           We have sent a confirmation email (if email sending is implemented).</p>
    <?php else: ?>
        <p style="color:#c00;font-weight:500">
            Sorry – we could not retrieve your reference.
            Please contact support if you believe the booking was made.
        </p>
    <?php endif; ?>

    <div style="margin-top:1.75rem;display:flex;gap:.75rem;justify-content:center;">
        <a class="btn-primary"
           href="<?= $publicUrl ?>/index.php?page=appointment_form">Book Another Appointment</a>

        <a class="btn-secondary"
           href="<?= $publicUrl ?>/index.php?page=appointment_form">Back to Home</a>
    </div>
</div>

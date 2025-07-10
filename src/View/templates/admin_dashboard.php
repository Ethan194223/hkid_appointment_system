<?php
// src/View/templates/admin_dashboard.php
?>
<h1>Admin Dashboard</h1>
<p><a href="index.php?page=admin_logout">Log out</a></p>

<?php if (!empty($dashboard_error)): ?>
    <div style="color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;
                padding:10px;border-radius:4px;margin-bottom:20px;">
        <?= htmlspecialchars($dashboard_error) ?>
    </div>
<?php endif; ?>
 
<?php if (empty($appointments)): ?>
    <p>No appointments have been booked yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Ref</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Booked at</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $appt): ?>
            <tr>
                <td><?= htmlspecialchars($appt['ref']) ?></td>
                <td><?= htmlspecialchars($appt['name']) ?></td>
                <td><?= htmlspecialchars($appt['email']) ?></td>
                <td><?= htmlspecialchars($appt['phone']) ?></td>
                <td><?= htmlspecialchars($appt['date']) ?></td>
                <td><?= htmlspecialchars($appt['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


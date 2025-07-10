<?php
// public/index.php  ── served by Apache

// 0. Show PHP errors while you’re developing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); // Also show startup errors
error_reporting(E_ALL);

// 1. Session
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Project constants
// PROJECT_ROOT is the directory containing 'public', 'src', 'config', etc.
define('PROJECT_ROOT', dirname(__DIR__));    // …/hkid_appointment_system
// PUBLIC_URL is the base web path to your public directory
define('PUBLIC_URL',  '/hkid_appointment_system/public/');   // <- adjust if your server setup differs

// 3. Bootstrap / autoloader
// Make sure core.php exists and correctly loads your classes (e.g., via an autoloader or direct requires)
if (file_exists(PROJECT_ROOT . '/src/core.php')) {
    require PROJECT_ROOT . '/src/core.php';
} else {
    error_log("Critical Error: core.php not found at " . PROJECT_ROOT . '/src/core.php');
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1><p>A critical system file is missing. Please contact support.</p>";
    exit;
}

// Ensure your controller namespace is correct
use Src\Controller\AppointmentController;

// Instantiate controller - wrap in try-catch for robustness
try {
    $ctrl = new AppointmentController();
} catch (\Throwable $e) {
    error_log("Error instantiating AppointmentController: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1><p>There was a problem loading a core part of the application.</p>";
    exit;
}

// Determine the page from GET parameter, default to 'appointment_form'
$page = $_GET['page'] ?? 'appointment_form';

// Routing logic
switch ($page) {
    case 'appointment_form':
    case 'home': // Allow 'home' to also show the appointment form
        $ctrl->showAppointmentForm();
        break;

    case 'submit_appointment':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->handleAppointmentSubmission(); // This method should handle its own redirects or output
        } else {
            // Redirect if accessed via GET
            header('Location: ' . PUBLIC_URL . 'index.php?page=appointment_form');
            exit;
        }
        break;

    case 'appointment_confirmation':
        $ctrl->showAppointmentConfirmation();
        break;

    case 'admin_login':
        $ctrl->showAdminLoginForm();
        break;

    case 'admin_authenticate':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->handleAdminLogin();
        } else {
            header('Location: ' . PUBLIC_URL . 'index.php?page=admin_login');
            exit;
        }
        break;

    case 'admin_dashboard':
        $ctrl->showAdminDashboard();
        break;

    case 'admin_logout':
        $ctrl->handleAdminLogout();
        break;

    default:
        http_response_code(404);
        // Consider including a more user-friendly 404 template page
        // include PROJECT_ROOT . '/src/View/templates/404.php';
        echo '<h1>404 – Page not found</h1><p>The requested page was not found on this server.</p>';
        break;
}

?>

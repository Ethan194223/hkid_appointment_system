<?php
// src/Controller/AppointmentController.php
namespace Src\Controller;

// Note: For a more maintainable approach, consider centralizing your DB connection
// using a helper class like Src\Lib\Database::pdo() and your config/database_config.php.
// For now, direct PDO instantiation is used as per recent examples for MAMP on Windows.

class AppointmentController
{
    /**
     * Show the appointment booking form.
     */
    public function showAppointmentForm(): void
    {
        $pageTitle = 'Book Your HKID Appointment';

        $viewData = [
            'pageTitle' => $pageTitle, // Pass pageTitle to be extracted
            'errors' => $_SESSION['form_errors'] ?? [],
            'formData' => $_SESSION['form_data'] ?? []
        ];
        unset($_SESSION['form_errors'], $_SESSION['form_data']); 

        ob_start();
        if (is_array($viewData)) {
            extract($viewData);
        }
        include PROJECT_ROOT . '/src/View/templates/appointment_form.php';
        $content = ob_get_clean();

        include PROJECT_ROOT . '/src/View/templates/header.php'; // $pageTitle is available here
        echo $content;
        include PROJECT_ROOT . '/src/View/templates/footer.php';
    }

    /**
     * Handle form submission (POST) and insert into MySQL.
     */
    public function handleAppointmentSubmission(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=appointment_form';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $name  = trim($_POST['name']  ?? '');
        $email_candidate = trim($_POST['email'] ?? '');
        $email = filter_var($email_candidate, FILTER_VALIDATE_EMAIL);
        $phone = trim($_POST['phone'] ?? '');
        $date_string  = trim($_POST['date']  ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = "Full Name is required.";
        }
        if (empty($email_candidate)) {
            $errors[] = "Email Address is required.";
        } elseif ($email === false) {
            $errors[] = "Invalid Email Address format.";
        }
        if (empty($phone)) { 
            $errors[] = "Phone Number is required.";
        }
        if (empty($date_string)) {
            $errors[] = "Preferred Date is required.";
        } else {
            $d = \DateTime::createFromFormat('Y-m-d', $date_string);
            if (!$d || $d->format('Y-m-d') !== $date_string) {
                $errors[] = "Invalid Preferred Date format. Please use YYYY-MM-DD.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=appointment_form';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $ref   = bin2hex(random_bytes(8));

        $dsn      = 'mysql:host=127.0.0.1;port=8889;dbname=hkid_app;charset=utf8mb4';
        $username = 'root';
        $password = 'root';

        try {
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $sql = 'INSERT INTO appointments (ref, name, email, phone, date)
                    VALUES (:ref, :name, :email, :phone, :date)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ref'   => $ref,
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':date'  => $date_string,
            ]);

            $_SESSION['last_appointment_ref'] = $ref; 
            unset($_SESSION['form_data'], $_SESSION['form_errors']);

            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=appointment_confirmation';
            header('Location: ' . $redirectUrl);
            exit;

        } catch (\PDOException $e) {
            error_log("Database Error on submission: " . $e->getMessage() . " (DSN: " . $dsn . ")");
            $_SESSION['form_errors'] = ["A database error occurred: " . $e->getMessage()];
            $_SESSION['form_data'] = $_POST;
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=appointment_form';
            header('Location: ' . $redirectUrl);
            exit;
        } catch (\Exception $e) {
            error_log("General Error on submission: " . $e->getMessage());
            $_SESSION['form_errors'] = ["An unexpected error occurred."];
            $_SESSION['form_data'] = $_POST;
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=appointment_form';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /**
     * Show a simple “thank you” / confirmation page.
     */
    public function showAppointmentConfirmation(): void
    {
        $pageTitle = 'Appointment Confirmed';
        $viewData = ['pageTitle' => $pageTitle]; // $ref will be read from session in the view

        ob_start();
        if (is_array($viewData)) {
            extract($viewData);
        }
        include PROJECT_ROOT . '/src/View/templates/appointment_confirmation.php';
        $content = ob_get_clean();

        include PROJECT_ROOT . '/src/View/templates/header.php';
        echo $content;
        include PROJECT_ROOT . '/src/View/templates/footer.php';
    }

    // --- Admin Methods ---

    /**
     * Show the Admin Login form.
     */
    public function showAdminLoginForm(): void
    {
        $pageTitle = 'Admin Login';

        // Grab any previous errors / old inputs from session
        $errors = $_SESSION['admin_login_errors'] ?? [];
        $old    = $_SESSION['admin_login_data']   ?? []; // For repopulating username if desired
        unset($_SESSION['admin_login_errors'], $_SESSION['admin_login_data']);

        $viewData = ['pageTitle' => $pageTitle, 'errors' => $errors, 'old' => $old];

        ob_start();
        if (is_array($viewData)) {
            extract($viewData); // Makes $pageTitle, $errors, $old available
        }
        include PROJECT_ROOT . '/src/View/templates/admin_login.php';
        $content = ob_get_clean();

        include PROJECT_ROOT . '/src/View/templates/header.php';
        echo $content;
        include PROJECT_ROOT . '/src/View/templates/footer.php';
    }

    /**
     * Handle Admin Login POST.
     */
    public function handleAdminLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=admin_login';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? ''; // Password should not be trimmed usually

        $errors = [];
        if (empty($username)) {
            $errors[] = 'Username is required.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        // Hardcoded check for demo - REPLACE with secure database check and hashed passwords
        if (empty($errors)) {
            if ($username === 'admin' && $password === 'secret123') {
                $_SESSION['admin_logged_in'] = true;
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true); 
                $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=admin_dashboard';
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                $errors[] = 'Invalid username or password.';
            }
        }

        // On failure, save errors + old data (username) and redirect back
        $_SESSION['admin_login_errors'] = $errors;
        $_SESSION['admin_login_data']   = ['username' => $username]; // Only repopulate username
        $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=admin_login';
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Show the Admin Dashboard (list all appointments).
     */
    public function showAdminDashboard(): void
    {
        // Protect route: ensure admin is logged in
        if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=admin_login';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $pageTitle = 'Admin Dashboard';
        $appointments = []; // Initialize appointments array

        // Fetch from DB
        // Consistent with direct PDO instantiation in handleAppointmentSubmission
        $dsn      = 'mysql:host=127.0.0.1;port=8889;dbname=hkid_app;charset=utf8mb4';
        $username_db = 'root'; // Suffix to avoid conflict with form username
        $password_db = 'root'; // Suffix to avoid conflict with form password

        try {
            $pdo = new \PDO($dsn, $username_db, $password_db, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            $stmt = $pdo->query('SELECT id, ref, name, email, phone, date, created_at FROM appointments ORDER BY created_at DESC'); // Assuming 'date' is your column name
            $appointments = $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Admin Dashboard DB Error: " . $e->getMessage());
            //  pass an error message to the dashboard view
            $dashboard_error = "Could not retrieve appointments: " . $e->getMessage();
        }

        $viewData = [
            'pageTitle' => $pageTitle,
            'appointments' => $appointments,
            'dashboard_error' => $dashboard_error ?? null
        ];

        ob_start();
        if (is_array($viewData)) {
            extract($viewData);
        }
        //  need to create src/View/templates/admin_dashboard.php
        // This view will expect an $appointments array and optionally $dashboard_error
        include PROJECT_ROOT . '/src/View/templates/admin_dashboard.php'; 
        $content = ob_get_clean();

        include PROJECT_ROOT . '/src/View/templates/header.php';
        echo $content;
        include PROJECT_ROOT . '/src/View/templates/footer.php';
    }

    /**
     * Handle Admin Logout.
     */
    public function handleAdminLogout(): void
    {
        unset($_SESSION['admin_logged_in']);
        // Regenerate session ID after logout for added security
        session_regenerate_id(true);

        $redirectUrl = (defined('PUBLIC_URL') ? PUBLIC_URL : '') . 'index.php?page=admin_login';
        header('Location: ' . $redirectUrl);
        exit;
    }
}

?>
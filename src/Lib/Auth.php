<?php
/**
 * auth.php
 *
 * Handles user registration, login, logout, and role‐based access control.
 * Uses PDO (from db.php) and Argon2id for secure password hashing/verification.
 */

session_start();
require_once __DIR__ . '/db.php';  // Adjust path if db.php is located elsewhere

/**
 * registerUser
 *
 * Registers a new user by hashing the password with Argon2id and inserting
 * into the `users` table. By default, every new user is assigned the role "user".
 *
 * @param string $email
 * @param string $password
 * @param string $role   (default: 'user')
 * @return bool          True on success, false on failure (e.g. duplicate email)
 */
function registerUser(string $email, string $password, string $role = 'user'): bool
{
    global $pdo;

    // 1. Hash the plain‑text password using Argon2id
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
    if ($hashedPassword === false) {
        return false;
    }

    // 2. Insert into `users` table (columns: id, email, password, role, created_at)
    $sql = "
        INSERT INTO users (email, password, role)
        VALUES (:email, :password, :role)
    ";
    $stmt = $pdo->prepare($sql);

    try {
        return $stmt->execute([
            ':email'    => $email,
            ':password' => $hashedPassword,
            ':role'     => $role
        ]);
    } catch (PDOException $e) {
        // You might want to log $e->getMessage() for debugging, but do NOT echo it to users
        return false;
    }
}

/**
 * loginUser
 *
 * Attempts to log in a user by checking the supplied email/password
 * against the database. On success, sets session variables.
 *
 * @param string $email
 * @param string $password
 * @return bool   True if credentials are valid; false otherwise
 */
function loginUser(string $email, string $password): bool
{
    global $pdo;

    // 1. Fetch the user row by email
    $sql = "
        SELECT id, email, password, role
        FROM users
        WHERE email = :email
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // No user with that email
        return false;
    }

    // 2. Verify the password using password_verify (Argon2id)
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    // 3. If the password is correct, regenerate session ID to prevent fixation
    session_regenerate_id(true);

    // 4. Store essential user data in the session
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];

    return true;
}

/**
 * isLoggedIn
 *
 * Returns true if a user is currently logged in (i.e., session contains user_id).
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * getUserRole
 *
 * Returns the role of the currently logged-in user, or null if not logged in.
 *
 * @return string|null
 */
function getUserRole(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * requireLogin
 *
 * Redirects to login.php if the user is not logged in. Call this at the top of
 * any page that requires authentication.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * requireRole
 *
 * Ensures that the currently logged-in user has one of the allowed roles;
 * otherwise, returns a 403 Forbidden response. Call this in pages where
 * only certain roles should have access (e.g., 'officer', 'admin').
 *
 * @param array|string $allowedRoles  A single role or an array of roles.
 */
function requireRole($allowedRoles): void
{
    requireLogin();

    $role = getUserRole();

    // Normalize $allowedRoles into an array if it’s a string
    if (is_string($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }

    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1>';
        echo '<p>You do not have permission to access this page.</p>';
        exit();
    }
}

/**
 * logoutUser
 *
 * Logs out the current user by clearing session data, destroying the session,
 * and deleting the session cookie. Call this from a logout endpoint.
 */
function logoutUser(): void
{
    // 1. Unset all session variables
    $_SESSION = [];

    // 2. If session cookies are used, delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // 3. Destroy the session on the server
    session_destroy();
}

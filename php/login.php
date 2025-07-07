<?php
// php/login.php
// Handles user login and account lockout.

require_once 'config.php'; // Include database configuration

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $email = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    // Validate input
    if (empty($email) || empty($password)) {
        $response['message'] = 'Please enter your email and password.';
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT user_id, password_hash, failed_login_attempts, account_locked_until FROM users WHERE email = :email";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
        $param_email = $email;

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();

                // Check for account lockout
                if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                    $response['message'] = 'Your account is locked. Please try again after ' . date('H:i', strtotime($user['account_locked_until'])) . '.';
                    echo json_encode($response);
                    exit();
                }

                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Password is correct, reset failed attempts and last login
                    $update_sql = "UPDATE users SET failed_login_attempts = 0, last_login = NOW(), account_locked_until = NULL WHERE user_id = :user_id";
                    $update_stmt = $pdo->prepare($update_sql);
                    $update_stmt->bindParam(":user_id", $user['user_id'], PDO::PARAM_INT);
                    $update_stmt->execute();

                    // Start a session (if you're using sessions for login state)
                    // session_start();
                    // $_SESSION['user_id'] = $user['user_id'];
                    // $_SESSION['email'] = $email;

                    $response['success'] = true;
                    $response['message'] = 'Login successful!';
                } else {
                    // Password is incorrect, increment failed attempts
                    $new_attempts = $user['failed_login_attempts'] + 1;
                    $lockout_time = NULL;

                    if ($new_attempts >= MAX_FAILED_ATTEMPTS) {
                        $lockout_time = date('Y-m-d H:i:s', strtotime('+' . LOCKOUT_DURATION_MINUTES . ' minutes'));
                        $response['message'] = 'Incorrect password. Your account has been locked for ' . LOCKOUT_DURATION_MINUTES . ' minutes due to too many failed attempts.';
                    } else {
                        $response['message'] = 'Incorrect password. You have ' . (MAX_FAILED_ATTEMPTS - $new_attempts) . ' attempts remaining before lockout.';
                    }

                    $update_sql = "UPDATE users SET failed_login_attempts = :attempts, account_locked_until = :lockout_time WHERE user_id = :user_id";
                    $update_stmt = $pdo->prepare($update_sql);
                    $update_stmt->bindParam(":attempts", $new_attempts, PDO::PARAM_INT);
                    $update_stmt->bindParam(":lockout_time", $lockout_time, PDO::PARAM_STR);
                    $update_stmt->bindParam(":user_id", $user['user_id'], PDO::PARAM_INT);
                    $update_stmt->execute();
                }
            } else {
                $response['message'] = 'No account found with that email address.';
            }
        } else {
            $response['message'] = 'Oops! Something went wrong. Please try again later.';
            error_log("Login error: " . $stmt->errorInfo()[2]);
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
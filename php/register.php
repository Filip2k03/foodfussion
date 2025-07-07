<?php
// php/register.php
// Handles user registration.

require_once 'config.php'; // Include database configuration

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get raw POST data (for fetch API)
    $input = json_decode(file_get_contents("php://input"), true);

    $firstName = trim($input['firstName'] ?? '');
    $lastName = trim($input['lastName'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit();
    }

    // Check if email already exists
    $sql = "SELECT user_id FROM users WHERE email = :email";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
        $param_email = $email;
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $response['message'] = 'This email is already registered.';
                echo json_encode($response);
                exit();
            }
        } else {
            $response['message'] = 'Oops! Something went wrong. Please try again later.';
            error_log("Register error: " . $stmt->errorInfo()[2]);
            echo json_encode($response);
            exit();
        }
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $sql = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (:first_name, :last_name, :email, :password_hash)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":first_name", $param_firstName, PDO::PARAM_STR);
        $stmt->bindParam(":last_name", $param_lastName, PDO::PARAM_STR);
        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
        $stmt->bindParam(":password_hash", $param_password_hash, PDO::PARAM_STR);

        $param_firstName = $firstName;
        $param_lastName = $lastName;
        $param_email = $email;
        $param_password_hash = $hashed_password;

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Registration successful! You can now log in.';
        } else {
            $response['message'] = 'Something went wrong with registration. Please try again.';
            error_log("Register error: " . $stmt->errorInfo()[2]);
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
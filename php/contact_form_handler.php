<?php
// php/contact_form_handler.php
// Handles submissions from the contact us form.

require_once 'config.php'; // Include database configuration

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $subject = trim($input['subject'] ?? '');
    $message = trim($input['message'] ?? '');

    // Validate input
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Please fill in all required fields (Name, Email, Message).';
        echo json_encode($response);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit();
    }

    // Determine message type (you might add a select field in frontend for this)
    $messageType = 'Enquiry'; // Default, or infer based on subject/content

    $sql = "INSERT INTO contact_messages (name, email, subject, message, message_type) VALUES (:name, :email, :subject, :message, :message_type)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
        $stmt->bindParam(":subject", $param_subject, PDO::PARAM_STR);
        $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
        $stmt->bindParam(":message_type", $param_messageType, PDO::PARAM_STR);

        $param_name = $name;
        $param_email = $email;
        $param_subject = $subject;
        $param_message = $message;
        $param_messageType = $messageType;

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Your message has been sent successfully! We will get back to you shortly.';
        } else {
            $response['message'] = 'Error sending your message. Please try again.';
            error_log("Contact form error: " . $stmt->errorInfo()[2]);
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
<?php
// php/submit_community_post.php
// Handles submission of community cookbook posts.

require_once 'config.php'; // Include database configuration

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    // In a real application, you'd get the user_id from the session after login
    // For this example, we'll use a placeholder or assume a logged-in user.
    // For now, let's assume user_id is passed for demonstration or use a default.
    // Replace with actual session check:
    // session_start();
    // $userId = $_SESSION['user_id'] ?? null;
    // if (!$userId) {
    //     $response['message'] = 'You must be logged in to submit a post.';
    //     echo json_encode($response);
    //     exit();
    // }
    $userId = 1; // Placeholder: Replace with actual user ID from session/authentication

    $title = trim($input['title'] ?? '');
    $postType = trim($input['postType'] ?? ''); // 'recipe', 'tip', 'experience'
    $content = trim($input['content'] ?? '');

    // Validate input
    if (empty($title) || empty($postType) || empty($content)) {
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit();
    }

    // Map frontend type to backend ENUM
    $dbPostType = '';
    switch ($postType) {
        case 'recipe':
            $dbPostType = 'Recipe Share';
            break;
        case 'tip':
            $dbPostType = 'Cooking Tip';
            break;
        case 'experience':
            $dbPostType = 'Culinary Experience';
            break;
        default:
            $response['message'] = 'Invalid post type.';
            echo json_encode($response);
            exit();
    }

    $sql = "INSERT INTO community_posts (user_id, title, content, post_type) VALUES (:user_id, :title, :content, :post_type)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":user_id", $param_userId, PDO::PARAM_INT);
        $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
        $stmt->bindParam(":content", $param_content, PDO::PARAM_STR);
        $stmt->bindParam(":post_type", $param_postType, PDO::PARAM_STR);

        $param_userId = $userId;
        $param_title = $title;
        $param_content = $content;
        $param_postType = $dbPostType;

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Your post has been submitted successfully!';
        } else {
            $response['message'] = 'Error submitting your post. Please try again.';
            error_log("Community post error: " . $stmt->errorInfo()[2]);
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
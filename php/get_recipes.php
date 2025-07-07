<?php
// php/get_recipes.php
// Example: Fetches recipes from the database.
// This would be called by your frontend to dynamically display recipes.

require_once 'config.php'; // Include database configuration

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    // Basic query to fetch all recipes
    // In a real app, you'd add filters, pagination, etc.
    $sql = "SELECT recipe_id, title, description, cuisine_type, dietary_preferences, difficulty, image_url FROM recipes ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $recipes = $stmt->fetchAll();

    if ($recipes) {
        $response['success'] = true;
        $response['data'] = $recipes;
    } else {
        $response['message'] = 'No recipes found.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Error fetching recipes: ' . $e->getMessage();
    error_log("Get recipes error: " . $e->getMessage());
}

echo json_encode($response);
?>
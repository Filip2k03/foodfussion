-- Database: foodfusion_db

-- Table for Users
CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL, -- Stores hashed password
    `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    `failed_login_attempts` INT DEFAULT 0,
    `account_locked_until` DATETIME NULL -- For account lockout functionality
);

-- Table for Recipe Categories (e.g., "Italian", "Vegan", "Dessert")
CREATE TABLE `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT
);

-- Table for Recipes
CREATE TABLE `recipes` (
    `recipe_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT, -- User who submitted the recipe (can be NULL for admin-added recipes)
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `ingredients` TEXT NOT NULL, -- Store as JSON or comma-separated list
    `instructions` TEXT NOT NULL,
    `prep_time` VARCHAR(50),
    `cook_time` VARCHAR(50),
    `servings` VARCHAR(50),
    `difficulty` ENUM('Easy', 'Medium', 'Hard') NOT NULL,
    `cuisine_type` VARCHAR(100), -- e.g., "Italian", "Mexican"
    `dietary_preferences` VARCHAR(255), -- e.g., "Vegetarian", "Gluten-Free" (comma-separated or JSON)
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
);

-- Junction table for Recipes and Categories (Many-to-Many relationship)
CREATE TABLE `recipe_categories` (
    `recipe_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    PRIMARY KEY (`recipe_id`, `category_id`),
    FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`recipe_id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
);

-- Table for Cooking Events
CREATE TABLE `events` (
    `event_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_date` DATETIME NOT NULL,
    `location` VARCHAR(255),
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for News Feed (Featured Recipes, Culinary Trends)
CREATE TABLE `news_feed` (
    `news_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `type` ENUM('Featured Recipe', 'Culinary Trend', 'Announcement') NOT NULL,
    `image_url` VARCHAR(255),
    `published_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Community Cookbook Posts (Tips, Experiences)
CREATE TABLE `community_posts` (
    `post_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL, -- Can be cooking tips, experiences, or even shared recipes
    `post_type` ENUM('Recipe Share', 'Cooking Tip', 'Culinary Experience') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

-- Table for Contact Us Messages
CREATE TABLE `contact_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255),
    `message` TEXT NOT NULL,
    `message_type` ENUM('Enquiry', 'Recipe Request', 'Feedback', 'Other') NOT NULL,
    `received_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_read` BOOLEAN DEFAULT FALSE
);

-- Table for Culinary Resources (Downloadable Recipe Cards, Tutorials)
CREATE TABLE `culinary_resources` (
    `resource_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `resource_type` ENUM('Recipe Card', 'Tutorial Video', 'Instructional Guide', 'Kitchen Hack') NOT NULL,
    `file_url` VARCHAR(255), -- URL for downloadable file or video link
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Educational Resources
CREATE TABLE `educational_resources` (
    `edu_resource_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `resource_type` ENUM('Article', 'Guide', 'Course', 'Infographic') NOT NULL,
    `content_url` VARCHAR(255), -- URL to the resource or internal content path
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

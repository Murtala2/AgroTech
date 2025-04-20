<?php
session_start();
include 'db_config.php'; // Ensure this file contains your DB connection details

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get form input
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare the query to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($user = $result->fetch_assoc()) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start the session and store user data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            // Redirect to the dashboard
            header("Location: Dash.php");
            exit;
        } else {
            echo "❌ Invalid password";
        }
    } else {
        echo "❌ Email not found";
    }
}

// Close the connection
$conn->close();
?>

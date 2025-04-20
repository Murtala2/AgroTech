<?php
include 'db_connect.php';

// Check if form data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data and sanitize it
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Check if any required field is empty
    if (empty($email) || empty($password) || empty($confirm_password)) {
        echo "❌ All fields are required!";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "❌ Passwords do not match!";
        exit;
    }

    // Check if email already exists in the database
    $sql_check_email = "SELECT * FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo "❌ Email is already registered!";
        exit;
    }

    // Encrypt the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql_insert = "INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $email, $hashed_password);

    if ($stmt_insert->execute()) {
        echo "Registration successful! Please <a href='Welcome.html'>login</a>.";
    } else {
        echo "❌ Error: " . $stmt_insert->error;
    }

    // Close the statements and connection
    $stmt_check->close();
    $stmt_insert->close();
    $conn->close();
} else {
    echo "❌ Invalid request method.";
}
?>

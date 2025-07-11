<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "wedding_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "❌ Username already exists. Please choose another.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            echo "✅ Registration successful! <a href='login.html'>Click here to login</a>.";
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
}

$conn->close();
?>

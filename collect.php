<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "wedding_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update status to 'Collected'
    $stmt = $conn->prepare("UPDATE food_donation SET status = 'Collected' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to fetch.php after update
        header("Location: fetch.php");
        exit;
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect to fetch.php if no POST data
    header("Location: fetch.php");
    exit;
}

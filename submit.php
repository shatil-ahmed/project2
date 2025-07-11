<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$database = "wedding_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$donor_name = $_POST['donor_name'] ?? '';
$contact_info = $_POST['contact_info'] ?? '';
$food_type = $_POST['food_type'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$donation_date = $_POST['donation_date'] ?? '';
$location = $_POST['location'] ?? '';

if (!$donor_name || !$contact_info || !$food_type || !$quantity || !$donation_date) {
    die("Please fill all required fields.");
}

$stmt = $conn->prepare("INSERT INTO food_donation (donor_name, contact_info, food_type, quantity, donation_date, location) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $donor_name, $contact_info, $food_type, $quantity, $donation_date, $location);

if ($stmt->execute()) {
    echo "Food donation submitted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

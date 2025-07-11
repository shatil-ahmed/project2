<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "wedding_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['userid'];
$username = $_SESSION['username'];

$success_msg = "";
$error_msg = "";

// ফর্ম সাবমিট হলে ডেটা ইনসার্ট করব
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = trim($_POST['quantity']);
    $location = trim($_POST['location']);
    $status = "Pending";
    $donation_date = date('Y-m-d');
    $donor_name = $username;
    $food_type = "General"; // আপনি চাইলে ফর্মে নতুন ফিল্ড দিতে পারেন

    if ($quantity == "" || $location == "") {
        $error_msg = "সব তথ্য সঠিকভাবে দিন।";
    } else {
        $stmt = $conn->prepare("INSERT INTO food_donation (donor_name, quantity, location, status, user_id, donation_date, food_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $donor_name, $quantity, $location, $status, $user_id, $donation_date, $food_type);

        if ($stmt->execute()) {
            $success_msg = "✅ Donation submitted successfully!";
        } else {
            $error_msg = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ইউজারের আগের ডোনেশন গুলো নিয়ে আসা
$stmt = $conn->prepare("SELECT id, donor_name, food_type, quantity, donation_date, location, status FROM food_donation WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Dashboard - Food Donation</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 700px; margin: 30px auto; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    form { margin-top: 20px; padding: 15px; border: 1px solid #ccc; }
    input[type=text], input[type=number] { width: 100%; padding: 8px; margin: 6px 0 12px 0; box-sizing: border-box; }
    input[type=submit] { background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; }
    .success { color: green; }
    .error { color: red; }
</style>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

<h3>Submit New Food Donation</h3>

<?php
if ($success_msg) {
    echo "<p class='success'>" . $success_msg . "</p>";
}
if ($error_msg) {
    echo "<p class='error'>" . $error_msg . "</p>";
}
?>

<form method="POST" action="">
    <label>Food Quantity (e.g., 50 kg):</label><br>
    <input type="text" name="quantity" required><br>

    <label>Location:</label><br>
    <input type="text" name="location" required><br>

    <input type="submit" value="Submit Donation">
</form>

<h3>Your Previous Food Donations:</h3>

<?php if ($result->num_rows > 0) : ?>
<table>
    <tr>
        <th>ID</th>
        <th>Donor Name</th>
        <th>Food Type</th>
        <th>Quantity</th>
        <th>Donation Date</th>
        <th>Location</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?php echo htmlspecialchars($row['id']); ?></td>
        <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
        <td><?php echo htmlspecialchars($row['food_type']); ?></td>
        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
        <td><?php echo htmlspecialchars($row['donation_date']); ?></td>
        <td><?php echo htmlspecialchars($row['location']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>You haven't submitted any food donations yet.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

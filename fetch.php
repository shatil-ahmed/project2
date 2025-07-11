<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wedding_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM food_donation ORDER BY donation_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Food Donations List</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f2f2f2; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        h2 { text-align: center; }
        form { margin: 0; }
        .logout { margin-bottom: 15px; text-align: right; }
    </style>
</head>
<body>

<div class="logout">
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Logout</a></p>
</div>

<h2>Available Food Donations</h2>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Donor Name</th>
                <th>Contact Info</th>
                <th>Food Type</th>
                <th>Quantity</th>
                <th>Donation Date</th>
                <th>Location</th>
                <th>Status</th>
                <th>Action</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . htmlspecialchars($row["donor_name"]) . "</td>
                <td>" . htmlspecialchars($row["contact_info"]) . "</td>
                <td>" . htmlspecialchars($row["food_type"]) . "</td>
                <td>" . htmlspecialchars($row["quantity"]) . "</td>
                <td>" . $row["donation_date"] . "</td>
                <td>" . htmlspecialchars($row["location"]) . "</td>
                <td>" . $row["status"] . "</td>
                <td>";

        if ($row["status"] !== "Collected") {
            echo '<form method="post" action="collect.php">
                    <input type="hidden" name="id" value="' . $row["id"] . '"/>
                    <input type="submit" value="Mark Collected"/>
                  </form>';
        } else {
            echo "Already Collected";
        }

        echo "</td></tr>";
    }

    echo "</table>";
} else {
    echo "<p>No food donations found.</p>";
}

$conn->close();
?>

</body>
</html>

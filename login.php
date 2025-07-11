<?php
session_start();

// ডাটাবেজ কানেকশন
$conn = new mysqli("localhost", "root", "", "wedding_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// ফর্ম সাবমিশন হ্যান্ডেল করা
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 'user' টেবিল থেকে ইউজার তথ্য ফেচ করা
    $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // পাসওয়ার্ড ভেরিফাই করা
        if (password_verify($password, $hashed_password)) {
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $username;

            // সফল হলে fetch.php-তে রিডাইরেক্ট করা
            header("Location: fetch.php");
            exit;
        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "❌ Username not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Wedding Food Project</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>

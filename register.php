<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $email, $phone, $hashed])) {
                header("Location: login.php?success=Registration successful! Please login.");
                exit;
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Spark Guest House</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-orange-600">Create Account</h2>
    
    <?php if (isset($error)): ?>
      <p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="full_name" placeholder="Full Name" required class="w-full border p-3 mb-4 rounded">
      <input type="email" name="email" placeholder="Email" required class="w-full border p-3 mb-4 rounded">
      <input type="tel" name="phone" placeholder="Phone (e.g. 0780142737)" required class="w-full border p-3 mb-4 rounded">
      <input type="password" name="password" placeholder="Password" required class="w-full border p-3 mb-4 rounded">
      <button type="submit" class="w-full bg-green-600 text-white py-3 rounded font-bold">Sign Up</button>
    </form>
    <p class="mt-4 text-center">Already have account? <a href="login.php" class="text-orange-600">Login</a></p>
  </div>
</body>
</html>
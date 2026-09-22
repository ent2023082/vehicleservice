<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = "Enter a username, valid email and password of at least 6 characters.";
    } else {
        $statement = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $statement->execute([$email]);

        if ($statement->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = $pdo->prepare("INSERT INTO users(username, email, password, role) VALUES(?, ?, ?, 'customer')");
            $statement->execute([$name, $email, $hashedPassword]);
            flash('success', 'Registration successful. Please log in.');
            header("Location: login.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-bg">
    <div class="container">
        <div class="auth-card">
            <h2>Create account</h2>
            <p class="text-muted">Start managing your vehicle service records.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" id="registerForm">
                <input class="form-control mb-3" name="username" placeholder="Username" required>
                <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
                <input class="form-control mb-3" type="password" name="password" placeholder="Password (6+ characters)" minlength="6" required>
                <button class="btn btn-warning w-100">Register</button>
            </form>

            <p class="mt-3">Already registered? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
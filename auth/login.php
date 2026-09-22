<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $statement = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $statement->execute([$email]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: " . ($user['role'] === 'admin' ? '../admin.php' : '../dashboard.php'));
        exit;
    }

    $error = "Invalid email or password.";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-bg">
    <div class="container">
        <div class="auth-card">
            <h2>Welcome back</h2>
            <?php show_flash(); ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
                <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
                <button class="btn btn-warning w-100">Login</button>
            </form>

            <p class="mt-3">No account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>
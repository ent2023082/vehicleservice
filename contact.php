<?php
session_start();
require_once "includes/db.php";
require_once "includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $statement = $pdo->prepare("INSERT INTO messages(name, email, message) VALUES(?, ?, ?)");
        $statement->execute([$name, $email, $message]);
        $ok = "Message submitted successfully.";
    } else {
        $error = "Please complete all fields with a valid email.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-car-front-fill me-1" aria-hidden="true"></i>AutoCare Pro</a>
            <a class="btn btn-outline-light btn-sm" href="index.php">Home</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h1>Contact Us</h1>
                <p class="text-muted">Send a question about vehicle servicing.</p>

                <?php if (isset($ok)): ?>
                    <div class="alert alert-success"><?= e($ok) ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" id="contactForm" class="card p-4 shadow-sm">
                    <input class="form-control mb-3" name="name" placeholder="Your name" required>
                    <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
                    <textarea class="form-control mb-3" name="message" rows="5" placeholder="Your message" required></textarea>
                    <button class="btn btn-warning">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
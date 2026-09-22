<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">🚗 AutoCare Pro</a>
            <a class="btn btn-warning btn-sm" href="../auth/register.php">Get Started</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1>Our Services</h1>
            <p class="text-muted">Common maintenance and repair services for everyday vehicles.</p>
            <input id="serviceSearch" class="form-control mx-auto" style="max-width:500px" placeholder="Search services...">
        </div>

        <div class="row g-4" id="serviceGrid">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4 service-item">
                    <div class="feature-card h-100">
                        <div class="icon">🛠️</div>
                        <h4><?= e($service['name']) ?></h4>
                        <p><?= e($service['description']) ?></p>
                        <strong><?= e($service['price']) ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="../js/app.js"></script>
</body>
</html>
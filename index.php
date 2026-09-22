<?php
session_start();
require_once __DIR__ . '/includes/db.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoCare Pro | Vehicle Service Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🚗 AutoCare Pro</a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ($_SESSION['role'] ?? 'customer') === 'admin' ? 'admin.php' : 'dashboard.php' ?>">
                                <?= ($_SESSION['role'] ?? 'customer') === 'admin' ? 'Admin Panel' : 'Dashboard' ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm ms-lg-2" href="auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm ms-lg-2" href="auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container py-5">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark mb-3">SMART VEHICLE CARE</span>
                    <h1 class="display-4 fw-bold">Keep your vehicle ready for every journey.</h1>
                    <p class="lead">
                        Track vehicles, book services, maintain service history and manage your next appointment from one simple dashboard.
                    </p>
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'auth/register.php'; ?>" class="btn btn-warning btn-lg me-2">Get Started</a>
                    <a href="pages/services.php" class="btn btn-outline-light btn-lg">View Services</a>
                </div>

                <div class="col-lg-5">
                    <div class="glass-card">
                        <h4>Service Reminder</h4>
                        <p class="mb-1">Oil &amp; Filter Service</p>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" style="width:78%"></div>
                        </div>
                        <small>Next service recommended within 1,200 km</small>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2>Everything in one place</h2>
                <p class="text-muted">Designed for vehicle owners and small service centers.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon">🚘</div>
                        <h5>Vehicle Records</h5>
                        <p>Add multiple vehicles and keep registration, mileage and model information organized.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon">🛠️</div>
                        <h5>Service Booking</h5>
                        <p>Choose a service and request an appointment with date and notes.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon">📋</div>
                        <h5>Service History</h5>
                        <p>Review previous work, costs and service dates from your dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <small>© 2026 AutoCare Pro — Vehicle Service Manager</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
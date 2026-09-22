<?php
require_once "includes/auth.php";
require_once "includes/db.php";
require_once "includes/functions.php";

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'vehicle') {
        $statement = $pdo->prepare("INSERT INTO vehicles(user_id, plate_no, make, model, year, mileage) VALUES(?, ?, ?, ?, ?, ?)");
        $statement->execute([
            $uid,
            trim($_POST['plate_no'] ?? ''),
            trim($_POST['make'] ?? ''),
            trim($_POST['model'] ?? ''),
            (int) ($_POST['year'] ?? 0),
            (int) ($_POST['mileage'] ?? 0),
        ]);
        flash('success', 'Vehicle added.');
        header("Location: dashboard.php");
        exit;
    }

    if ($action === 'appointment') {
        $statement = $pdo->prepare("INSERT INTO appointments(user_id, vehicle_id, service_type, appointment_date, notes) VALUES(?, ?, ?, ?, ?)");
        $statement->execute([
            $uid,
            $_POST['vehicle_id'] ?? 0,
            $_POST['service_type'] ?? '',
            $_POST['appointment_date'] ?? '',
            trim($_POST['notes'] ?? ''),
        ]);
        flash('success', 'Appointment requested.');
        header("Location: dashboard.php");
        exit;
    }

    if ($action === 'delete_vehicle') {
        $statement = $pdo->prepare("DELETE FROM vehicles WHERE id = ? AND user_id = ?");
        $statement->execute([$_POST['id'] ?? 0, $uid]);
        flash('success', 'Vehicle deleted.');
        header("Location: dashboard.php");
        exit;
    }
}

$vehiclesQuery = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY id DESC");
$vehiclesQuery->execute([$uid]);
$vehicles = $vehiclesQuery->fetchAll();

$appointmentsQuery = $pdo->prepare("SELECT a.*, v.plate_no FROM appointments a JOIN vehicles v ON v.id = a.vehicle_id WHERE a.user_id = ? ORDER BY appointment_date DESC");
$appointmentsQuery->execute([$uid]);
$appointments = $appointmentsQuery->fetchAll();

$historyQuery = $pdo->prepare("SELECT s.*, v.plate_no FROM service_records s JOIN vehicles v ON v.id = s.vehicle_id WHERE s.user_id = ? ORDER BY service_date DESC");
$historyQuery->execute([$uid]);
$history = $historyQuery->fetchAll();

$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🚗 AutoCare Pro</a>
            <div>
                <a class="btn btn-outline-light btn-sm me-2" href="index.php">Home</a>
                <a class="btn btn-outline-light btn-sm" href="auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Hello, <?= e($_SESSION['username']) ?> 👋</h1>
                <p class="text-muted">Manage your vehicles and service activity.</p>
            </div>

            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#vehicleModal">+ Add Vehicle</button>
        </div>

        <?php show_flash(); ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <span>Vehicles</span>
                    <strong><?= count($vehicles) ?></strong>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <span>Appointments</span>
                    <strong><?= count($appointments) ?></strong>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <span>Service Records</span>
                    <strong><?= count($history) ?></strong>
                </div>
            </div>
        </div>

        <section class="mb-5">
            <h3 class="mb-3">My Vehicles</h3>
            <div class="row g-3">
                <?php foreach ($vehicles as $x): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card h-100">
                            <div class="d-flex justify-content-between">
                                <h5><?= e($x['make'] . ' ' . $x['model']) ?></h5>
                                <span class="badge bg-dark"><?= e($x['plate_no']) ?></span>
                            </div>
                            <p class="mb-1">Year: <?= e($x['year']) ?></p>
                            <p>Mileage: <?= number_format($x['mileage']) ?> km</p>

                            <form method="post">
                                <input type="hidden" name="action" value="delete_vehicle">
                                <input type="hidden" name="id" value="<?= $x['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this vehicle?')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$vehicles): ?>
                    <div class="col-12">
                        <div class="alert alert-info">Add your first vehicle to book a service.</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Book a Service</h3>
            </div>

            <div class="card p-4 shadow-sm">
                <form method="post" class="row g-3" id="bookingForm">
                    <input type="hidden" name="action" value="appointment">

                    <div class="col-md-4">
                        <label>Vehicle</label>
                        <select class="form-select" name="vehicle_id" required>
                            <option value="">Choose...</option>
                            <?php foreach ($vehicles as $x): ?>
                                <option value="<?= $x['id'] ?>"><?= e($x['plate_no'] . ' - ' . $x['make'] . ' ' . $x['model']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Service</label>
                        <select class="form-select" name="service_type" required>
                            <option value="">Choose...</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['name']) ?>"><?= e($service['name']) ?> (<?= e($service['price']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Date</label>
                        <input class="form-control" type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-12">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Describe any issue..."></textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-warning" <?= (!$services || !$vehicles) ? 'disabled' : '' ?>>Request Appointment</button>
                    </div>
                </form>
            </div>
        </section>

        <section>
            <h3>Appointment History</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $x): ?>
                            <tr>
                                <td><?= e($x['plate_no']) ?></td>
                                <td><?= e($x['service_type']) ?></td>
                                <td><?= e($x['appointment_date']) ?></td>
                                <td>
                                    <span class="badge <?= match ($x['status']) {
                                        'Confirmed' => 'text-bg-success',
                                        'Rejected' => 'text-bg-danger',
                                        'Completed' => 'text-bg-primary',
                                        'Cancelled' => 'text-bg-secondary',
                                        default => 'text-bg-warning',
                                    } ?>"><?= e($x['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="modal fade" id="vehicleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5>Add Vehicle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="action" value="vehicle">
                        <input class="form-control mb-2" name="plate_no" placeholder="Plate number e.g. WP CAB-1234" required>
                        <input class="form-control mb-2" name="make" placeholder="Make e.g. Toyota" required>
                        <input class="form-control mb-2" name="model" placeholder="Model e.g. Prius" required>
                        <input class="form-control mb-2" type="number" name="year" min="1950" max="<?= date('Y') + 1 ?>" placeholder="Year" required>
                        <input class="form-control" type="number" name="mileage" min="0" placeholder="Mileage (km)" required>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-warning">Save Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
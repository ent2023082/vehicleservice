<?php
require_once "includes/auth.php";
require_admin();
require_once "includes/db.php";
require_once "includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['add_service', 'update_service', 'delete_service'], true)) {
        try {
            if ($action === 'add_service') {
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = trim($_POST['price'] ?? '');

                if ($name === '' || $description === '' || $price === '') {
                    throw new InvalidArgumentException('Complete all service fields.');
                }

                $statement = $pdo->prepare("INSERT INTO services(name, description, price) VALUES(?, ?, ?)");
                $statement->execute([$name, $description, $price]);
                flash('success', 'Service added.');
            } elseif ($action === 'update_service') {
                $serviceId = (int) ($_POST['service_id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = trim($_POST['price'] ?? '');

                if ($serviceId < 1 || $name === '' || $description === '' || $price === '') {
                    throw new InvalidArgumentException('Complete all service fields.');
                }

                $statement = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
                $statement->execute([$name, $description, $price, $serviceId]);
                flash('success', 'Service updated.');
            } else {
                $statement = $pdo->prepare("DELETE FROM services WHERE id = ?");
                $statement->execute([(int) ($_POST['service_id'] ?? 0)]);
                flash('success', 'Service deleted.');
            }
        } catch (InvalidArgumentException $e) {
            flash('danger', $e->getMessage());
        } catch (PDOException $e) {
            flash('danger', 'Service name already exists or could not be saved.');
        }

        header("Location: admin.php#services");
        exit;
    }

    $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($appointmentId > 0 && in_array($status, ['Confirmed', 'Rejected'], true)) {
        $statement = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND status = 'Pending'");
        $statement->execute([$status, $appointmentId]);
        flash(
            $statement->rowCount() ? 'success' : 'warning',
            $statement->rowCount() ? "Request $status." : 'This request was already reviewed.'
        );
    } else {
        flash('danger', 'Invalid request action.');
    }

    header("Location: admin.php");
    exit;
}

$appointments = $pdo->query("SELECT a.*, u.username, u.email, v.plate_no, v.make, v.model FROM appointments a JOIN users u ON u.id = a.user_id JOIN vehicles v ON v.id = a.vehicle_id ORDER BY a.status = 'Pending' DESC, a.appointment_date ASC, a.created_at DESC")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
$pendingCount = 0;

foreach ($appointments as $appointment) {
    if ($appointment['status'] === 'Pending') {
        $pendingCount++;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel | AutoCare Pro</title>
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
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1>Admin Panel</h1>
                <p class="text-muted mb-0">Review and manage customer service requests.</p>
            </div>

            <div class="stat-card py-2 px-4">
                <span>Pending requests</span>
                <strong><?= e($pendingCount) ?></strong>
            </div>
        </div>

        <?php show_flash(); ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td>
                                        <?= e($appointment['username']) ?><br>
                                        <small class="text-muted"><?= e($appointment['email']) ?></small>
                                    </td>
                                    <td>
                                        <?= e($appointment['plate_no']) ?><br>
                                        <small class="text-muted"><?= e($appointment['make'] . ' ' . $appointment['model']) ?></small>
                                    </td>
                                    <td><?= e($appointment['service_type']) ?></td>
                                    <td><?= e($appointment['appointment_date']) ?></td>
                                    <td><?= e($appointment['notes'] ?: 'No notes') ?></td>
                                    <td>
                                        <span class="badge <?= match ($appointment['status']) {
                                            'Confirmed' => 'text-bg-success',
                                            'Rejected' => 'text-bg-danger',
                                            'Completed' => 'text-bg-primary',
                                            'Cancelled' => 'text-bg-secondary',
                                            default => 'text-bg-warning',
                                        } ?>">
                                            <?= e($appointment['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($appointment['status'] === 'Pending'): ?>
                                            <div class="d-flex justify-content-end gap-2">
                                                <form method="post">
                                                    <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                                    <input type="hidden" name="status" value="Confirmed">
                                                    <button class="btn btn-sm btn-success">Accept</button>
                                                </form>

                                                <form method="post">
                                                    <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                                    <input type="hidden" name="status" value="Rejected">
                                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Reviewed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$appointments): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No service requests yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <section id="services" class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Manage Services</h2>
                    <p class="text-muted mb-0">Add, update, or remove services offered to customers.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="action" value="add_service">

                        <div class="col-md-3">
                            <label>Service name</label>
                            <input class="form-control" name="name" required>
                        </div>

                        <div class="col-md-4">
                            <label>Description</label>
                            <input class="form-control" name="description" required>
                        </div>

                        <div class="col-md-3">
                            <label>Price</label>
                            <input class="form-control" name="price" placeholder="LKR 8,000+" required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-warning w-100">Add Service</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <form method="post" class="row g-2">
                                    <input type="hidden" name="action" value="update_service">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

                                    <div class="col-md-4">
                                        <label class="small">Name</label>
                                        <input class="form-control" name="name" value="<?= e($service['name']) ?>" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small">Description</label>
                                        <input class="form-control" name="description" value="<?= e($service['description']) ?>" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small">Price</label>
                                        <input class="form-control" name="price" value="<?= e($service['price']) ?>" required>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-sm btn-primary">Update</button>
                                    </div>
                                </form>

                                <form method="post" class="text-end mt-2" onsubmit="return confirm('Delete this service?')">
                                    <input type="hidden" name="action" value="delete_service">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$services): ?>
                    <div class="col-12">
                        <div class="alert alert-info">No services configured.</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>

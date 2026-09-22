<?php
$host = "localhost";
$db = "vehicle_service_manager";
$user = "root";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $roleColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();
    if (!$roleColumn) {
        $pdo->exec("ALTER TABLE users ADD role ENUM('customer','admin') NOT NULL DEFAULT 'customer' AFTER password");
    }

    $statusColumn = $pdo->query("SHOW COLUMNS FROM appointments LIKE 'status'")->fetch();
    if ($statusColumn && stripos($statusColumn['Type'], 'Rejected') === false) {
        $pdo->exec("ALTER TABLE appointments MODIFY status ENUM('Pending','Confirmed','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS services(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE, description TEXT NOT NULL, price VARCHAR(50) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

    $serviceCount = (int) $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($serviceCount === 0) {
        $seed = $pdo->prepare("INSERT INTO services(name, description, price) VALUES(?, ?, ?)");
        $defaultServices = [
            ['Oil Change', 'Engine oil and filter replacement.', 'LKR 8,000+'],
            ['Full Service', 'Comprehensive inspection and scheduled maintenance.', 'LKR 18,000+'],
            ['Brake Inspection', 'Brake pads, discs and fluid inspection.', 'LKR 5,000+'],
            ['AC Service', 'AC inspection, cleaning and gas check.', 'LKR 7,500+'],
            ['Battery Check', 'Battery health and charging-system test.', 'LKR 2,500+'],
            ['Tyre Service', 'Pressure, rotation, balancing and alignment.', 'LKR 4,000+'],
        ];

        foreach ($defaultServices as $service) {
            $seed->execute($service);
        }
    }

    $admin = $pdo->prepare("INSERT INTO users(username, email, password, role) VALUES('Administrator', 'admin@example.com', ?, 'admin') ON DUPLICATE KEY UPDATE role = 'admin'");
    $admin->execute(['$2y$10$tvDb0gwTh6R0GpgGDDP7T.zJ2BvqkhoHt5eQ6AWmHVt.MWPgmS9VO']);
} catch (PDOException $e) {
    die("Database connection failed. Import database.sql and check XAMPP MySQL.");
}
?>
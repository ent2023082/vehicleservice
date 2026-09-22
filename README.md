# AutoCare Pro - Vehicle Service Manager

A PHP and MySQL vehicle service management system for vehicle owners and service center administrators.

## Features

- User registration and login
- Admin authentication and admin panel
- Add, update, and delete vehicles
- Book vehicle service appointments
- Review and manage appointment requests
- Maintain service history
- Display available services and prices
- Contact form for customer messages
- Admin message cards with update and delete actions
- Responsive interface using Bootstrap 5

## Requirements

Install the following software before running the project:

- XAMPP with Apache, MySQL, and PHP
- A modern web browser
- A code editor such as VS Code

PHP and MySQL are provided by XAMPP, so a separate PHP installation is not required when using XAMPP.

## Project Location

The project must be inside the Apache web root. The default XAMPP web root on Windows is:

```text
C:\xampp\htdocs\
```

For example, copy or move the project folder to:

```text
C:\xampp\htdocs\vehicleservice\
```

The final path should contain files such as:

```text
C:\xampp\htdocs\vehicleservice\index.php
C:\xampp\htdocs\vehicleservice\database.sql
C:\xampp\htdocs\vehicleservice\includes\db.php
```

If the project remains in another folder, Apache will not serve it through the normal `localhost` URL unless you configure a virtual host.

## XAMPP Setup

### 1. Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/.
2. Install XAMPP, preferably using the default folder:

```text
C:\xampp
```

3. During installation, make sure Apache, MySQL, and PHP are selected.

### 2. Copy the project

Copy the complete `vehicleservice` folder into:

```text
C:\xampp\htdocs\
```

Do not copy only individual PHP files. The `auth`, `css`, `images`, `includes`, `js`, and `pages` folders are required.

### 3. Start Apache and MySQL

1. Open **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Confirm that both services show a green running status.

## Database Setup

### Recommended method: import `database.sql`

1. Open this address in your browser:

```text
http://localhost/phpmyadmin/
```

2. Select the **Import** tab.
3. Click **Choose File**.
4. Select the project file:

```text
C:\xampp\htdocs\vehicleservice\database.sql
```

5. Leave the format as **SQL**.
6. Click **Import** or **Go**.
7. Confirm that the `vehicle_service_manager` database has been created.
8. Confirm that these tables exist:

```text
users
vehicles
appointments
services
service_records
messages
```

The SQL file creates the database and inserts the default services and administrator account.

### Database connection settings

The current application connection is configured in `includes/db.php`:

```php
$host = "localhost";
$db = "vehicle_service_manager";
$user = "root";
$pass = "";
```

These values match the default XAMPP MySQL configuration. If you set a password for the MySQL `root` user, update `$pass` in `includes/db.php` before opening the application.

The application also checks for required columns and creates default services when the database connection is successful. Importing `database.sql` first is still recommended for a clean setup.

## Run the Project

After Apache and MySQL are running, open:

```text
http://localhost/vehicleservice/
```

The home page should display the AutoCare Pro vehicle service manager.

You can also open the main file directly:

```text
http://localhost/vehicleservice/index.php
```

Do not open PHP files by double-clicking them from File Explorer. PHP files must be served through Apache.

## Login Accounts

### Administrator

```text
Email: admin@example.com
Password: Admin@123
```

Use the administrator account to access:

```text
http://localhost/vehicleservice/admin.php
```

The admin panel can manage:

- Pending appointments
- Services and prices
- Customer messages

### Customer

Create a customer account from:

```text
http://localhost/vehicleservice/auth/register.php
```

After registration, log in from:

```text
http://localhost/vehicleservice/auth/login.php
```

Customer users can manage their vehicles, book appointments, and view service history.

## Main URLs

| Page | URL |
| --- | --- |
| Home | `http://localhost/vehicleservice/` |
| Services | `http://localhost/vehicleservice/pages/services.php` |
| Contact | `http://localhost/vehicleservice/contact.php` |
| Register | `http://localhost/vehicleservice/auth/register.php` |
| Login | `http://localhost/vehicleservice/auth/login.php` |
| Customer dashboard | `http://localhost/vehicleservice/dashboard.php` |
| Admin panel | `http://localhost/vehicleservice/admin.php` |
| phpMyAdmin | `http://localhost/phpmyadmin/` |

## Project Structure

```text
vehicleservice/
|-- admin.php                 Admin dashboard
|-- contact.php               Customer contact form
|-- dashboard.php             Customer dashboard
|-- database.sql              Database schema and seed data
|-- index.php                 Home page
|-- README.md                 Setup and usage guide
|-- auth/
|   |-- login.php             Login page
|   |-- logout.php            Logout handler
|   |-- register.php          Registration page
|-- css/
|   |-- style.css             Application styles
|-- images/                   Image assets
|-- includes/
|   |-- auth.php              Authentication and admin checks
|   |-- db.php                PDO database connection
|   |-- functions.php         Shared PHP helper functions
|-- js/
|   |-- app.js                Shared browser-side behavior
|-- pages/
    |-- services.php          Public services page
```

## Common Problems

### Apache will not start

Another program may already be using port 80 or 443.

1. Open XAMPP Control Panel.
2. Check the Apache error log.
3. Stop applications using the conflicting port, or change the Apache port.
4. If Apache uses port `8080`, open the project with:

```text
http://localhost:8080/vehicleservice/
```

### MySQL will not start

Another MySQL or MariaDB service may already be running.

1. Stop the other MySQL service from Windows Services.
2. Restart MySQL from XAMPP.
3. Check the MySQL error log if it still fails.

### Database connection failed

Check the following:

- MySQL is running in XAMPP.
- The database name is `vehicle_service_manager`.
- The database was imported successfully.
- The username and password in `includes/db.php` match your MySQL account.
- Apache is serving the current project folder.

### 404 Not Found

Make sure the project folder is directly inside `C:\xampp\htdocs\` and that the URL includes the folder name:

```text
http://localhost/vehicleservice/
```

### CSS or JavaScript does not load

Check that the project folders are present and named exactly:

```text
css
js
```

Also make sure the project is opened through Apache rather than using a `file:///` path.

### Admin login does not work

Re-import `database.sql`, or check that the `users` table contains:

```text
admin@example.com
```

The application also creates or promotes this administrator account when `includes/db.php` connects successfully.

## Security Notes

This project is configured for local XAMPP development. Before deploying it publicly:

- Change the default administrator password.
- Set a strong MySQL password and update `includes/db.php`.
- Move database credentials into environment variables or a protected configuration file.
- Add CSRF protection to state-changing forms.
- Use HTTPS.
- Disable detailed database errors in production.
- Review and restrict Apache directory access.
- Keep XAMPP, PHP, MySQL, and dependencies updated.

## Development Workflow

1. Start Apache and MySQL in XAMPP.
2. Edit files in the project folder.
3. Refresh the browser at `http://localhost/vehicleservice/`.
4. Use phpMyAdmin to inspect database records.
5. Log out and test both customer and administrator workflows after authentication changes.

## Stopping the Project

When finished:

1. Return to the XAMPP Control Panel.
2. Click **Stop** next to Apache.
3. Click **Stop** next to MySQL.

Stopping the services is optional for local development, but it prevents XAMPP services from running when they are not needed.

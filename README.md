# HMIS - Hospital Management Information System

A complete Hospital Management Information System built with **Laravel** to manage patients, doctors, appointments, billing, pharmacy, and reports.

## Features

- 🏥 **Dashboard** – Overview of key stats (patients, doctors, today's appointments, pending bills)
- 👤 **Patient Management** – Register, view, edit, and delete patient records
- 👨‍⚕️ **Doctor Management** – Manage doctor profiles and specializations
- 📅 **Appointment Scheduling** – Book, update, and track appointments
- 💵 **Billing** – Create and manage patient bills with payment status
- 💊 **Pharmacy** – Track medicine inventory with low-stock alerts

## Requirements

- PHP 8.2+
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js & npm (for frontend assets)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/frkit/hmis.git
cd hmis
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` to set your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hmis
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run database migrations

```bash
php artisan migrate
```

### 5. (Optional) Seed demo data

```bash
php artisan db:seed
```

### 6. Start the development server

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

## Project Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── PatientController.php
│   ├── DoctorController.php
│   ├── AppointmentController.php
│   ├── BillingController.php
│   └── MedicineController.php
├── Models/
│   ├── Patient.php
│   ├── Doctor.php
│   ├── Appointment.php
│   ├── Billing.php
│   └── Medicine.php
database/migrations/        # Database schema
resources/views/            # Blade templates
routes/web.php              # Application routes
```

## License

This project is open-source under the [MIT license](LICENSE).

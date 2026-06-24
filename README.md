markdown
# Workforce Management & Attendance Tracking System

## Overview
A comprehensive workforce management system with role-based access control for Admin, Manager, and Employee roles.

## Features
- **Role Management:** Admin, Manager, Employee roles with Spatie Permission
- **Attendance Tracking:** Check-in/out with live location (latitude/longitude)
- **Live Timer:** Real-time working hours tracking that persists after page refresh
- **Leave Management:** 6 types of leave with approval workflow
  - Casual Leave, Sick Leave, Half-Day, Early Leave, Work From Home, Field Visit
- **Department & Designation:** Hierarchical organization structure
- **Location Tracking:** Reverse geocoding to display location names
- **Admin Dashboard:** Total employees, present, absent, on leave, attendance statistics
- **Manager Dashboard:** Team management, attendance, leave approvals
- **Employee Dashboard:** Self-attendance, leave applications, profile management

## Tech Stack
- **Backend:** Laravel 12
- **Frontend:** Bootstrap 5, jQuery
- **Database:** MySQL
- **Authentication:** Laravel Breeze
- **Authorization:** Spatie Permission
- **Timezone:** Asia/Kolkata (IST)

## Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL >= 5.7

### Setup Steps

1. Clone the repository
```bash
git clone https://github.com/saurav12bhetwal/workforce-management-system.git
cd workforce-management-system
Install dependencies

bash
composer install
Copy environment file

bash
cp .env.example .env
Generate application key

bash
php artisan key:generate
Configure database in .env

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workforce_db
DB_USERNAME=root
DB_PASSWORD=
Run migrations and seeders

bash
php artisan migrate --seed
Start the server

bash
php artisan serve
Access the application

text
http://localhost:8000
Default Login Credentials
Role	Email	Password
Admin	admin@test.com	password
Manager	manager@test.com	password
Employee	employee@test.com	password
Project Structure
text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── DepartmentController.php
│   │   │   ├── DesignationController.php
│   │   │   ├── LeaveController.php
│   │   │   ├── AttendanceController.php
│   │   ├── Manager/
│   │   │   ├── DashboardController.php
│   │   │   ├── TeamController.php
│   │   │   ├── LeaveController.php
│   │   │   └── AttendanceController.php
│   │   └── Employee/
│   │       ├── DashboardController.php
│   │       ├── AttendanceController.php
│   │       ├── LeaveController.php
│   │       └── ProfileController.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── Department.php
│   ├── Designation.php
│   ├── Attendance.php
│   └── LeaveRequest.php
├── Helpers/
│   └── LocationHelper.php
resources/
└── views/
    ├── admin/
    ├── manager/
    └── employee/
database/
├── migrations/
└── seeders/
Database Schema
Core Tables
users - Employee, Manager, Admin accounts

departments - Department master

designations - Designation master (belongs to department)

attendances - Daily attendance records with location

leave_requests - Leave applications with status

Key Features in Detail
1. Attendance System
Check-in with location capture (latitude, longitude)

Real-time working timer

Timer persists after page refresh by fetching the check_in time from database

Check-out with location capture

Automatic working hours calculation

Location name display via reverse geocoding

2. Leave Management
6 leave types: Casual, Sick, Half-Day, Early Leave, WFH, Field Visit

Apply leave with date range and reason

Overlapping leave prevention

Approve/Reject with rejection reason

Leave history with filters

3. Role-Based Access
Admin: Full system control

Manager: Team management only

Employee: Self-data only
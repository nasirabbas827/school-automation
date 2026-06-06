# school_automation_final

A lightweight PHP web application that streamlines the daily administrative tasks of a school—class scheduling, fee management, staff payroll, student/parent records, and feedback handling—all from a single admin portal.

---

## Overview

`school_automation_final` provides a clean, menu‑driven interface for school administrators to **create, read, update, and delete** (CRUD) core entities such as classes, fees, staff, teachers, students, and parents. The system also includes:

- Salary calculation & printable reports  
- Feedback collection and moderation  
- Secure admin authentication  

The project is structured for easy deployment on any standard LAMP/LEMP stack.

---

## Features

| Category | Description |
|----------|-------------|
| **Admin Dashboard** | Central hub (`admin_home.php`) with navigation (`admin_navbar.php`). |
| **Entity Management** | Add, edit, delete, and view: <br>• Classes (`add_class.php`, `edit_class.php`, `view_classes.php`) <br>• Fees (`add_fee.php`, `edit_fee.php`, `view_fee.php`) <br>• Parents (`add_parent.php`, `edit_parent.php`) <br>• Staff & Salaries (`add_staff.php`, `edit_staff.php`, `add_salary.php`, `edit_salary.php`, `print_salary.php`) <br>• Teachers (`add_teacher.php`, `edit_teacher.php`) <br>• Students (`add_student.php`, `edit_student.php`) |
| **Authentication** | Secure admin login (`admin_login.php`) with logout (`logout.php`). |
| **Feedback System** | Collect and moderate feedback (`view_feedback.php`, `delete_feedback.php`). |
| **File Uploads** | Profile picture handling for staff and students. |
| **Database** | Pre‑populated schema (`Database/school_db.sql`). |
| **Documentation** | Project description and requirements (`Fall 2023_CS619_8928.docx`). |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **Web Server** | Apache or Nginx (LAMP/LEMP) |
| **Frontend** | HTML5, CSS3, Bootstrap (optional) |
| **Version Control** | Git (GitHub) |
| **Documentation** | Microsoft Word (`.docx`) |

---

## Installation

> **Prerequisites**: A working LAMP/LEMP environment with Composer (optional) and MySQL.

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/school_automation_final.git
   cd school_automation_final
   ```

2. **Create a MySQL database**

   ```sql
   CREATE DATABASE school_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema**

   ```bash
   mysql -u YOUR_DB_USER -p school_db < Database/school_db.sql
   ```

4. **Configure database connection**

   Edit `admin/config.php` and replace the placeholder values with your credentials:

   ```php
   define('
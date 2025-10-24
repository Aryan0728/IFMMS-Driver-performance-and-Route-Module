IFMMS: Driver Performance and Route Module
==========================================

🚀 Overview
------------
This project is part of the Intelligent Fleet Maintenance and Monitoring System (IFMMS).
It focuses on the Driver Performance and Route Module, enabling monitoring, feedback management, and route optimization using Laravel, PHP, and SQL.

🛠️ Tech Stack
--------------
- Framework: Laravel 10+
- Backend: PHP 8.x
- Database: MySQL / MariaDB
- Frontend: Blade (HTML, CSS, JS)
- Server: Apache / Laravel’s built-in server
- Tools: Composer, Artisan, Git

⚙️ Prerequisites
-----------------
Make sure you have these installed before running the project:

| Tool | Minimum Version | Check Command |
|------|-----------------|----------------|
| PHP | 8.0+ | php -v |
| Composer | 2.x | composer -V |
| MySQL | 5.7+ | mysql -V |
| Git | Any | git --version |

📦 Installation Steps
----------------------

1️⃣ Clone the Repository
    git clone https://github.com/Aryan0728/IFMMS-Driver-performance-and-Route-Module.git

2️⃣ Go to the Project Folder
    cd IFMMS-Driver-performance-and-Route-Module

3️⃣ Install Dependencies
    composer install

4️⃣ Create a Copy of the .env File
    cp .env.example .env
    (On Windows use: copy .env.example .env)

5️⃣ Generate the Application Key
    php artisan key:generate

6️⃣ Set Up the Database
    - Open .env
    - Update these lines with your local database settings:

      DB_DATABASE=ifmms_db
      DB_USERNAME=root
      DB_PASSWORD=

7️⃣ Run Migrations (and Seed Data if available)
    php artisan migrate
    or
    php artisan migrate --seed

8️⃣ Start the Local Development Server
    php artisan serve

Then visit your app at:
👉 http://127.0.0.1:8000

🧰 Common Commands
-------------------
| Purpose | Command |
|----------|----------|
| Clear cache | php artisan cache:clear |
| Run migrations | php artisan migrate |
| Rollback migrations | php artisan migrate:rollback |
| Create controller | php artisan make:controller ControllerName |
| Start server | php artisan serve |

👨‍💻 Project Modules
--------------------
- Driver Performance Tracking – Analyze driver efficiency and incidents
- Route Management – Monitor, plan, and optimize driver routes
- Feedback & Incident Reporting – Log and manage feedback from drivers
- Admin Panel – Centralized monitoring and performance review

🧾 Notes
---------
- For best performance, use XAMPP or Laragon with PHP 8.0+.
- Ensure your storage/ and bootstrap/cache/ folders are writable.
- If you face permission errors, run:
      php artisan config:clear
      php artisan cache:clear
      php artisan serve


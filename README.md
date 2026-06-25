# 🌸 Tamboli Samaj Portal — Scholarship & Pratibha Samman Portal

The **Tamboli Samaj Portal** is an enterprise-grade, secure, and production-ready web application designed to manage community student records, scholarship distributions, and the **Pratibha Samman** (academic and achievement awards). 

It features a public website with an application tracker, a secure student portal for document submission and application tracking, a representative/moderator panel, and a comprehensive admin control center.

---

## 🛠️ Technology Stack

* **Language**: PHP 8.3 (Strict types enforced across core components)
* **Database**: MySQL 8+ (PDO parameterized querying, strict input validation)
* **Authentication**: Delight IM Auth (wrapped via `App\Core\Auth`)
* **Styling**: Bootstrap 5 + Bootstrap Icons (Vanilla CSS layout customizations)
* **Mail Server**: PHPMailer (configured over secure SMTP SSL/TLS)
* **Development Environment**: Laragon (Windows OS)
* **Production Environment**: Shared Hosting (Hostinger optimized `.htaccess` rules)

---

## 📂 Directory Structure & Codebase Map

To keep this homepage clean, we've organized the folder inventory and detailed explanations. 

* **Complete Map**: For a file-by-file breakdown of what every file does in detail, see our [🧭 Codebase Index](file:///c:/laragon/www/Tamoli-Prathibha-samman/docs/CODEBASE_INDEX.md).
* **Collapsible Folders**: Click the tabs below to explore the structure of each folder.

<details>
<summary>📂 App Core & Controllers (app/)</summary>

* [`app/config/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/config/): Houses app configuration settings (database connection, application parameters, path configurations).
* [`app/controllers/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/controllers/): Contains application controllers processing requests (Student portal, admin functions, authentication flow).
* [`app/core/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/core/): The application engine (Routing, Auth wrapper, Logger, Mailer, Database wrapper, CSRF system, File uploader).
* [`app/middleware/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/middleware/): Access control check middleware (student, admin, guest, or representative checks).
* [`app/models/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/models/): Core database query model structures (User, Student, Application, Session).
* [`app/routes/web.php`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/routes/web.php): Registers all URL endpoints and maps them to controllers and security middleware.
* [`app/views/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/app/views/): PHP layout pages (layouts, student dashboard, admin views, error screens).

</details>

<details>
<summary>📂 Database, Web & Server Settings (database/, public/, storage/)</summary>

* [`database/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/database/):
  - `schema/001_create_tables.sql`: Direct MySQL import schema table definitions.
  - `setup.php`: Automatic command-line database migration and seeding installer.
* [`public/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/public/):
  - `assets/`: UI assets (custom stylesheets, Bootstrap libraries, illustrations).
  - `uploads/`: Secure folder where uploaded profile pictures and marksheet files are saved.
  - `index.php`: The master gateway through which all routes are processed.
  - `.htaccess`: Manages URL rewrites and activates secure headers.
* [`storage/`](file:///c:/laragon/www/Tamoli-Prathibha-samman/storage/): Houses internal cache and log files, blocked from direct browser access.

</details>

---

## 📑 How to Write Collapsible Tabs in Markdown

GitHub Markdown supports HTML elements like `<details>` and `<summary>` to create interactive collapsible blocks (acting as "accordion tabs"). This is highly recommended to prevent long configuration texts from overwhelming developers reading your project page.

### Example Code:
```html
<details>
<summary>🛠️ Click to expand Database Settings</summary>

Here you can write code snippets or lists:
* Host: `127.0.0.1`
* Database: `tamboli_samaj`

</details>
```

---

## ⚙️ Getting Started & Local Installation

### Prerequisites
- **Laragon** (or XAMPP) with **PHP 8.3+** and **MySQL 8.0+**
- **Composer** (PHP dependency manager)

### Installation Steps

1. **Clone the Repository**:
   Clone the code to your local machine and open the directory.

2. **Configure Environment Variables**:
   Copy `.env.example` to `.env` in the project root:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and fill in your local database credentials, SMTP host settings, and other configurations:
   ```ini
   APP_NAME="Tamboli Samaj Portal"
   APP_URL=http://localhost:8000
   APP_DEBUG=true
   APP_TIMEZONE=Asia/Kolkata
   APP_SECRET=a-secure-random-64-character-string-for-hash-generation

   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=tamboli_samaj
   DB_USER=root
   DB_PASS=yourpassword
   ```

3. **Install Composer Dependencies**:
   Install PSR-4 autoloading libraries and other external dependencies:
   ```bash
   composer install
   ```

4. **Initialize & Seed the Database**:
   Run the CLI database setup script. This script automatically drops/creates the database, imports the full schema, sets up default configuration settings, inserts the academic sessions, and creates a default super admin user:
   ```bash
   php database/setup.php
   ```

5. **Start the Local Web Server**:
   You can run the built-in Composer development script:
   ```bash
   composer start
   ```
   Or launch a local PHP development server pointing directly to the `public/` directory:
   ```bash
   php -S localhost:8000 -t public
   ```
   Access the portal at `http://localhost:8000`.

---

## 👤 Default Credentials for Development

Once the setup script finishes, you can log in using:

* **Default Administrator**:
  - **Email**: `admin@tamoli.org`
  - **Password**: `password123`
  - **Role**: Super Admin

* **Student Registration**:
  - Students register directly via the `/register` route.
  - The system automatically generates a unique `student_code` (e.g., `TSP-2026-XXXXX`) upon sign-up.

---

## 📑 Core Database Schema

The database consists of **18 tables** (including delight-im/auth tables). The main business entities are structured as follows:

| Table Name | Description |
| :--- | :--- |
| `users` | Base account credentials, password hashes, and user role mask. |
| `students` | Profile information (first/last name, DOB, mobile, address, unique student code). |
| `academic_sessions` | Stores academic years (e.g., `2025-26`, `2026-27`). Controls application windows. |
| `student_academics` | Academic performance records per student per session (marks, percentage, college/school). |
| `application_types` | Lookup table representing application modules (`Scholarship`, `Pratibha Samman`). |
| `application_status` | Status tracker (`Pending`, `Approved`, `Rejected`, `Disputed`). |
| `applications` | Main application table mapping students, sessions, types, bank data, and achievement ranks. |
| `document_types` | Documents required (`Photo`, `Marksheet`, `Passbook`, `Certificate`, `Aadhaar`, etc.). |
| `application_documents` | Keeps track of stored filenames, MIME types, and file validation status. |
| `announcements` | General portal announcements published by administrators. |
| `settings` | Dynamic key-value pairs representing global config values (e.g. active session, app status). |

---

## 🔒 Security Architecture & Hardening Controls

This application has been hardened to prevent common vulnerabilities before production deployment:

1. **CSRF Protection**:
   - Every state-changing HTTP request (`POST`/`PUT`/`DELETE`) must include a valid CSRF token.
   - Handled via `App\Core\Csrf` class and checked in all secure controllers.

2. **SQL Injection (SQLi) Prevention**:
   - Database queries utilize parameterized PDO queries.
   - Column keys are strictly whitelisted inside `Student` & `Application` model updates to prevent column-injection attacks on dynamic updates.

3. **Cross-Site Scripting (XSS) Prevention**:
   - Standard output rendering uses `App\Core\Helpers::esc()` to safely encode values.
   - Structured JSON responses use clean JSON serialization headers.

4. **Upload Hardening (Mime Type and Script Execution)**:
   - Uploaded files are run through `finfo` byte-level MIME checks to prevent extension spoofing.
   - All uploaded files are stored inside `public/uploads/` with a strong `.htaccess` file configured to block the execution of executable scripts (like `.php`, `.phtml`, `.cgi`).

5. **Route Middleware Protection**:
   - The application router maps middlewares to secure controller methods.
   - Safe-closed design: router automatically terminates request flow (500) if any middleware check fails to run or throws an exception.

6. **Information Disclosure & Production Logging**:
   - Detailed database error trace and debug messages are suppressed when `APP_DEBUG=false`. A generic, branded error page is shown instead.
   - Sensitive password hashes and reset codes are scrubbed/redacted from developer logs.

---

## 🚀 Production Deployment Checklist

Ensure the following steps are performed before launching on Hostinger shared hosting:

- [ ] Rename `.env.example` to `.env` and fill in the production SMTP credentials.
- [ ] Set `APP_DEBUG=false` in `.env` to prevent folder paths and error outputs from leaking to the frontend.
- [ ] Set `SESSION_SECURE=true` in `.env` (requires active SSL certificate on domain).
- [ ] Set a strong 64-character random alphanumeric string for `APP_SECRET`.
- [ ] Ensure the domain root points directly to the `public/` subdirectory. If your hosting requires placing code in `public_html/`, copy the contents of `public/` into `public_html/` and place the parent directories outside the public directory for security.
- [ ] Change the default administrator password from `password123` to a secure production password immediately after initial migration.
- [ ] Verify that directory permissions for `storage/` and `public/uploads/` permit writing for the PHP process (`755` or `775`).

---

## 📞 Support & Maintenance

For support, please contact the administrators at the community headquarters or send an email to `admin@tamoli.org`. 

*Jai Tamboli Samaj!*

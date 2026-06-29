# AGENTS.md — Tamboli Samaj Portal

This file is written for AI coding agents who need to understand, modify, or extend the **Tamboli Samaj Portal** codebase. All information below is derived from the actual files in the repository.

---

## Project Overview

The Tamboli Samaj Portal is a PHP web application for managing community student records, scholarship applications, and the **Pratibha Samman** academic/achievement awards. It provides:

* A public landing page with an application tracker.
* A student portal for profile management, scholarship/Pratibha applications, and document uploads.
* A representative/moderator panel.
* An admin control center for reviewing applications, managing users, announcements, and academic sessions.

The project follows a custom, lightweight **Model-View-Controller (MVC)** pattern with a single front controller.

---

## Technology Stack

| Layer | Technology |
| --- | --- |
| Language | PHP 8.1+ (every PHP file uses `declare(strict_types=1);`) |
| Database | MySQL 8+ via PDO |
| Authentication | Delight IM Auth (`delight-im/auth`) |
| Mail | PHPMailer (`phpmailer/phpmailer`) |
| Frontend | Bootstrap 5, Bootstrap Icons, vanilla CSS/JS |
| Local server | PHP built-in server / Laragon |
| Production | Apache shared hosting (Hostinger), document root must be `public/` |

### Key Configuration Files

* `composer.json` — PSR-4 autoloading (`App\` → `app/`) and Composer scripts.
* `.env` — Application secrets, database, session, and SMTP credentials. Created from `.env.example`.
* `app/Config/constants.php` — Defines `APP_ROOT`, `APP_DEBUG`, `APP_TIMEZONE`, `APP_SECRET`, `APP_URL`.
* `app/Config/paths.php` — Defines `VIEW_PATH`, `UPLOAD_PATH`, `STORAGE_PATH`, `LOG_PATH`, etc.
* `app/Config/app.php` — App name/URL/debug, session params, pagination defaults.
* `app/Config/database.php` — PDO DSN and options.
* `public/.htaccess` — URL rewrite to `index.php` plus security headers.
* `storage/.htaccess` and `uploads/.htaccess` — Deny direct web access.
* `public/uploads/.htaccess` — Blocks execution of `.php` files inside the public upload folder.

---

## Repository Layout

```
Tamoli-Prathibha-samman/
├── app/
│   ├── Config/           # app, constants, database, paths
│   ├── Controllers/      # 10 controllers (Home, Auth, Dashboard, Profile, Application, Admin*)
│   ├── Core/             # 16 core classes (App, Router, Database, Auth, Csrf, Response, etc.)
│   ├── Middleware/       # Auth, Admin, Student, Guest, Representative middleware
│   ├── Models/           # 6 models (User, Student, Application, AcademicSession, lookups)
│   ├── Routes/web.php    # All route definitions
│   └── Views/            # PHP templates (layouts + feature folders)
├── database/
│   ├── schema/001_create_tables.sql
│   ├── setup.php         # CLI installer/seeder
│   └── update_schema.php # Safe column-add migration script
├── public/
│   ├── index.php         # Front controller
│   ├── assets/           # CSS, JS, images
│   ├── uploads/          # Public upload folder (profile photos)
│   ├── diagnose.php      # Deployment diagnostic helper
│   └── .htaccess         # Rewrite rules and security headers
├── storage/
│   ├── cache/ logs/ temp/
├── uploads/              # Application documents (served via controller)
├── vendor/               # Composer dependencies
├── .env.example
├── composer.json
├── composer.lock
├── README.md
└── docs/CODEBASE_INDEX.md
```

**Note on directory names:** The physical directories under `app/` are capitalized (`Config`, `Controllers`, `Core`, `Middleware`, `Models`, `Routes`, `Views`) to match the PSR-4 `App\` namespace. Existing documentation sometimes refers to them in lowercase, but the real filesystem paths use the capitalized form.

---

## Request Lifecycle & Entry Point

1. `public/index.php` loads the Composer autoloader, parses `.env` manually, loads `app/Config/constants.php` and `app/Config/paths.php`, configures error reporting, sets the timezone, starts the session, and registers a global exception handler.
2. It instantiates `App\Core\App` and calls `run()`.
3. `app/Core/App.php` loads `app/Routes/web.php` (the `$router` variable is available in scope) and calls `$router->resolve($method, $uri)`.
4. `app/Core/Router.php` normalizes the URI, matches routes with `{param}` placeholders, executes any middleware, and dispatches `Controller@method`.
5. Controllers interact with models and render views via `Response::view()`.

There is no `.env` parser library; `.env` is read line-by-line in `public/index.php` and the setup scripts.

---

## Routing

Routes are registered in **`app/Routes/web.php`** using the `Router` instance:

```php
$router->get('/', 'HomeController@index');
$router->post('/applications/{id}/edit', 'ApplicationController@update');
```

The router supports:

* GET and POST verbs.
* `{param}` placeholders converted to named regex groups.
* Optional per-route middleware arrays (third argument).
* Grouped routes with shared prefix and/or middleware via `$router->group()`.

**Important:** The current `web.php` does **not** attach middleware arrays to most routes. Controllers perform role checks directly with `Auth::isStudent()`, `Auth::isAdmin()`, etc. Middleware classes exist and can be wired via `$router->group(['middleware' => ['auth', 'admin']], ...)` if needed.

### Main Route Areas

* Public: `/`, `/home`
* Auth: `/login`, `/register`, `/forgot-password`, `/reset-password`, `/logout`
* Student: `/dashboard`, `/profile`, `/profile/edit`, `/applications/*`
* Admin: `/admin`, `/admin/applications`, `/admin/students`, `/admin/reps`, `/admin/announcements`, `/admin/settings`
* Representative: `/representative`

---

## Middleware & Access Control

Middleware classes live in `app/Middleware/`:

* `AuthMiddleware`
* `AdminMiddleware`
* `StudentMiddleware`
* `RepresentativeMiddleware`
* `GuestMiddleware`

Each middleware has a `handle()` method that redirects on failure.

Auth logic is centralized in **`app/Core/Auth.php`**, a wrapper around `Delight\Auth\Auth`:

* `Auth::login($email, $password)` — for admins/representatives; rejects student roles.
* `Auth::studentLogin($email, $password)` — for students; rejects non-student roles.
* `Auth::registerStudent(...)` — creates a Delight Auth user with `Role::SUBSCRIBER`, then inserts a `students` row.
* Role helpers: `isStudent()`, `isAdmin()`, `isSuperAdmin()`, `isRepresentative()`.

---

## Controllers

Controllers are classes in `app/Controllers/` with public methods. There are 10 controllers:

| Controller | Purpose |
| --- | --- |
| `HomeController` | Public landing + application tracker by reference number |
| `AuthController` | Login, register, forgot/reset password, logout |
| `DashboardController` | Student / Admin / Representative dashboards |
| `ProfileController` | View/edit profile + profile photo upload |
| `ApplicationController` | Scholarship & Pratibha forms, store/update, document AJAX upload/delete, file streaming, resubmission |
| `AdminApplicationController` | List/review applications; approve/reject/dispute |
| `AdminUserController` | Student management; representative CRUD (super-admin only) |
| `AdminAnnouncementController` | Announcement CRUD |
| `AdminSettingsController` | Global settings + academic session create/activate |
| `ErrorController` | Error rendering is handled by `Response::abort()` |

Controllers generally:

1. Validate CSRF on POST actions via `Csrf::validate()`.
2. Validate input via `Validator::make()`.
3. Call models.
4. Redirect with `Flash` messages or render views with `Response::view()`.

---

## Models & Database Access

Models are in `app/Models/` and use the `Database` singleton (PDO) from `app/Core/Database.php`.

| Model | Purpose |
| --- | --- |
| `User` | Admin/rep users table access |
| `Student` | Student profile CRUD; whitelisted `update()` columns |
| `Application` | Main application CRUD + document management; complex joins; self-healing schema migration |
| `AcademicSession` | Session lookup/active session |
| `ApplicationType` / `ApplicationStatus` | Lookup tables |

### Workflow Methods on `Application`

| Method | Purpose |
| --- | --- |
| `isComplete(int $id): bool` | Checks all required fields + documents are present for the application type. Used before final submit. |
| `generateApplicationNumber(int $applicationId, int $typeId): string` | Atomically generates `TSVS-{year}-{seq}` (type=1) or `TSVP-{year}-{seq}` (type=2) using `application_counters` table with `FOR UPDATE` row lock. Falls back to `ApplicationNumberGenerator::format()` on failure. |
| `transitionStatus(int $id, int $toStatusId, ?int $performedBy): bool` | Wraps `updateStatus()` with optional logging. |

### Conventions

* Use parameterized PDO queries for all dynamic values.
* `Student::update()` and `Application::update()` whitelist columns to prevent column-injection attacks.
* The `Application` model calls `autoMigrateSchema()` when a missing column error is detected, adding known application columns at runtime. This is convenient but unusual; prefer `database/update_schema.php` for controlled schema changes.

---

## Views

Views are PHP templates in `app/Views/`. Rendering uses `extract()` to expose view data.

```php
Response::view('dashboard/student', ['student' => $student]);
```

* Layouts: `header.php`, `footer.php`, `navbar.php`, `flash-message.php`, `admin-header.php`, `admin-sidebar.php`, `student-sidebar.php`, etc.
* Feature folders: `home/`, `auth/`, `dashboard/`, `profile/`, `applications/`, `admin/`, `errors/`.
* Always escape dynamic output with `\App\Core\Helpers::esc()` in templates.
* Use `Csrf::field()` inside forms.

---

## Core Services

| Class | Purpose |
| --- | --- |
| `App` | Bootstrapper |
| `Router` | GET/POST route registration, grouped middleware/prefix, `{param}` matching |
| `Database` | Singleton PDO connection |
| `Auth` | Delight Auth wrapper + role checks |
| `Session` | Secure session start + CRUD helpers |
| `Csrf` | Token generation/validation |
| `Input` | Trimmed POST/GET/file access |
| `Validator` | Fluent validation (required, email, mobile, min/max, numeric, in, matches, date) |
| `Response` | Redirect, JSON, view rendering, error aborts |
| `Helpers` | `esc()`, `random()`, `url()`, `formatBytes()`, `arrayGet()`, `slug()`, `currentUrl()` |
| `FileUploader` | MIME/extension validation, rename, move uploads |
| `Flash` | Session flash messages |
| `Logger` | Daily rotating file logs (`storage/logs/app-YYYY-MM-DD.log`) |
| `Mailer` | PHPMailer SMTP; debug fallback logs to `storage/logs/mail_resets.log` |
| `Pagination` | Placeholder, not currently used |
| `ApplicationNumberGenerator` | Formats application numbers such as `TSVS-{year}-{id}` |

---

## Configuration & Environment

1. Copy `.env.example` to `.env` at the project root.
2. Fill in local/production values for `APP_*`, `DB_*`, `SESSION_*`, and `SMTP_*`.
3. `public/index.php` and the database CLI scripts parse `.env` into `$_ENV`/`putenv()`.
4. `app/Config/constants.php` and `app/Config/paths.php` turn those values into PHP constants.

**Never commit `.env` or `vendor/`** — both are listed in `.gitignore`.

---

## Build & Run Commands

This is a PHP project; there is no compile step.

```bash
# Install dependencies
composer install

# Run the local development server
composer start
# Equivalent to:
php -S localhost:8000 -t public

# Laragon alias (same command)
composer start-laragon
```

---

## Database Setup & Migrations

### Initial setup

```bash
php database/setup.php
```

This script:

1. Reads `.env`.
2. Drops/creates the database.
3. Imports `database/schema/001_create_tables.sql`.
4. Creates a default super-admin user: `admin@tamoli.org` / `password123`.
5. Seeds academic sessions (`2025-26`, `2026-27`).

### Safe schema updates

```bash
php database/update_schema.php
```

Adds missing columns to the `applications` table without dropping data. Run this after pulling updates that introduce new application fields.

### Runtime self-healing

`Application::create()` and `Application::update()` detect unknown-column PDO errors and call `autoMigrateSchema()` before retrying once. This is a safety net, not a replacement for `update_schema.php`.

---

## Code Style Guidelines

Follow the existing conventions already enforced in the codebase:

1. **Strict types:** Every PHP file must start with:
   ```php
   <?php
   declare(strict_types=1);
   ```
2. **Namespaces:** Use PSR-4 namespaces (`App\Controllers\`, `App\Models\`, `App\Core\`, `App\Middleware\`).
3. **Class/file names:** PascalCase, matching the namespace path. Directory names under `app/` are capitalized.
4. **Controller methods:** Public, typed where appropriate, return `void` for normal page renders, and use `Response::redirect()` / `Response::json()` / `Response::view()`.
5. **Input handling:** Use `Input` helper or `$_POST` directly, then validate with `Validator::make()`.
6. **Output escaping:** In views, wrap user-controlled output with `\App\Core\Helpers::esc()`.
7. **CSRF:** Every POST action must call `Csrf::validate()` first and include `Csrf::field()` in the form.
8. **Database updates:** Whitelist column names in model `update()` methods.
9. **Uploads:** Use `FileUploader`; application documents go under `uploads/applications/{id}/`, profile photos go under `public/uploads/profiles/`.
10. **Logging:** Use `Logger::info()`, `Logger::warning()`, `Logger::error()` for audit/debug events.

---

## Security Considerations

* **CSRF protection** is required on all state-changing requests.
* **SQL injection** is prevented by PDO parameterization and column whitelisting.
* **XSS** is prevented by `Helpers::esc()` in views.
* **Upload hardening:** `FileUploader` validates extensions and byte-level MIME types. `public/uploads/.htaccess` denies execution of `.php` files.
* **Session security:** Configurable secure cookie (`SESSION_SECURE=true` requires HTTPS).
* **Information disclosure:** Set `APP_DEBUG=false` in production to hide paths and stack traces; generic branded error pages are shown.
* **Sensitive data:** Password hashes and reset tokens are not logged.
* **Access control:** Controllers currently gate roles manually; middleware classes are available if you want to enforce roles at the route level.

---

## Testing

There is **no formal test suite** currently. There is no `phpunit.xml`, `tests/` directory, or CI/CD configuration. If you add tests, create a `tests/` directory and add PHPUnit via Composer, keeping the same namespace conventions.

---

## Deployment

Target environment is Apache shared hosting (Hostinger).

1. Copy `.env.example` to `.env` and fill production values.
2. Set `APP_DEBUG=false`.
3. Set `SESSION_SECURE=true` (requires an active SSL certificate).
4. Set a strong 64-character `APP_SECRET`.
5. Point the domain document root to the `public/` directory.
6. Ensure `vendor/` is present on the server (upload it or run `composer install --no-dev`).
7. Ensure PHP 8.1+ and required extensions (`pdo`, `mbstring`, `json`, `fileinfo`) are enabled.
8. Make `storage/` and `public/uploads/` writable by the PHP process (`755`/`775`).
9. Run `php database/setup.php` for the first deploy, then `php database/update_schema.php` for updates.
10. Change the default admin password immediately after setup.
11. Remove or restrict `public/diagnose.php` after troubleshooting.

---

## Working Notes for AI Agents

* **Keep controllers lean.** Business logic belongs in models or helpers.
* **Do not bloat existing models with raw SQL arrays.** Reuse query patterns and keep joins in the `Application` model where they already exist.
* **Always escape output** in views with `Helpers::esc()`.
* **Always whitelist columns** when adding dynamic model updates.
* **Always enforce CSRF** for new POST endpoints.
* **Upload paths:** Application documents are stored under `uploads/applications/{id}/` (there is no dedicated file-serving route; files are referred to via their path in templates). Profile photos are stored under `public/uploads/profiles/` and served directly.
* **Middleware is defined but not heavily wired** in `web.php`; if you add protected route groups, prefer the existing middleware classes instead of duplicating role checks in controllers.
* **Self-healing schema:** The `Application` model can add columns at runtime. Do not rely on this for intentional schema changes; prefer `database/update_schema.php`.
* **Bilingual UI:** The views mix Hindi and English; preserve existing language choices when editing templates.

For deeper file-by-file details, see `docs/CODEBASE_INDEX.md` and `README.md`.
gual UI:** The views mix Hindi and English; preserve existing language choices when editing templates.

For deeper file-by-file details, see `docs/CODEBASE_INDEX.md` and `README.md`.

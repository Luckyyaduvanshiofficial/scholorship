# Tamboli Samaj Portal — Technical Implementation Roadmap

**Version:** 1.0
**Status:** Frozen
**Role:** Senior PHP Architect — Implementation Blueprint

---

## Table of Contents

1. [Architecture Review](#1-architecture-review)
2. [Missing Components Identified](#2-missing-components-identified)
3. [Implementation Order](#3-implementation-order)
4. [Module Dependencies](#4-module-dependencies)
5. [File Creation Order](#5-file-creation-order)
6. [Bootstrap Sequence](#6-bootstrap-sequence)
7. [Authentication Architecture](#7-authentication-architecture)
8. [Database Connection Architecture](#8-database-connection-architecture)
9. [Session Architecture](#9-session-architecture)
10. [File Upload Architecture](#10-file-upload-architecture)
11. [Authorization Strategy](#11-authorization-strategy)
12. [Error Handling Strategy](#12-error-handling-strategy)
13. [Logging Strategy](#13-logging-strategy)
14. [Application Status Workflow](#14-application-status-workflow)
15. [Document Verification Workflow](#15-document-verification-workflow)
16. [Phase 1: Project Bootstrap](#phase-1--project-bootstrap)
17. [Phase 2: Authentication System](#phase-2--authentication-system)
18. [Phase 3: Student Profile Module](#phase-3--student-profile-module)
19. [Phase 4: Academic Module](#phase-4--academic-module)
20. [Phase 5: Application Module](#phase-5--application-module)
21. [Phase 6: Document Upload Module](#phase-6--document-upload-module)
22. [Phase 7: Application Tracking Module](#phase-7--application-tracking-module)
23. [Phase 8: Admin Dashboard](#phase-8--admin-dashboard)
24. [Phase 9: Representative Dashboard](#phase-9--representative-dashboard)
25. [Phase 10: Announcements Module](#phase-10--announcements-module)

---

## 1. ARCHITECTURE REVIEW

### What is Approved (No Changes Allowed)

| Concern | Decision |
|---|---|
| Backend language | PHP 8.3 — pure, no framework |
| Database | MySQL 8 — PDO only |
| Frontend | Bootstrap 5 + Bootstrap Icons |
| Architecture style | Lightweight MVC |
| Namespace root | `App\` via PSR-4 |
| Entry point | `public/index.php` |
| Authentication | PHP native sessions |
| Route file | Single `web.php` |
| Config | `.env` file |
| Upload storage | Local filesystem under `uploads/` |
| Total database tables | 13 tables — schema frozen |

### Architecture Diagram

```
Request → public/index.php → App.php (bootstrap)
                                 │
                    ┌────────────┼────────────┐
                    ▼            ▼            ▼
              config/       core/         routes/
              (.env)    (Router, DB,    (web.php)
                        Session, Auth,
                        Logger)
                    │
                    ▼
           Router matches route
                    │
         ┌─────────┼─────────┐
         ▼                   ▼
   Middleware Stack    Controller
   (Auth, Role)        (Logic)
         │                   │
         └─────┬─────────────┘
               ▼
         Model (PDO)
               │
               ▼
          Database
               │
               ▼
         View (HTML)
               │
               ▼
          Response
```

### What This Architecture Avoids

- No composer dependencies beyond autoloader
- No ORM — direct PDO with prepared statements
- No template engine — raw PHP in views
- No DI container — manual instantiation
- No event system — direct method calls
- No queue/jobs — synchronous only

### Verified Against PRD

All 10 modules from the PRD map to the folder structure. All 13 tables are mapped. All 4 user roles (Guest, Student, Representative, Admin) are supported. All application statuses are covered.

---

## 2. MISSING COMPONENTS IDENTIFIED

### Gap 1 — CSRF Protection

**Problem:** No CSRF token mechanism in the architecture. All POST forms are vulnerable without it.

**Solution File:** `app/core/Csrf.php`

**Where Used:**
- Every POST form must include `<?= Csrf::field() ?>`
- Every POST endpoint must validate via `Csrf::validate()`
- Login, register, profile edit, application submit, document upload, admin actions

**Integration:** Call `Csrf::validate()` at the top of every controller method handling POST.

---

### Gap 2 — Flash Message System

**Problem:** Session-based feedback (success/error messages) referenced in UX wireframes but no dedicated mechanism.

**Solution File:** `app/core/Flash.php`

**Where Used:**
- After login success → "Welcome back, Name"
- After form submit → "Application submitted successfully"
- After document upload → "Document uploaded"
- After admin action → "Application approved"
- All redirects that need user feedback

**Integration:** Call `Flash::set('success', 'message')` before redirect. Render in `layouts/flash-message.php`.

---

### Gap 3 — Student Authentication Table

**Problem:** The `users` table has `role` enum with `super_admin`, `admin`, `representative`. There is no student login column on the `students` table. Students need authentication but the `users` table only stores staff.

**Evaluation:** Adding `password_hash` to `students` table creates mixed concerns (profile data + auth data). Creating a separate `student_users` table fragments auth. The cleanest approach for the current schema:

**Decision:** Add `password_hash VARCHAR(255)` and `status TINYINT` to the `students` table. The `Session` class stores `user_type` (`student` / `admin` / `representative`) to differentiate. This is the simplest path that avoids table duplication.

**Schema Change Required:**
```
ALTER TABLE students ADD COLUMN password_hash VARCHAR(255) AFTER email;
ALTER TABLE students ADD COLUMN status TINYINT DEFAULT 1 AFTER password_hash;
```

**Migration File:** `database/schema/01_add_student_auth.sql`

---

### Gap 4 — Input Sanitization Utility

**Problem:** Need a consistent way to fetch and sanitize `$_GET`, `$_POST`, `$_FILES` data.

**Solution File:** `app/core/Input.php`

**Methods:**
- `Input::get(string $key, $default = null): ?string`
- `Input::post(string $key, $default = null): ?string`
- `Input::file(string $key): ?array`
- `Input::all(): array`
- `Input::only(array $keys): array`
- `Input::except(array $keys): array`

All methods run `trim()` and basic sanitization. No HTML entity encoding (that's done in views). This centralizes input access instead of scattered `$_POST` calls.

---

### Gap 5 — Pagination Utility

**Problem:** Admin/Representative dashboards need paginated tables. Every list page (applications, students, announcements) will need pagination.

**Solution File:** `app/core/Pagination.php`

**Contract:**
- `Pagination::paginate(int $total, int $perPage = 20, int $currentPage = 1): array`
- Returns: `['offset', 'limit', 'total_pages', 'current_page', 'has_next', 'has_prev']`

**Where Used:** Every controller method that lists records.

---

### Gap 6 — Application Number Generator

**Problem:** `application_no` format `TSVS-2026-000001` requires sequential generation per session year. Must be atomic to avoid duplicates under concurrent requests.

**Solution File:** `app/core/ApplicationNumberGenerator.php`

**Logic:**
1. Lock `applications` table or use `INSERT ... SELECT MAX(...)` inside a transaction
2. Format: `TSVS-{session_year}-{6-digit-padded-counter}`
3. Counter resets per session year

---

### Gap 7 — Response/Redirect Helper

**Problem:** The `Response.php` class is in the folder structure but its contract is not defined. Consistent redirect patterns are needed.

**Solution File:** `app/core/Response.php`

**Methods:**
- `Response::redirect(string $url, int $statusCode = 302): void`
- `Response::back(): void` — redirect to `$_SERVER['HTTP_REFERER']` or `/`
- `Response::json(array $data, int $statusCode = 200): void`
- `Response::view(string $view, array $data = []): void`

---

### Gap 8 — Setting Model Usage

**Problem:** `Setting.php` model exists but no defined usage. What settings does the portal require?

**Decision:** Use a `settings` table (key-value) instead:

| Column | Type |
|---|---|
| id | BIGINT UNSIGNED PK |
| key | VARCHAR(100) UNIQUE |
| value | TEXT |
| updated_at | DATETIME |

**Settings stored:**
- `site_name` — "Tamboli Samaj Portal"
- `contact_email` — admin contact
- `contact_phone` — admin phone
- `scholarship_open` — `0`/`1` toggle
- `pratibha_open` — `0`/`1` toggle
- `current_session_id` — active academic session

**Schema Change:**
```
CREATE TABLE settings ...
```

**Migration File:** `database/schema/02_create_settings.sql`

---

### Summary of All Missing Components

| # | Component | File | Priority |
|---|---|---|---|
| 1 | CSRF Protection | `app/core/Csrf.php` | Critical |
| 2 | Flash Messages | `app/core/Flash.php` | High |
| 3 | Student Auth Columns | Schema migration | Critical |
| 4 | Input Sanitization | `app/core/Input.php` | High |
| 5 | Pagination Utility | `app/core/Pagination.php` | Medium |
| 6 | Application Number Generator | `app/core/ApplicationNumberGenerator.php` | High |
| 7 | Response Helper | `app/core/Response.php` | High |
| 8 | Settings Table | Schema migration | Medium |

---

## 3. IMPLEMENTATION ORDER

The implementation order follows a strict dependency chain. Nothing is built before its prerequisite is ready.

```
Phase 1  → Project Bootstrap          (Foundation)
Phase 2  → Authentication System      (Depends on Phase 1)
Phase 3  → Student Profile Module     (Depends on Phase 2)
Phase 4  → Academic Module            (Depends on Phase 3)
Phase 5  → Application Module         (Depends on Phase 3 + 4)
Phase 6  → Document Upload Module     (Depends on Phase 5)
Phase 7  → Application Tracking       (Depends on Phase 5)
Phase 8  → Admin Dashboard            (Depends on Phase 2 + 5)
Phase 9  → Representative Dashboard   (Depends on Phase 2 + 5)
Phase 10 → Announcements Module       (Depends on Phase 2)
```

### Rationale

- Phase 1 must come first — nothing works without config, DB, routing
- Phase 2 must come second — nothing is secured without auth
- Phase 3 before Phase 4 — academics belong to students
- Phase 3+4 before Phase 5 — applications need student + academic data
- Phase 5 before Phase 6 — documents belong to applications
- Phase 5 before Phase 7 — tracking requires existing applications
- Phase 8+9 can run in parallel after Phase 5
- Phase 10 has minimal dependencies — can start after Phase 2

---

## 4. MODULE DEPENDENCIES

### Dependency Graph

```
                    ┌──────────────────┐
                    │  Phase 1: Bootstrap│
                    └────────┬─────────┘
                             │
                    ┌────────▼─────────┐
                    │ Phase 2: Auth    │
                    └────────┬─────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
          ▼                  ▼                  ▼
   ┌─────────────┐   ┌─────────────┐   ┌──────────────┐
   │Phase 3:     │   │Phase 10:    │   │Phase 8: Admin│
   │Profile      │   │Announcements│   │Dashboard     │
   └──────┬──────┘   └─────────────┘   └──────┬───────┘
          │                                   │
          ▼                                   │
   ┌─────────────┐                            │
   │Phase 4:     │                            │
   │Academics    │                            │
   └──────┬──────┘                            │
          │                                   │
          ▼                                   │
   ┌─────────────┐                            │
   │Phase 5:     │◄───────────────────────────┘
   │Applications │
   └──────┬──────┘
          │
     ┌────┴────┐
     │         │
     ▼         ▼
┌─────────┐ ┌─────────┐
│Phase 6: │ │Phase 7: │
│Documents│ │Tracking │
└────┬────┘ └────┬────┘
     │           │
     └─────┬─────┘
           │
           ▼
   ┌─────────────┐
   │Phase 9:     │
   │Rep Dashboard│
   └─────────────┘
```

### Table: What Each Phase Needs Before It Can Start

| Phase | Must Complete First |
|---|---|
| Phase 1 | Nothing |
| Phase 2 | Phase 1 |
| Phase 3 | Phase 2 |
| Phase 4 | Phase 3 |
| Phase 5 | Phase 3, Phase 4 |
| Phase 6 | Phase 5 |
| Phase 7 | Phase 5 |
| Phase 8 | Phase 2, Phase 5 |
| Phase 9 | Phase 2, Phase 5, Phase 6, Phase 7 |
| Phase 10 | Phase 2 |

### Table: Which Core Files Each Phase Needs

| Phase | Core Files Required |
|---|---|
| Phase 1 | All 10 core classes: App, Database, Router, Session, Auth, Validator, FileUploader, Logger, Helpers, Response + 4 missing: Csrf, Flash, Input, Pagination |
| Phase 2 | App, Database, Router, Session, Auth, Csrf, Flash, Input, Response, Helpers |
| Phase 3 | All Phase 2 core + Student model |
| Phase 4 | All Phase 3 core + StudentAcademic model, AcademicSession model |
| Phase 5 | All Phase 4 core + Application, ApplicationType, ApplicationStatus, ApplicationNumberGenerator models |
| Phase 6 | All Phase 5 core + FileUploader, ApplicationDocument, DocumentType |
| Phase 7 | All Phase 5 core + ApplicationStatusLog, ApplicationStatus |
| Phase 8 | All Phase 5 core + all models for dashboard aggregation |
| Phase 9 | All Phase 5 core + Phase 6 + 7 core |
| Phase 10 | Phase 2 core + Announcement model |

---

## 5. FILE CREATION ORDER

This is the exact order to create files. Follow this sequence exactly to avoid referencing files that don't exist yet.

### Batch 1 — Configuration (Phase 1 Start)

```
.env.example
.env
app/config/constants.php
app/config/paths.php
app/config/database.php
app/config/app.php
```

### Batch 2 — Core Foundation (Phase 1)

```
app/core/Helpers.php        ← No dependencies
app/core/Logger.php         ← Depends on Helpers, constants
app/core/Database.php       ← Depends on config/database.php
app/core/Session.php        ← Depends on Helpers
app/core/Flash.php          ← Depends on Session
app/core/Csrf.php           ← Depends on Session
app/core/Input.php          ← No dependencies
app/core/Validator.php      ← Depends on Input
app/core/Pagination.php     ← No dependencies
app/core/Response.php       ← Depends on Flash
app/core/Auth.php           ← Depends on Session, Database
app/core/FileUploader.php   ← Depends on Helpers, constants
app/core/ApplicationNumberGenerator.php ← Depends on Database
```

### Batch 3 — Router + Middleware (Phase 1 End)

```
app/core/Router.php                 ← Depends on Response
app/core/App.php                    ← Depends on Router, Database, Session, Config
app/middleware/AuthMiddleware.php   ← Depends on Auth, Response
app/middleware/GuestMiddleware.php  ← Depends on Auth, Response
app/middleware/AdminMiddleware.php  ← Depends on Auth, Response
app/middleware/RepresentativeMiddleware.php ← Depends on Auth, Response
app/middleware/StudentMiddleware.php ← Depends on Auth, Response
```

### Batch 4 — Entry Points (Phase 1 Complete)

```
public/index.php          ← Depends on App.php
public/.htaccess          ← URL rewriting for clean URLs
.htaccess (root)          ← Redirect all traffic to public/
```

### Batch 5 — Models (Create as Needed Per Phase)

```
Create each model when its phase starts, not all at once.
See each phase section for model creation order.
```

### Batch 6 — Controllers (Create as Needed Per Phase)

```
Create each controller when its phase starts.
See each phase section.
```

### Batch 7 — Views (Create Per Controller Action)

```
Create views after their controller method is written.
See each phase section.
```

### Batch 8 — Routes (Add After Controller Created)

```
Add routes to app/routes/web.php after controller file is created.
See each phase section.
```

---

## 6. BOOTSTRAP SEQUENCE

The exact sequence that happens on every request through `public/index.php`:

```
1. Define ROOT_PATH constant
   └── dirname(__DIR__, 2) → project root

2. Load Composer autoloader
   └── require ROOT_PATH . '/vendor/autoload.php'

3. Load .env file
   └── Parse ROOT_PATH . '/.env'
   └── Populate $_ENV superglobal

4. Load configuration files in order:
   └── app/config/constants.php
   └── app/config/paths.php
   └── app/config/database.php
   └── app/config/app.php

5. Set error reporting
   └── Based on APP_DEBUG in .env
   └── Debug ON: E_ALL, display_errors=On
   └── Debug OFF: E_ALL & ~E_DEPRECATED, display_errors=Off, log_errors=On

6. Set timezone
   └── From config/app.php → 'Asia/Kolkata'

7. Start session
   └── Session::start()
   └── Configure: secure, httponly, samesite=Lax
   └── Regenerate ID on login

8. Initialize Database singleton
   └── Database::getInstance()
   └── Connection is lazy — connects on first query

9. Initialize Router
   └── Load routes from app/routes/web.php

10. Dispatch request
    └── $app = new App\Core\App();
    └── $app->run();
    └── Inside run():
        ├── Router::resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])
        ├── Execute middleware chain for matched route
        ├── If middleware passes → call controller method
        ├── If middleware fails → redirect or 403
        └── Catch exceptions → show error page or log
```

### Error Page Mapping

| Exception / Status | View |
|---|---|
| 401 | `app/views/errors/401.php` |
| 403 | `app/views/errors/403.php` |
| 404 | `app/views/errors/404.php` |
| 500 | `app/views/errors/500.php` |
| PDOException | `app/views/errors/500.php` + log |
| Throwable | `app/views/errors/500.php` + log |

---

## 7. AUTHENTICATION ARCHITECTURE

### Authentication Flow

```
┌──────────────────────────────────────────────────────┐
│                   LOGIN FLOW                          │
├──────────────────────────────────────────────────────┤
│                                                       │
│  1. User submits mobile + password                    │
│      │                                                │
│      ▼                                                │
│  2. Auth::attempt(mobile, password)                   │
│      │                                                │
│      ├── Look up students.mobile                      │
│      │   └── Student login (user_type = 'student')    │
│      │                                                │
│      ├── Look up users.email                          │
│      │   └── Admin/Rep login (user_type = role)       │
│      │                                                │
│      ▼                                                │
│  3. password_verify(password, stored_hash)            │
│      │                                                │
│      ├── Match → Session::set('user_id', id)          │
│      │           Session::set('user_type', type)      │
│      │           Session::regenerate()                │
│      │           Redirect to dashboard                │
│      │                                                │
│      └── Fail → Flash::set('error', 'Invalid...')     │
│                 Redirect back to login                │
│                                                       │
└──────────────────────────────────────────────────────┘
```

### Login Endpoint

```
POST /login
Controller: AuthController@login
Middleware: GuestMiddleware (redirect if already logged in)
```

### Registration Flow (Student Only)

```
POST /register
Controller: AuthController@register

1. Validate: mobile (unique), password (min 8 chars)
2. Insert into students table
3. Hash password with password_hash(PASSWORD_BCRYPT)
4. Auto-login: set session
5. Redirect to Profile completion
```

### Password Hashing

```
Algorithm: BCRYPT
Cost: 12 (DEFAULT)
Method: password_hash($password, PASSWORD_BCRYPT)
Verify: password_verify($password, $hash)
```

### Session Structure

```php
$_SESSION['user_id']   => int    // students.id or users.id
$_SESSION['user_type'] => string // 'student' | 'admin' | 'super_admin' | 'representative'
$_SESSION['user_name'] => string // first_name or name
$_SESSION['csrf_token']=> string // CSRF token
$_SESSION['flash']     => array  // Flash messages
```

### Auth Helper Methods

```
Auth::check(): bool              — Is user logged in?
Auth::user(): ?array             — Current user row
Auth::id(): ?int                 — Current user ID
Auth::isStudent(): bool          — user_type === 'student'
Auth::isAdmin(): bool            — user_type === 'admin' || 'super_admin'
Auth::isRepresentative(): bool   — user_type === 'representative'
Auth::guest(): bool              — Not logged in
Auth::logout(): void             — Destroy session
```

### Logout Flow

```
POST /logout
Controller: AuthController@logout

1. Session::destroy()
2. Redirect to /
```

### Route Protection Pattern

```php
// Guest only (login/register pages)
$router->get('/login', 'AuthController@loginForm', ['guest']);

// Authenticated only
$router->get('/dashboard', 'DashboardController@index', ['auth']);

// Role-specific
$router->get('/admin', 'AdminController@dashboard', ['auth', 'admin']);
$router->get('/representative', 'DashboardController@repDashboard', ['auth', 'representative']);
$router->get('/profile', 'ProfileController@index', ['auth', 'student']);
```

### Forgot Password (Future)

The `forgot-password.php` view exists. Implementation deferred. Current workaround: Admin resets student password via admin panel.

---

## 8. DATABASE CONNECTION ARCHITECTURE

### Configuration

```
File: .env
Variables:
  DB_HOST=localhost
  DB_PORT=3306
  DB_NAME=tamboli_samaj
  DB_USER=root
  DB_PASS=

File: app/config/database.php
  Reads $_ENV values
  Defines PDO options array
```

### PDO Options (Immutable)

```php
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
PDO::ATTR_EMULATE_PREPARES   => false              // Real prepared statements
PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
```

### Singleton Pattern

```
Database::getInstance() → PDO object

First call:    Create connection → store in private static $instance
Subsequent:    Return existing $instance
```

### Model Base Class Contract

Every model receives PDO via constructor injection:

```php
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
```

### Prepared Statement Requirement

Every SQL query must use prepared statements. No exceptions.

```php
// ALWAYS:
$stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);

// NEVER:
$stmt = $this->db->query("SELECT * FROM students WHERE id = $id");
```

### Transaction Pattern

```php
$this->db->beginTransaction();
try {
    // Multiple queries
    $this->db->commit();
} catch (\Exception $e) {
    $this->db->rollBack();
    throw $e;
}
```

Used by: Application submit (insert application + scholarship/pratibha details + documents + status log in one transaction).

---

## 9. SESSION ARCHITECTURE

### Configuration

```
Session name:        TSP_SESSION          (not default PHPSESSID)
Session lifetime:    86400                (24 hours)
Cookie path:         /
Cookie domain:       (auto — from request)
Cookie secure:       true in production   (HTTPS only)
Cookie httponly:     true                 (no JS access)
Cookie samesite:     Lax
```

### Session Start

Called exactly once per request in `public/index.php` bootstrap sequence.

```
Session::start()
  → session_name('TSP_SESSION')
  → session_set_cookie_params($lifetime, '/', '', $secure, true)
  → session_start()
  → Regenerate ID on privilege escalation (login)
```

### Session Data

```php
// Core session keys
user_id      — int
user_type    — 'student'|'admin'|'super_admin'|'representative'
user_name    — string
csrf_token   — string (64-char hex)

// Flash data — stored in session, cleared after one read
flash        — ['success' => [...], 'error' => [...]]
old_input    — array (for form repopulation after validation errors)
```

### Session Security

- Regenerate session ID after login (prevents session fixation)
- Never store passwords in session
- Clear session on logout
- Session timeout check on every authenticated request (optional — Phase 2+)

---

## 10. FILE UPLOAD ARCHITECTURE

### Upload Directory Structure

```
uploads/
├── profile/
│   └── {student_code}/           ← Profile photos
│
├── applications/
│   ├── {year}/
│   │   └── {application_no}/
│   │       ├── marksheets/       ← Marksheet uploads
│   │       ├── passbooks/        ← Bank passbook uploads
│   │       ├── certificates/     ← Certificate uploads
│   │       ├── photos/           ← Application-specific photos
│   │       └── other/            ← Miscellaneous documents
```

### File Naming Convention

```
{timestamp}_{random-8-chars}.{extension}
Example: 1687550400_a3f8c2e1.jpg
```

Original filename is preserved in the `application_documents.original_name` column.

### Allowed File Types

| Category | Extensions | Max Size |
|---|---|---|
| Image | jpg, jpeg, png | 2 MB |
| Document | pdf | 5 MB |

Validation: Check both extension AND MIME type. Do not trust extension alone.

### Upload Flow

```
1. FileUploader::validate($_FILES['file'])
   ├── Check upload error codes
   ├── Check file size
   ├── Check extension against whitelist
   ├── Check MIME type via finfo
   └── Return true/false with error message

2. FileUploader::upload($_FILES['file'], string $directory)
   ├── Generate stored_name
   ├── Create directory if not exists
   ├── move_uploaded_file()
   └── Return stored_name or throw exception

3. Insert record into application_documents
   ├── application_id
   ├── document_type_id
   ├── original_name
   ├── stored_name
   ├── mime_type
   ├── file_size
   └── verification_status = 'pending'
```

### Security Measures

- Store files ABOVE web root or in a directory with `.htaccess` denying direct access
- All file access goes through a proxy script: `serve-file.php?token=...`
- Token is a one-time, short-lived hash tied to the application and document
- Validate token before streaming the file
- Set proper `Content-Type` and `Content-Disposition` headers

### `uploads/.htaccess`

```
Deny from all
```

### File Serving Endpoint

```
GET /file/{token}
Controller: DocumentController@serve
Middleware: auth

1. Validate token (hash of document_id + timestamp + secret)
2. Verify token not expired (10 minutes)
3. Fetch file path from application_documents
4. Stream file with readfile()
5. Set proper headers
```

---

## 11. AUTHORIZATION STRATEGY

### Role Hierarchy

```
super_admin
    ├── Full access — everything
    │
admin
    ├── Manage students
    ├── Manage applications (approve/reject)
    ├── Manage announcements
    ├── Manage representatives
    ├── View reports
    │
representative
    ├── View assigned students/applications
    ├── Verify documents
    ├── Add remarks
    ├── Recommend approval
    │
student
    ├── Own profile (view/edit)
    ├── Own academics (view/edit)
    ├── Own applications (create/view)
    ├── Own documents (upload/view)
    ├── Own application status (view only)
    │
guest
    └── View homepage, announcements, login, register
```

### Middleware Implementation

```
AuthMiddleware
  ✓ Must be logged in
  ✗ Redirect to /login if not

GuestMiddleware
  ✓ Must NOT be logged in
  ✗ Redirect to /dashboard if logged in

AdminMiddleware
  ✓ Must be admin or super_admin
  ✗ 403 if not

RepresentativeMiddleware
  ✓ Must be representative
  ✗ 403 if not

StudentMiddleware
  ✓ Must be student
  ✗ 403 if not
```

### Route Group Middleware Assignment

```php
// In web.php
$router->group(['middleware' => 'auth'], function($router) {
    // All routes here require login

    $router->group(['middleware' => 'admin'], function($router) {
        // All routes here require admin
        $router->get('/admin', 'AdminController@dashboard');
    });
});
```

### Resource Ownership Check

When a student accesses their own data, verify ownership:

```
ProfileController@edit:
  $studentId = Auth::id();
  // Only allow editing own profile
  // Model query: WHERE id = ? AND that ID belongs to logged-in student
```

Pattern: Controller methods that accept an `$id` parameter must verify:
1. The record exists
2. The record belongs to the authenticated user (or user is admin)
3. Show 404 if record not found (don't reveal existence)
4. Show 403 if record exists but doesn't belong to user

---

## 12. ERROR HANDLING STRATEGY

### Error Levels

| Level | Action | Example |
|---|---|---|
| Development | Display full error with trace | APP_DEBUG=true |
| Production | Log error, show generic 500 page | APP_DEBUG=false |

### Exception Handling

```
App::run() wraps dispatch in try/catch:

try {
    Router::resolve();
} catch (PDOException $e) {
    Logger::error('Database error', ['message' => $e->getMessage()]);
    showErrorPage(500);
} catch (\Throwable $e) {
    Logger::error('Application error', ['message' => $e->getMessage()]);
    showErrorPage(500);
}
```

### HTTP Error Pages

```
401 → Unauthorized (not logged in)
403 → Forbidden (logged in but wrong role)
404 → Not Found (route or resource doesn't exist)
500 → Internal Server Error (unexpected exception)
```

### Validation Errors

```
Pattern:
1. Validator::validate($data, $rules) → array of errors
2. If errors: Flash::set('errors', $errors)
3. Flash::set('old', $_POST) — repopulate form
4. Redirect back
5. View renders errors and old values
```

### PDO Error Handling

PDO throws `PDOException` on query failure. Always let it throw — never silence with `@` or empty try/catch. The global exception handler in `App::run()` catches and logs it.

---

## 13. LOGGING STRATEGY

### Log File

```
storage/logs/app.log
```

New file per day:
```
storage/logs/app-2026-06-23.log
```

### Logger Methods

```
Logger::info(string $message, array $context = [])
Logger::warning(string $message, array $context = [])
Logger::error(string $message, array $context = [])
```

### Log Format

```
[2026-06-23 14:30:45] [ERROR] [Database] Connection failed: Access denied
[2026-06-23 14:31:02] [INFO] [Auth] User 42 logged in
[2026-06-23 14:32:10] [WARNING] [FileUpload] File too large: 15MB (max: 5MB)
```

### What Gets Logged

| Event | Level |
|---|---|
| Failed login attempt | WARNING |
| Successful login | INFO |
| Application submitted | INFO |
| Application status change | INFO |
| Document upload | INFO |
| Document verification | INFO |
| Admin action (approve/reject) | INFO |
| Database connection failure | ERROR |
| Uncaught exceptions | ERROR |
| CSRF token mismatch | WARNING |
| 403/404 errors | WARNING |

### What Does NOT Get Logged

- Passwords (ever)
- Full session data
- Full file contents
- Credit/debit card numbers
- Personal identification numbers (Aadhaar, PAN)

### Log Rotation

Automatic cleanup: Keep logs for 30 days. Delete older files.

```
Logger::rotate(int $keepDays = 30): void
```

---

## 14. APPLICATION STATUS WORKFLOW

### Status Definitions

| Status | ID | Meaning | Who Sets It |
|---|---|---|---|
| Draft | 1 | Application started but not submitted | Student |
| Submitted | 2 | Application submitted, awaiting verification | Student (on submit) |
| Under Review | 3 | Representative is verifying | Representative |
| Approved | 4 | Application approved for scholarship | Admin |
| Rejected | 5 | Application rejected | Admin |

### Status Transition Rules (State Machine)

```
                    ┌──────────┐
                    │  Draft   │
                    └────┬─────┘
                         │ Student submits
                         ▼
                    ┌──────────┐
                    │ Submitted│
                    └────┬─────┘
                         │ Representative picks up
                         ▼
                 ┌──────────────┐
                 │ Under Review │
                 └──────┬───────┘
                        │
              ┌─────────┴─────────┐
              │                   │
              ▼                   ▼
        ┌──────────┐       ┌──────────┐
        │ Approved │       │ Rejected │
        └──────────┘       └──────────┘
```

### Forbidden Transitions

```
Draft      → Approved      ✗ (must go through Submitted)
Draft      → Under Review  ✗ (must go through Submitted)
Approved   → Rejected      ✗ (final state — cannot change)
Rejected   → Approved      ✗ (final state — cannot change)
Approved   → Draft         ✗ (cannot revert to draft)
```

### Status Log Audit Trail

Every status change creates a row in `application_status_logs`:

```
application_id  → which application
old_status_id   → previous status (NULL for first change)
new_status_id   → new status
changed_by      → users.id or students.id
remarks         → reason for change
created_at      → timestamp
```

This creates a complete history. The timeline on the tracking page reads from this table.

### Status Display Colors

| Status | Bootstrap Badge Class |
|---|---|
| Draft | `bg-secondary` |
| Submitted | `bg-info` |
| Under Review | `bg-warning text-dark` |
| Approved | `bg-success` |
| Rejected | `bg-danger` |

---

## 15. DOCUMENT VERIFICATION WORKFLOW

### Verification States

| State | Meaning |
|---|---|
| `pending` | Uploaded but not yet verified |
| `verified` | Representative/admin confirmed document is valid |
| `rejected` | Document is invalid/wrong/illegible |

### Verification Flow

```
Student uploads document
         │
         ▼
  [pending] ──────────────┐
         │                │
         │ Representative or Admin reviews
         │                │
    ┌────┴────┐           │
    │         │           │
    ▼         ▼           │
[verified] [rejected]     │
    │         │           │
    │         │ Student uploads new document
    │         └───────────┘
    │              (sets back to pending)
    │
    ▼
All documents verified
    │
    ▼
Application eligible for approval
```

### Verification Rules

```
When Representative clicks "Verify":
  1. Mark document as 'verified'
  2. Log action in application_status_logs with remarks

When Representative clicks "Reject Document":
  1. Mark document as 'rejected'
  2. Add remark explaining why
  3. Student sees notification to re-upload
  4. Application status may remain "Under Review"

Auto-check on Application Approval:
  └── Are ALL required documents in 'verified' state?
      YES → Allow approval
      NO  → Block approval, show message: 
            "Cannot approve. Documents pending verification."
```

### Required Documents Per Application Type

| Application Type | Required Documents |
|---|---|
| Scholarship | Marksheet, Bank Passbook, Photo |
| Pratibha Samman | Marksheet, Certificate, Photo |

### Document Display in Admin Panel

```
┌──────────────────────────────────────────────┐
│ Document Verification Panel                   │
├──────────────────────────────────────────────┤
│                                               │
│  Type: Marksheet                              │
│  File: 10th-marksheet.pdf                     │
│  Status: [pending ▼]  → verified / rejected   │
│  Uploaded: 2026-06-20 14:30                   │
│                                               │
│  [View Document]  [Verify]  [Reject]          │
│                                               │
│  Remarks: ______________________              │
│                                               │
└──────────────────────────────────────────────┘
```

---

## PHASE 1 — PROJECT BOOTSTRAP

### Purpose

Establish the project skeleton. After this phase, the application loads without errors, serves the homepage, and has all configuration, routing, and core utilities ready. No features exist yet — just the empty shell that future phases plug into.

### Required Files

```
New files to create:
  .env.example
  .env
  .htaccess (root)
  public/.htaccess
  public/index.php

  app/config/constants.php
  app/config/paths.php
  app/config/database.php
  app/config/app.php

  app/core/Helpers.php
  app/core/Logger.php
  app/core/Database.php
  app/core/Session.php
  app/core/Flash.php
  app/core/Csrf.php
  app/core/Input.php
  app/core/Validator.php
  app/core/Pagination.php
  app/core/Response.php
  app/core/Auth.php           ← Stub only (login/logout not yet built)
  app/core/FileUploader.php
  app/core/ApplicationNumberGenerator.php
  app/core/Router.php
  app/core/App.php

  app/middleware/AuthMiddleware.php
  app/middleware/GuestMiddleware.php
  app/middleware/AdminMiddleware.php
  app/middleware/RepresentativeMiddleware.php
  app/middleware/StudentMiddleware.php

  app/controllers/HomeController.php    ← Serves landing page
  app/controllers/ErrorController.php   ← Serves 401/403/404/500 pages

  app/views/layouts/header.php
  app/views/layouts/footer.php
  app/views/layouts/navbar.php
  app/views/layouts/sidebar.php         ← Empty shell per role
  app/views/layouts/flash-message.php

  app/views/home/index.php               ← Landing page

  app/views/errors/401.php
  app/views/errors/403.php
  app/views/errors/404.php
  app/views/errors/500.php

  app/routes/web.php

  public/assets/css/style.css           ← Empty starter
  public/assets/css/admin.css           ← Empty starter
  public/assets/js/app.js               ← Empty starter
  public/assets/js/admin.js             ← Empty starter

Directory structure:
  public/assets/css/
  public/assets/js/
  public/assets/images/logo/
  public/assets/images/banners/
  public/assets/images/icons/
  uploads/profile/
  uploads/applications/
  storage/logs/
  storage/cache/
  storage/temp/
  database/schema/
  database/seeds/
  database/backups/
```

### Database Tables Used

None yet. Schema files are in `database/schema/` but not executed.

### Dependencies

None. This is the foundation phase.

### Acceptance Criteria

- [ ] `public/index.php` loads without errors
- [ ] `.env` is parsed and `$_ENV` populated
- [ ] All config files load in correct order
- [ ] Database singleton connects successfully (test with a `SELECT 1`)
- [ ] Router matches `/` and `/home` to HomeController
- [ ] Visiting `http://localhost:8000/` shows the landing page
- [ ] Visiting a non-existent URL (`/xyz`) shows `404.php` error page
- [ ] Error pages (401, 403, 404, 500) render correctly
- [ ] CSRF token is generated and present in session
- [ ] Flash messages display and clear after one read
- [ ] Logger writes to `storage/logs/app-{date}.log`
- [ ] All PHP files declare `declare(strict_types=1);`
- [ ] No PHP warnings or notices with `APP_DEBUG=true`
- [ ] `APP_DEBUG=false` suppresses display_errors and enables log_errors

### Test Commands

```bash
# Test database connection
php -r "require 'vendor/autoload.php'; use App\Core\Database; Database::getInstance(); echo 'OK';"

# Test routing
curl http://localhost:8000/        # → 200, landing page
curl http://localhost:8000/nonexistent  # → 404 page

# Test CSRF
# Add a debug route that dumps CSRF token, verify in browser

# Test logging
# Trigger a log entry, verify file created in storage/logs/
```

---

## PHASE 2 — AUTHENTICATION SYSTEM

### Purpose

Enable login, registration, logout, and session management. After this phase, students can register and log in. Admins and representatives can log in via seeded accounts. Route protection via middleware works. CSRF protection is active on all forms.

### Required Files

```
New:
  app/models/User.php
  app/models/Student.php

  app/controllers/AuthController.php        ← login, register, logout, forgot-password
  app/controllers/DashboardController.php   ← Role-based dashboard routing

  app/views/auth/login.php
  app/views/auth/register.php
  app/views/auth/forgot-password.php

  app/views/dashboard/index.php             ← After-login landing

  database/schema/01_add_student_auth.sql
  database/seeds/admin_seeder.php           ← Creates default admin account
  database/seeds/representative_seeder.php  ← Creates sample representative

Modified:
  app/core/Auth.php                         ← Fully implement login/logout/check
  app/routes/web.php                        ← Add auth routes

  public/assets/css/style.css               ← Add auth page styles
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `students` | Student registration + login (new columns: password_hash, status) |
| `users` | Admin and representative login |

### Routes Added

```
GET   /login              AuthController@loginForm        [guest]
POST  /login              AuthController@login            [guest]
GET   /register           AuthController@registerForm     [guest]
POST  /register           AuthController@register          [guest]
POST  /logout             AuthController@logout           [auth]
GET   /forgot-password    AuthController@forgotPasswordForm [guest]
POST  /forgot-password    AuthController@forgotPassword    [guest]
GET   /dashboard          DashboardController@index       [auth]
```

### Dependencies

- Phase 1 complete (config, routing, session, CSRF, flash messages)

### Controller Logic Summary

```
AuthController@login:
  POST only
  1. Csrf::validate()
  2. Get mobile + password from Input::post()
  3. Try students.mobile first → student login
  4. If not found, try users.email → staff login
  5. password_verify()
  6. On success: Session::set(...), redirect to dashboard
  7. On failure: Flash error, redirect back

AuthController@register:
  POST only
  1. Csrf::validate()
  2. Validate: mobile (required, unique, 10 digits), password (min 8)
  3. Insert into students
  4. Auto-login
  5. Flash success, redirect to profile completion

AuthController@logout:
  POST only
  1. Session::destroy()
  2. Redirect to /

DashboardController@index:
  1. Check Auth::user_type
  2. Student → redirect to student dashboard view
  3. Admin/super_admin → redirect to admin dashboard
  4. Representative → redirect to representative dashboard
```

### Acceptance Criteria

- [ ] Student can register with mobile + password
- [ ] Student can login with mobile + password
- [ ] Admin can login with email + password (from seeder)
- [ ] Representative can login with email + password (from seeder)
- [ ] Wrong credentials show error flash message
- [ ] After login, redirects to correct role-based dashboard
- [ ] Logout destroys session and redirects to homepage
- [ ] Guest can access `/login`, `/register`, `/`
- [ ] Guest cannot access `/dashboard` (redirected to `/login`)
- [ ] Logged-in user cannot access `/login` (redirected to `/dashboard`)
- [ ] CSRF token present on all forms
- [ ] POST requests without CSRF token are rejected
- [ ] Password is hashed with BCRYPT (verify in database)
- [ ] Session ID changes after login (regenerated)
- [ ] Error page 401 renders when accessing protected route without login
- [ ] Error page 403 renders when accessing route without correct role

---

## PHASE 3 — STUDENT PROFILE MODULE

### Purpose

After registration, student completes their profile. Profile data is stored once and reused across all future applications. This phase creates the student profile CRUD.

### Required Files

```
New:
  app/models/Student.php                   ← Already created in Phase 2, extend here
  app/controllers/ProfileController.php

  app/views/profile/index.php              ← View profile
  app/views/profile/edit.php               ← Edit profile form

Modified:
  app/routes/web.php                       ← Add profile routes
  public/assets/css/style.css              ← Add profile page styles
  public/assets/js/app.js                  ← Add form validation JS
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `students` | All profile fields |

### Routes Added

```
GET   /profile            ProfileController@index      [auth, student]
GET   /profile/edit       ProfileController@edit       [auth, student]
POST  /profile/update     ProfileController@update     [auth, student]
```

### Controller Logic Summary

```
ProfileController@index:
  1. Fetch student by Auth::id()
  2. Load profile view with student data
  3. Show completion percentage

ProfileController@edit:
  1. Fetch student by Auth::id()
  2. Load edit form with current values

ProfileController@update:
  POST only
  1. Csrf::validate()
  2. Validate all fields (required, formats)
  3. Update students table WHERE id = Auth::id()
  4. Flash success, redirect to profile view
```

### Profile Completion Percentage

```
Calculation:
  total_fields = count of profile form fields
  filled_fields = count of non-null, non-empty fields
  percentage = (filled_fields / total_fields) * 100

Display: Progress bar on dashboard and profile page
```

### Acceptance Criteria

- [ ] Student can view their profile
- [ ] Student can edit their profile
- [ ] All fields validated (required fields, mobile format, email format)
- [ ] Validation errors show inline with old values preserved
- [ ] Profile update success shows flash message
- [ ] Profile completion percentage calculates correctly
- [ ] Profile data persists (verify in database after update)
- [ ] Other students cannot view/edit this student's profile
- [ ] Admin can view any student's profile (but basic view only at this phase)

---

## PHASE 4 — ACADEMIC MODULE

### Purpose

Student adds their academic details for a specific academic session. One student can have multiple academic records (one per session). This data is used when applying for scholarships.

### Required Files

```
New:
  app/models/AcademicSession.php
  app/models/StudentAcademic.php
  app/controllers/AcademicController.php

  app/views/academics/index.php            ← List student's academic records
  app/views/academics/create.php           ← Add new academic record
  app/views/academics/edit.php             ← Edit existing record

Modified:
  app/routes/web.php

  database/seeds/academic_session_seeder.php ← Create sessions (2025-26, 2026-27)
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `academic_sessions` | Academic years (2025-26, 2026-27) |
| `student_academics` | Student's academic records per session |

### Routes Added

```
GET   /academics                   AcademicController@index     [auth, student]
GET   /academics/create            AcademicController@create    [auth, student]
POST  /academics/store             AcademicController@store     [auth, student]
GET   /academics/{id}/edit         AcademicController@edit      [auth, student]
POST  /academics/{id}/update       AcademicController@update    [auth, student]
```

### Controller Logic Summary

```
AcademicController@index:
  1. Fetch all student_academics for Auth::id()
  2. JOIN academic_sessions for session name
  3. Show list with edit buttons

AcademicController@create:
  1. Show form
  2. Dropdown of active academic sessions

AcademicController@store:
  1. Csrf::validate()
  2. Validate: session_id, course_name, marks, percentage
  3. Check UNIQUE constraint (student_id, session_id)
  4. Insert
  5. Flash success, redirect to index

AcademicController@edit:
  1. Fetch record, verify belongs to student
  2. Show edit form

AcademicController@update:
  1. Csrf::validate()
  2. Validate all fields
  3. Update WHERE id = ? AND student_id = Auth::id()
  4. Flash success, redirect to index
```

### Acceptance Criteria

- [ ] Student can add academic record for a session
- [ ] Student can edit their academic record
- [ ] Student cannot add duplicate records for same session
- [ ] Marks and percentage calculate correctly (auto-calc percentage)
- [ ] Academic records list shows session name, course, percentage
- [ ] Student can only see their own academic records
- [ ] Active academic sessions appear in dropdown
- [ ] Seeded academic sessions exist (2025-26, 2026-27)

---

## PHASE 5 — APPLICATION MODULE

### Purpose

Student creates scholarship or pratibha samman applications. This is the core feature. Auto-fetches profile and academic data. Generates unique application number. Supports both application types.

### Required Files

```
New:
  app/models/Application.php
  app/models/ApplicationType.php
  app/models/ApplicationStatus.php
  app/models/ScholarshipDetail.php
  app/models/PratibhaDetail.php
  app/models/ApplicationStatusLog.php

  app/controllers/ApplicationController.php

  app/views/applications/index.php          ← List student's applications
  app/views/applications/create.php         ← Select application type
  app/views/applications/scholarship.php    ← Scholarship application form
  app/views/applications/pratibha.php       ← Pratibha samman application form
  app/views/applications/documents.php      ← Document upload section (Phase 6 integration point)

Modified:
  app/routes/web.php

  database/seeds/application_type_seeder.php
  database/seeds/application_status_seeder.php
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `applications` | Core application record |
| `application_types` | Scholarship / Pratibha Samman |
| `application_status` | Status definitions |
| `scholarship_details` | Scholarship-specific fields |
| `pratibha_details` | Pratibha-specific fields |
| `application_status_logs` | Status change history |
| `students` | Auto-fetch profile |
| `student_academics` | Auto-fetch education |
| `academic_sessions` | Current academic year |

### Routes Added

```
GET   /applications                           ApplicationController@index           [auth, student]
GET   /applications/create                    ApplicationController@create          [auth, student]
POST  /applications/type                      ApplicationController@selectType      [auth, student]
GET   /applications/scholarship               ApplicationController@scholarship     [auth, student]
POST  /applications/scholarship               ApplicationController@storeScholarship [auth, student]
GET   /applications/pratibha                  ApplicationController@pratibha        [auth, student]
POST  /applications/pratibha                  ApplicationController@storePratibha   [auth, student]
GET   /applications/{application_no}          ApplicationController@show            [auth]
GET   /applications/{application_no}/documents ApplicationController@documents      [auth, student]
```

### Controller Logic Summary

```
ApplicationController@index:
  1. Fetch all applications for Auth::id()
  2. JOIN application_types and application_status
  3. Show card-based list with status badges

ApplicationController@create:
  1. Check: student has completed profile? → No → redirect to profile
  2. Check: student has added academics? → No → redirect to academics
  3. Show application type selection page

ApplicationController@selectType:
  POST only
  1. Get type_id
  2. Check no duplicate: UNIQUE(student_id, session_id, application_type_id)
  3. If scholarship → redirect to scholarship form
  4. If pratibha → redirect to pratibha form

ApplicationController@scholarship:
  1. Show scholarship form
  2. Pre-fill profile fields (from students table)
  3. Pre-fill academic fields (from student_academics)

ApplicationController@storeScholarship:
  POST only
  1. Csrf::validate()
  2. BEGIN TRANSACTION
  3. Generate application_no (ApplicationNumberGenerator)
  4. INSERT into applications (status = 'Submitted')
  5. INSERT into scholarship_details
  6. INSERT into application_status_logs (old = NULL, new = Submitted)
  7. COMMIT
  8. Flash success, redirect to show page

ApplicationController@pratibha / storePratibha:
  Same flow as scholarship but for pratibha_details

ApplicationController@show:
  1. Fetch application by application_no
  2. Verify: student owns it OR user is admin/representative
  3. Load all related data (profile, academics, scholarship/pratibha details)
  4. Show read-only view with status timeline
```

### Application Number Format

```
TSVS-{session_year}-{6-digit-counter}
Example: TSVS-2026-000001
```

### Duplicate Prevention

```
Before creating application, check:
  SELECT COUNT(*) FROM applications
  WHERE student_id = ? AND session_id = ? AND application_type_id = ?

If count > 0 → show error: "You have already applied for this."
```

### Acceptance Criteria

- [ ] Student sees "Apply Now" button on dashboard
- [ ] Student selects application type (Scholarship / Pratibha Samman)
- [ ] Form pre-fills profile and academic data
- [ ] Student fills remaining fields and submits
- [ ] Application number is generated (TSVS-2026-000XXX format)
- [ ] Application status set to "Submitted"
- [ ] Status log entry created
- [ ] Student sees application confirmation page
- [ ] Student cannot create duplicate application for same type+session
- [ ] Student can view list of all their applications with status badges
- [ ] Student can view individual application details
- [ ] All operations inside transaction (rollback on failure)
- [ ] Application number is unique (validated at DB level)

---

## PHASE 6 — DOCUMENT UPLOAD MODULE

### Purpose

Student uploads required documents for their application. Documents are stored securely on the filesystem. Each document is linked to an application and document type. Verification status is tracked.

### Required Files

```
New:
  app/models/ApplicationDocument.php
  app/models/DocumentType.php
  app/controllers/DocumentController.php

  app/views/applications/documents.php       ← Document upload page
  app/views/applications/documents-list.php  ← List uploaded documents

Modified:
  app/core/FileUploader.php                  ← Fully implement (stub from Phase 1)
  app/routes/web.php

  database/seeds/document_type_seeder.php

  uploads/applications/                      ← Directory structure created
  uploads/.htaccess                          ← Deny direct access
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `application_documents` | Uploaded files |
| `document_types` | Document categories |
| `applications` | Parent application |

### Routes Added

```
GET   /applications/{application_no}/documents       DocumentController@index      [auth, student]
POST  /applications/{application_no}/documents/upload DocumentController@upload     [auth, student]
GET   /file/{token}                                  DocumentController@serve      [auth]
```

### Controller Logic Summary

```
DocumentController@index:
  1. Fetch application by application_no
  2. Fetch all application_documents for this application
  3. Show upload page with existing documents list
  4. Show required vs uploaded status

DocumentController@upload:
  POST only
  1. Csrf::validate()
  2. FileUploader::validate($_FILES['document'])
  3. Generate storage path
  4. FileUploader::upload($_FILES['document'], $path)
  5. INSERT into application_documents
  6. Flash success, redirect back

DocumentController@serve:
  1. Validate token (HMAC of document_id + timestamp + APP_SECRET)
  2. Check token expiry (10 minutes)
  3. Fetch document record
  4. Verify application ownership or staff role
  5. Stream file with readfile()
  6. Set Content-Type and Content-Disposition headers
```

### File Validation

```
Allowed extensions: jpg, jpeg, png, pdf
Max sizes: 2MB (images), 5MB (PDF)
MIME check: finfo(FILEINFO_MIME_TYPE)
```

### Security

```
1. uploads/.htaccess denies all direct access
2. All file access through serve endpoint with token
3. Token is one-time and time-limited (10 min)
4. Token includes document_id to prevent accessing other documents
5. Ownership verified before streaming
```

### Acceptance Criteria

- [ ] Student can upload JPG, PNG, PDF files
- [ ] Files larger than max size are rejected with error message
- [ ] Invalid file types are rejected
- [ ] File stored in correct directory structure
- [ ] File name in DB: original_name and stored_name different
- [ ] File accessible only through serve endpoint (not direct URL)
- [ ] Token authentication works for file serving
- [ ] Token expires after 10 minutes
- [ ] Cannot access another student's documents via serve endpoint
- [ ] Upload page shows list of already uploaded documents
- [ ] Required documents list shows missing vs completed

---

## PHASE 7 — APPLICATION TRACKING MODULE

### Purpose

Student views their application status and complete audit trail. Status timeline shows every change with dates and remarks. Application detail page shows all submitted data.

### Required Files

```
New:
  app/services/StatusService.php
  app/services/TrackingService.php

  app/views/applications/show.php            ← Full application view with timeline
  app/views/applications/track.php           ← Status tracking page

Modified:
  app/controllers/ApplicationController.php   ← Add show method
  app/routes/web.php
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `applications` | Core application data |
| `application_status` | Status names and colors |
| `application_status_logs` | Audit trail |
| `scholarship_details` | Scholarship data |
| `pratibha_details` | Pratibha data |
| `application_documents` | Uploaded documents |
| `students` | Student profile |

### Routes Added

```
GET   /applications/{application_no}              ApplicationController@show        [auth]
GET   /applications/{application_no}/track        ApplicationController@track       [auth, student]
```

### Controller Logic Summary

```
ApplicationController@show:
  1. Fetch application + all related data
  2. Authorization: Student owns it OR admin/representative
  3. Load status timeline from application_status_logs
  4. Show full read-only view

ApplicationController@track:
  1. Same as show but student-focused view
  2. Emphasize timeline and next steps
  3. Show document verification status
```

### Timeline Component Data

The tracking page renders a vertical timeline:

```
[●] Submitted — 2026-06-20 14:30
 │
[●] Under Review — 2026-06-21 10:15
 │   Remarks: Documents being verified
 │
[◌] Approved — Pending
 │
[◌] Scholarship Released — Pending
```

Each step shows:
- Status name
- Date changed
- Who changed it (Representative name or Admin name)
- Remarks

### Acceptance Criteria

- [ ] Student sees complete application data (read-only)
- [ ] Student sees status timeline with dates
- [ ] Timeline shows who made each status change
- [ ] Timeline shows remarks for each change
- [ ] Current status is highlighted
- [ ] Future statuses shown as pending (grayed out)
- [ ] Document upload status visible on tracking page
- [ ] Student cannot see other students' applications
- [ ] Admin/Representative can see any application

---

## PHASE 8 — ADMIN DASHBOARD

### Purpose

Administrator dashboard with full application management. View all applications with filters. Approve or reject applications. Manage students, representatives, and settings.

### Required Files

```
New:
  app/controllers/AdminController.php

  app/views/admin/dashboard.php              ← Admin home with KPI cards + charts
  app/views/admin/students.php               ← Student list with search
  app/views/admin/student-show.php           ← Single student view
  app/views/admin/applications.php           ← Application list with filters
  app/views/admin/application-show.php       ← Full application review
  app/views/admin/representatives.php        ← Representative management
  app/views/admin/settings.php               ← Portal settings

Modified:
  app/routes/web.php
  app/views/layouts/sidebar.php              ← Admin sidebar menu

  public/assets/css/admin.css                ← Admin-specific styles
  public/assets/js/admin.js                  ← Admin JS (charts, filters)
```

### Database Tables Used

| Table | Purpose |
|---|---|
| All tables | Dashboard aggregation, management CRUD |

### Routes Added

```
GET   /admin                                    AdminController@dashboard         [auth, admin]
GET   /admin/students                           AdminController@students          [auth, admin]
GET   /admin/students/{id}                      AdminController@studentShow       [auth, admin]
GET   /admin/applications                       AdminController@applications       [auth, admin]
GET   /admin/applications/{application_no}      AdminController@applicationShow   [auth, admin]
POST  /admin/applications/{application_no}/status AdminController@updateStatus    [auth, admin]
POST  /admin/applications/{application_no}/assign AdminController@assignRepresentative [auth, admin]
GET   /admin/representatives                    AdminController@representatives   [auth, admin]
GET   /admin/settings                           AdminController@settings          [auth, admin]
POST  /admin/settings                           AdminController@updateSettings    [auth, admin]
```

### Controller Logic Summary

```
AdminController@dashboard:
  Aggregation queries:
    - Total students (COUNT from students)
    - Total applications (COUNT from applications)
    - Pending applications (COUNT WHERE status = 'Submitted' OR 'Under Review')
    - Approved applications (COUNT WHERE status = 'Approved')
    - Rejected applications (COUNT WHERE status = 'Rejected')
    - Applications by district (GROUP BY)
    - Recent applications (LIMIT 10, ORDER BY created_at DESC)

AdminController@applications:
  Filters:
    - application_type_id (dropdown)
    - status_id (dropdown)
    - session_id (dropdown)
    - district (text search)
    - application_no (search)
  Pagination: 20 per page
  Show table with columns: App No, Student Name, Type, Status, District, Date

AdminController@applicationShow:
  Full application review:
    - Student profile data
    - Academic data
    - Application type-specific fields
    - Documents list with view/verify buttons
    - Status timeline
    - Action buttons: Approve, Reject, Assign Representative

AdminController@updateStatus:
  POST only
  1. Csrf::validate()
  2. Validate status transition rules (Section 14)
  3. BEGIN TRANSACTION
  4. Update applications.status_id
  5. Insert application_status_logs
  6. COMMIT
  7. Flash success, redirect back

AdminController@assignRepresentative:
  POST only
  1. Update applications.reviewed_by
  2. Log action
```

### Dashboard KPI Cards

```
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ Total     │ │ Pending   │ │ Approved  │ │ Rejected  │
│ Students  │ │ Verif.    │ │            │ │            │
│    150    │ │     45    │ │     80    │ │      5    │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
```

### Acceptance Criteria

- [ ] Admin dashboard shows KPI cards with correct counts
- [ ] Applications list loads with pagination
- [ ] Filters work: type, status, session, district, search
- [ ] Admin can view full application details
- [ ] Admin can approve an application
- [ ] Admin can reject an application with remarks
- [ ] Admin can assign representative to an application
- [ ] Status transitions follow state machine rules
- [ ] Cannot approve/reject without proper status transition
- [ ] Status change creates audit log entry
- [ ] Student list shows all registered students
- [ ] Settings page saves and loads portal settings
- [ ] Mobile responsive: tables become cards, cards stack vertically

---

## PHASE 9 — REPRESENTATIVE DASHBOARD

### Purpose

Representative (village/area coordinator) reviews and verifies applications assigned to them. They can approve or reject documents and add remarks before recommending to admin.

### Required Files

```
New:
  app/controllers/RepresentativeController.php

  app/views/reps/dashboard.php               ← Rep home with assigned applications
  app/views/reps/applications.php            ← Assigned applications list
  app/views/reps/application-show.php        ← Application review with verify actions
  app/views/reps/documents.php               ← Document verification panel

Modified:
  app/routes/web.php
  app/views/layouts/sidebar.php              ← Rep sidebar menu
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `applications` | Filter by reviewed_by |
| `application_documents` | Verify documents |
| `application_status_logs` | Log verification actions |
| `students` | View student details |

### Routes Added

```
GET   /representative                                  RepresentativeController@dashboard          [auth, representative]
GET   /representative/applications                     RepresentativeController@applications        [auth, representative]
GET   /representative/applications/{application_no}    RepresentativeController@show               [auth, representative]
POST  /representative/applications/{application_no}/verify         RepresentativeController@verify  [auth, representative]
POST  /representative/applications/{application_no}/recommend      RepresentativeController@recommend [auth, representative]
POST  /representative/documents/{id}/verify                       RepresentativeController@verifyDocument [auth, representative]
POST  /representative/documents/{id}/reject                       RepresentativeController@rejectDocument [auth, representative]
```

### Controller Logic Summary

```
RepresentativeController@dashboard:
  1. Fetch applications WHERE reviewed_by = Auth::id()
  2. Group by status
  3. Show KPI cards: Total, Pending Verification, Verified, Recommended

RepresentativeController@applications:
  Same query as dashboard but paginated with filters

RepresentativeController@show:
  Full application view (read-only student + academic + application data)
  Document verification panel (per document)
  Remarks form
  Action buttons

RepresentativeController@verify:
  POST only
  1. Mark all documents as 'verified'
  2. Update status to 'Under Review' if currently 'Submitted'
  3. Add remarks
  4. Log action

RepresentativeController@recommend:
  POST only
  1. Add recommendation remarks
  2. Log action (status stays same, just adds note)
  3. Flash: "Recommendation submitted. Awaiting admin approval."

RepresentativeController@verifyDocument / rejectDocument:
  POST only
  1. Update application_documents.verification_status
  2. Flash success
```

### Acceptance Criteria

- [ ] Representative sees only their assigned applications
- [ ] Dashboard shows counts by status
- [ ] Representative can view full application details
- [ ] Representative can verify individual documents
- [ ] Representative can reject individual documents with remarks
- [ ] Representative can add general remarks
- [ ] Representative can mark application as "Under Review"
- [ ] Representative can recommend application for admin approval
- [ ] All actions are logged in status_logs
- [ ] Representative cannot access admin-only actions (approve, reject final)
- [ ] Representative cannot see applications not assigned to them

---

## PHASE 10 — ANNOUNCEMENTS MODULE

### Purpose

Admin publishes announcements visible on homepage. Announcements can be pinned and have expiry dates. Students and guests see active announcements.

### Required Files

```
New:
  app/models/Announcement.php
  app/controllers/AnnouncementController.php

  app/views/announcements/index.php          ← Public announcement list
  app/views/announcements/show.php           ← Single announcement view
  app/views/admin/announcements.php          ← Admin: CRUD announcements
  app/views/admin/announcement-form.php      ← Admin: create/edit form

Modified:
  app/routes/web.php
  app/views/home/index.php                   ← Add announcement cards section
```

### Database Tables Used

| Table | Purpose |
|---|---|
| `announcements` | Announcement records |

### Routes Added

```
# Public
GET   /announcements                    AnnouncementController@index     [guest]
GET   /announcement/{slug}              AnnouncementController@show      [guest]

# Admin
GET   /admin/announcements              AnnouncementController@adminIndex      [auth, admin]
GET   /admin/announcements/create       AnnouncementController@create          [auth, admin]
POST  /admin/announcements              AnnouncementController@store           [auth, admin]
GET   /admin/announcements/{id}/edit    AnnouncementController@edit            [auth, admin]
POST  /admin/announcements/{id}/update  AnnouncementController@update          [auth, admin]
POST  /admin/announcements/{id}/delete  AnnouncementController@destroy         [auth, admin]
```

### Controller Logic Summary

```
AnnouncementController@index (public):
  1. Fetch announcements WHERE is_active = 1
  2. ORDER BY created_at DESC
  3. Paginate: 10 per page
  4. Show list view

AnnouncementController@show (public):
  1. Fetch by slug
  2. Show full announcement

AnnouncementController@admin CRUD:
  Standard CRUD with CSRF protection
  Slug auto-generated from title
  Author set to Auth::id()
```

### Homepage Integration

The landing page (`home/index.php`) includes a "Latest Announcements" section showing the 5 most recent active announcements.

### Acceptance Criteria

- [ ] Public can view announcements list
- [ ] Public can view individual announcement by slug
- [ ] Homepage shows latest 5 announcements
- [ ] Admin can create announcement with title, content, slug
- [ ] Admin can edit announcement
- [ ] Admin can delete announcement (soft-delete: is_active = 0)
- [ ] Admin can toggle is_active (publish/unpublish)
- [ ] Slug auto-generated from title
- [ ] Slug must be unique
- [ ] Inactive announcements don't appear on public pages
- [ ] Author is recorded on creation
- [ ] Mobile responsive announcement cards

---

## IMPLEMENTATION NOTES

### Before Starting Any Phase

1. Read the relevant section of this roadmap
2. Create a Git branch: `git checkout -b phase-X-name`
3. Create files in the exact order listed in Section 5
4. After each file, verify with `php -l filename.php` for syntax
5. After completing a phase, run all acceptance criteria checks
6. Merge to main only after all criteria pass

### During Development

- Use `declare(strict_types=1);` in every PHP file
- Use prepared statements for ALL SQL queries
- Validate ALL user input before processing
- Use CSRF tokens on ALL POST forms
- Use Flash messages for ALL user feedback
- Log ALL status changes and admin actions
- Test on mobile viewport (375px width) after every page build

### Database Migrations

Execute schema files in order:
```
database/schema/01_add_student_auth.sql        ← Phase 2
database/schema/02_create_settings.sql         ← Phase 1 (for Phase 8)
```

Seed files:
```
database/seeds/admin_seeder.php                ← Phase 2
database/seeds/representative_seeder.php       ← Phase 2
database/seeds/academic_session_seeder.php     ← Phase 4
database/seeds/application_type_seeder.php     ← Phase 5
database/seeds/application_status_seeder.php   ← Phase 5
database/seeds/document_type_seeder.php        ← Phase 6
```

### Default Credentials (Seeded)

| Role | Login ID | Password |
|---|---|---|
| Super Admin | admin@tambolisamaj.org | Admin@2026 |
| Admin | committee@tambolisamaj.org | Admin@2026 |
| Representative | rep01@tambolisamaj.org | Rep@2026 |

Password hashed with BCRYPT before inserting.

---

## TOTAL FILE COUNT

| Phase | New Files | Modified Files |
|---|---|---|
| Phase 1 | 42 | 0 |
| Phase 2 | 11 | 3 |
| Phase 3 | 2 | 3 |
| Phase 4 | 4 | 1 |
| Phase 5 | 8 | 1 |
| Phase 6 | 4 | 2 |
| Phase 7 | 2 | 2 |
| Phase 8 | 7 | 3 |
| Phase 9 | 5 | 2 |
| Phase 10 | 5 | 2 |
| **Total** | **90** | **19** |

---

## POST-IMPLEMENTATION (Phase 11 — Future)

Not in current scope. Listed for awareness:

- Forgot password (email reset link)
- Mobile OTP login
- Email notifications on status change
- Export applications to Excel/PDF
- Application PDF download (acknowledgement slip)
- Student dashboard analytics
- Annual report generation
- Bulk SMS integration
- WhatsApp notifications

---

**End of Implementation Roadmap**
